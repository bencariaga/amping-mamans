<?php

namespace App\Actions\User;

use App\Models\User\Member;
use Illuminate\Support\Str;

class ValidateUsernameConfirmation
{
    public function execute(string $input, Member $user): bool
    {
        $expected = Str::of($user->first_name.' '.$user->last_name)->trim();

        $normalize = fn (string $s): string => Str::of($s)->trim()->replace('/\s+/', ' ')->lower();

        return $normalize($input) === $normalize($expected);
    }

    public function getExpectedValue(Member $user): string
    {
        return Str::of($user->first_name.' '.$user->last_name)->trim();
    }
}
