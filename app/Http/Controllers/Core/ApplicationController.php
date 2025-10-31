<?php

namespace App\Http\Controllers\Core;

use App\Actions\Application\DeleteApplication;
use App\Actions\Application\GetLatestActiveTariff;
use App\Actions\Application\VerifyPhoneNumber;
use App\Http\Controllers\Communication\MessageController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Financial\BudgetUpdateController;
use App\Models\Communication\MessageTemplate;
use App\Models\Operation\Application;
use App\Models\Operation\ExpenseRange;
use App\Models\Operation\Service;
use App\Models\User\AffiliatePartner;
use App\Models\User\Applicant;
use App\Services\FakeSmsService;
use App\Services\TextBeeService;
use App\Support\Number;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{
    private function formatPatientData($applicant)
    {
        $patients = $applicant->patients->map(function ($patient) {
            $member = $patient->client->member ?? null;

            return [
                'patient_id' => $patient->patient_id,
                'patient_name' => $member->full_name ?? '',
                'patient_first_name' => $member->first_name ?? '',
                'patient_middle_name' => $member->middle_name ?? '',
                'patient_last_name' => $member->last_name ?? '',
                'patient_suffix' => $member->suffix ?? '',
            ];
        })->filter(fn ($p) => ! empty($p['patient_name']))->values()->all();

        $firstPatient = collect($patients)->first();

        return [
            'patients' => $patients,
            'patient_name' => $firstPatient['patient_name'] ?? '',
            'patient_id' => $firstPatient['patient_id'] ?? '',
            'patient_first_name' => $firstPatient['patient_first_name'] ?? '',
            'patient_middle_name' => $firstPatient['patient_middle_name'] ?? '',
            'patient_last_name' => $firstPatient['patient_last_name'] ?? '',
            'patient_suffix' => $firstPatient['patient_suffix'] ?? '',
        ];
    }

    public function showAssistanceRequest(Request $request)
    {
        $services = Service::orderBy('service_type')->get();
        $affiliate_partners = AffiliatePartner::orderBy('affiliate_partner_name')->get();
        $message_templates = MessageTemplate::orderBy('msg_tmp_title')->get();

        $applicantId = $request->query('applicant');
        $applicantData = null;

        if ($applicantId) {
            $applicant = Applicant::with(['client.contacts', 'client.member', 'patients.client.member'])->where('applicant_id', $applicantId)->first();

            if ($applicant) {
                $phone = $applicant->client->contacts->where('contact_type', 'Application')->first();
                $member = $applicant->client->member ?? null;
                $patientData = $this->formatPatientData($applicant);

                $applicantData = collect([
                    'phone_number' => $phone->phone_number ?? '',
                    'applicant_id' => $applicant->applicant_id,
                    'applicant_name' => $member->full_name ?? '',
                    'applicant_first_name' => $member->first_name ?? '',
                    'applicant_middle_name' => $member->middle_name ?? '',
                    'applicant_last_name' => $member->last_name ?? '',
                    'applicant_suffix' => $member->suffix ?? '',
                ])->merge($patientData)->toArray();
            }
        }

        return view('pages.sidebar.application-entry.request-service-assistance', [
            'services' => $services,
            'affiliate_partners' => $affiliate_partners,
            'message_templates' => $message_templates,
            'applicantData' => $applicantData,
        ]);
    }

    public function verifyPhoneNumber(Request $request, VerifyPhoneNumber $verifyPhoneNumber)
    {
        try {
            $request->validate(['phone_number' => 'required|string|max:30']);
            $phoneNumber = $request->input('phone_number');
            $clientCandidates = $request->input('candidates', []);

            $applicant = $verifyPhoneNumber->execute($phoneNumber, $clientCandidates);

            if (!$applicant) {
                return response()->json(['error' => 'Applicant with this phone number does not exist.'], 404);
            }

            $member = $applicant->client->member ?? null;

            if (!$member) {
                Log::error('Applicant found but related member is missing.', ['applicant_id' => $applicant->applicant_id]);

                return response()->json(['error' => 'Applicant profile is incomplete.'], 404);
            }

            $fullName = $member->full_name ?? '';
            $patientData = $this->formatPatientData($applicant);

            return response()->json(collect([
                'message' => 'Phone number has a match, applicant found.',
                'applicant_name' => $fullName,
                'applicant_id' => $applicant->applicant_id,
                'applicant_first_name' => $member->first_name ?? '',
                'applicant_middle_name' => $member->middle_name ?? '',
                'applicant_last_name' => $member->last_name ?? '',
                'applicant_suffix' => $member->suffix ?? '',
            ])->merge($patientData)->toArray());
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('verifyPhoneNumber error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['error' => 'An unexpected error occurred during phone verification.'], 500);
        }
    }



    public function calculateAssistanceAmount(Request $request, GetLatestActiveTariff $getLatestActiveTariff)
    {
        try {
            $request->validate([
                'service_id' => 'required|string',
                'billed_amount' => 'required|numeric|min:0',
            ]);

            $serviceId = $request->input('service_id');
            $billedAmount = $request->input('billed_amount');

            $tariffList = $getLatestActiveTariff->execute($serviceId);

            if (!$tariffList) {
                return response()->json(['error' => 'No active tariff list found for this service.'], 404);
            }

            $expenseRange = ExpenseRange::where('tariff_list_id', $tariffList->tariff_list_id)
                ->where('service_id', $serviceId)
                ->where('exp_range_min', '<=', $billedAmount)
                ->where('exp_range_max', '>=', $billedAmount)
                ->first();

            if (!$expenseRange) {
                return response()->json(['error' => 'The expense ranges of this service type for this amount does not exist.'], 404);
            }

            $coveragePercent = (float) $expenseRange->coverage_percent;
            $assistanceAmount = ($billedAmount * $coveragePercent) / 100;

            return response()->json([
                'assistance_amount' => (float) $assistanceAmount,
                'tariff_list_version' => $tariffList->tariff_list_id,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('calculateAssistanceAmount error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['error' => 'An unexpected error occurred during assistance calculation.'], 500);
        }
    }

    public function store(Request $request, MessageController $messageController, BudgetUpdateController $budgetUpdateController, GLController $glController, TextBeeService $textBeeService, FakeSmsService $fakeSmsService)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'applicant_id' => 'required|string|exists:applicants,applicant_id',
                'patient_id' => 'required|string|exists:patients,patient_id',
                'msg_tmp_id' => 'required|string|exists:message_templates,msg_tmp_id',
                'phone_number' => 'required|string|max:20',
                'patient_name_hidden' => 'required|string|max:100',
                'service_id' => 'required|string|exists:services,service_id',
                'affiliate_partner_id' => 'nullable|string|exists:affiliate_partners,affiliate_partner_id',
                'billed_amount' => 'required|string|max:20',
                'assistance_amount' => 'required|string|max:20',
                'tariff_list_version' => 'required|string|max:20',
                'applied_at' => 'required|date',
                'reapply_at' => 'required|date|after:applied_at',
                'message_text' => 'required|string|max:1000',
            ]);

            $billedAmount = (int) Str::replace(',', '', $request->input('billed_amount'));
            $assistanceAmount = (int) Str::replace(',', '', $request->input('assistance_amount'));
            $serviceId = $request->input('service_id');
            $tariffListVersion = $request->input('tariff_list_version');
            
            // Find the correct expense range based on the tariff list version and service
            $expRange = ExpenseRange::where('tariff_list_id', $tariffListVersion)
                ->where('service_id', $serviceId)
                ->where('exp_range_min', '<=', $billedAmount)
                ->where('exp_range_max', '>=', $billedAmount)
                ->first();

            $data = [
                'applicant_id' => $request->input('applicant_id'),
                'patient_id' => $request->input('patient_id'),
                'affiliate_partner_id' => $request->input('affiliate_partner_id'),
                'exp_range_id' => $expRange->exp_range_id ?? null,
                'billed_amount' => $billedAmount,
                'assistance_amount' => $assistanceAmount,
                'applied_at' => $request->input('applied_at'),
                'reapply_at' => $request->input('reapply_at'),
            ];

            $application = Application::create($data);

            $budgetUpdate = $budgetUpdateController->createForApplication($application, $assistanceAmount);
            $guaranteeLetter = $glController->createForApplication($application, $budgetUpdate);

            $request->merge(['application_id' => $application->application_id]);
            $messageId = $messageController->sendMessage($request, $textBeeService, $fakeSmsService);
            $application->update(['message_id' => $messageId]);

            DB::commit();

            return response()->json([
                'message' => 'Application entry has been successfully created and SMS notification has been sent to applicant!',
                'redirect' => route('applications.list'),
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Store application validation error: '.$e->getMessage(), ['errors' => $e->errors(), 'trace' => $e->getTraceAsString()]);

            return response()->json(['error' => 'Validation failed: Please check your inputs.', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Store application error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['error' => 'An unexpected error occurred while creating the application.'], 500);
        }
    }

    private function aliasId(string $id, string $prefix): string
    {
        $withoutPrefix = Str::after($id, $prefix.'-');

        if (Str::contains($withoutPrefix, '-') !== false) {
            [$year, $num] = Str::of($withoutPrefix)->explode('-', 2)->all();
        } else {
            $year = Str::substr($withoutPrefix, 0, 4);
            $num = Str::substr($withoutPrefix, 4);
        }

        $numLast5 = Str::substr($num, -5);

        return $year.'-'.$numLast5;
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
                'er.exp_range_min',
                'er.exp_range_max',
                's.service_type',
                'apn.affiliate_partner_name'
            )
            ->where('a.application_id', $applicationId)
            ->first();

        if (! $application) {
            return response()->json(['html' => '<div>Application not found.</div>'], 404);
        }

        $applicationAlias = $this->aliasId($application->application_id, 'APPLICATION');
        $middleInitial = $application->client_middle_name ? Str::substr($application->client_middle_name, 0, 1).'.' : '';
        $applicantName = Str::of("{$application->client_last_name}, {$application->client_first_name} {$middleInitial}")->trim();
        $patientName = '';

        if ($application->patient_id) {
            $patient = DB::table('patients as p')
                ->join('clients as c', 'p.client_id', '=', 'c.client_id')
                ->join('members as m', 'c.member_id', '=', 'm.member_id')
                ->where('p.patient_id', $application->patient_id)
                ->select('m.first_name as patient_first_name', 'm.middle_name as patient_middle_name', 'm.last_name as patient_last_name')
                ->first();

            $middleInitial = $patient->patient_middle_name ? Str::substr($patient->patient_middle_name, 0, 1).'.' : '';
            $patientName = Str::of("{$patient->patient_last_name}, {$patient->patient_first_name} {$middleInitial}")->trim();
        }

        $html = '<div class="details-grid">';
        $html .= '<div class="detail-label">Application Number:</div>';
        $html .= '<div class="detail-value">'.e($applicationAlias).'</div>';

        $html .= '<div class="detail-label">Applicant Name:</div>';
        $html .= '<div class="detail-value">'.e($applicantName).'</div>';

        $html .= '<div class="detail-label">Patient Name:</div>';
        $html .= '<div class="detail-value">'.e($patientName).'</div>';

        $html .= '<div class="detail-label">Service Type Applied:</div>';
        $html .= '<div class="detail-value">'.e($application->service_type ?? 'N/A').'</div>';

        $html .= '<div class="detail-label">Billed Amount:</div>';
        $html .= '<div class="detail-value">₱ '.Number::format($application->billed_amount).'</div>';

        $html .= '<div class="detail-label">Assistance Amount:</div>';
        $html .= '<div class="detail-value">₱ '.Number::format($application->assistance_amount).'</div>';

        $html .= '<div class="detail-label">Affiliate Partner:</div>';
        $html .= '<div class="detail-value">'.e($application->affiliate_partner_name ?? 'N/A').'</div>';

        $html .= '<div class="detail-label">Applied At:</div>';
        $html .= '<div class="detail-value">'.e($application->applied_at).'</div>';

        $html .= '<div class="detail-label">Reapply At:</div>';
        $html .= '<div class="detail-value">'.e($application->reapply_at).'</div>';
        $html .= '</div>';

        return response()->json(['html' => $html]);
    }

    public function destroy(string $applicationId, DeleteApplication $deleteApplication)
    {
        DB::beginTransaction();

        try {
            $deleteApplication->execute($applicationId);
            DB::commit();

            return response()->json([
                'message' => 'Application and associated Guarantee Letter successfully deleted.',
                'redirect' => route('applications.list'),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Destroy application error: '.$e->getMessage(), ['application_id' => $applicationId, 'trace' => $e->getTraceAsString()]);

            return response()->json(['error' => 'An unexpected error occurred while deleting the application.'], 500);
        }
    }
}
