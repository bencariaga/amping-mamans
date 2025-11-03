<?php

namespace App\Actions\TariffList;

use App\Actions\IdGeneration\GenerateDataId;
use App\Actions\IdGeneration\GenerateTariffListId;
use App\Actions\IdGeneration\GenerateExpenseRangeId;
use App\Models\Operation\Data;
use App\Models\Operation\ExpenseRange;
use App\Models\Operation\TariffList;
use Illuminate\Support\Facades\DB;

class CreateTariffList
{
    public function execute(string $effectivityDate, array $selectedServices): TariffList
    {
        return DB::transaction(function () use ($effectivityDate, $selectedServices) {
            $dataId = GenerateDataId::execute();
            $tariffListId = GenerateTariffListId::execute();

            Data::create([
                'data_id' => $dataId,
                'archive_status' => 'Unarchived',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $tariffList = TariffList::create([
                'tariff_list_id' => $tariffListId,
                'data_id' => $dataId,
                'tl_status' => 'Draft',
                'effectivity_date' => $effectivityDate,
            ]);

            foreach ($selectedServices as $serviceId) {
                ExpenseRange::create([
                    'exp_range_id' => GenerateExpenseRangeId::execute(),
                    'tariff_list_id' => $tariffListId,
                    'service_id' => $serviceId,
                    'exp_range_min' => 0,
                    'exp_range_max' => 0,
                    'coverage_percent' => 0,
                ]);
            }

            return $tariffList->fresh();
        });
    }
}
