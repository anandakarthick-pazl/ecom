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

class GrnReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $grns;
    protected $summary;

    public function __construct($grns, $summary)
    {
        $this->grns = collect($grns);
        $this->summary = $summary;
    }

    public function collection()
    {
        return $this->grns;
    }

    public function headings(): array
    {
        return [
            'GRN Number',
            'PO Number',
            'Supplier',
            'Received Date',
            'Invoice Number',
            'Invoice Date',
            'Invoice Amount',
            'Total Items',
            'Status',
            'Received By',
            'Created At'
        ];
    }

    public function map($grn): array
    {
        return [
            $grn->grn_number,
            $grn->purchaseOrder->po_number ?? 'N/A',
            $grn->supplier->display_name ?? 'N/A',
            $grn->received_date,
            $grn->invoice_number ?? 'N/A',
            $grn->invoice_date ?? 'N/A',
            '₹' . number_format($grn->invoice_amount, 2),
            $grn->items->count(),
            ucfirst($grn->status),
            $grn->receivedBy->name ?? 'N/A',
            $grn->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A1:K' . ($this->grns->count() + 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'GRN Report';
    }
}
