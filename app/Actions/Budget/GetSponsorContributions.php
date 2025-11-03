<?php

namespace App\Actions\Budget;

use App\Models\Operation\BudgetUpdate;
use App\Models\User\Sponsor;
use Illuminate\Support\Collection;

class GetSponsorContributions
{
    public function execute(string $sponsorId): array
    {
        $sponsor = Sponsor::with(['member.account.data'])->find($sponsorId);

        if (!$sponsor) {
            return ['sponsor' => null, 'contributions' => collect([])];
        }

        $contributions = BudgetUpdate::with('data')
            ->where('sponsor_id', $sponsorId)
            ->where('possessor', 'Sponsor')
            ->where('reason', 'Sponsor Donation')
            ->get()
            ->sortBy(function ($contribution) {
                return optional($contribution->data)->created_at;
            })
            ->values();

        $runningTotal = 0;

        $contributions = $contributions->map(function ($item) use (&$runningTotal) {
            $runningTotal += $item->amount_change;
            $item->total_amount = $runningTotal;
            $item->amount_spent = 0.00;
            $item->amount_accum = $item->amount_change;

            return $item;
        });

        return [
            'sponsor' => $sponsor,
            'contributions' => $contributions,
        ];
    }
}
