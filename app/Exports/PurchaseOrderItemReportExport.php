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

class PurchaseOrderItemReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $items;

    public function __construct($items)
    {
        $this->items = collect($items);
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        return [
            'PO Number',
            'PO Date',
            'Supplier',
            'Product Name',
            'Product Code',
            'Category',
            'Quantity Ordered',
            'Unit Price',
            'Total Price',
            'Quantity Received',
            'Pending Quantity',
            'Status'
        ];
    }

    public function map($item): array
    {
        $received = $item->grnItems->sum('received_quantity');
        $pending = $item->quantity - $received;
        
        return [
            $item->purchaseOrder->po_number,
            $item->purchaseOrder->po_date,
            $item->purchaseOrder->supplier->display_name ?? 'N/A',
            $item->product->name,
            $item->product->code ?? 'N/A',
            $item->product->category->name ?? 'N/A',
            $item->quantity,
            '₹' . number_format($item->unit_price, 2),
            '₹' . number_format($item->total_price, 2),
            $received,
            $pending,
            $pending > 0 ? 'Partial' : 'Completed'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A1:L' . ($this->items->count() + 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Purchase Order Items Report';
    }
}
