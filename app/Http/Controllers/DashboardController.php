<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Financial\TariffListController;
use App\Models\Operation\ExpenseRange;
use App\Models\Operation\Service;
use App\Models\Operation\TariffList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected $tariffListController;

    public function __construct(TariffListController $tariffListController)
    {
        $this->tariffListController = $tariffListController;
    }

    public function showDashboard(): View|RedirectResponse
    {
        $serviceTariffMapping = $this->tariffListController->getServiceTariffMapping();
        $activeServices = $this->getActiveServices();

        return view('dashboard', [
            'user' => Auth::user(),
            'serviceTariffMapping' => $serviceTariffMapping,
            'activeServices' => $activeServices,
        ]);
    }

    private function getActiveServices(): array
    {
        // Get the active tariff (TL-2025-OCT-1)
        $activeTariff = TariffList::where('tariff_list_id', 'LIKE', '%-OCT-%')
            ->orderBy('effectivity_date', 'desc')
            ->first();

        if (!$activeTariff) {
            return [];
        }

        // Get all services associated with this tariff
        $serviceIds = ExpenseRange::where('tariff_list_id', $activeTariff->tariff_list_id)
            ->pluck('service_id')
            ->unique();

        $services = Service::whereIn('service_id', $serviceIds)->get();

        $result = [];
        foreach ($services as $service) {
            $result[$service->service_type] = $activeTariff->tariff_list_id;
        }

        return $result;
    }

    public function guaranteeLetter(): View
    {
        return view('pages.dashboard.templates.guarantee-letters');
    }
}
