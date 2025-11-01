<?php

namespace App\Actions\Applicant;

use App\Models\User\Client;
use App\Models\User\Member;

class CheckApplicantDuplication
{
    public function execute(string $firstName, string $lastName, ?string $middleName = null, ?string $suffix = null, ?string $excludeClientId = null): bool
    {
        $query = Member::where('member_type', 'Client')
            ->where('first_name', $firstName)
            ->where('last_name', $lastName)
            ->where('middle_name', $middleName)
            ->where('suffix', $suffix);

        if ($excludeClientId) {
            $clientMemberId = Client::where('client_id', $excludeClientId)->value('member_id');
            if ($clientMemberId) {
                $query->where('member_id', '!=', $clientMemberId);
            }
        }

        return $query->exists();
    }
}
