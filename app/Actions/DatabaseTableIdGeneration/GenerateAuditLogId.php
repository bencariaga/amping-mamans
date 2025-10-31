<?php

namespace App\Actions\DatabaseTableIdGeneration;

use App\Models\Audit\AuditLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateAuditLogId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "LOG-{$year}-{$month}";
        $latest = AuditLog::where('al_id', 'like', "{$base}-%")->latest('al_id')->value('al_id');
        $seq = $latest ? (int) Str::substr($latest, -9) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 9, '0');
    }
}
