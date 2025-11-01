<?php

namespace App\Actions\User;

use App\Models\User\Member;

class CheckUserDuplication
{
    public function execute(array $data, ?string $excludeMemberId = null): bool
    {
        $query = Member::where('member_type', 'Staff')->where('first_name', $data['first_name'])->where('last_name', $data['last_name']);

        if (isset($data['middle_name'])) {
            $query->where('middle_name', $data['middle_name']);
        } else {
            $query->whereNull('middle_name');
        }

        if (isset($data['suffix'])) {
            $query->where('suffix', $data['suffix']);
        } else {
            $query->whereNull('suffix');
        }

        if ($excludeMemberId) {
            $query->where('member_id', '!=', $excludeMemberId);
        }

        return $query->exists();
    }
}
