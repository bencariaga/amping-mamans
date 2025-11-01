<?php

namespace App\Actions\User;

use App\Models\User\Member;
use Illuminate\Support\Facades\Hash;

class ChangeUserPassword
{
    public function execute(Member $user, string $newPassword): void
    {
        $user->staff->password = Hash::make($newPassword);
        $user->staff->save();
    }
}
