<?php

namespace App\Observers;

use App\Models\Operation\Data;
use Illuminate\Support\Facades\Log;

class DataObserver
{
    public function created(Data $data): void
    {
        Log::info('Data record created', [
            'data_id' => $data->data_id,
            'archive_status' => $data->archive_status,
        ]);
    }

    public function updated(Data $data): void
    {
        if ($data->wasChanged('archive_status')) {
            Log::info('Data archive status changed', [
                'data_id' => $data->data_id,
                'old_status' => $data->getOriginal('archive_status'),
                'new_status' => $data->archive_status,
            ]);
        }
    }

    public function deleted(Data $data): void
    {
        Log::info('Data record deleted', [
            'data_id' => $data->data_id,
        ]);
    }
}
