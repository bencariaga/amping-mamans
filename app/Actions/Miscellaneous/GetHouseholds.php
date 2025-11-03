<?php

namespace App\Actions\Miscellaneous;

use App\Models\User\Household;
use Illuminate\Database\Eloquent\Collection;

class GetHouseholds
{
    public static function execute(): Collection
    {
        return Household::orderBy('household_name', 'asc')->get();
    }
}
