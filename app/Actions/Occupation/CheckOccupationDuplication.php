<?php

namespace App\Actions\Occupation;

use App\Models\Authentication\Occupation;

class CheckOccupationDuplication
{
    public function execute(string $occupationName, ?string $excludeOccupationId = null): bool
    {
        $query = Occupation::where('occupation', $occupationName);

        if ($excludeOccupationId) {
            $query->where('occupation_id', '!=', $excludeOccupationId);
        }

        return $query->exists();
    }
}
