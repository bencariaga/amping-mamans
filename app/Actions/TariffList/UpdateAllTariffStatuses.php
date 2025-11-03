<?php

namespace App\Actions\TariffList;

use App\Models\Operation\TariffList;

class UpdateAllTariffStatuses
{
    protected CalculateTariffStatus $calculateTariffStatus;

    public function __construct(CalculateTariffStatus $calculateTariffStatus)
    {
        $this->calculateTariffStatus = $calculateTariffStatus;
    }

    public function execute(): void
    {
        $allTariffLists = TariffList::with('expenseRanges')->get();

        foreach ($allTariffLists as $tariffList) {
            $statusData = $this->calculateTariffStatus->execute($tariffList, $allTariffLists);
            $tariffList->tl_status = $statusData['status'];
            $tariffList->save();
        }
    }
}
