<?php

namespace App\Livewire\Inventory;

use Livewire\Component;

class MerchandiseInventory extends Component
{

     // Custom Columns Properties
     public $avlBal = false;
     public $avlQty = true;
     public $totalReserved = false;
     public $code = true;
     public $location = false;
     public $uom = true;
     public $brand = false;
     public $status = false;
     public $category = false;
     public $classification = false;
     public $barcode = false;


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
        // $this->inventory = Inventory::all(); // Fetch all inventory items from the database
    }
}
