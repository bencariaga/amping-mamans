<?php

namespace App\Http\Controllers\Profile;

use App\Actions\Client\DeleteApplicant;
use App\Actions\Client\UpdateApplicant;
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

class ApplicantProfileController extends Controller
{
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
            'barangay' => ['nullable', 'string'],
            'occupation_id' => ['nullable', 'string', 'exists:occupations,occupation_id'],
            'custom_occupation' => ['nullable', 'string', 'max:30'],
            'job_status' => ['nullable', 'string', 'in:Retired,Permanent,Contractual,Casual'],
            'monthly_income' => ['required', 'integer', 'min:0', 'max:999999'],
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
            'province' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'municipality' => ['nullable', 'string'],
            'subdivision' => ['nullable', 'string'],
            'purok' => ['nullable', 'string'],
            'sitio' => ['nullable', 'string'],
            'street' => ['nullable', 'string'],
            'phase' => ['nullable', 'string'],
            'block_number' => ['nullable', 'string'],
            'house_number' => ['nullable', 'string'],
        ]);

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
