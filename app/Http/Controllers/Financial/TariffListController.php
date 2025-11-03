<?php

namespace App\Http\Controllers\Financial;

use App\Actions\TariffList\AddServiceAction;
use App\Actions\TariffList\CheckEffectivityDateResponse;
use App\Actions\TariffList\CreateTariffListView;
use App\Actions\TariffList\DestroyTariffList;
use App\Actions\TariffList\EditTariffList;
use App\Actions\TariffList\GetTakenDatesResponse;
use App\Actions\TariffList\RemoveServiceAction;
use App\Actions\TariffList\ShowTariffList;
use App\Actions\TariffList\ShowTariffLists;
use App\Actions\TariffList\StoreTariffList;
use App\Actions\TariffList\UpdateTariffListAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TariffListController extends Controller
{
    public function showTariffLists()
    {
        return app(ShowTariffLists::class)->execute();
    }

    public function create()
    {
        return app(CreateTariffListView::class)->execute();
    }

    public function getTakenDates()
    {
        return app(GetTakenDatesResponse::class)->execute();
    }

    public function checkEffectivityDate(Request $request)
    {
        return app(CheckEffectivityDateResponse::class)->execute($request);
    }

    public function store(Request $request)
    {
        return app(StoreTariffList::class)->execute($request);
    }

    public function edit(string $tariffListId)
    {
        return app(EditTariffList::class)->execute($tariffListId);
    }

    public function show(string $tariffListId)
    {
        return app(ShowTariffList::class)->execute($tariffListId);
    }

    public function update(Request $request, string $tariffListId)
    {
        return app(UpdateTariffListAction::class)->execute($request, $tariffListId);
    }

    public function addService(Request $request, string $tariffListId)
    {
        return app(AddServiceAction::class)->execute($request, $tariffListId);
    }

    public function removeService(Request $request, string $tariffListId)
    {
        return app(RemoveServiceAction::class)->execute($request, $tariffListId);
    }

    public function destroy(string $tariffListId)
    {
        return app(DestroyTariffList::class)->execute($tariffListId);
    }
}
