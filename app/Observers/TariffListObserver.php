<?php

namespace App\Observers;

use App\Models\Operation\TariffList;
use Illuminate\Support\Facades\Log;

class TariffListObserver
{
    public function created(TariffList $tariffList): void
    {
        Log::info('Tariff list created', [
            'tariff_list_id' => $tariffList->tariff_list_id,
            'tariff_list_name' => $tariffList->tariff_list_name,
            'version' => $tariffList->tariff_list_version,
        ]);
    }

    public function updated(TariffList $tariffList): void
    {
        if ($tariffList->wasChanged('tariff_list_version')) {
            Log::info('Tariff list version changed', [
                'tariff_list_id' => $tariffList->tariff_list_id,
                'old_version' => $tariffList->getOriginal('tariff_list_version'),
                'new_version' => $tariffList->tariff_list_version,
            ]);

            if ($tariffList->tariff_list_version === 'Active') {
                $this->deactivateOtherTariffs($tariffList);
            }
        }
    }

    public function deleted(TariffList $tariffList): void
    {
        Log::info('Tariff list deleted', [
            'tariff_list_id' => $tariffList->tariff_list_id,
        ]);
    }

    private function deactivateOtherTariffs(TariffList $activeTariff): void
    {
        TariffList::where('tariff_list_id', '!=', $activeTariff->tariff_list_id)
            ->where('tariff_list_version', 'Active')
            ->update(['tariff_list_version' => 'Inactive']);
    }
}
