<?php

namespace App\Exports;

use App\Models\Item;
use App\Models\Cardex;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MerchandiseInventoryExport implements FromCollection, WithHeadings
{
    protected $options;

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function collection()
    {
        $items = Item::with(['location', 'uom', 'brand', 'category', 'classification','sellingPrice'])
            ->where([['company_id', auth()->user()->branch->company_id],['item_status','ACTIVE']])
            ->get()
            ->map(function ($item) {
                $totalIn = Cardex::where('status', 'final')->where('item_id', $item->id)->where('source_branch_id', auth()->user()->branch_id)->sum('qty_in');
                $totalOut = Cardex::where('status', 'final')->where('item_id', $item->id)->where('source_branch_id', auth()->user()->branch_id)->sum('qty_out');
                $totalReserved = Cardex::where('status', 'reserved')->where('item_id', $item->id)->where('source_branch_id', auth()->user()->branch_id)->sum('qty_out');

                $item->total_balance   = $totalIn - $totalOut;
                $item->total_reserved  = $totalReserved;
                $item->total_available = $item->total_balance - $totalReserved;

                return $item;
            });

        return $items->map(function ($item) {
            $data = [];

            if ($this->options['code'])  $data[] = $item->item_code;
            if ($this->options['barcode']) $data[] = $item->item_barcode;
            if ($this->options['category']) $data[] = $item->category->category_name ?? '';
            if ($this->options['classification']) $data[] = $item->classification->classification_name ?? '';
            if ($this->options['uom']) $data[] = $item->units->unit_symbol ?? '';
            if ($this->options['brand']) $data[] = $item->brand->brand_name ?? '';
            if ($this->options['status']) $data[] = $item->item_status;
            if ($this->options['avlBal']) $data[] = $item->total_balance ?? 0;
            if ($this->options['avlQty']) $data[] = $item->total_available ?? 0;
            if ($this->options['totalReserved']) $data[] = $item->total_reserved ?? 0;
            if ($this->options['location']) $data[] = $item->location->location_name ?? '';
            if ($this->options['SRP']) $data[] = $item->sellingPrice->amount ?? 0;

            return $data;
        });
    }

    public function headings(): array
    {
        $headings = [];

        if ($this->options['code'])  $headings[] = 'Item Code';
        if ($this->options['barcode']) $headings[] = 'Barcode';
        if ($this->options['category']) $headings[] = 'Category';
        if ($this->options['classification']) $headings[] = 'Classification';
        if ($this->options['uom']) $headings[] = 'Unit';
        if ($this->options['brand']) $headings[] = 'Brand';
        if ($this->options['status']) $headings[] = 'Status';
        if ($this->options['avlBal']) $headings[] = 'Avl. Bal.';
        if ($this->options['avlQty']) $headings[] = 'Avl. QTY';
        if ($this->options['totalReserved']) $headings[] = 'Total Reserved';
        if ($this->options['location']) $headings[] = 'Location';
        if ($this->options['SRP']) $headings[] = 'Selling Price';

        return $headings;
    }
}
