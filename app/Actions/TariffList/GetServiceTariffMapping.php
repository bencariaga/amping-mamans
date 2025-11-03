<?php

namespace App\Actions\TariffList;

use App\Models\Operation\ExpenseRange;
use App\Models\Operation\Service;
use App\Models\Operation\TariffList;

class GetServiceTariffMapping
{
    public function execute(): array
    {
        $services = Service::all();
        $serviceTariffs = [];

        foreach ($services as $service) {
            $tariffIds = ExpenseRange::where('service_id', $service->service_id)
                ->pluck('tariff_list_id')
                ->unique()
                ->toArray();

            if (empty($tariffIds)) {
                $serviceTariffs[$service->service] = 'N/A';
                continue;
            }

            $latestTariff = TariffList::whereIn('tariff_list_id', $tariffIds)
                ->orderBy('effectivity_date', 'desc')
                ->orderBy('tariff_list_id', 'desc')
                ->first();

            $serviceTariffs[$service->service] = $latestTariff ? $latestTariff->tariff_list_id : 'N/A';
        }

        return $serviceTariffs;
    }
}
