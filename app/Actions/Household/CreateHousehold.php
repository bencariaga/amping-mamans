<?php

namespace App\Actions\Household;

use App\Models\User\Household;
use InvalidArgumentException;

class CreateHousehold
{
    public function execute(string $householdName): Household
    {
        if (empty(trim($householdName))) {
            throw new InvalidArgumentException('Household name is required.');
        }

        $exists = Household::where('household_name', $householdName)->exists();
        if ($exists) {
            throw new InvalidArgumentException('A household with this name already exists.');
        }

        return Household::create(['household_name' => $householdName]);
    }
}
