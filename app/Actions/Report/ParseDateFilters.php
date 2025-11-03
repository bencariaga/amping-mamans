<?php

namespace App\Actions\Report;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ParseDateFilters
{
    public function execute(Request $request): array
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $month = $request->get('month');
        $year = $request->get('year');

        if ($dateFrom && $dateTo) {
            $start = Carbon::parse($dateFrom)->startOfDay();
            $end = Carbon::parse($dateTo)->endOfDay();

            return [$start, $end, $start->toDateString().' to '.$end->toDateString()];
        }

        if ($month && $year) {
            $start = Carbon::createFromDate((int) $year, (int) $month, 1)->startOfMonth();
            $end = (clone $start)->endOfMonth();

            return [$start, $end, $start->format('F Y')];
        }

        if ($year) {
            $start = Carbon::createFromDate((int) $year, 1, 1)->startOfYear();
            $end = (clone $start)->endOfYear();

            return [$start, $end, $start->format('Y')];
        }

        $end = Carbon::now()->endOfDay();
        $start = (clone $end)->subDays(29)->startOfDay();

        return [$start, $end, 'Last 30 days'];
    }
}
