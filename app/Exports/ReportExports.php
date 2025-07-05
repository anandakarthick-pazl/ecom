<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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
            'Date',
            'Supplier',
            'Items Count',
            'Subtotal',
            'Tax',
            'Discount',
            'Total Amount',
            'Status',
            'Expected Delivery',
            'Created By'
        ];
    }

    public function map($po): array
    {
        return [
            $po->po_number,
            $po->po_date->format('Y-m-d'),
            $po->supplier->display_name ?? 'N/A',
            $po->items->count(),
            number_format($po->subtotal, 2),
            number_format($po->tax_amount, 2),
            number_format($po->discount, 2),
            number_format($po->total_amount, 2),
            ucfirst($po->status),
            $po->expected_delivery_date ? $po->expected_delivery_date->format('Y-m-d') : 'N/A',
            $po->creator->name ?? 'N/A'
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
        return 'Purchase Orders';
    }
}

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
            'Date Received',
            'PO Number',
            'Supplier',
            'Invoice Number',
            'Invoice Date',
            'Invoice Amount',
            'Items Count',
            'Status',
            'Received By'
        ];
    }

    public function map($grn): array
    {
        return [
            $grn->grn_number,
            $grn->received_date->format('Y-m-d'),
            $grn->purchaseOrder->po_number ?? 'N/A',
            $grn->supplier->display_name ?? 'N/A',
            $grn->invoice_number ?? 'N/A',
            $grn->invoice_date ? $grn->invoice_date->format('Y-m-d') : 'N/A',
            number_format($grn->invoice_amount ?? 0, 2),
            $grn->items->count(),
            ucfirst($grn->status),
            $grn->receiver->name ?? 'N/A'
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
        return 'GRN Report';
    }
}

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
            'Items Count',
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
            $adjustment->adjustment_date->format('Y-m-d'),
            ucfirst($adjustment->type),
            $adjustment->reason,
            $adjustment->items->count(),
            number_format($adjustment->total_adjustment_value, 2),
            ucfirst($adjustment->status),
            $adjustment->creator->name ?? 'N/A',
            $adjustment->approver->name ?? 'N/A',
            $adjustment->approved_at ? $adjustment->approved_at->format('Y-m-d') : 'N/A'
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
        return 'Stock Adjustments';
    }
}

class IncomeReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect([
            ['category' => 'INCOME', 'type' => 'POS Sales', 'amount' => $this->data['income']['pos_sales']],
            ['category' => 'INCOME', 'type' => 'Online Sales', 'amount' => $this->data['income']['online_sales']],
            ['category' => 'INCOME', 'type' => 'Total Income', 'amount' => $this->data['income']['total']],
            ['category' => '', 'type' => '', 'amount' => ''],
            ['category' => 'EXPENSES', 'type' => 'Purchases', 'amount' => $this->data['expenses']['purchases']],
            ['category' => 'EXPENSES', 'type' => 'Stock Loss', 'amount' => $this->data['expenses']['stock_loss']],
            ['category' => 'EXPENSES', 'type' => 'Total Expenses', 'amount' => $this->data['expenses']['total']],
            ['category' => '', 'type' => '', 'amount' => ''],
            ['category' => 'PROFIT', 'type' => 'Net Profit', 'amount' => $this->data['net_profit']],
            ['category' => 'PROFIT', 'type' => 'Profit Margin (%)', 'amount' => number_format($this->data['profit_margin'], 2) . '%'],
        ]);
    }

    public function headings(): array
    {
        return ['Category', 'Type', 'Amount (₹)'];
    }

    public function map($item): array
    {
        return [
            $item['category'],
            $item['type'],
            is_numeric($item['amount']) ? number_format($item['amount'], 2) : $item['amount']
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
        return 'Income Report';
    }
}
