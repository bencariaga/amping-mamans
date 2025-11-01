<?php

namespace App\Actions\User;

use App\Models\User\Member;
use Illuminate\Support\Facades\Auth;

class ShowUserProfile
{
    public function execute(?Member $user = null): Member
    {
        if (! $user || Auth::id() === $user->member_id) {
            $user = Auth::user();
        }

        $user->load(['staff.role', 'account.data']);

        return $user;
    }
}
