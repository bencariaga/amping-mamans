<?php

namespace App\Actions\Miscellaneous;

use App\Models\User\AffiliatePartner;
use Illuminate\Database\Eloquent\Collection;

class GetAffiliatePartners
{
    public static function execute(): Collection
    {
        return AffiliatePartner::join('third_parties', 'affiliate_partners.tp_id', '=', 'third_parties.tp_id')
            ->join('accounts', 'third_parties.account_id', '=', 'accounts.account_id')
            ->join('data', 'accounts.data_id', '=', 'data.data_id')
            ->where('data.archive_status', 'Unarchived')
            ->orderBy('data.updated_at', 'desc')
            ->select('affiliate_partners.*')
            ->get();
    }
}
