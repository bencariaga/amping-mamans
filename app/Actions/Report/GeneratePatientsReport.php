<?php

namespace App\Actions\Report;

use App\Models\User\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GeneratePatientsReport
{
    public function execute(Request $request, $start, $end)
    {
        $hasPatientsCreated = Schema::hasColumn('patients', 'created_at');
        
        $items = Patient::query()
            ->join('clients', 'patients.client_id', '=', 'clients.client_id')
            ->join('members', 'clients.member_id', '=', 'members.member_id')
            ->when($start && $end && $hasPatientsCreated, function ($q) use ($start, $end) {
                $q->whereBetween(DB::raw('DATE(patients.created_at)'), [$start->toDateString(), $end->toDateString()]);
            })
            ->when($start && $end && ! $hasPatientsCreated, function ($q) use ($start, $end) {
                $startYear = (int) $start->year;
                $endYear = (int) $end->year;
                $years = range($startYear, $endYear);
                $q->where(function ($qq) use ($years) {
                    foreach ($years as $y) {
                        $qq->orWhere('patients.patient_id', 'like', "PATIENT-{$y}%");
                    }
                });
            })
            ->orderBy($hasPatientsCreated ? 'patients.created_at' : 'patients.patient_id', 'desc')
            ->get(array_filter([
                'patients.patient_id',
                DB::raw("CONCAT(members.first_name, ' ', COALESCE(members.middle_name, ''), ' ', members.last_name, ' ', COALESCE(members.suffix, '')) as full_name"),
                'clients.sex',
                'clients.age',
                'patients.patient_category',
                $hasPatientsCreated ? 'patients.created_at as created_at' : DB::raw('SUBSTRING(patients.patient_id, 9, 4) as created_year'),
            ]));

        return [
            'items' => $items,
            'summary' => ['total' => $items->count()],
        ];
    }
}
