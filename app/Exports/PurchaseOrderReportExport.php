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

class PurchaseOrderReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $purchaseOrders;
    protected $summary;

    public function __construct($purchaseOrders, $summary)
    {
        $this->purchaseOrders = collect($purchaseOrders);
        $this->summary = $summary;
    }

    public function collection()
    {
        return $this->purchaseOrders;
    }

    public function headings(): array
    {
        return [
            'PO Number',
            'PO Date',
            'Supplier',
            'Status',
            'Total Items',
            'Sub Total',
            'Tax Amount',
            'Total Amount',
            'Expected Date',
            'Created By',
            'Created At'
        ];
    }

    public function map($purchaseOrder): array
    {
        return [
            $purchaseOrder->po_number,
            $purchaseOrder->po_date,
            $purchaseOrder->supplier->display_name ?? 'N/A',
            ucfirst($purchaseOrder->status),
            $purchaseOrder->items->count(),
            '₹' . number_format($purchaseOrder->sub_total, 2),
            '₹' . number_format($purchaseOrder->tax_amount, 2),
            '₹' . number_format($purchaseOrder->total_amount, 2),
            $purchaseOrder->expected_date ?? 'N/A',
            $purchaseOrder->creator->name ?? 'N/A',
            $purchaseOrder->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A1:K' . ($this->purchaseOrders->count() + 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Purchase Order Report';
    }
}
