<?php

namespace App\Actions\DatabaseTableIdGeneration;

use App\Models\User\Patient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GeneratePatientId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "PATIENT-{$year}-{$month}";
        $latest = Patient::where('patient_id', 'like', "{$base}-%")->latest('patient_id')->value('patient_id');
        $seq = $latest ? (int) Str::substr($latest, -5) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 5, '0');
    }
}
