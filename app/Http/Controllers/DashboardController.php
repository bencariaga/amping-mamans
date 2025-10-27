<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Financial\TariffListController;
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

        return view('dashboard', [
            'user' => Auth::user(),
            'serviceTariffMapping' => $serviceTariffMapping,
        ]);
    }

    public function guaranteeLetter(): View
    {
        return view('pages.dashboard.templates.guarantee-letters');
    }
}
