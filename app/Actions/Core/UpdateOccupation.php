<?php

namespace App\Actions\Core;

use App\Models\Authentication\Occupation;

class UpdateOccupation
{
    public function execute(string $occupationId, string $occupationName): Occupation
    {
        $occupation = Occupation::where('occupation_id', $occupationId)->firstOrFail();

        $occupation->update(['occupation' => $occupationName]);

        return $occupation->fresh();
    }
}
