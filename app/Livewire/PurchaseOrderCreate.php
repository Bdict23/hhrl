<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\RequisitionType;
use App\Models\Item;
use App\Models\Signatory;

class PurchaseOrderCreate extends Component
{
    public $suppliers = [];
    public $types = [];
    public $items = [];
    public $approver = [];
    public $reviewer= [];

    public function mount()
    {
        $this->fechdata();
    }
    public function fechdata()
    {
    $this->suppliers = Supplier::where([['supplier_status', 'ACTIVE'],['company_id', auth()->user()->emp_id]])->get();
    $this->types =  RequisitionType::all();
    $this->items = Item::with('priceLevel')->where('item_status', 'ACTIVE' )->get();
    $this->approver = Signatory::where([['signatory_type', 'APPROVER'],['module','PURCHASING' ],['branch_id', auth()->user()->branch_id]])->get();
    $this->reviewer = Signatory::where([['signatory_type', 'REVIEWER'],['module','PURCHASING' ],['branch_id', auth()->user()->branch_id]])->get();
    }
    public function render()
    {
        return view('livewire.purchase-order-create');
    }
}
