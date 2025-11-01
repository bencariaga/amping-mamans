<?php

namespace App\Actions\Household;

use App\Models\Authentication\Account;
use App\Models\Authentication\Occupation;
use App\Models\User\Client;
use App\Models\User\HouseholdMember;
use App\Models\User\Member;
use Illuminate\Support\Facades\DB;

class UpdateHouseholdMembers
{
    public function execute(string $householdId, array $validatedMembers, string $householdName): void
    {
        DB::transaction(function () use ($householdId, $validatedMembers) {
            $existingClientIds = HouseholdMember::query()->where('household_id', $householdId)->pluck('client_id')->toArray();
            $updatedClientIds = [];

            foreach ($validatedMembers as $memberData) {
                $isNewPureMember = ! $memberData['is_client'];
                $readOnlyFields = $this->getReadOnlyFields($memberData['client_type'] ?? 'HOUSEHOLD_MEMBER');

                $clientId = $memberData['client_id'] ?? Account::generateId('CLIENT');

                $member = Member::updateOrCreate(
                    ['member_id' => $clientId],
                    [
                        'last_name' => $memberData['last_name'],
                        'first_name' => $memberData['first_name'],
                        'middle_name' => $memberData['middle_name'] ?? null,
                        'suffix' => $memberData['suffix'] ?? null,
                    ]
                );

                $clientData = ['member_id' => $member->member_id];

                if ($isNewPureMember || ! collect($readOnlyFields)->contains('birthdate')) {
                    $clientData['birthdate'] = $memberData['birthdate'] ?? null;
                }

                if ($isNewPureMember || ! collect($readOnlyFields)->contains('civil_status')) {
                    $clientData['civil_status'] = $memberData['civil_status'] ?? null;
                }

                if ($isNewPureMember || ! collect($readOnlyFields)->contains('occupation')) {
                    $occupation = Occupation::where('occupation', $memberData['occupation'])->first();
                    $clientData['occupation_id'] = $occupation ? $occupation->occupation_id : null;
                }

                if ($isNewPureMember || ! collect($readOnlyFields)->contains('monthly_income')) {
                    $clientData['monthly_income'] = (int) ($memberData['monthly_income'] ?? 0) ?: null;
                }

                Client::updateOrCreate(['client_id' => $clientId], $clientData);

                HouseholdMember::updateOrCreate(
                    ['household_id' => $householdId, 'client_id' => $clientId],
                    [
                        'educational_attainment' => $memberData['education'] ?? null,
                        'relationship_to_applicant' => $memberData['relation_to_head'] ?? null,
                    ]
                );

                $updatedClientIds[] = $clientId;
            }

            $clientsToRemove = collect($existingClientIds)->diff($updatedClientIds)->values()->all();

            if (! empty($clientsToRemove)) {
                HouseholdMember::query()->where('household_id', $householdId)->whereIn('client_id', $clientsToRemove)->delete();
            }
        });
    }

    private function getReadOnlyFields(string $clientType): array
    {
        if ($clientType === 'APPLICANT') {
            return ['last_name', 'first_name', 'middle_name', 'suffix', 'birthdate', 'age', 'civil_status', 'occupation', 'monthly_income'];
        } elseif ($clientType === 'PATIENT') {
            return ['last_name', 'first_name', 'middle_name', 'suffix', 'birthdate', 'age'];
        }

        return [];
    }
}
