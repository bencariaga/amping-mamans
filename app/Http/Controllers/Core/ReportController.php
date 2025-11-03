<?php

namespace App\Http\Controllers\Core;

use App\Actions\Report\GenerateApplicantsReport;
use App\Actions\Report\GenerateApplicationsReport;
use App\Actions\Report\GeneratePatientsReport;
use App\Actions\Report\GenerateTariffsReport;
use App\Actions\Report\ParseDateFilters;
use App\Exports\ApplicantsReportExport;
use App\Exports\ApplicationsReportExport;
use App\Exports\PatientsReportExport;
use App\Exports\TariffsReportExport;
use App\Http\Controllers\Controller;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('pages.reports.index');
    }

    public function downloadPdfDompdf(Request $request, string $type, ParseDateFilters $parseDateAction)
    {
        [$label] = $parseDateAction->execute($request);
        $view = $this->show($request, $type, $parseDateAction);
        $data = $view->getData();

        $html = view('pages.reports.pdf', [
            'type' => $type,
            'rangeLabel' => $label,
            'items' => $data['items'] ?? collect(),
            'summary' => $data['summary'] ?? [],
        ])->render();

        $pdf = PDF::loadHTML($html)->setPaper('a4', 'portrait');
        $filename = sprintf('%s-report-%s.pdf', $type, Carbon::now()->format('Y-m-d_H-i-s'));

        return $pdf->download($filename);
    }

    public function show(Request $request, string $type, ?ParseDateFilters $parseDateAction = null, ?GenerateApplicantsReport $applicantsAction = null, ?GeneratePatientsReport $patientsAction = null, ?GenerateApplicationsReport $applicationsAction = null, ?GenerateTariffsReport $tariffsAction = null)
    {
        $parseDateAction = $parseDateAction ?? app(ParseDateFilters::class);

        [$start, $end, $label] = $parseDateAction->execute($request);
        $extra = [];

        switch ($type) {
            case 'applicants':
                $applicantsAction = $applicantsAction ?? app(GenerateApplicantsReport::class);
                $result = $applicantsAction->execute($request, $start, $end);
                $items = $result['items'];
                $summary = $result['summary'];
                break;

            case 'patients':
                $patientsAction = $patientsAction ?? app(GeneratePatientsReport::class);
                $result = $patientsAction->execute($request, $start, $end);
                $items = $result['items'];
                $summary = $result['summary'];
                break;

            case 'applications':
                $applicationsAction = $applicationsAction ?? app(GenerateApplicationsReport::class);
                $result = $applicationsAction->execute($request, $start, $end);
                $items = $result['items'];
                $summary = $result['summary'];
                $extra = $result['extra'];
                break;

            case 'tariffs':
                $tariffsAction = $tariffsAction ?? app(GenerateTariffsReport::class);
                $result = $tariffsAction->execute($request, $start, $end);
                $items = $result['items'];
                $summary = $result['summary'];
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

    public function downloadPdf(Request $request, string $type, ParseDateFilters $parseDateAction)
    {
        [$label] = $parseDateAction->execute($request);

        $viewData = [
            'type' => $type,
            'rangeLabel' => $label,
        ];

        $view = $this->show($request, $type, $parseDateAction);
        $data = $view->getData();
        $viewData['items'] = $data['items'] ?? collect();
        $viewData['summary'] = $data['summary'] ?? [];
        $html = view('pages.reports.pdf', $viewData)->render();
        $tmpDir = storage_path('snappy-temp');

        if (! File::exists($tmpDir)) {
            File::makeDirectory($tmpDir, 0775, true);
        }

        $htmlPath = $tmpDir.DIRECTORY_SEPARATOR.'report_'.$type.'_'.uniqid().'.html';
        File::put($htmlPath, $html);

        $pdf = PDF::loadFile($htmlPath)->setPaper('letter', 'portrait');

        if (method_exists($pdf, 'setBinary')) {
            $pdf->setBinary(base_path('vendor/wemersonjanuario/wkhtmltopdf-windows/bin/64bit/wkhtmltopdf.exe'));
        }

        if (method_exists($pdf, 'setTemporaryFolder')) {
            $pdf->setTemporaryFolder($tmpDir);
        }

        if (method_exists($pdf, 'setOption')) {
            $pdf->setOption('enable-local-file-access', true);
            $pdf->setOption('encoding', 'UTF-8');
            $pdf->setOption('zoom', 1.1);
            $pdf->setOption('disable-smart-shrinking', true);
            $pdf->setOption('dpi', 300);
        }

        $filename = sprintf('%s-report-%s.pdf', $type, Carbon::now()->format('Y-m-d_H-i-s'));

        Log::info('Reports PDF generation', ['type' => $type, 'html' => $htmlPath]);

        return $pdf->download($filename);
    }

    public function downloadXlsx(Request $request, string $type, ParseDateFilters $parseDateAction)
    {
        $view = $this->show($request, $type, $parseDateAction);
        $data = $view->getData();
        $items = $data['items'] ?? collect();

        $exportClass = match ($type) {
            'applicants' => new ApplicantsReportExport($items),
            'patients' => new PatientsReportExport($items),
            'applications' => new ApplicationsReportExport($items),
            'tariffs' => new TariffsReportExport($items),
            default => abort(404),
        };

        $filename = sprintf('%s-report-%s.xlsx', $type, Carbon::now()->format('Y-m-d_H-i-s'));

        return Excel::download($exportClass, $filename);
    }
}
