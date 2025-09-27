<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            $request->validate(['phone_number' => 'required|string|max:30']);
            $phoneNumber = $request->input('phone_number');
            $clientCandidates = $request->input('candidates', []);
            $normalizedCandidates = $this->buildPhoneCandidates($phoneNumber, $clientCandidates);

            if (empty($normalizedCandidates)) {
                return response()->json(['error' => 'No phone candidates generated.'], 422);
            }

            $applicant = Applicant::whereHas('client.contacts', function ($query) use ($normalizedCandidates) {
                $query->where('contact_type', 'Application')->whereIn('phone_number', $normalizedCandidates);
            })->with(['client.member', 'patients.member'])->first();

            if (!$applicant) {
                Log::debug('Phone verification candidates', ['candidates' => $normalizedCandidates, 'input' => $phoneNumber]);
                return response()->json(['error' => 'Applicant with this phone number does not exist.'], 404);
            }

            $member = $applicant->client->member ?? null;
            if (!$member) {
                Log::error('Applicant found but related member is missing.', ['applicant_id' => $applicant->applicant_id]);
                return response()->json(['error' => 'Applicant profile is incomplete.'], 404);
            }

            $fullName = Str::of("{$member->first_name} {$member->middle_name} {$member->last_name} {$member->suffix}")->trim();

            $patients = $applicant->patients->map(function ($patient) use ($applicant) {
                $pm = $patient->member ?? null;

                return [
                    'patient_id' => $patient->patient_id,
                    'first_name' => $pm->first_name ?? '',
                    'middle_name' => $pm->middle_name ?? '',
                    'last_name' => $pm->last_name ?? '',
                    'suffix' => $pm->suffix ?? '',
                    'is_applicant' => ($pm && $pm->member_id === ($applicant->client->member_id ?? null)),
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
            Log::error('verifyPhoneNumber error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Assistance request submitted successfully.'], 500);
        }
    }

    private function buildPhoneCandidates($inputPhone, $clientCandidates = [])
    {
        $candidates = collect();

        if ($inputPhone) {
            $raw = Str::of($inputPhone)->trim();
            $digits = Str::replaceMatches('/\D+/', '', $raw);
            $candidates->push($raw);
            $candidates->push($digits);

            if (Str::startsWith($digits, '63') && Str::length($digits) >= 12) {
                $local = Str::of($digits)->substr(2)->prepend('0');
                $candidates->push($local);
                $candidates->push(Str::of($local)->substr(0, 4) . '-' . Str::substr($local, 4, 3) . '-' . Str::substr($local, 7));
                $candidates->push(Str::of($digits)->prepend('+'));
            } elseif (Str::startsWith($digits, '0') && Str::length($digits) === 11) {
                $candidates->push(Str::of($digits)->substr(0, 4) . '-' . Str::substr($digits, 4, 3) . '-' . Str::substr($digits, 7));
                $candidates->push(Str::of($digits)->substr(1)->prepend('+63'));
            } elseif (Str::startsWith($digits, '9') && Str::length($digits) === 10) {
                $local = Str::of($digits)->prepend('0');
                $candidates->push($local);
                $candidates->push(Str::of($local)->substr(0, 4) . '-' . Str::substr($local, 4, 3) . '-' . Str::substr($local, 7));
                $candidates->push(Str::of($local)->substr(1)->prepend('+63'));
            }
        }

        collect($clientCandidates)->each(function ($cand) use ($candidates) {
            $c = Str::of($cand)->trim();

            if ($c) {
                $candidates->push($c);
                $d = Str::replaceMatches('/\D+/', '', $c);

                if ($d) {
                    $candidates->push($d);
                }
            }
        });

        return $candidates->filter()->unique()->values()->all();
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

            $tariffList = TariffList::whereHas('expenseRanges', function ($q) use ($serviceId) {
                $q->where('service_id', $serviceId);
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
            Log::error('calculateAssistanceAmount error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Assistance request submitted successfully.'], 500);
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
                'assistance_amount' => 'required',
                'applied_at' => 'required|date',
                'reapply_at' => 'required|date',
                'patient_id' => 'required|string',
            ]);

            $billedAmount = $request->input('billed_amount');
            $serviceId = $request->input('service_id');

            $tariffList = TariffList::whereHas('expenseRanges', function ($q) use ($serviceId) {
                $q->where('service_id', $serviceId);
            })->orderBy('effectivity_date', 'desc')->firstOrFail();

            $expenseRange = ExpenseRange::where('tariff_list_id', $tariffList->tariff_list_id)
                ->where('service_id', $serviceId)
                ->where('exp_range_min', '<=', $billedAmount)
                ->where('exp_range_max', '>=', $billedAmount)
                ->firstOrFail();

            $assistanceAmountRaw = $request->input('assistance_amount');

            if (is_string($assistanceAmountRaw)) {
                $assistanceAmountRaw = Str::replaceMatches('/[^\d.]/', '', $assistanceAmountRaw);

                if ($assistanceAmountRaw === '') $assistanceAmountRaw = 0;

                $assistanceAmountRaw = (float) $assistanceAmountRaw;
            } elseif (is_numeric($assistanceAmountRaw)) {
                $assistanceAmountRaw = (float) $assistanceAmountRaw;
            } else {
                $assistanceAmountRaw = 0;
            }

            $appliedAt = Carbon::parse($request->input('applied_at'))->toDateTimeString();
            $reapplyAt = Carbon::parse($request->input('reapply_at'))->toDateString();

            $application = Application::create([
                'applicant_id' => $request->input('applicant_id'),
                'affiliate_partner_id' => $request->input('affiliate_partner_id'),
                'exp_range_id' => $expenseRange->exp_range_id,
                'billed_amount' => $billedAmount,
                'applied_at' => $appliedAt,
                'reapply_at' => $reapplyAt,
                'patient_id' => $request->input('patient_id'),
            ]);

            if ($application) {
                DB::table('applications')->where('application_id', $application->application_id)->update([
                    'assistance_amount' => $assistanceAmountRaw
                ]);
            }

            return response()->json([
                'message' => 'Assistance request submitted successfully.',
                'redirect' => route('dashboard'),
                'application_id' => $application->application_id ?? null
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('store application error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Assistance request submitted successfully.'], 500);
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

        $middleInitial = $application->client_middle_name ? Str::substr($application->client_middle_name, 0, 1) . '.' : '';
        $applicantName = Str::of("{$application->client_last_name}, {$application->client_first_name} {$middleInitial}")->trim();

        $html = '<div class="details-grid">';
        $html .= '<div class="detail-label">Application ID:</div>';
        $html .= '<div class="detail-value">' . e($application->application_id) . '</div>';

        $html .= '<div class="detail-label">Applicant:</div>';
        $html .= '<div class="detail-value">' . e($applicantName) . '</div>';

        $html .= '<div class="detail-label">Service:</div>';
        $html .= '<div class="detail-value">' . e($application->service_type ?? 'N/A') . '</div>';

        $html .= '<div class="detail-label">Billed Amount:</div>';
        $html .= '<div class="detail-value">' . Number::format($application->billed_amount, 2) . '</div>';

        $html .= '<div class="detail-label">Assistance Amount:</div>';
        $html .= '<div class="detail-value">' . Number::format($application->assist_amount, 2) . '</div>';

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
