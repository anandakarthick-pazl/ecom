<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $customers;

    public function __construct($customers)
    {
        $this->customers = collect($customers);
    }

    public function collection()
    {
        return $this->customers;
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Email',
            'Phone',
            'Registration Date',
            'Total Orders',
            'Total POS Sales',
            'Online Spent (₹)',
            'POS Spent (₹)',
            'Total Spent (₹)',
            'Status'
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->name ?? 'N/A',
            $customer->email ?? 'N/A',
            $customer->mobile_number ?? 'N/A',
            $customer->created_at ? $customer->created_at->format('Y-m-d') : 'N/A',
            $customer->orders_count ?? 0,
            $customer->pos_sales_count ?? 0,
            number_format($customer->total_online_spent ?? 0, 2),
            number_format($customer->total_pos_spent ?? 0, 2),
            number_format(($customer->total_online_spent ?? 0) + ($customer->total_pos_spent ?? 0), 2),
            ucfirst($customer->status ?? 'active')
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
        return 'Customer Report';
    }
}
