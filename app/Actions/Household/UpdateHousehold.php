<?php

namespace App\Actions\Household;

use App\Models\User\Household;
use InvalidArgumentException;

class UpdateHousehold
{
    public function execute(string $householdId, string $householdName): Household
    {
        if (empty(trim($householdName))) {
            throw new InvalidArgumentException('Household name is required.');
        }

        $household = Household::findOrFail($householdId);

        $exists = Household::where('household_name', $householdName)
            ->where('household_id', '!=', $householdId)
            ->exists();

        if ($exists) {
            throw new InvalidArgumentException('A household with this name already exists.');
        }

        $household->update(['household_name' => $householdName]);

        return $household->fresh();
    }
}
