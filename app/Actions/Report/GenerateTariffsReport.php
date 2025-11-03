<?php

namespace App\Actions\Report;

use App\Models\Operation\TariffList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GenerateTariffsReport
{
    public function execute(Request $request, $start, $end)
    {
        $hasTariffCreated = Schema::hasColumn('tariff_lists', 'created_at');
        
        $items = TariffList::query()
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween(DB::raw('DATE(effectivity_date)'), [$start->toDateString(), $end->toDateString()]);
            })
            ->orderBy('effectivity_date', 'desc')
            ->get(array_filter([
                'tariff_list_id',
                'effectivity_date',
                'tl_status',
                $hasTariffCreated ? 'created_at' : null,
            ]));

        return [
            'items' => $items,
            'summary' => [
                'total' => $items->count(),
                'active' => $items->where('tl_status', 'Active')->count(),
                'inactive' => $items->where('tl_status', 'Inactive')->count(),
                'draft' => $items->where('tl_status', 'Draft')->count(),
                'scheduled' => $items->where('tl_status', 'Scheduled')->count(),
            ],
        ];
    }
}
