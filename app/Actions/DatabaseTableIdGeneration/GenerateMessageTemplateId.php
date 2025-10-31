<?php

namespace App\Actions\DatabaseTableIdGeneration;

use App\Models\Communication\MessageTemplate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateMessageTemplateId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "MSG-TMP-{$year}-{$month}";
        $latest = MessageTemplate::where('msg_tmp_id', 'like', "{$base}-%")->latest('msg_tmp_id')->value('msg_tmp_id');
        $seq = $latest ? (int) Str::substr($latest, -1) : 0;
        return "{$base}-".($seq + 1);
    }
}
