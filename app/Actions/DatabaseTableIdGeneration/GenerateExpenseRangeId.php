<?php

namespace App\Actions\DatabaseTableIdGeneration;

use App\Models\Operation\ExpenseRange;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateExpenseRangeId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "EXP-RANGE-{$year}-{$month}";
        $latest = ExpenseRange::where('exp_range_id', 'like', "{$base}-%")->latest('exp_range_id')->value('exp_range_id');
        $seq = $latest ? (int) Str::substr($latest, -5) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 5, '0');
    }
}
