<?php

namespace App\Actions\IdGeneration;

use App\Models\Operation\GLTemplate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateGLTemplateId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $base = "GL-TMP-{$year}";
        $latest = GLTemplate::where('gl_tmp_id', 'like', "{$base}-%")->latest('gl_tmp_id')->value('gl_tmp_id');
        $seq = $latest ? (int) Str::substr($latest, -2) : 0;

        return "{$base}-".Str::padLeft($seq + 1, 2, '0');
    }
}
