<?php

namespace App\Actions\TariffList;

use Illuminate\View\View;

class CreateTariffListView
{
    public function execute(): View
    {
        return view('pages.tariff-list.tariff-list-create');
    }
}
