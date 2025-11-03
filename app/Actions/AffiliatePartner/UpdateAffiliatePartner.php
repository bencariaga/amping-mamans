<?php

namespace App\Actions\AffiliatePartner;

use App\Models\User\AffiliatePartner;

class UpdateAffiliatePartner
{
    public function execute(string $partnerId, string $partnerName, string $partnerType): AffiliatePartner
    {
        $partner = AffiliatePartner::findOrFail($partnerId);

        $partner->update([
            'affiliate_partner_name' => $partnerName,
            'affiliate_partner_type' => $partnerType,
        ]);

        if ($partner->account && $partner->account->data) {
            $partner->account->data->touch();
        }

        return $partner->fresh();
    }
}
