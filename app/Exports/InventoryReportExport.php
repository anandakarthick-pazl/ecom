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

class InventoryReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $products;

    public function __construct($products)
    {
        $this->products = collect($products);
    }

    public function collection()
    {
        return $this->products;
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Product Code',
            'Category',
            'Current Stock',
            'Low Stock Threshold',
            'Cost Price',
            'Selling Price',
            'Stock Value (Cost)',
            'Stock Value (Selling)',
            'Status',
            'Last Updated'
        ];
    }

    public function map($product): array
    {
        $stockValue = $product->stock * $product->cost_price;
        $sellingValue = $product->stock * $product->price;
        
        $status = 'In Stock';
        if ($product->stock <= 0) {
            $status = 'Out of Stock';
        } elseif ($product->stock <= $product->low_stock_threshold) {
            $status = 'Low Stock';
        }
        
        return [
            $product->name,
            $product->code ?? 'N/A',
            $product->category->name ?? 'N/A',
            $product->stock,
            $product->low_stock_threshold ?? 0,
            '₹' . number_format($product->cost_price, 2),
            '₹' . number_format($product->price, 2),
            '₹' . number_format($stockValue, 2),
            '₹' . number_format($sellingValue, 2),
            $status,
            $product->updated_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A1:K' . ($this->products->count() + 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Inventory Report';
    }
}
