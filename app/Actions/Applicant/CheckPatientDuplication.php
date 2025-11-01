<?php

namespace App\Actions\Applicant;

use App\Models\User\Client;
use App\Models\User\Member;

class CheckPatientDuplication
{
    public function execute(string $firstName, string $lastName, ?string $middleName = null, ?string $suffix = null, ?string $excludeApplicantId = null): bool
    {
        $query = Member::where('member_type', 'Client')
            ->where('first_name', $firstName)
            ->where('last_name', $lastName)
            ->where('middle_name', $middleName)
            ->where('suffix', $suffix);

        if ($excludeApplicantId) {
            $patientClientIds = Client::whereHas('patients', function ($q) use ($excludeApplicantId) {
                $q->where('applicant_id', '!=', $excludeApplicantId);
            })->pluck('member_id');

            if ($patientClientIds->isNotEmpty()) {
                $query->whereIn('member_id', $patientClientIds);
            } else {
                return false;
            }
        }

        return $query->exists();
    }

    public function checkWithinApplicantPatients(array $patients): array
    {
        $duplicates = [];
        $seen = [];

        foreach ($patients as $index => $patient) {
            $key = strtolower(trim($patient['first_name'] ?? '')).'|'.
                   strtolower(trim($patient['last_name'] ?? '')).'|'.
                   strtolower(trim($patient['middle_name'] ?? '')).'|'.
                   strtolower(trim($patient['suffix'] ?? ''));

            if (isset($seen[$key])) {
                $duplicates[] = $index + 1;
            } else {
                $seen[$key] = true;
            }
        }

        return $duplicates;
    }
}
