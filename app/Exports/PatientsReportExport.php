<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PatientsReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
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
        return ['Patient ID', 'Full Name', 'Sex', 'Age', 'Category', 'Created'];
    }

    public function map($row): array
    {
        return [
            $row->patient_id,
            $row->full_name,
            $row->sex ?? '',
            $row->age ?? '',
            $row->patient_category ?? '',
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
        return 'Patients Report';
    }
}
