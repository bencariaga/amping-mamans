<?php

namespace App\Actions\DatabaseTableIdGeneration;

use App\Models\Audit\Report;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateReportId
{
    public static function execute(): string
    {
        $year = Carbon::now()->year;
        $base = "REPORT-{$year}";
        $latest = Report::where('report_id', 'like', "{$base}-%")->latest('report_id')->value('report_id');
        $seq = $latest ? (int) Str::substr($latest, -3) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 3, '0');
    }
}
