<?php

namespace App\Actions\Core;

use App\Models\Operation\Service;

class UpdateService
{
    public function execute(string $serviceId, string $serviceName, ?string $assistScope = null): Service
    {
        $service = Service::where('service_id', $serviceId)->firstOrFail();

        $service->update([
            'service_type' => $serviceName,
            'assist_scope' => $assistScope,
        ]);

        return $service->fresh();
    }
}
