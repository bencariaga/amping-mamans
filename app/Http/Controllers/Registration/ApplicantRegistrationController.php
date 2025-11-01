<?php

namespace App\Http\Controllers\Registration;

use App\Actions\Applicant\CheckApplicantDuplication;
use App\Actions\Applicant\CheckPatientDuplication;
use App\Actions\Applicant\CreateApplicant;
use App\Http\Controllers\Controller;
use App\Models\Authentication\Occupation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApplicantRegistrationController extends Controller
{
    public function __construct(
        private CheckApplicantDuplication $checkApplicantDuplication,
        private CheckPatientDuplication $checkPatientDuplication
    ) {}

    public function create()
    {
        $occupations = Occupation::all();

        return view('pages.sidebar.profiles.register.applicant', ['occupations' => $occupations]);
    }

    public function store(Request $request, CreateApplicant $createApplicant)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:20'],
            'middle_name' => ['nullable', 'string', 'max:20'],
            'last_name' => ['required', 'string', 'max:20'],
            'suffix' => ['nullable', 'string', 'in:Sr.,Jr.,II,III,IV,V'],
            'birth_date' => ['required', 'date'],
            'sex' => ['required', 'string', 'in:Male,Female'],
            'civil_status' => ['required', 'string', 'in:Single,Married,Widowed,Separated'],
            'phone_number' => ['required', 'string', 'max:17', 'unique:contacts,contact_number'],
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
            $validated['suffix'] ?? null
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
                    $patient['suffix'] ?? null
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

        $createApplicant->execute($validated);

        return redirect()->route('profiles.applicants.list')->with('success', 'Applicant has been added successfully.');
    }
}
