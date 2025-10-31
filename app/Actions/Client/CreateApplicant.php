<?php

namespace App\Actions\Client;

use App\Actions\DatabaseTableIdGeneration\GenerateAccountId;
use App\Actions\DatabaseTableIdGeneration\GenerateApplicantId;
use App\Actions\DatabaseTableIdGeneration\GenerateClientId;
use App\Actions\DatabaseTableIdGeneration\GenerateContactId;
use App\Actions\DatabaseTableIdGeneration\GenerateDataId;
use App\Actions\DatabaseTableIdGeneration\GenerateMemberId;
use App\Actions\DatabaseTableIdGeneration\GeneratePatientId;
use App\Models\Authentication\Account;
use App\Models\Authentication\Occupation;
use App\Models\Operation\Data;
use App\Models\User\Applicant;
use App\Models\User\Client;
use App\Models\User\Contact;
use App\Models\User\Member;
use App\Models\User\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateApplicant
{
    public function execute(array $validatedData): Applicant
    {
        return DB::transaction(function () use ($validatedData) {
            $dataId = GenerateDataId::execute();
            $accountId = GenerateAccountId::execute();
            $memberId = GenerateMemberId::execute();
            $clientId = GenerateClientId::execute();
            $contactId = GenerateContactId::execute();
            $applicantId = GenerateApplicantId::execute();

            Data::create([
                'data_id' => $dataId,
                'archive_status' => 'Unarchived',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Account::create([
                'account_id' => $accountId,
                'data_id' => $dataId,
                'account_status' => 'Active',
                'registered_at' => now(),
            ]);

            $occupationId = $this->handleOccupation($validatedData);

            $fullName = collect([
                $validatedData['first_name'],
                $validatedData['middle_name'] ?? null,
                $validatedData['last_name'],
                $validatedData['suffix'] ?? null,
            ])->filter()->implode(' ');

            Member::create([
                'member_id' => $memberId,
                'account_id' => $accountId,
                'first_name' => Str::title($validatedData['first_name']),
                'middle_name' => Str::title($validatedData['middle_name'] ?? '') ?: null,
                'last_name' => Str::title($validatedData['last_name']),
                'suffix' => $validatedData['suffix'] ?? null,
                'full_name' => Str::title($fullName),
            ]);

            Client::create([
                'client_id' => $clientId,
                'member_id' => $memberId,
                'occupation_id' => $occupationId,
                'birthdate' => $validatedData['birth_date'],
                'age' => $validatedData['applicant_age'],
                'sex' => $validatedData['sex'],
                'civil_status' => $validatedData['civil_status'],
                'monthly_income' => is_numeric($validatedData['monthly_income']) ? (float) $validatedData['monthly_income'] : 0,
            ]);

            $formattedPhone = $this->normalizePhoneNumber($validatedData['phone_number']);

            Contact::create([
                'contact_id' => $contactId,
                'client_id' => $clientId,
                'contact_type' => 'Application',
                'contact_number' => $formattedPhone,
            ]);

            $phicCategory = $validatedData['phic_affiliation'] === 'Affiliated' ? ($validatedData['phic_category'] ?? null) : null;
            $jobStatus = ($validatedData['job_status'] ?? '') === '' ? null : $validatedData['job_status'];

            $applicant = Applicant::create([
                'applicant_id' => $applicantId,
                'client_id' => $clientId,
                'province' => $validatedData['province'] ?? 'South Cotabato',
                'city' => $validatedData['city'] ?? 'General Santos',
                'municipality' => $validatedData['municipality'] ?? 'N / A',
                'barangay' => ($validatedData['barangay'] ?? '') === '' ? null : $validatedData['barangay'],
                'subdivision' => ($validatedData['subdivision'] ?? '') === '' ? null : $validatedData['subdivision'],
                'purok' => ($validatedData['purok'] ?? '') === '' ? null : $validatedData['purok'],
                'sitio' => ($validatedData['sitio'] ?? '') === '' ? null : $validatedData['sitio'],
                'street' => $validatedData['street'] ?? '',
                'phase' => ($validatedData['phase'] ?? '') === '' ? null : $validatedData['phase'],
                'block_number' => ($validatedData['block_number'] ?? '') === '' ? null : $validatedData['block_number'],
                'house_number' => ($validatedData['house_number'] ?? '') === '' ? null : $validatedData['house_number'],
                'job_status' => $jobStatus,
                'house_occup_status' => $validatedData['house_occup_status'],
                'lot_occup_status' => $validatedData['lot_occup_status'],
                'phic_affiliation' => $validatedData['phic_affiliation'],
                'phic_category' => $phicCategory,
                'is_also_patient' => ($validatedData['include_applicant_as_patient'] ?? false) ? 'Yes' : 'No',
                'patient_number' => $validatedData['patient_number'],
            ]);

            $this->createPatients($validatedData, $applicantId, $clientId, $accountId);

            return $applicant;
        });
    }

    private function handleOccupation(array $validatedData): ?string
    {
        if (!empty($validatedData['custom_occupation'])) {
            $dataId = GenerateDataId::execute();

            Data::create([
                'data_id' => $dataId,
                'archive_status' => 'Unarchived',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $occupation = Occupation::create([
                'occupation_id' => app(\App\Actions\DatabaseTableIdGeneration\GenerateOccupationId::class)->execute(),
                'data_id' => $dataId,
                'occupation' => $validatedData['custom_occupation'],
            ]);

            return $occupation->occupation_id;
        }

        return $validatedData['occupation_id'] ?? null;
    }

    private function createPatients(array $validatedData, string $applicantId, string $clientId, string $accountId): void
    {
        foreach ($validatedData['patients'] as $index => $patientData) {
            if (($validatedData['include_applicant_as_patient'] ?? false) && $index == 1) {
                $patientClientId = $clientId;
            } else {
                $pMemberId = GenerateMemberId::execute();
                $patientClientId = GenerateClientId::execute();
                $patientFullName = collect([
                    $patientData['first_name'],
                    $patientData['middle_name'] ?? null,
                    $patientData['last_name'],
                    $patientData['suffix'] ?? null,
                ])->filter()->implode(' ');

                Member::create([
                    'member_id' => $pMemberId,
                    'account_id' => $accountId,
                    'member_type' => 'Patient',
                    'first_name' => Str::title($patientData['first_name']),
                    'middle_name' => Str::title($patientData['middle_name'] ?? '') ?: null,
                    'last_name' => Str::title($patientData['last_name']),
                    'suffix' => $patientData['suffix'] ?? null,
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
                'patient_id' => GeneratePatientId::execute(),
                'client_id' => $patientClientId,
                'applicant_id' => $applicantId,
                'patient_category' => $patientData['patient_category'] ?: null,
            ]);
        }
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
        } elseif (!Str::startsWith($clean, '0')) {
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
