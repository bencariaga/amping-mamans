<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Authentication\Occupation;

class ClientRegistrationController extends Controller
{
    private function generateNextId(string $prefix, string $table, string $primaryKey): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $max = DB::table($table)->where($primaryKey, 'like', "{$base}-%")->max($primaryKey);
        $last = $max ? (int) Str::afterLast($max, '-') : 0;
        return $base . '-' . Str::padLeft($last + 1, 9, '0');
    }

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
