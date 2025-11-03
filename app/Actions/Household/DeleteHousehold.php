<?php

namespace App\Actions\Household;

use App\Models\User\Household;
use InvalidArgumentException;

class DeleteHousehold
{
    public function execute(string $householdId): void
    {
        $household = Household::findOrFail($householdId);

        $memberCount = $household->householdMembers()->count();
        if ($memberCount > 0) {
            throw new InvalidArgumentException("Cannot delete household '{$household->household_name}' because {$memberCount} member(s) are associated with it.");
        }

        $household->delete();
    }
}
