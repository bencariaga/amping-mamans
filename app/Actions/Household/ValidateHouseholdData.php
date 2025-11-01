<?php

namespace App\Actions\Household;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ValidateHouseholdData
{
    public function execute(Request $request): array
    {
        return $request->validate([
            'household_name' => ['required', 'string', 'max:20'],
            'members.*.last_name' => ['required', 'string', 'max:255'],
            'members.*.first_name' => ['required', 'string', 'max:255'],
            'members.*.middle_name' => ['nullable', 'string', 'max:255'],
            'members.*.suffix' => ['nullable', 'string', Rule::in(['Sr.', 'Jr.', 'II', 'III', 'IV', 'V'])],
            'members.*.birthdate' => ['nullable', 'date'],
            'members.*.civil_status' => ['nullable', 'string'],
            'members.*.occupation' => ['nullable', 'string'],
            'members.*.monthly_income' => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'members.*.education' => ['nullable', 'string'],
            'members.*.relation_to_head' => ['required', 'string'],
            'members.*.client_id' => ['nullable', 'string', 'max:22'],
            'members.*.is_client' => ['boolean'],
            'members.*.client_type' => ['required', 'string'],
        ]);
    }
}
