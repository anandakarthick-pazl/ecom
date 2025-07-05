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
            (object)[
                'category' => 'Income',
                'subcategory' => 'POS Sales',
                'amount' => $this->data['income']['pos_sales']
            ],
            (object)[
                'category' => 'Income',
                'subcategory' => 'Online Sales',
                'amount' => $this->data['income']['online_sales']
            ],
            (object)[
                'category' => 'Income',
                'subcategory' => 'Total Income',
                'amount' => $this->data['income']['total']
            ],
            (object)[
                'category' => '',
                'subcategory' => '',
                'amount' => ''
            ],
            (object)[
                'category' => 'Expenses',
                'subcategory' => 'Purchases',
                'amount' => $this->data['expenses']['purchases']
            ],
            (object)[
                'category' => 'Expenses',
                'subcategory' => 'Stock Loss',
                'amount' => $this->data['expenses']['stock_loss']
            ],
            (object)[
                'category' => 'Expenses',
                'subcategory' => 'Total Expenses',
                'amount' => $this->data['expenses']['total']
            ],
            (object)[
                'category' => '',
                'subcategory' => '',
                'amount' => ''
            ],
            (object)[
                'category' => 'Net Profit',
                'subcategory' => '',
                'amount' => $this->data['net_profit']
            ],
            (object)[
                'category' => 'Profit Margin',
                'subcategory' => '',
                'amount' => number_format($this->data['profit_margin'], 2) . '%'
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Category',
            'Description',
            'Amount'
        ];
    }

    public function map($item): array
    {
        return [
            $item->category,
            $item->subcategory,
            is_numeric($item->amount) ? '₹' . number_format($item->amount, 2) : $item->amount
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A1:C' . ($this->collection()->count() + 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        // Bold total rows
        $sheet->getStyle('A4:C4')->getFont()->setBold(true);
        $sheet->getStyle('A8:C8')->getFont()->setBold(true);
        $sheet->getStyle('A10:C11')->getFont()->setBold(true);

        foreach (range('A', 'C') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Income & Loss Report';
    }
}
