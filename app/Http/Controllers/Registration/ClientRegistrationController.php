<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\Authentication\Occupation;
use App\Models\User\Member;
use App\Models\User\Patient;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $fullName = collect([
            $request->first_name,
            $request->middle_name,
            $request->last_name,
            $request->suffix,
        ])->filter()->implode(' ');

        $existsApplicant = Member::where('member_type', 'Applicant')
            ->where('first_name', $request->first_name)
            ->where('middle_name', $request->middle_name)
            ->where('last_name', $request->last_name)
            ->where('suffix', $request->suffix ?? '')
            ->exists();

        $existsPatient = Patient::where('first_name', $request->first_name)
            ->where('middle_name', $request->middle_name)
            ->where('last_name', $request->last_name)
            ->where('suffix', $request->suffix ?? '')
            ->exists();

        if ($existsApplicant) {
            return back()->withInput()->withErrors([
                'duplicate_applicant' => 'This applicant account already exists with the same name.',
                'duplicate_patient_as_applicant' => "This patient {$fullName} already exists as an existing applicant.",
            ]);
        }

        if ($existsPatient) {
            return back()->withInput()->withErrors([
                'duplicate_patient' => "This patient {$fullName} already exists as a patient of an existing applicant.",
            ]);
        }
    }
}
