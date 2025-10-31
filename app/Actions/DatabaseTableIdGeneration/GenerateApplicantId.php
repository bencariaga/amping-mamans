<?php

namespace App\Actions\DatabaseTableIdGeneration;

use App\Models\User\Applicant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateApplicantId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "APPLICANT-{$year}-{$month}";
        $latest = Applicant::where('applicant_id', 'like', "{$base}-%")->latest('applicant_id')->value('applicant_id');
        $seq = $latest ? (int) Str::substr($latest, -4) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 4, '0');
    }
}
