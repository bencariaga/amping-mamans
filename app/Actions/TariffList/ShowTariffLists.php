<?php

namespace App\Actions\TariffList;

use Illuminate\Http\RedirectResponse;

class ShowTariffLists
{
    public function execute(): RedirectResponse
    {
        return redirect()->route('tariff-lists');
    }
}
