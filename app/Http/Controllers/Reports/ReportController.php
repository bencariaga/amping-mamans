<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Operation\Application;
use App\Models\Operation\TariffList;
use App\Models\Operation\Service;
use App\Models\User\Applicant;
use App\Models\User\Client;
use App\Models\User\Member;
use App\Models\User\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use PDF;
use Barryvdh\DomPDF\Facade\Pdf as DompdfPdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('pages.reports.index');
    }

    public function downloadPdfDompdf(Request $request, string $type)
    {
        [$start, $end, $label] = $this->parseDateFilters($request);
        $view = $this->show($request, $type);
        $data = $view->getData();

        $html = view('pages.reports.pdf.generic', [
            'type' => $type,
            'rangeLabel' => $label,
            'items' => $data['items'] ?? collect(),
            'summary' => $data['summary'] ?? [],
        ])->render();

        $pdf = DompdfPdf::loadHTML($html)->setPaper('a4', 'portrait');
        $filename = sprintf('%s-report-%s.pdf', $type, Carbon::now()->format('Ymd_His'));
        return $pdf->download($filename);
    }

    public function show(Request $request, string $type)
    {
        [$start, $end, $label] = $this->parseDateFilters($request);
        $extra = [];

        switch ($type) {
            case 'applicants':
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
                        $startYear = (int) $start->year; $endYear = (int) $end->year; $years = range($startYear, $endYear);
                        $q->where(function ($qq) use ($years) { foreach ($years as $y) { $qq->orWhere('applicants.applicant_id', 'like', "APPLICANT-{$y}%"); } });
                    })
                    ->orderBy($hasApplicantsCreated ? 'applicants.created_at' : 'applicants.applicant_id', 'desc')
                    ->get(array_filter([
                        'applicants.applicant_id',
                        'members.full_name',
                        'contacts.phone_number',
                        'clients.monthly_income',
                        $hasApplicantsCreated ? 'applicants.created_at as created_at' : DB::raw("SUBSTRING(applicants.applicant_id, 10, 4) as created_year"),
                        'applicants.barangay',
                    ]));
                $summary = [
                    'total' => $items->count(),
                ];
                break;

            case 'patients':
                $hasPatientsCreated = Schema::hasColumn('patients', 'created_at');
                $items = Patient::query()
                    ->join('clients', 'patients.client_id', '=', 'clients.client_id')
                    ->join('members', 'clients.member_id', '=', 'members.member_id')
                    ->when($start && $end && $hasPatientsCreated, function ($q) use ($start, $end) {
                        $q->whereBetween(DB::raw('DATE(patients.created_at)'), [$start->toDateString(), $end->toDateString()]);
                    })
                    ->when($start && $end && ! $hasPatientsCreated, function ($q) use ($start, $end) {
                        $startYear = (int) $start->year; $endYear = (int) $end->year; $years = range($startYear, $endYear);
                        $q->where(function ($qq) use ($years) { foreach ($years as $y) { $qq->orWhere('patients.patient_id', 'like', "PATIENT-{$y}%"); } });
                    })
                    ->orderBy($hasPatientsCreated ? 'patients.created_at' : 'patients.patient_id', 'desc')
                    ->get(array_filter([
                        'patients.patient_id',
                        'members.full_name',
                        'clients.sex',
                        'clients.age',
                        'patients.patient_category',
                        $hasPatientsCreated ? 'patients.created_at as created_at' : DB::raw("SUBSTRING(patients.patient_id, 9, 4) as created_year"),
                    ]));
                $summary = [
                    'total' => $items->count(),
                ];
                break;

            case 'applications':
                $items = Application::query()
                    ->join('applicants', 'applications.applicant_id', '=', 'applicants.applicant_id')
                    ->join('clients as applicant_clients', 'applicants.client_id', '=', 'applicant_clients.client_id')
                    ->join('members as applicant_members', 'applicant_clients.member_id', '=', 'applicant_members.member_id')
                    ->leftJoin('patients', 'applications.patient_id', '=', 'patients.patient_id')
                    ->leftJoin('clients as patient_clients', 'patients.client_id', '=', 'patient_clients.client_id')
                    ->leftJoin('members as patient_members', 'patient_clients.member_id', '=', 'patient_members.member_id')
                    ->leftJoin('affiliate_partners', 'applications.affiliate_partner_id', '=', 'affiliate_partners.affiliate_partner_id')
                    ->leftJoin('expense_ranges', 'applications.exp_range_id', '=', 'expense_ranges.exp_range_id')
                    ->leftJoin('services', 'expense_ranges.service_id', '=', 'services.service_id')
                    ->when($request->filled('service_id'), function ($q) use ($request) {
                        $q->where('expense_ranges.service_id', $request->get('service_id'));
                    })
                    ->when($start && $end, function ($q) use ($start, $end) {
                        $q->whereBetween(DB::raw('DATE(applications.applied_at)'), [$start->toDateString(), $end->toDateString()]);
                    })
                    ->orderBy('applications.applied_at', 'desc')
                    ->get([
                        'applications.application_id',
                        'applicant_members.full_name as applicant_name',
                        'patient_members.full_name as patient_name',
                        'affiliate_partners.affiliate_partner_name',
                        'services.service_type as service_name',
                        'applications.billed_amount',
                        'applications.assistance_amount',
                        'applications.applied_at',
                    ]);
                $summary = [
                    'total' => $items->count(),
                    'billed' => (int) $items->sum('billed_amount'),
                    'assisted' => (int) $items->sum('assistance_amount'),
                ];
                $extra['services'] = Service::all(['service_id','service_type']);
                break;

            case 'tariffs':
                $hasTariffCreated = Schema::hasColumn('tariff_lists', 'created_at');
                $items = TariffList::query()
                    ->when($start && $end, function ($q) use ($start, $end) {
                        $q->whereBetween(DB::raw('DATE(effectivity_date)'), [$start->toDateString(), $end->toDateString()]);
                    })
                    ->orderBy('effectivity_date', 'desc')
                    ->get(array_filter([
                        'tariff_list_id',
                        'effectivity_date',
                        'tl_status',
                        $hasTariffCreated ? 'created_at' : null,
                    ]));
                $summary = [
                    'total' => $items->count(),
                    'active' => $items->where('tl_status', 'Active')->count(),
                    'inactive' => $items->where('tl_status', 'Inactive')->count(),
                    'draft' => $items->where('tl_status', 'Draft')->count(),
                    'scheduled' => $items->where('tl_status', 'Scheduled')->count(),
                ];
                break;

            default:
                abort(404);
        }

        return view('pages.reports.show', [
            'type' => $type,
            'items' => $items,
            'summary' => $summary,
            'rangeLabel' => $label,
            'query' => $request->all(),
            'extra' => $extra,
        ]);
    }

    public function downloadPdf(Request $request, string $type)
    {
        [$start, $end, $label] = $this->parseDateFilters($request);
        // Prepare a minimal, self-contained PDF view (avoid external assets)
        $viewData = [
            'type' => $type,
            'rangeLabel' => $label,
        ];
        // Reuse data from show() to avoid duplicating queries
        $view = $this->show($request, $type);
        $data = $view->getData();
        $viewData['items'] = $data['items'] ?? collect();
        $viewData['summary'] = $data['summary'] ?? [];
        $html = view('pages.reports.pdf.generic', $viewData)->render();

        // Ensure a writable temp directory (avoid C:\\Windows\\Temp issues)
        $tmpDir = storage_path('snappy-temp');
        if (!File::exists($tmpDir)) {
            File::makeDirectory($tmpDir, 0775, true);
        }

        // Save HTML to a temp file and use loadFile (more reliable on Windows)
        $htmlPath = $tmpDir . DIRECTORY_SEPARATOR . 'report_' . $type . '_' . uniqid() . '.html';
        File::put($htmlPath, $html);

        $pdf = PDF::loadFile($htmlPath)->setPaper('a4', 'portrait');
        // Force the same wkhtmltopdf binary as manual run
        if (method_exists($pdf, 'setBinary')) {
            $pdf->setBinary(base_path('vendor/wemersonjanuario/wkhtmltopdf-windows/bin/64bit/wkhtmltopdf.exe'));
        }
        if (method_exists($pdf, 'setTemporaryFolder')) {
            $pdf->setTemporaryFolder($tmpDir);
        }
        // Minimal reliable options only
        if (method_exists($pdf, 'setOption')) {
            $pdf->setOption('enable-local-file-access', true);
            $pdf->setOption('encoding', 'UTF-8');
        }
        // Note: wkhtmltopdf doesn't support a 'tmpdir' option; using setTemporaryFolder above

        $filename = sprintf('%s-report-%s.pdf', $type, Carbon::now()->format('Ymd_His'));

        // Debug hint in logs
        Log::info('Reports PDF generation', ['type' => $type, 'html' => $htmlPath]);

        return $pdf->download($filename);
    }

    public function downloadCsv(Request $request, string $type)
    {
        // Render current dataset
        $view = $this->show($request, $type);
        $data = $view->getData();
        $items = $data['items'] ?? collect();

        $headersMap = [
            'applicants' => ['Applicant ID','Full Name','Phone','Monthly Income','Barangay','Created'],
            'patients' => ['Patient ID','Full Name','Sex','Age','Category','Created'],
            'applications' => ['Applicant','Patient','Affiliate Partner','Service','Billed','Assisted','Applied At'],
            'tariffs' => ['Tariff List ID','Effectivity Date','Status','Created'],
        ];

        $rows = [];
        $headers = $headersMap[$type] ?? [];
        $rows[] = $headers;

        foreach ($items as $row) {
            if ($type === 'applicants') {
                $rows[] = [
                    $row->applicant_id,
                    $row->full_name,
                    $row->phone_number ?? '',
                    (int) $row->monthly_income,
                    $row->barangay ?? '',
                    (string) $row->created_at,
                ];
            } elseif ($type === 'patients') {
                $rows[] = [
                    $row->patient_id,
                    $row->full_name,
                    $row->sex ?? '',
                    $row->age ?? '',
                    $row->patient_category ?? '',
                    (string) $row->created_at,
                ];
            } elseif ($type === 'applications') {
                $rows[] = [
                    $row->applicant_name ?? '',
                    $row->patient_name ?? '',
                    $row->affiliate_partner_name ?? '',
                    $row->service_name ?? '',
                    (int) $row->billed_amount,
                    (int) $row->assistance_amount,
                    (string) $row->applied_at,
                ];
            } elseif ($type === 'tariffs') {
                $rows[] = [
                    $row->tariff_list_id,
                    (string) $row->effectivity_date,
                    $row->tl_status,
                    (string) $row->created_at,
                ];
            }
        }

        $filename = sprintf('%s-report-%s.csv', $type, Carbon::now()->format('Ymd_His'));

        $callback = function () use ($rows) {
            $FH = fopen('php://output', 'w');
            foreach ($rows as $r) {
                fputcsv($FH, $r);
            }
            fclose($FH);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function parseDateFilters(Request $request): array
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $month = $request->get('month');
        $year = $request->get('year');

        if ($dateFrom && $dateTo) {
            $start = Carbon::parse($dateFrom)->startOfDay();
            $end = Carbon::parse($dateTo)->endOfDay();
            return [$start, $end, $start->toDateString().' to '.$end->toDateString()];
        }

        if ($month && $year) {
            $start = Carbon::createFromDate((int) $year, (int) $month, 1)->startOfMonth();
            $end = (clone $start)->endOfMonth();
            return [$start, $end, $start->format('F Y')];
        }

        if ($year) {
            $start = Carbon::createFromDate((int) $year, 1, 1)->startOfYear();
            $end = (clone $start)->endOfYear();
            return [$start, $end, $start->format('Y')];
        }

        // Default: last 30 days
        $end = Carbon::now()->endOfDay();
        $start = (clone $end)->subDays(29)->startOfDay();
        return [$start, $end, 'Last 30 days'];
    }
}
