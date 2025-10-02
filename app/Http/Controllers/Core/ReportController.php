<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operation\Application;
use Illuminate\Support\Carbon;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class ReportController extends Controller
{
    public function assistanceRequests(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        $applications = Application::whereYear('applied_at', $year)
            ->with(['applicant', 'expenseRange'])
            ->orderBy('applied_at', 'desc')
            ->get();

        return view('pages.reports.assistance-requests', [
            'applications' => $applications,
            'year' => $year,
        ]);
    }

    public function assistanceRequestsPdf(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        $applications = Application::whereYear('applied_at', $year)
            ->with(['applicant', 'expenseRange'])
            ->orderBy('applied_at', 'desc')
            ->get();

        $pdf = PDF::loadView('pages.reports.assistance-requests-pdf', [
            'applications' => $applications,
            'year' => $year,
        ]);

        return $pdf->download("assistance_requests_{$year}.pdf");
    }
}