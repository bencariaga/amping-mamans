<?php

namespace App\Actions\User;

use App\Models\User\Member;
use Illuminate\Support\Carbon;

class DeactivateUserAccount
{
    public function execute(Member $user): Member
    {
        $account = $user->account;
        $data = $account->data;
        
        if ($account->account_status === 'Deactivated') {
            $account->account_status = 'Active';
            $data->archive_status = 'Unarchived';
            $data->archived_at = null;
        } else {
            $account->account_status = 'Deactivated';
            $data->archive_status = 'Archived';
            $data->archived_at = Carbon::now();
        }
        
        $account->save();
        $data->save();

        return $user->fresh(['account', 'account.data']);
    }
}
