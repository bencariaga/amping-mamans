<?php

namespace App\Actions\Financial;

use App\Actions\DatabaseTableIdGeneration\GenerateAccountId;
use App\Actions\DatabaseTableIdGeneration\GenerateDataId;
use App\Actions\DatabaseTableIdGeneration\GenerateMemberId;
use App\Actions\DatabaseTableIdGeneration\GenerateSponsorId;
use App\Models\Authentication\Account;
use App\Models\Operation\Data;
use App\Models\User\Member;
use App\Models\User\Sponsor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CreateSponsor
{
    public function execute(array $sponsorData): Sponsor
    {
        return DB::transaction(function () use ($sponsorData) {
            $dataId = GenerateDataId::execute();
            $accountId = GenerateAccountId::execute();
            $memberId = GenerateMemberId::execute();
            $sponsorId = GenerateSponsorId::execute();

            Data::create([
                'data_id' => $dataId,
                'archive_status' => 'Unarchived',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            Account::create([
                'account_id' => $accountId,
                'data_id' => $dataId,
                'account_status' => 'Active',
                'registered_at' => Carbon::now(),
            ]);

            Member::create([
                'member_id' => $memberId,
                'account_id' => $accountId,
                'member_type' => 'Sponsor',
                'first_name' => $sponsorData['first_name'] ?? null,
                'middle_name' => $sponsorData['middle_name'] ?? null,
                'last_name' => $sponsorData['last_name'] ?? null,
                'suffix' => $sponsorData['suffix'] ?? null,
            ]);

            return Sponsor::create([
                'sponsor_id' => $sponsorId,
                'member_id' => $memberId,
                'sponsor_type' => $sponsorData['sponsor_type'],
                'designation' => $sponsorData['designation'] ?? null,
                'organization_name' => $sponsorData['organization_name'] ?? null,
            ]);
        });
    }
}
