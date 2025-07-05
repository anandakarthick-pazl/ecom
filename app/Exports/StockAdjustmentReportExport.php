<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StockAdjustmentReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $adjustments;
    protected $summary;

    public function __construct($adjustments, $summary)
    {
        $this->adjustments = collect($adjustments);
        $this->summary = $summary;
    }

    public function collection()
    {
        return $this->adjustments;
    }

    public function headings(): array
    {
        return [
            'Adjustment Number',
            'Date',
            'Type',
            'Reason',
            'Total Items',
            'Total Value Impact',
            'Status',
            'Created By',
            'Approved By',
            'Approved Date'
        ];
    }

    public function map($adjustment): array
    {
        return [
            $adjustment->adjustment_number,
            $adjustment->adjustment_date,
            ucfirst($adjustment->type),
            $adjustment->reason,
            $adjustment->items->count(),
            '₹' . number_format($adjustment->total_adjustment_value ?? 0, 2),
            ucfirst($adjustment->status),
            $adjustment->creator->name ?? 'N/A',
            $adjustment->approver->name ?? 'Pending',
            $adjustment->approved_at ? $adjustment->approved_at->format('d/m/Y') : 'Pending'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A1:J' . ($this->adjustments->count() + 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Stock Adjustment Report';
    }
}
