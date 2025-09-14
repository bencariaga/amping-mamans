<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Operation\Service;
use App\Models\Operation\ExpenseRange;
use App\Models\Operation\TariffList;
use App\Models\Operation\Application;
use App\Models\User\AffiliatePartner;
use App\Models\User\Applicant;
use Exception;

class ApplicationController extends Controller
{
    public function showAssistanceRequest()
    {
        $services = Service::orderBy('service_type')->get();
        $affiliate_partners = AffiliatePartner::orderBy('affiliate_partner_name')->get();
        return view('pages.sidebar.application-entry.assistance-request', ['services' => $services, 'affiliate_partners' => $affiliate_partners]);
    }

    public function verifyPhoneNumber(Request $request)
    {
        try {
            $request->validate(['phone_number' => 'required|string|max:13']);
            $phoneNumber = $request->input('phone_number');

            $applicant = Applicant::whereHas('client.contacts', function ($query) use ($phoneNumber) {
                $query->where('contact_type', 'Application')
                    ->where(function ($q) use ($phoneNumber) {
                        $q->where('phone_number', $phoneNumber)
                            ->orWhere('phone_number_other', 'like', "%{$phoneNumber}%");
                    });
            })->with(['client.member', 'patients.member'])->first();

            if (!$applicant) {
                return response()->json(['error' => 'Applicant with this phone number does not exist.'], 404);
            }

            $member = $applicant->client->member;
            $firstName = $member->first_name ?? '';
            $middleName = $member->middle_name ?? '';
            $lastName = $member->last_name ?? '';
            $suffix = $member->suffix ?? '';
            $fullName = Str::of("{$firstName} {$middleName} {$lastName} {$suffix}")->trim();

            $patients = $applicant->patients->map(function ($patient) use ($applicant) {
                return [
                    'patient_id' => $patient->patient_id,
                    'first_name' => $patient->member->first_name ?? '',
                    'middle_name' => $patient->member->middle_name ?? '',
                    'last_name' => $patient->member->last_name ?? '',
                    'suffix' => $patient->member->suffix ?? '',
                    'is_applicant' => $patient->member_id === $applicant->client->member_id,
                ];
            });

            return response()->json([
                'message' => 'Phone number has matched, applicant found.',
                'applicant_name' => $fullName,
                'applicant_id' => $applicant->applicant_id,
                'patients' => $patients
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function calculateAssistanceAmount(Request $request)
    {
        try {
            $request->validate([
                'service_id' => 'required|string',
                'billed_amount' => 'required|numeric|min:0',
            ]);

            $serviceId = $request->input('service_id');
            $billedAmount = $request->input('billed_amount');

            $tariffList = TariffList::whereHas('expenseRanges.service', function ($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })->orderBy('effectivity_date', 'desc')->first();

            if (!$tariffList) {
                return response()->json(['error' => 'No tariff list found for this service.'], 404);
            }

            $expenseRange = ExpenseRange::where('tariff_list_id', $tariffList->tariff_list_id)
                ->where('service_id', $serviceId)
                ->where('exp_range_min', '<=', $billedAmount)
                ->where('exp_range_max', '>=', $billedAmount)
                ->first();

            if (!$expenseRange) {
                return response()->json(['error' => 'The expense ranges of this service type for this amount does not exist.'], 404);
            }

            return response()->json([
                'assistance_amount' => (float) $expenseRange->assist_amount,
                'tariff_list_version' => $tariffList->tariff_list_id
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'applicant_id' => 'required|string',
                'affiliate_partner_id' => 'required|string',
                'service_id' => 'required|string',
                'billed_amount' => 'required|numeric|min:0',
                'assistance_amount' => 'required|numeric|min:0',
                'applied_at' => 'required|date',
                'reapply_at' => 'required|date',
                'patient_id' => 'required|string',
            ]);

            $billedAmount = $request->input('billed_amount');
            $serviceId = $request->input('service_id');

            $tariffList = TariffList::whereHas('expenseRanges.service', function ($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })->orderBy('effectivity_date', 'desc')->firstOrFail();

            $expenseRange = ExpenseRange::where('tariff_list_id', $tariffList->tariff_list_id)
                ->where('service_id', $serviceId)
                ->where('exp_range_min', '<=', $billedAmount)
                ->where('exp_range_max', '>=', $billedAmount)
                ->firstOrFail();

            $assistanceAmountRaw = $request->input('assistance_amount');

            if (is_string($assistanceAmountRaw)) {
                $assistanceAmountRaw = (string) Str::of($assistanceAmountRaw)->replaceMatches('/[^\d.]/', '');
            }

            $appliedAt = Carbon::parse($request->input('applied_at'))->toDateTimeString();
            $reapplyAt = Carbon::parse($request->input('reapply_at'))->toDateString();

            $application = Application::create([
                'applicant_id' => $request->input('applicant_id'),
                'affiliate_partner_id' => $request->input('affiliate_partner_id'),
                'exp_range_id' => $expenseRange->exp_range_id,
                'billed_amount' => $billedAmount,
                'assistance_amount' => $assistanceAmountRaw,
                'patient_id' => $request->input('patient_id'),
                'applied_at' => $appliedAt,
                'reapply_at' => $reapplyAt,
            ]);

            return response()->json([
                'message' => 'Assistance request submitted successfully.',
                'redirect' => route('dashboard'),
                'application_id' => $application->application_id ?? null
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function details($applicationId)
    {
        $application = DB::table('applications as a')
            ->leftJoin('applicants as ap', 'a.applicant_id', '=', 'ap.applicant_id')
            ->leftJoin('clients as c', 'ap.client_id', '=', 'c.client_id')
            ->leftJoin('members as m', 'c.member_id', '=', 'm.member_id')
            ->leftJoin('expense_ranges as er', 'a.exp_range_id', '=', 'er.exp_range_id')
            ->leftJoin('services as s', 'er.service_id', '=', 's.service_id')
            ->leftJoin('affiliate_partners as apn', 'a.affiliate_partner_id', '=', 'apn.affiliate_partner_id')
            ->select(
                'a.*',
                'ap.*',
                'c.*',
                'm.first_name as client_first_name',
                'm.middle_name as client_middle_name',
                'm.last_name as client_last_name',
                'er.assist_amount',
                'er.exp_range_min',
                'er.exp_range_max',
                's.service_type',
                'apn.affiliate_partner_name'
            )
            ->where('a.application_id', $applicationId)
            ->first();

        if (!$application) {
            return response()->json(['html' => '<div>Application not found.</div>'], 404);
        }

        $firstName = $application->client_first_name ?? '';
        $middleName = $application->client_middle_name ?? '';
        $lastName = $application->client_last_name ?? '';
        $middleInitial = $middleName ? Str::substr($middleName, 0, 1) . '.' : '';
        $applicantName = Str::of("{$lastName}, {$firstName} {$middleInitial}")->trim();

        $html = '<div class="details-grid">';
        $html .= '<div class="detail-label">Application ID:</div>';
        $html .= '<div class="detail-value">' . e($application->application_id) . '</div>';

        $html .= '<div class="detail-label">Applicant:</div>';
        $html .= '<div class="detail-value">' . e($applicantName) . '</div>';

        $html .= '<div class="detail-label">Service:</div>';
        $html .= '<div class="detail-value">' . e($application->service_type ?? 'N/A') . '</div>';

        $html .= '<div class="detail-label">Billed Amount:</div>';
        $html .= '<div class="detail-value">' . Number::currency($application->billed_amount, 'PHP') . '</div>';

        $html .= '<div class="detail-label">Assistance Amount:</div>';
        $html .= '<div class="detail-value">' . Number::currency($application->assist_amount, 'PHP') . '</div>';

        $html .= '<div class="detail-label">Affiliate Partner:</div>';
        $html .= '<div class="detail-value">' . e($application->affiliate_partner_name ?? 'N/A') . '</div>';

        $html .= '<div class="detail-label">Applied At:</div>';
        $html .= '<div class="detail-value">' . e($application->applied_at) . '</div>';

        $html .= '<div class="detail-label">Reapply At:</div>';
        $html .= '<div class="detail-value">' . e($application->reapply_at) . '</div>';
        $html .= '</div>';

        return response()->json(['html' => $html]);
    }
}
