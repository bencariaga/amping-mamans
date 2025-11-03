<?php

namespace App\Actions\Report;

use App\Models\User\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GenerateApplicantsReport
{
    public function execute(Request $request, $start, $end)
    {
        $hasApplicantsCreated = Schema::hasColumn('applicants', 'created_at');
        
        $items = Applicant::query()
            ->join('clients', 'applicants.client_id', '=', 'clients.client_id')
            ->join('members', 'clients.member_id', '=', 'members.member_id')
            ->leftJoin('contacts', function ($q) {
                $q->on('clients.client_id', '=', 'contacts.client_id')
                    ->where('contacts.contact_type', '=', 'Application');
            })
            ->when($request->filled('barangay'), function ($q) use ($request) {
                $q->where('applicants.barangay', $request->get('barangay'));
            })
            ->when($start && $end && $hasApplicantsCreated, function ($q) use ($start, $end) {
                $q->whereBetween(DB::raw('DATE(applicants.created_at)'), [$start->toDateString(), $end->toDateString()]);
            })
            ->when($start && $end && ! $hasApplicantsCreated, function ($q) use ($start, $end) {
                $startYear = (int) $start->year;
                $endYear = (int) $end->year;
                $years = range($startYear, $endYear);
                $q->where(function ($qq) use ($years) {
                    foreach ($years as $y) {
                        $qq->orWhere('applicants.applicant_id', 'like', "APPLICANT-{$y}%");
                    }
                });
            })
            ->orderBy($hasApplicantsCreated ? 'applicants.created_at' : 'applicants.applicant_id', 'desc')
            ->get(array_filter([
                'applicants.applicant_id',
                DB::raw("CONCAT(members.first_name, ' ', COALESCE(members.middle_name, ''), ' ', members.last_name, ' ', COALESCE(members.suffix, '')) as full_name"),
                'contacts.contact_number as phone_number',
                'clients.monthly_income',
                $hasApplicantsCreated ? 'applicants.created_at as created_at' : DB::raw('SUBSTRING(applicants.applicant_id, 10, 4) as created_year'),
                'applicants.barangay',
            ]));

        return [
            'items' => $items,
            'summary' => ['total' => $items->count()],
        ];
    }
}
