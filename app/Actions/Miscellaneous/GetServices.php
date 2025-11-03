<?php

namespace App\Actions\Miscellaneous;

use App\Models\Operation\Service;
use Illuminate\Database\Eloquent\Collection;

class GetServices
{
    public static function execute(): Collection
    {
        return Service::join('data', 'services.data_id', '=', 'data.data_id')
            ->where('data.archive_status', 'Unarchived')
            ->orderBy('data.updated_at', 'desc')
            ->select('services.*')
            ->get();
    }
}
