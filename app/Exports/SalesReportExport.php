<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport implements WithMultipleSheets
{
    protected $posSales;
    protected $onlineOrders;
    protected $summary;

    public function __construct($posSales, $onlineOrders, $summary)
    {
        $this->posSales = $posSales;
        $this->onlineOrders = $onlineOrders;
        $this->summary = $summary;
    }

    public function sheets(): array
    {
        return [
            'Summary' => new SalesSummarySheet($this->summary),
            'POS Sales' => new PosSalesSheet($this->posSales),
            'Online Orders' => new OnlineOrdersSheet($this->onlineOrders),
            'Commissions' => new CommissionsSheet($this->posSales->filter(function($sale) {
                return $sale->commission !== null;
            })),
        ];
    }
}

class SalesSummarySheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $summary;

    public function __construct($summary)
    {
        $this->summary = $summary;
    }

    public function collection()
    {
        return collect([
            ['metric' => 'POS Sales Count', 'value' => $this->summary['pos_sales_count']],
            ['metric' => 'POS Sales Total', 'value' => $this->summary['pos_sales_total']],
            ['metric' => 'Online Sales Count', 'value' => $this->summary['online_sales_count']],
            ['metric' => 'Online Sales Total', 'value' => $this->summary['online_sales_total']],
            ['metric' => 'Total Sales', 'value' => $this->summary['total_sales']],
            ['metric' => 'Cash Sales', 'value' => $this->summary['cash_sales']],
            ['metric' => 'Digital Sales', 'value' => $this->summary['digital_sales']],
            // Commission metrics
            ['metric' => '', 'value' => ''], // Empty row for separation
            ['metric' => 'COMMISSION SUMMARY', 'value' => ''],
            ['metric' => 'Total Commission Amount', 'value' => $this->summary['total_commission_amount'] ?? 0],
            ['metric' => 'Pending Commission Amount', 'value' => $this->summary['pending_commission_amount'] ?? 0],
            ['metric' => 'Paid Commission Amount', 'value' => $this->summary['paid_commission_amount'] ?? 0],
            ['metric' => 'Total Commission Records', 'value' => $this->summary['total_commission_count'] ?? 0],
            ['metric' => 'Pending Commission Records', 'value' => $this->summary['pending_commission_count'] ?? 0],
            ['metric' => 'Paid Commission Records', 'value' => $this->summary['paid_commission_count'] ?? 0],
        ]);
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function map($item): array
    {
        return [$item['metric'], $item['value']];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }
}

class PosSalesSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function collection()
    {
        return $this->sales;
    }

    public function headings(): array
    {
        return [
            'Invoice Number',
            'Date',
            'Customer',
            'Items Count',
            'Subtotal',
            'Tax',
            'Discount',
            'Total Amount',
            'Payment Method',
            'Cashier',
            'Status',
            // Commission columns
            'Commission Enabled',
            'Reference Name',
            'Commission %',
            'Commission Amount',
            'Commission Status'
        ];
    }

    public function map($sale): array
    {
        $commission = $sale->commission;
        
        return [
            $sale->invoice_number,
            $sale->sale_date,
            $sale->customer_name ?? 'Walk-in',
            $sale->items->count(),
            number_format($sale->subtotal, 2),
            number_format($sale->tax_amount, 2),
            number_format($sale->discount_amount, 2),
            number_format($sale->total_amount, 2),
            ucfirst(str_replace('_', ' ', $sale->payment_method)),
            $sale->cashier->name ?? 'N/A',
            ucfirst($sale->status),
            // Commission data
            $commission ? 'Yes' : 'No',
            $commission ? $commission->reference_name : 'N/A',
            $commission ? number_format($commission->commission_percentage, 2) . '%' : 'N/A',
            $commission ? number_format($commission->commission_amount, 2) : 'N/A',
            $commission ? ucfirst($commission->status) : 'N/A'
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
        return 'POS Sales';
    }
}

class OnlineOrdersSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Date',
            'Customer',
            'Items Count',
            'Subtotal',
            'Shipping',
            'Tax',
            'Total Amount',
            'Status'
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->created_at->format('Y-m-d'),
            $order->customer->name ?? 'Guest',
            $order->items->count(),
            number_format($order->subtotal, 2),
            number_format($order->shipping_cost, 2),
            number_format($order->tax_amount, 2),
            number_format($order->total_amount, 2),
            ucfirst($order->status)
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
        return 'Online Orders';
    }
}

class CommissionsSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $salesWithCommissions;

    public function __construct($salesWithCommissions)
    {
        $this->salesWithCommissions = $salesWithCommissions;
    }

    public function collection()
    {
        return $this->salesWithCommissions;
    }

    public function headings(): array
    {
        return [
            'Invoice Number',
            'Sale Date',
            'Customer',
            'Reference Name',
            'Commission %',
            'Base Amount',
            'Commission Amount',
            'Commission Status',
            'Created Date',
            'Paid Date',
            'Notes'
        ];
    }

    public function map($sale): array
    {
        $commission = $sale->commission;
        
        return [
            $sale->invoice_number,
            $sale->sale_date,
            $sale->customer_name ?? 'Walk-in',
            $commission->reference_name,
            number_format($commission->commission_percentage, 2) . '%',
            number_format($commission->base_amount, 2),
            number_format($commission->commission_amount, 2),
            ucfirst($commission->status),
            $commission->created_at->format('Y-m-d H:i:s'),
            $commission->paid_at ? $commission->paid_at->format('Y-m-d H:i:s') : 'Not Paid',
            $commission->notes ?? 'N/A'
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
        return 'Commissions';
    }
}
