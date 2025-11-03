<?php

namespace App\Actions\IdGeneration;

use App\Models\Operation\BudgetUpdate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateBudgetUpdateId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "BDG-UPD-{$year}-{$month}";
        $latest = BudgetUpdate::where('budget_update_id', 'like', "{$base}-%")->latest('budget_update_id')->value('budget_update_id');
        $seq = $latest ? (int) Str::substr($latest, -5) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 5, '0');
    }
}
