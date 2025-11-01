<?php

namespace App\Actions\Applicant;

use App\Models\Authentication\Account;
use App\Models\Operation\Data;
use App\Models\User\Applicant;
use App\Models\User\Client;
use App\Models\User\Member;
use Illuminate\Support\Facades\DB;

class DeleteApplicant
{
    public function execute(Applicant $applicant): void
    {
        DB::transaction(function () use ($applicant) {
            $mainApplicantMemberId = $applicant->client->member->member_id;
            $patientMemberIdsToDelete = [];

            foreach ($applicant->patients as $patient) {
                if ($patient->client && $patient->client->member_id !== $mainApplicantMemberId) {
                    $patientMemberIdsToDelete[] = $patient->client->member_id;
                }
                $patient->delete();
            }

            $applicant->client->contacts()->delete();
            $applicant->delete();

            $dataId = $applicant->client->member->account->data_id;
            $accountId = $applicant->client->member->account_id;
            $memberId = $applicant->client->member_id;
            $clientId = $applicant->client_id;

            $clientToDelete = Client::find($clientId);
            if ($clientToDelete) {
                $clientToDelete->delete();
            }

            foreach ($patientMemberIdsToDelete as $memberIdToDel) {
                $clientToDelete = Client::where('member_id', $memberIdToDel)->first();
                if ($clientToDelete) {
                    $clientToDelete->delete();
                }

                $memberToDelete = Member::find($memberIdToDel);
                if ($memberToDelete) {
                    $memberToDelete->delete();
                }
            }

            $memberToDelete = Member::find($memberId);
            if ($memberToDelete) {
                $memberToDelete->delete();
            }

            $accountToDelete = Account::find($accountId);
            if ($accountToDelete) {
                $accountToDelete->delete();
            }

            $dataToDelete = Data::find($dataId);
            if ($dataToDelete) {
                $dataToDelete->delete();
            }
        });
    }
}
