<?php

namespace App\Actions\DatabaseTableIdGeneration;

use App\Models\User\Contact;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateContactId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "CONTACT-{$year}-{$month}";
        $latest = Contact::where('contact_id', 'like', "{$base}-%")->latest('contact_id')->value('contact_id');
        $seq = $latest ? (int) Str::substr($latest, -4) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 4, '0');
    }
}
