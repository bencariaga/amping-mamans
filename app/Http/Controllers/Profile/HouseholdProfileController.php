<?php

namespace App\Http\Controllers\Profile;

use App\Actions\Household\GetHouseholdClients;
use App\Actions\Household\PrepareHouseholdMembers;
use App\Actions\Household\SearchHouseholdClients;
use App\Actions\Household\UpdateHouseholdMembers;
use App\Actions\Household\ValidateHouseholdData;
use App\Http\Controllers\Controller;
use App\Models\Authentication\Occupation;
use App\Models\User\Household;
use App\Models\User\Member;
use Illuminate\Http\Request;

class HouseholdProfileController extends Controller
{
    public function __construct(
        private GetHouseholdClients $getHouseholdClients,
        private SearchHouseholdClients $searchHouseholdClients,
        private ValidateHouseholdData $validateHouseholdData,
        private PrepareHouseholdMembers $prepareHouseholdMembers,
        private UpdateHouseholdMembers $updateHouseholdMembers
    ) {}

    public function show(Household $household)
    {
        $occupations = Occupation::query()->orderBy('occupation')->pluck('occupation')->toArray();
        $clients = $this->getHouseholdClients->execute($household->household_id);

        return view('pages.sidebar.profiles.profile.households', [
            'household' => $household,
            'clients' => $clients,
            'occupations' => $occupations,
        ]);
    }

    public function search(Request $request)
    {
        $results = $this->searchHouseholdClients->execute($request);

        return response()->json(['results' => $results]);
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

    public function update(Request $request, Household $household)
    {
        $members = $this->prepareHouseholdMembers->execute($request->input('members', []));
        $request->merge(['members' => $members]);
        $validated = $this->validateHouseholdData->execute($request);
        $household->update(['household_name' => $validated['household_name']]);
        $this->updateHouseholdMembers->execute($household->household_id, $validated['members'], $validated['household_name']);

        return redirect()->route('profiles.households.list')->with('success', 'Household has been updated successfully.');
    }

    public function destroy(Household $household)
    {
        $household->delete();

        return redirect()->route('profiles.households.list')->with('success', 'Household has been deleted successfully.');
    }
}
