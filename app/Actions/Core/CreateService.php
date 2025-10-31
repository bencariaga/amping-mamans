<?php

namespace App\Actions\Core;

use App\Actions\DatabaseTableIdGeneration\GenerateDataId;
use App\Actions\DatabaseTableIdGeneration\GenerateServiceId;
use App\Models\Operation\Data;
use App\Models\Operation\Service;
use Illuminate\Support\Facades\DB;

class CreateService
{
    public function execute(string $serviceName, ?string $assistScope = null): Service
    {
        return DB::transaction(function () use ($serviceName, $assistScope) {
            $dataId = GenerateDataId::execute();

            Data::create([
                'data_id' => $dataId,
                'archive_status' => 'Unarchived',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return Service::create([
                'service_id' => GenerateServiceId::execute(),
                'data_id' => $dataId,
                'service_type' => $serviceName,
                'assist_scope' => $assistScope,
            ]);
        });
    }
}
