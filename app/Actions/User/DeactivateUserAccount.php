<?php

namespace App\Actions\User;

use App\Models\User\Member;
use Illuminate\Support\Carbon;

class DeactivateUserAccount
{
    public function execute(Member $user): Member
    {
        $account = $user->account;
        $account->account_status = $account->account_status === 'Deactivated' ? 'Active' : 'Deactivated';
        $account->save();

        return $user->fresh(['account']);
    }
}
