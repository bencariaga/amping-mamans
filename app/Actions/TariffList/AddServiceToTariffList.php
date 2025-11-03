<?php

namespace App\Actions\TariffList;

use App\Actions\IdGeneration\GenerateExpenseRangeId;
use App\Exceptions\FinanceException;
use App\Models\Operation\ExpenseRange;
use App\Models\Operation\TariffList;

class AddServiceToTariffList
{
    public function execute(string $tariffListId, string $serviceId): void
    {
        $tariffList = TariffList::where('tariff_list_id', $tariffListId)->firstOrFail();

        $exists = ExpenseRange::where('tariff_list_id', $tariffListId)
            ->where('service_id', $serviceId)
            ->exists();

        if ($exists) {
            throw FinanceException::serviceAlreadyExists($serviceId);
        }

        ExpenseRange::create([
            'exp_range_id' => GenerateExpenseRangeId::execute(),
            'tariff_list_id' => $tariffListId,
            'service_id' => $serviceId,
            'exp_range_min' => 0,
            'exp_range_max' => 0,
            'coverage_percent' => 0,
        ]);
    }
}
