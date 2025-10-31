<?php

namespace App\Http\Controllers;

use App\Models\Operation\GuaranteeLetter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class DashboardController extends Controller
{

    public function showDashboard(): View|RedirectResponse
    {
        $currentYear = Carbon::now()->year;
        $startOfYear = Carbon::create($currentYear, 1, 1, 0, 0, 0);
        $endOfYear = Carbon::create($currentYear, 12, 31, 23, 59, 59);

        $startOfToday = Carbon::now()->startOfDay();
        $endOfToday = Carbon::now()->endOfDay();

        $glYearCount = GuaranteeLetter::join('data', 'guarantee_letters.data_id', '=', 'data.data_id')
            ->whereBetween('data.created_at', [$startOfYear, $endOfYear])
            ->where('data.archive_status', 'Unarchived')
            ->count();

        $glTodayCount = GuaranteeLetter::join('data', 'guarantee_letters.data_id', '=', 'data.data_id')
            ->whereBetween('data.created_at', [$startOfToday, $endOfToday])
            ->where('data.archive_status', 'Unarchived')
            ->count();

        return view('dashboard', [
            'user' => Auth::user(),
            'glYearCount' => $glYearCount,
            'glTodayCount' => $glTodayCount,
            'currentYear' => $currentYear,
        ]);
    }

    public function guaranteeLetter(): View
    {
        return view('pages.dashboard.templates.guarantee-letters');
    }

    public function clearCache(): RedirectResponse
    {
        try {
            Artisan::call('optimize:clear');
            Artisan::call('debugbar:clear');

            return redirect()->route('dashboard')->with('success', 'Cache cleared successfully!');
        } catch (Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
}
