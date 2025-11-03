<?php

namespace App\Actions\IdGeneration;

use App\Models\Authentication\Account;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class GenerateAccountId
{
    public static function execute(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = Str::upper($now->format('M'));
        $base = "ACCOUNT-{$year}-{$month}";
        $latest = Account::where('account_id', 'like', "{$base}-%")->latest('account_id')->value('account_id');
        $seq = $latest ? (int) Str::substr($latest, -4) : 0;
        return "{$base}-".Str::padLeft($seq + 1, 4, '0');
    }
}
