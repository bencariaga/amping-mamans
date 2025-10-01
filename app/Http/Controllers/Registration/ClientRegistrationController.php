<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\Authentication\Occupation;

class ClientRegistrationController extends Controller
{
    public function create()
    {
        $occupations = Occupation::all();
        $selectedOccupationName = '— Select —';
        $oldOccId = old('occupation_id');

        if ($oldOccId) {
            $occ = Occupation::find($oldOccId);
            $selectedOccupationName = $occ ? $occ->occupation : '— Select —';
        }

        return view('pages.sidebar.profiles.register.applicant', ['occupations' => $occupations, 'selectedOccupationName' => $selectedOccupationName]);
    }
}
