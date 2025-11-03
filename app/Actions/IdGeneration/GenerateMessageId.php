<?php

namespace App\Actions\IdGeneration;

use App\Models\Communication\Message;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateMessageId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "MESSAGE-{$year}-{$month}";
        $latest = Message::where('message_id', 'like', "{$base}-%")->latest('message_id')->value('message_id');
        $seq = $latest ? (int) Str::substr($latest, -4) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 4, '0');
    }
}
