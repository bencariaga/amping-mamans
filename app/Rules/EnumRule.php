<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EnumRule implements ValidationRule
{
    private string $enumClass;

    private ?string $message = null;

    public function __construct(string $enumClass)
    {
        $this->enumClass = $enumClass;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! enum_exists($this->enumClass)) {
            $fail('The validation could not be performed because the enum class does not exist.');

            return;
        }

        $cases = $this->enumClass::cases();
        $isValid = false;

        foreach ($cases as $case) {
            if (($case->value ?? $case->name) === $value) {
                $isValid = true;
                break;
            }
        }

        if (! $isValid) {
            $fail($this->buildMessage($attribute));
        }
    }

    private function buildMessage(string $attribute): string
    {
        if ($this->message) {
            return $this->message;
        }

        $validValues = array_map(
            fn ($case) => $case->value ?? $case->name,
            $this->enumClass::cases()
        );

        return 'The '.$attribute.' must be one of: '.implode(', ', $validValues);
    }

    public function withMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
