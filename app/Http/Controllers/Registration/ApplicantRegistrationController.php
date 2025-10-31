<?php

namespace App\Http\Controllers\Registration;

use App\Actions\Client\CreateApplicant;
use App\Http\Controllers\Controller;
use App\Models\Authentication\Occupation;
use Illuminate\Http\Request;

class ApplicantRegistrationController extends Controller
{
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
            'phone_number' => ['required', 'string', 'max:17', 'unique:contacts,phone_number'],
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

        $createApplicant->execute($validated);

        return redirect()->route('profiles.applicants.list')->with('success', 'Applicant has been added successfully.');
    }

}
