<?php

namespace App\Actions\Household;

use App\Models\User\HouseholdMember;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class GetHouseholdClients
{
    public function execute(string $householdId): array
    {
        $clients = HouseholdMember::query()->where('household_id', $householdId)->with(['client.member', 'client.applicant', 'client.patient', 'client.occupation', 'client.client_type'])->get()->map(function ($householdMember) {
            $client = $householdMember->client;

            if (! $client) {
                return null;
            }

            $clientType = 'HOUSEHOLD_MEMBER';
            $isClient = false;
            $readOnlyFields = [];

            if ($client->client_type == $clientType) {
                switch ($client->client_type) {
                    case 'CLIENT':
                        $clientType = 'CLIENT';
                        $isClient = true;
                        $readOnlyFields = ['last_name', 'first_name', 'middle_name', 'suffix', 'birthdate', 'age', 'civil_status', 'occupation', 'monthly_income'];
                        break;
                    case 'APPLICANT':
                        $clientType = 'APPLICANT';
                        $isClient = true;
                        $readOnlyFields = ['last_name', 'first_name', 'middle_name', 'suffix', 'birthdate', 'age'];
                        break;
                    case 'PATIENT':
                        $clientType = 'PATIENT';
                        $isClient = true;
                        $readOnlyFields = ['last_name', 'first_name', 'middle_name', 'suffix', 'birthdate', 'age'];
                        break;
                }
            }

            $monthlyIncome = optional($client->member)->monthly_income ?? optional($client)->monthly_income;

            return [
                'client_id' => $client->client_id,
                'client_type' => $clientType,
                'is_client' => $isClient,
                'read_only_fields' => $readOnlyFields,
                'last_name' => $client->member->last_name ?? '',
                'first_name' => $client->member->first_name ?? '',
                'middle_name' => $client->member->middle_name ?? '',
                'suffix' => $client->member->suffix ?? '',
                'birthdate' => $client->birthdate,
                'age' => $client->birthdate ? Carbon::parse($client->birthdate)->age : null,
                'civil_status' => $client->civil_status,
                'occupation' => $client->occupation ? $client->occupation->occupation : null,
                'monthly_income' => $monthlyIncome ? Number::format($monthlyIncome, 0) : '',
                'education' => $householdMember->educational_attainment,
                'relation_to_head' => $householdMember->relationship_to_applicant,
            ];
        })
            ->filter()
            ->values()
            ->toArray();

        if (empty($clients)) {
            $clients = [[
                'client_id' => '',
                'client_type' => 'HOUSEHOLD_MEMBER',
                'is_client' => false,
                'read_only_fields' => [],
                'last_name' => '',
                'first_name' => '',
                'middle_name' => '',
                'suffix' => '',
                'birthdate' => null,
                'age' => null,
                'civil_status' => '',
                'occupation' => '',
                'monthly_income' => '',
                'education' => '',
                'relation_to_head' => '',
            ]];
        }

        return $clients;
    }
}
