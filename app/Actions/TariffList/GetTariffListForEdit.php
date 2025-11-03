<?php

namespace App\Actions\TariffList;

use App\Models\Operation\ExpenseRange;
use App\Models\Operation\Service;
use App\Models\Operation\TariffList;

class GetTariffListForEdit
{
    public function execute(string $tariffListId): array
    {
        $tariffList = TariffList::with(['data'])->where('tariff_list_id', $tariffListId)->firstOrFail();

        $allServices = Service::all();

        $usedServiceIds = ExpenseRange::where('tariff_list_id', $tariffListId)
            ->distinct()
            ->pluck('service_id')
            ->toArray();

        $availableServices = $allServices->whereNotIn('service_id', $usedServiceIds);
        $usedServices = $allServices->whereIn('service_id', $usedServiceIds);

        return [
            'tariffListModel' => $tariffList,
            'usedServices' => $usedServices,
            'availableServices' => $availableServices,
        ];
    }
}
