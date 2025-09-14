<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Controllers\Financial\TariffListController;

class DashboardController extends Controller
{
    public function showDashboard(): View|RedirectResponse
    {
        $tariffListController = new TariffListController();
        $latestTariffListVersion = $tariffListController->getLatestTariffListVersion();

        return view('dashboard', ['user' => Auth::user(), 'latestTariffListVersion' => $latestTariffListVersion]);
    }
}
