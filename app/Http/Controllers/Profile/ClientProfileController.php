<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Storage\Data;
use App\Models\User\Member;
use App\Models\User\Client;
use App\Models\User\Applicant;
use App\Models\Authentication\Account;

class ClientProfileController extends Controller
{
    public function show(Applicant $applicant)
    {
        return view('pages.sidebar.profiles.profile.applicants', ['applicantId' => $applicant->applicant_id]);
    }

    public function destroy(Request $request, Applicant $applicant)
    {
        $fullName = (string) Str::of("{$applicant->client->member->first_name} {$applicant->client->member->middle_name} {$applicant->client->member->last_name} {$applicant->client->member->suffix}")->trim();
        $entered = Str::of($request->input('deleteConfirmationText', ''))->trim();

        if (strcasecmp($entered, $fullName) !== 0) {
            session()->flash('error', 'Confirmation text does not match.');
            return back()->withInput();
        }

        DB::transaction(function () use ($applicant) {
            $mainApplicantMemberId = $applicant->client->member->member_id;
            $patientMemberIdsToDelete = [];

            foreach ($applicant->patients as $patient) {
                if ($patient->member_id !== $mainApplicantMemberId) {
                    $patientMemberIdsToDelete[] = $patient->member_id;
                }

                $patient->delete();
            }

            $applicant->client->contacts()->delete();
            $applicant->delete();
            $dataId = $applicant->client->member->account->data_id;
            $accountId = $applicant->client->member->account_id;
            $memberId = $applicant->client->member_id;
            $clientId = $applicant->client_id;

            Client::find($clientId)->delete();

            foreach ($patientMemberIdsToDelete as $memberIdToDel) {
                $clientToDelete = Client::where('member_id', $memberIdToDel)->first();

                if ($clientToDelete) {
                    $clientToDelete->delete();
                }

                Member::find($memberIdToDel)->delete();
            }

            Member::find($memberId)->delete();
            Account::find($accountId)->delete();
            Data::find($dataId)->delete();
        });

        session()->flash('success', 'Applicant and all associated data have been successfully deleted.');
        return redirect()->route('profiles.applicants.list');
    }
}
