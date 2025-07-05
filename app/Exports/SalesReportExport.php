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
            'Status'
        ];
    }

    public function map($sale): array
    {
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
            ucfirst($sale->status)
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
