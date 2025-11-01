<?php

namespace App\Actions\Household;

use App\Models\User\Client;
use App\Models\User\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchHouseholdClients
{
    public function execute(Request $request): array
    {
        $term = $request->input('q', '');

        if (strlen($term) < 2) {
            return [];
        }

        $applicantClientIds = DB::table('applicants')->pluck('client_id')->all();
        $patientClientIds = DB::table('patients')->pluck('client_id')->all();

        $uniqueClientIds = collect($applicantClientIds)->merge($patientClientIds)->unique()->values()->all();

        $memberIds = Client::whereIn('client_id', $uniqueClientIds)->pluck('member_id')->unique()->toArray();

        if (empty($memberIds)) {
            return [];
        }

        $members = Member::search($term)->query(fn ($query) => $query->whereIn('member_id', $memberIds)->with(['client' => function ($q) {
            $q->with(['applicant', 'patient', 'member.account', 'occupation']);
        }]))->take(5)->get();

        return $members->map(function ($member) {
            $isApplicant = ! is_null($member->client->applicant);
            $isPatient = ! is_null($member->client->patient);
            $isActiveAccount = optional($member->client->member->account)->account_status === 'Active';
            $isVerified = ($isApplicant || $isPatient) && $isActiveAccount;

            return [
                'id' => $member->client->client_id,
                'text' => $member->full_name,
                'last_name' => $member->last_name,
                'first_name' => $member->first_name,
                'middle_name' => $member->middle_name,
                'suffix' => $member->suffix,
                'birthdate' => optional($member->client)->birthdate,
                'age' => optional($member->client)->age,
                'civil_status' => optional($member->client)->civil_status,
                'occupation' => optional(optional($member->client)->occupation)->occupation,
                'monthly_income' => optional($member->client)->monthly_income,
                'is_verified' => $isVerified,
                'is_applicant' => $isApplicant,
                'is_patient' => $isPatient,
            ];
        })->toArray();
    }
}
