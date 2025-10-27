<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\Storage\Data;
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

        $data = Data::create([
            'data_status' => 'Unarchived',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Household::create([
            'household_id' => 'HOUSEHOLD-'.now()->format('Y').'-'.str_pad(Household::count() + 1, 9, '0', STR_PAD_LEFT),
            'data_id' => $data->data_id,
            'household_name' => $validated['household_name'],
        ]);

        return redirect()->route('profiles.households.list')->with('success', 'Household created successfully.');
    }
}
