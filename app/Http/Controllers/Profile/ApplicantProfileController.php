<?php

namespace App\Http\Controllers\Profile;

use App\Actions\Applicant\CheckApplicantDuplication;
use App\Actions\Applicant\CheckPatientDuplication;
use App\Actions\Applicant\DeleteApplicant;
use App\Actions\Applicant\UpdateApplicant;
use App\Http\Controllers\Controller;
use App\Models\Authentication\Occupation;
use App\Models\User\Applicant;
use App\Models\User\Client;
use App\Models\User\Contact;
use App\Models\User\Member;
use App\Models\User\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ApplicantProfileController extends Controller
{
    public function __construct(
        private CheckApplicantDuplication $checkApplicantDuplication,
        private CheckPatientDuplication $checkPatientDuplication
    ) {}

    public function show(Applicant $applicant)
    {
        $occupations = Occupation::all();
        $applicantData = $this->loadApplicantData($applicant);

        return view('pages.sidebar.profiles.profile.applicants', [
            'applicant' => $applicant,
            'occupations' => $occupations,
            'applicantData' => $applicantData,
        ]);
    }

    public function update(Request $request, Applicant $applicant, UpdateApplicant $updateApplicant)
    {
        $contactId = Contact::where('client_id', $applicant->client_id)->where('contact_type', 'Application')->value('contact_id');

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:20'],
            'middle_name' => ['nullable', 'string', 'max:20'],
            'last_name' => ['required', 'string', 'max:20'],
            'suffix' => ['nullable', 'string', 'in:Sr.,Jr.,II,III,IV,V'],
            'birth_date' => ['required', 'date'],
            'sex' => ['required', 'string', 'in:Male,Female'],
            'civil_status' => ['required', 'string', 'in:Single,Married,Widowed,Separated'],
            'phone_number' => ['required', 'string', 'max:17', Rule::unique('contacts', 'contact_number')->ignore($contactId, 'contact_id')],
            'barangay' => ['required', 'string'],
            'occupation_id' => ['nullable', 'string', 'exists:occupations,occupation_id'],
            'custom_occupation' => ['nullable', 'string', 'max:30'],
            'job_status' => ['required', 'string', 'in:Retired,Permanent,Contractual,Casual'],
            'monthly_income' => ['required', 'integer', 'min:0', 'max:9999999'],
            'house_occup_status' => ['required', 'string', 'in:Owner,Renter,House Sharer'],
            'lot_occup_status' => ['required', 'string', 'in:Owner,Renter,Lot Sharer,Informal Settler'],
            'phic_affiliation' => ['required', 'string', 'in:Affiliated,Unaffiliated'],
            'phic_category' => ['nullable', 'string', 'in:Self-Employed,Sponsored / Indigent,Employed'],
            'patient_number' => ['required', 'integer', 'min:1', 'max:10'],
            'patients.*.last_name' => ['required', 'string', 'max:20'],
            'patients.*.first_name' => ['required', 'string', 'max:20'],
            'patients.*.middle_name' => ['nullable', 'string', 'max:20'],
            'patients.*.suffix' => ['nullable', 'string', 'in:Sr.,Jr.,II,III,IV,V'],
            'patients.*.sex' => ['required', 'string', 'in:Male,Female'],
            'patients.*.age' => ['required', 'integer', 'min:1', 'max:999'],
            'patients.*.patient_category' => ['nullable', 'string', 'in:PWD,Senior'],
            'include_applicant_as_patient' => ['nullable', 'boolean'],
            'applicant_age' => ['required', 'integer', 'min:0'],
            'province' => ['required', 'string'],
            'city' => ['required', 'string'],
            'municipality' => ['nullable', 'string'],
            'subdivision' => ['nullable', 'string', 'max:20'],
            'purok' => ['nullable', 'string', 'max:20'],
            'sitio' => ['nullable', 'string', 'max:20'],
            'street' => ['nullable', 'string', 'max:20'],
            'phase' => ['nullable', 'string', 'max:10'],
            'block_number' => ['nullable', 'string', 'max:10'],
            'house_number' => ['nullable', 'string', 'max:10'],
        ], [
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'birth_date.required' => 'The birthdate is required.',
            'sex.required' => 'The gender / sex is required.',
            'civil_status.required' => 'The civil status is required.',
            'phone_number.required' => 'The phone number is required.',
            'phone_number.unique' => 'This phone number is already registered.',
            'barangay.required' => 'The barangay is required.',
            'job_status.required' => 'The job status is required.',
            'monthly_income.required' => 'The monthly income is required.',
            'house_occup_status.required' => 'The house occupancy status is required.',
            'lot_occup_status.required' => 'The lot occupancy status is required.',
            'phic_affiliation.required' => 'The PhilHealth affiliation is required.',
            'patient_number.required' => 'The number of patients is required.',
            'province.required' => 'The province is required.',
            'city.required' => 'The city is required.',
            'patients.*.first_name.required' => 'This patient\'s first name is required.',
            'patients.*.last_name.required' => 'This patient\'s last name is required.',
            'patients.*.sex.required' => 'This patient\'s gender / sex is required.',
            'patients.*.age.required' => 'This patient\'s age is required.',
        ]);

        if ($this->checkApplicantDuplication->execute(
            $validated['first_name'],
            $validated['last_name'],
            $validated['middle_name'] ?? null,
            $validated['suffix'] ?? null,
            $applicant->client_id
        )) {
            throw ValidationException::withMessages([
                'first_name' => 'An applicant with this name already exists.',
            ]);
        }

        if (isset($validated['patients']) && is_array($validated['patients'])) {
            $internalDuplicates = $this->checkPatientDuplication->checkWithinApplicantPatients($validated['patients']);
            if (! empty($internalDuplicates)) {
                throw ValidationException::withMessages([
                    'patients' => 'Duplicate patient names found at positions: '.implode(', ', $internalDuplicates),
                ]);
            }

            foreach ($validated['patients'] as $index => $patient) {
                if ($this->checkPatientDuplication->execute(
                    $patient['first_name'],
                    $patient['last_name'],
                    $patient['middle_name'] ?? null,
                    $patient['suffix'] ?? null,
                    $applicant->applicant_id
                )) {
                    throw ValidationException::withMessages([
                        "patients.{$index}.first_name" => 'A patient with this name already exists in another applicant record.',
                    ]);
                }

                $applicantKey = strtolower(trim($validated['first_name'])).'|'.
                    strtolower(trim($validated['last_name'])).'|'.
                    strtolower(trim($validated['middle_name'] ?? '')).'|'.
                    strtolower(trim($validated['suffix'] ?? ''));
                $patientKey = strtolower(trim($patient['first_name'])).'|'.
                    strtolower(trim($patient['last_name'])).'|'.
                    strtolower(trim($patient['middle_name'] ?? '')).'|'.
                    strtolower(trim($patient['suffix'] ?? ''));

                if ($applicantKey === $patientKey) {
                    throw ValidationException::withMessages([
                        "patients.{$index}.first_name" => 'This patient\'s name cannot be the same as the applicant name.',
                    ]);
                }
            }
        }

        $updateApplicant->execute($applicant, $validated);

        return redirect()->route('profiles.applicants.show', ['applicant' => $applicant->applicant_id])->with('success', 'Applicant profile has been updated successfully.');
    }

    public function destroy(Request $request, Applicant $applicant, DeleteApplicant $deleteApplicant)
    {
        $fullName = (string) Str::of("{$applicant->client->member->first_name} {$applicant->client->member->last_name}")->trim();
        $entered = Str::of($request->input('deleteConfirmationText', ''))->trim();

        if (strcasecmp($entered, $fullName) !== 0) {
            session()->flash('error', 'Confirmation text does not match.');

            return back()->withInput();
        }

        $deleteApplicant->execute($applicant);

        session()->flash('success', 'Applicant and all associated data have been successfully deleted.');

        return redirect()->route('profiles.applicants.list');
    }

    private function loadApplicantData(Applicant $applicant): array
    {
        $client = $applicant->client;
        $member = $client->member;
        $contact = Contact::where('client_id', $client->client_id)->where('contact_type', 'Application')->first();

        $patients = Patient::where('applicant_id', $applicant->applicant_id)->get();
        $patientsData = [];

        $applicantPatient = $patients->firstWhere('client_id', $client->client_id);
        $otherPatients = $patients->where('client_id', '!=', $client->client_id)->values();

        $currentIndex = 1;

        if ($applicantPatient) {
            $patientClient = Client::where('client_id', $applicantPatient->client_id)->first();
            $patientMember = $patientClient ? Member::where('member_id', $patientClient->member_id)->first() : null;

            $patientsData[$currentIndex] = [
                'last_name' => $patientMember->last_name ?? '',
                'first_name' => $patientMember->first_name ?? '',
                'middle_name' => $patientMember->middle_name ?? '',
                'suffix' => $patientMember->suffix ?? '',
                'sex' => $patientClient->sex ?? '',
                'age' => (int) ($patientClient->age ?? 0),
                'patient_category' => $applicantPatient->patient_category ?? '',
                'client_id' => $applicantPatient->client_id,
            ];
            $currentIndex++;
        }

        foreach ($otherPatients as $patient) {
            $patientClient = Client::where('client_id', $patient->client_id)->first();
            $patientMember = $patientClient ? Member::where('member_id', $patientClient->member_id)->first() : null;

            $patientsData[$currentIndex] = [
                'last_name' => $patientMember->last_name ?? '',
                'first_name' => $patientMember->first_name ?? '',
                'middle_name' => $patientMember->middle_name ?? '',
                'suffix' => $patientMember->suffix ?? '',
                'sex' => $patientClient->sex ?? '',
                'age' => (int) ($patientClient->age ?? 0),
                'patient_category' => $patient->patient_category ?? '',
                'client_id' => $patient->client_id,
            ];
            $currentIndex++;
        }

        if (empty($patientsData)) {
            $patientsData = [1 => ['last_name' => '', 'first_name' => '', 'middle_name' => '', 'suffix' => '', 'sex' => '', 'age' => '', 'patient_category' => '', 'client_id' => null]];
        }

        return [
            'first_name' => $member->first_name,
            'middle_name' => $member->middle_name,
            'last_name' => $member->last_name,
            'suffix' => $member->suffix ?? '',
            'birth_date' => $client->birthdate,
            'sex' => $client->sex,
            'civil_status' => $client->civil_status,
            'phone_number' => $contact->contact_number ?? '',
            'province' => $applicant->province ?? 'South Cotabato',
            'city' => $applicant->city ?? 'General Santos',
            'municipality' => $applicant->municipality ?? 'N / A',
            'barangay' => $applicant->barangay ?? '',
            'subdivision' => $applicant->subdivision ?? '',
            'purok' => $applicant->purok ?? '',
            'sitio' => $applicant->sitio ?? '',
            'street' => $applicant->street ?? '',
            'phase' => $applicant->phase ?? '',
            'block_number' => $applicant->block_number ?? '',
            'house_number' => $applicant->house_number ?? '',
            'occupation_id' => $client->occupation_id,
            'custom_occupation' => '',
            'job_status' => $applicant->job_status ?? '',
            'monthly_income' => isset($client->monthly_income) ? (int) $client->monthly_income : 0,
            'house_occup_status' => $applicant->house_occup_status ?? '',
            'lot_occup_status' => $applicant->lot_occup_status ?? '',
            'phic_affiliation' => $applicant->phic_affiliation ?? '',
            'phic_category' => $applicant->phic_category ?? null,
            'patient_number' => (int) $applicant->patient_number,
            'include_applicant_as_patient' => $applicant->is_also_patient === 'Yes',
            'applicant_age' => (int) $client->age,
            'patients' => $patientsData,
        ];
    }
}
