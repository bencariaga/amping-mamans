<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ApplicantsReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
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
        return ['Applicant ID', 'Full Name', 'Phone', 'Monthly Income', 'Barangay', 'Created'];
    }

    public function map($row): array
    {
        return [
            $row->applicant_id,
            $row->full_name,
            $row->phone_number ?? '',
            (int) $row->monthly_income,
            $row->barangay ?? '',
            (string) $row->created_at,
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
        return 'Applicants Report';
    }
}
