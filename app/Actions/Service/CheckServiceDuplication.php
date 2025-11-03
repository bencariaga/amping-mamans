<?php

namespace App\Actions\Service;

use App\Models\Operation\Service;

class CheckServiceDuplication
{
    public function execute(string $serviceName, ?string $excludeServiceId = null): bool
    {
        $query = Service::where('service', $serviceName);

        if ($excludeServiceId) {
            $query->where('service_id', '!=', $excludeServiceId);
        }

        return $query->exists();
    }
}
