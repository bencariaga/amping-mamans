<?php

namespace App\Actions\Miscellaneous;

use App\Models\Authentication\Occupation;
use Illuminate\Database\Eloquent\Collection;

class GetOccupations
{
    public static function execute(): Collection
    {
        return Occupation::join('data', 'occupations.data_id', '=', 'data.data_id')
            ->where('data.archive_status', 'Unarchived')
            ->orderBy('data.updated_at', 'desc')
            ->select('occupations.*')
            ->get();
    }
}
