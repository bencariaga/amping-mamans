<?php

namespace App\Actions\Financial;

use App\Models\Authentication\Account;
use App\Models\Operation\Data;
use App\Models\Operation\GuaranteeLetter;
use App\Models\User\AffiliatePartner;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteAffiliatePartner
{
    public function execute(string $partnerId): void
    {
        DB::transaction(function () use ($partnerId) {
            $partner = AffiliatePartner::findOrFail($partnerId);

            $guaranteeLetterCount = GuaranteeLetter::where('affiliate_partner_id', $partner->affiliate_partner_id)->count();

            if ($guaranteeLetterCount > 0) {
                throw new Exception("Cannot delete Affiliate Partner '{$partner->affiliate_partner_name}' because {$guaranteeLetterCount} guarantee letter(s) are associated with it.");
            }

            $accountId = $partner->account_id;
            $partner->delete();

            $referencing = DB::table('affiliate_partners')->where('account_id', $accountId)->exists();

            if (!$referencing) {
                $account = Account::find($accountId);
                if ($account) {
                    $dataId = $account->data_id;
                    $account->delete();
                    Data::where('data_id', $dataId)->delete();
                }
            }
        });
    }
}
