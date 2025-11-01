<?php

namespace App\Rules;

use App\Actions\User\ValidateUsernameConfirmation;
use App\Models\User\Member;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MatchesUsername implements ValidationRule
{
    public function __construct(
        private Member $user,
        private ValidateUsernameConfirmation $validator
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->validator->execute($value, $this->user)) {
            $fail('Your confirmation input does not match the expected value.');
        }
    }
}
