<?php

namespace App\Actions\Financial;

use App\Models\Authentication\Account;
use App\Models\Operation\BudgetUpdate;
use App\Models\Operation\Data;
use App\Models\User\Member;
use App\Models\User\Sponsor;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteSponsor
{
    public function execute(string $sponsorId): void
    {
        DB::transaction(function () use ($sponsorId) {
            $sponsor = Sponsor::with('member.account')->findOrFail($sponsorId);

            $budgetUpdateCount = BudgetUpdate::where('sponsor_id', $sponsor->sponsor_id)->count();

            if ($budgetUpdateCount > 0) {
                throw new Exception("Cannot delete Sponsor '{$sponsor->sponsor_name}' because {$budgetUpdateCount} budget update(s) are associated with it.");
            }

            $memberId = $sponsor->member_id;
            $accountId = $sponsor->member->account_id ?? null;
            $dataId = $sponsor->member->account->data_id ?? null;

            $sponsor->delete();

            if ($memberId) {
                Member::where('member_id', $memberId)->delete();
            }

            if ($accountId) {
                Account::where('account_id', $accountId)->delete();
            }

            if ($dataId) {
                Data::where('data_id', $dataId)->delete();
            }
        });
    }
}
