<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EnumRule implements Rule
{
    private string $enumClass;
    private ?string $message = null;

    public function __construct(string $enumClass)
    {
        $this->enumClass = $enumClass;
    }

    public function passes($attribute, $value): bool
    {
        if (!enum_exists($this->enumClass)) {
            return false;
        }

        $cases = $this->enumClass::cases();

        foreach ($cases as $case) {
            if ($case->value === $value || $case->name === $value) {
                return true;
            }
        }

        return false;
    }

    public function message(): string
    {
        if ($this->message) {
            return $this->message;
        }

        $validValues = array_map(
            fn($case) => $case->value ?? $case->name,
            $this->enumClass::cases()
        );

        return 'The :attribute must be one of: ' . implode(', ', $validValues);
    }

    public function withMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }
}
