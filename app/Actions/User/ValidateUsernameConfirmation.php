<?php

namespace App\Actions\User;

use App\Models\User\Member;
use Illuminate\Support\Str;

class ValidateUsernameConfirmation
{
    public function execute(string $input, Member $user): bool
    {
        $expected = $this->getExpectedValue($user);

        $normalize = fn (string $s): string => Str::of($s)->squish()->lower()->toString();

        return $normalize($input) === $normalize($expected);
    }

    public function getExpectedValue(Member $user): string
    {
        return $user->full_name;
    }
}
