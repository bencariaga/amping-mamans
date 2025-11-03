<?php

namespace App\Actions\Service;

use App\Models\Operation\Service;
use InvalidArgumentException;

class UpdateService
{
    public function __construct(
        private CheckServiceDuplication $checkServiceDuplication
    ) {}

    public function execute(string $serviceId, string $serviceName): Service
    {
        if ($this->checkServiceDuplication->execute($serviceName, $serviceId)) {
            throw new InvalidArgumentException('A service with this name already exists.');
        }

        $service = Service::where('service_id', $serviceId)->firstOrFail();

        $service->update([
            'service' => $serviceName,
        ]);

        return $service->fresh();
    }
}
