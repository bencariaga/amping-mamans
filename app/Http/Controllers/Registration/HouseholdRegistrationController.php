<?php

namespace App\Http\Controllers\Registration;

use App\Actions\IdGeneration\GenerateHouseholdId;
use App\Http\Controllers\Controller;
use App\Models\User\Household;
use Illuminate\Http\Request;

class HouseholdRegistrationController extends Controller
{
    public function create()
    {
        return view('pages.sidebar.profiles.register.household');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'household_name' => 'required|string|max:20',
        ]);

        Household::create([
            'household_id' => GenerateHouseholdId::execute(),
            'household_name' => $validated['household_name'],
        ]);

        return redirect()->route('profiles.households.list')->with('success', 'Household created successfully.');
    }
}
