<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Authentication\Occupation;
use App\Models\User\Client;
use App\Models\User\Household;
use App\Models\User\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class HouseholdProfileController extends Controller
{
    public function show(Household $household)
    {
        $occupations = Occupation::query()->orderBy('occupation')->pluck('occupation')->toArray();

        $clients = Household::query()
            ->where('household_id', $household->household_id)
            ->with(['client.member', 'client.applicant', 'client.patient', 'client.occupation'])
            ->get()
            ->map(function ($householdMember) {
                $client = $householdMember->client;

                if (! $client) {
                    return null;
                }

                $clientType = 'HOUSEHOLD_MEMBER';

                if ($client->applicant) {
                    $clientType = 'APPLICANT';
                } elseif ($client->patient) {
                    $clientType = 'PATIENT';
                }

                $monthlyIncome = optional($client->member)->monthly_income ?? optional($client)->monthly_income;

                return [
                    'client_id' => $client->client_id,
                    'client_type' => $clientType,
                    'member' => $client->member,
                    'birthdate' => $client->birthdate,
                    'age' => $client->birthdate ? Carbon::parse($client->birthdate)->age : null,
                    'civil_status' => $client->civil_status,
                    'occupation' => $client->occupation ? $client->occupation->occupation : null,
                    'monthly_income' => $monthlyIncome ? Number::format($monthlyIncome) : null,
                    'education' => $householdMember->educational_attainment,
                    'relation_to_head' => $householdMember->relationship_to_applicant,
                ];
            })
            ->filter()
            ->values()
            ->toArray();

        if (empty($clients)) {
            $clients = [null];
        }

        return view('pages.sidebar.profiles.profile.households', [
            'household' => $household,
            'clients' => $clients,
            'occupations' => $occupations,
        ]);
    }

    public function search(Request $request)
    {
        $term = $request->input('search', '');
        if (strlen($term) < 2) {
            return response()->json(['results' => []]);
        }

        $applicantClientIds = DB::table('applicants')->pluck('client_id')->all();
        $patientClientIds = DB::table('patients')->pluck('client_id')->all();

        $uniqueClientIds = collect($applicantClientIds)->merge($patientClientIds)->unique()->values()->all();

        $memberIds = Client::whereIn('client_id', $uniqueClientIds)->pluck('member_id')->unique()->toArray();

        if (empty($memberIds)) {
            return response()->json(['results' => []]);
        }

        $members = Member::search($term)
            ->query(fn ($query) => $query->whereIn('member_id', $memberIds)->with(['client' => function ($q) {
                $q->with(['applicant', 'patient', 'member.account', 'occupation']);
            }]))
            ->take(5)
            ->get();

        $results = $members->map(function ($member) {
            $isApplicant = ! is_null($member->client->applicant);
            $isPatient = ! is_null($member->client->patient);
            $isActiveAccount = optional($member->client->member->account)->account_status === 'Active';
            $isVerified = ($isApplicant || $isPatient) && $isActiveAccount;

            $firstNamePart = collect([$member->first_name, $member->middle_name, $member->suffix])->filter()->implode(' ');
            $fullName = collect([$member->last_name.',', $firstNamePart])->filter()->implode(' ');

            return [
                'id' => $member->client->client_id,
                'text' => $fullName,
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
        });

        return response()->json([
            'results' => $results->toArray(),
        ]);
    }

    public function verifyName(Request $request)
    {
        $lastName = $request->input('last_name');
        $firstName = $request->input('first_name');
        $exists = Member::query()->where('last_name', $lastName)->where('first_name', $firstName)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function verifyFullName(Request $request)
    {
        $lastName = $request->input('last_name');
        $firstName = $request->input('first_name');
        $middleName = $request->input('middle_name');

        $exists = Member::query()->where('last_name', $lastName)->where('first_name', $firstName)->where('middle_name', $middleName)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function update(Household $household)
    {
        return redirect()->route('profiles.households.list')->with('success', 'Household has been updated successfully.');
    }

    public function destroy(Household $household)
    {
        $household->delete();

        return redirect()->route('profiles.households.list')->with('success', 'Household has been deleted successfully.');
    }
}
