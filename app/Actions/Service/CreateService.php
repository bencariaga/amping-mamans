<?php

namespace App\Actions\Service;

use App\Actions\IdGeneration\GenerateDataId;
use App\Actions\IdGeneration\GenerateServiceId;
use App\Models\Operation\Data;
use App\Models\Operation\Service;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CreateService
{
    public function __construct(
        private CheckServiceDuplication $checkServiceDuplication
    ) {}

    public function execute(string $serviceName): Service
    {
        if ($this->checkServiceDuplication->execute($serviceName)) {
            throw new InvalidArgumentException('A service with this name already exists.');
        }

        return DB::transaction(function () use ($serviceName) {
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
                'service' => $serviceName,
            ]);
        });
    }
}
