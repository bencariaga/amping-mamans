<?php

namespace App\Actions\Report;

use App\Models\Operation\Application;
use App\Models\Operation\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GenerateApplicationsReport
{
    public function execute(Request $request, $start, $end)
    {
        $items = Application::query()
            ->join('patients', 'applications.patient_id', '=', 'patients.patient_id')
            ->join('applicants', 'patients.applicant_id', '=', 'applicants.applicant_id')
            ->join('clients', 'applicants.client_id', '=', 'clients.client_id')
            ->join('members', 'clients.member_id', '=', 'members.member_id')
            ->leftJoin('expense_ranges', 'applications.exp_range_id', '=', 'expense_ranges.exp_range_id')
            ->when($request->filled('service_id'), function ($q) use ($request) {
                $q->where('expense_ranges.service_id', $request->get('service_id'));
            })
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween(DB::raw('DATE(applications.application_date)'), [$start->toDateString(), $end->toDateString()]);
            })
            ->orderBy('applications.application_date', 'desc')
            ->get([
                'applications.application_id',
                DB::raw("CONCAT(members.first_name, ' ', COALESCE(members.middle_name, ''), ' ', members.last_name, ' ', COALESCE(members.suffix, '')) as full_name"),
                'expense_ranges.service_id as service_id',
                'applications.billed_amount',
                'applications.assistance_amount',
                'applications.application_date as applied_at',
            ]);

        return [
            'items' => $items,
            'summary' => [
                'total' => $items->count(),
                'billed' => (int) $items->sum('billed_amount'),
                'assisted' => (int) $items->sum('assistance_amount'),
            ],
            'extra' => ['services' => Service::all(['service_id', 'service'])],
        ];
    }
}
