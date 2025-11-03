<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ApplicationsReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        return ['Application ID', 'Applicant', 'Service ID', 'Billed', 'Assisted', 'Applied At'];
    }

    public function map($row): array
    {
        return [
            $row->application_id,
            $row->full_name,
            $row->service_id,
            (int) $row->billed_amount,
            (int) $row->assistance_amount,
            (string) $row->applied_at,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Applications Report';
    }
}
