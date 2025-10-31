<?php

namespace App\Actions\Financial;

use App\Actions\DatabaseTableIdGeneration\GenerateAccountId;
use App\Actions\DatabaseTableIdGeneration\GenerateAffiliatePartnerId;
use App\Actions\DatabaseTableIdGeneration\GenerateDataId;
use App\Models\Authentication\Account;
use App\Models\Operation\Data;
use App\Models\User\AffiliatePartner;
use Illuminate\Support\Facades\DB;

class CreateAffiliatePartner
{
    public function execute(string $partnerName, string $partnerType): AffiliatePartner
    {
        return DB::transaction(function () use ($partnerName, $partnerType) {
            $dataId = GenerateDataId::execute();

            Data::create([
                'data_id' => $dataId,
                'archive_status' => 'Unarchived',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $accountId = GenerateAccountId::execute();

            Account::create([
                'account_id' => $accountId,
                'data_id' => $dataId,
                'account_status' => 'Active',
                'registered_at' => now(),
            ]);

            return AffiliatePartner::create([
                'affiliate_partner_id' => GenerateAffiliatePartnerId::execute(),
                'account_id' => $accountId,
                'affiliate_partner_name' => $partnerName,
                'affiliate_partner_type' => $partnerType,
            ]);
        });
    }
}
