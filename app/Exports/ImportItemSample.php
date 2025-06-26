<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ImportItemSample implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Add a sample row to test if the export is working
        return collect([
            [
                'Samsung Galaxy S21',
                'Latest smartphone with advanced features',
                'Pcs',
                '01223456789',
                'Electronics',
                'Gadgets',
                'Mobile Phones',
                'Samsung',
            ]
        ]);
    }
    public function headings(): array
    {
        return [
            'Item',
            'Description',
            'Unit',
            'Barcode',
            'Category',
            'Classification',
            'Sub Classification',
            'Brand'
        ];
    }
}
