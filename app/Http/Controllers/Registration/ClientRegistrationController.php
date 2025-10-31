<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\Authentication\Account;
use App\Models\Authentication\Occupation;
use App\Models\Operation\Data;
use App\Models\User\Applicant;
use App\Models\User\Client;
use App\Models\User\Contact;
use App\Models\User\Member;
use App\Models\User\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClientRegistrationController extends Controller
{
    public function create()
    {
        $occupations = Occupation::all();

        return view('pages.sidebar.profiles.register.applicant', ['occupations' => $occupations]);
    }

    public function store(Request $request)
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

        DB::transaction(function () use ($validated) {
            $dataId = $this->generateNextId('DATA', 'data', 'data_id');
            $acctId = $this->generateNextId('ACCOUNT', 'accounts', 'account_id');
            $memberId = $this->generateNextId('MEMBER', 'members', 'member_id');
            $clientId = $this->generateNextId('CLIENT', 'clients', 'client_id');
            $contactId = $this->generateNextId('CONTACT', 'contacts', 'contact_id');
            $applicantId = $this->generateNextId('APPLICANT', 'applicants', 'applicant_id');

            Data::create([
                'data_id' => $dataId,
                'data_status' => 'Unarchived',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Account::create([
                'account_id' => $acctId,
                'data_id' => $dataId,
                'account_status' => 'Active',
                'registered_at' => now(),
            ]);

            $occId = $validated['occupation_id'] ?? null;

            if (! empty($validated['custom_occupation'])) {
                $d2 = $this->generateNextId('DATA', 'data', 'data_id');

                Data::create([
                    'data_id' => $d2,
                    'data_status' => 'Unarchived',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $occupation = Occupation::create([
                    'occupation_id' => $this->generateNextId('OCCUP', 'occupations', 'occupation_id'),
                    'data_id' => $d2,
                    'occupation' => $validated['custom_occupation'],
                ]);

                $occId = $occupation->occupation_id;
            }

            $fullName = collect([
                $validated['first_name'],
                $validated['middle_name'],
                $validated['last_name'],
                $validated['suffix'],
            ])->filter()->implode(' ');

            Member::create([
                'member_id' => $memberId,
                'account_id' => $acctId,
                'first_name' => Str::title($validated['first_name']),
                'middle_name' => Str::title($validated['middle_name']) ?: null,
                'last_name' => Str::title($validated['last_name']),
                'suffix' => $validated['suffix'] ?: null,
                'full_name' => Str::title($fullName),
            ]);

            Client::create([
                'client_id' => $clientId,
                'member_id' => $memberId,
                'occupation_id' => $occId,
                'birthdate' => $validated['birth_date'],
                'age' => $validated['applicant_age'],
                'sex' => $validated['sex'],
                'civil_status' => $validated['civil_status'],
                'monthly_income' => is_numeric($validated['monthly_income']) ? (float) $validated['monthly_income'] : 0,
            ]);

            $formattedPhone = $this->normalizePhoneNumber($validated['phone_number']);

            Contact::create([
                'contact_id' => $contactId,
                'client_id' => $clientId,
                'contact_type' => 'Application',
                'phone_number' => $formattedPhone,
            ]);

            $phicCategory = $validated['phic_affiliation'] === 'Affiliated' ? ($validated['phic_category'] ?? null) : null;
            $jobStatus = ($validated['job_status'] ?? '') === '' ? null : $validated['job_status'];

            Applicant::create([
                'applicant_id' => $applicantId,
                'client_id' => $clientId,
                'province' => $validated['province'] ?? 'South Cotabato',
                'city' => $validated['city'] ?? 'General Santos',
                'municipality' => $validated['municipality'] ?? 'N / A',
                'barangay' => ($validated['barangay'] ?? '') === '' ? null : $validated['barangay'],
                'subdivision' => ($validated['subdivision'] ?? '') === '' ? null : $validated['subdivision'],
                'purok' => ($validated['purok'] ?? '') === '' ? null : $validated['purok'],
                'sitio' => ($validated['sitio'] ?? '') === '' ? null : $validated['sitio'],
                'street' => $validated['street'] ?? '',
                'phase' => ($validated['phase'] ?? '') === '' ? null : $validated['phase'],
                'block_number' => ($validated['block_number'] ?? '') === '' ? null : $validated['block_number'],
                'house_number' => ($validated['house_number'] ?? '') === '' ? null : $validated['house_number'],
                'job_status' => $jobStatus,
                'house_occup_status' => $validated['house_occup_status'],
                'lot_occup_status' => $validated['lot_occup_status'],
                'phic_affiliation' => $validated['phic_affiliation'],
                'phic_category' => $phicCategory,
                'is_also_patient' => ($validated['include_applicant_as_patient'] ?? false) ? 'Yes' : 'No',
                'patient_number' => $validated['patient_number'],
            ]);

            foreach ($validated['patients'] as $index => $patientData) {
                if (($validated['include_applicant_as_patient'] ?? false) && $index == 1) {
                    $patientClientId = $clientId;
                } else {
                    $pMemberId = $this->generateNextId('MEMBER', 'members', 'member_id');
                    $patientClientId = $this->generateNextId('CLIENT', 'clients', 'client_id');
                    $patientFullName = collect([
                        $patientData['first_name'],
                        $patientData['middle_name'],
                        $patientData['last_name'],
                        $patientData['suffix'],
                    ])->filter()->implode(' ');

                    Member::create([
                        'member_id' => $pMemberId,
                        'account_id' => $acctId,
                        'member_type' => 'Patient',
                        'first_name' => Str::title($patientData['first_name']),
                        'middle_name' => Str::title($patientData['middle_name']) ?: null,
                        'last_name' => Str::title($patientData['last_name']),
                        'suffix' => $patientData['suffix'] ?: null,
                        'full_name' => Str::title($patientFullName),
                    ]);

                    Client::create([
                        'client_id' => $patientClientId,
                        'member_id' => $pMemberId,
                        'age' => $patientData['age'],
                        'sex' => $patientData['sex'],
                    ]);
                }

                Patient::create([
                    'patient_id' => $this->generateNextId('PATIENT', 'patients', 'patient_id'),
                    'client_id' => $patientClientId,
                    'applicant_id' => $applicantId,
                    'patient_category' => $patientData['patient_category'] ?: null,
                ]);
            }
        });

        return redirect()->route('profiles.applicants.list')->with('success', 'Applicant has been added successfully.');
    }

    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $max = DB::table($table)->where($primaryKey, 'like', "{$base}-%")->max($primaryKey);
        $last = $max ? (int) Str::afterLast($max, '-') : 0;

        return $base.'-'.Str::padLeft($last + 1, 9, '0');
    }

    private function normalizePhoneNumber(string $value): string
    {
        $raw = Str::of($value)->trim();
        $clean = Str::of($raw)->replaceMatches('/[^0-9+]/', '')->toString();

        if (Str::startsWith($clean, '+')) {
            $clean = Str::substr($clean, 1);
        }

        $clean = Str::of($clean)->replaceMatches('/[^0-9]/', '')->toString();

        if (Str::startsWith($clean, '63')) {
            $clean = '0'.Str::substr($clean, 2);
        } elseif (Str::startsWith($clean, '9')) {
            $clean = '0'.$clean;
        } elseif (! Str::startsWith($clean, '0')) {
            $clean = '0'.$clean;
        }

        if (strlen($clean) >= 11) {
            $part1 = Str::substr($clean, 0, 4);
            $part2 = Str::substr($clean, 4, 3);
            $part3 = Str::substr($clean, 7, 4);
            $formatted = $part1;

            if ($part2 !== false && $part2 !== '') {
                $formatted .= '-'.$part2;
            }

            if ($part3 !== false && $part3 !== '') {
                $formatted .= '-'.$part3;
            }

            return $formatted;
        }

        if (strlen($clean) > 4) {
            $part1 = Str::substr($clean, 0, 4);
            $part2 = Str::substr($clean, 4);

            return $part1.($part2 ? '-'.$part2 : '');
        }

        return $clean;
    }
}
