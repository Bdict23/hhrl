<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Cardex;
use App\Models\Item;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MerchandiseInventoryExport;


class MerchandiseInventory extends Component
{

     // Custom Columns Properties
     public $avlBal = true;
     public $avlQty = true;
     public $totalReserved = true;
     public $code = true;
     public $location = false;
     public $uom = true;
     public $brand = false;
     public $status = false;
     public $category = false;
     public $classification = false;
     public $barcode = true;
     public $sellingPrice = false;

     // cardex data
     public $cardex;


    public function render()
    {
        return view('livewire.inventory.merchandise-inventory');
    }

    public function mount()
    {
        $this->getInventory();
       
    }

    public function getInventory()
    {
        $myItems = Item::with('sellingPrice')->where([['company_id', auth()->user()->branch->company_id],['item_status','ACTIVE']])->get();
        $this->cardex = $myItems->map(function ($item) {
            $totalIn = Cardex::where('status', 'final')->where('item_id', $item->id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_in');
            $totalOut = Cardex::where('status', 'final')->where('item_id', $item->id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_out');
            $totalReserved = Cardex::where('status', 'reserved')->where('item_id', $item->id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_out');
            $item->total_balance = $totalIn - $totalOut;
            $item->total_reserved = $totalReserved;
            $item->total_available = $item->total_balance - $totalReserved;

            return $item;
        });
        // dd($this->cardex);
    }
    public function updated($propertyName)
    {
        if ($propertyName === 'avlBal' || $propertyName === 'avlQty' || $propertyName === 'totalReserved' || $propertyName === 'code' || $propertyName === 'location' || $propertyName === 'uom' || $propertyName === 'brand' || $propertyName === 'status' || $propertyName === 'category' || $propertyName === 'classification' || $propertyName === 'barcode' || $propertyName === 'sellingPrice') {
            // Re-fetch the inventory data when any of the properties change
            $this->getInventory();
        }
    }
    
    public function exportInventory()
        {
            $options = [
                'avlBal'         => $this->avlBal,
                'avlQty'         => $this->avlQty,
                'totalReserved'  => $this->totalReserved,
                'code'           => $this->code,
                'location'       => $this->location,
                'uom'            => $this->uom,
                'brand'          => $this->brand,
                'status'         => $this->status,
                'category'       => $this->category,
                'classification' => $this->classification,
                'barcode'        => $this->barcode,
                'SRP'            => $this->sellingPrice,
            ];

            return Excel::download(new MerchandiseInventoryExport($options), 'merchandise_inventory.xlsx');
        }
}
