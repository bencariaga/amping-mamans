<?php

namespace App\Actions\TariffList;

use App\Exceptions\FinanceException;
use App\Models\Operation\ExpenseRange;
use App\Models\Operation\TariffList;

class RemoveServiceFromTariffList
{
    public function execute(string $tariffListId, string $serviceId): void
    {
        $tariffList = TariffList::where('tariff_list_id', $tariffListId)->firstOrFail();

        $serviceCount = ExpenseRange::where('tariff_list_id', $tariffListId)
            ->distinct('service_id')
            ->count('service_id');

        if ($serviceCount <= 1) {
            throw FinanceException::cannotRemoveLastService();
        }

        ExpenseRange::where('tariff_list_id', $tariffListId)
            ->where('service_id', $serviceId)
            ->delete();
    }
}
