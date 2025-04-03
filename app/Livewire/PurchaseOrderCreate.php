<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\Term;
use App\Models\Item;
use App\Models\Signatory;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;


class PurchaseOrderCreate extends Component
{
    public $suppliers = [];
    public $terms = [];
    public $items = [];
    public $approver = [];
    public $reviewer= [];
    public $selectedItems = [];
    public $purchaseRequest = [];
    public $requisitionNumber;
    public $supplierId;
    public $mPoNumber;
    public $term_id =null;
    public $remarks;
    public $reviewer_id;
    public $approver_id;

    protected $rules = [
        'supplierId' => 'required|exists:suppliers,id',
        'mPoNumber' => 'nullable|string|max:25',
        'term_id' => 'required|exists:terms,id',
        'remarks' => 'nullable|string|max:55',
        'reviewer_id' => 'required|exists:signatories,id',
        'approver_id' => 'required|exists:signatories,id',
        'selectedItems' => 'required|array',
        'selectedItems.*' => 'exists:items,id',
        'purchaseRequest.*.qty' => 'required|integer|min:1',
    ];
    protected $messages = [
        'selectedItems.required' => 'The item list cannot be empty.',
        'purchaseRequest.*.qty.required' => 'The quantity is required.',
        'supplierId' => 'The supplier is required.',
        'term_id' => 'The payment term is required.',
        'reviewer_id' => 'The reviewer is required.',
        'approver_id' => 'The approver is required.',
    ];

    public function mount()
    {
        $this->fetchdata();
    }

    public function store()
    {
        $this->validate();
        
                // Save to requisitionInfos table
                $requisitionInfo = new RequisitionInfo();
                $latestId = RequisitionInfo::max('id') + 1;
                $this->requisitionNumber = 'PO:' . now()->format('my') . '-' . str_pad($latestId, 3, '0', STR_PAD_LEFT);
                $requisitionInfo->supplier_id = $this->supplierId;
                $requisitionInfo->prepared_by = auth()->user()->emp_id;
                $requisitionInfo->approved_by = $this->approver_id;
                $requisitionInfo->reviewed_by = $this->reviewer_id;
                $requisitionInfo->requisition_status = 'PREPARING';
                $requisitionInfo->trans_date = now();
                $requisitionInfo->term_id = $this->term_id;
                $requisitionInfo->remarks = $this->remarks;
                $requisitionInfo->category = 'PO';
                $requisitionInfo->merchandise_po_number = $this->mPoNumber;
                $requisitionInfo->requisition_number = $this->requisitionNumber;
                $requisitionInfo->from_branch_id = auth()->user()->branch_id;
                $requisitionInfo->save();

        // Process the selected items and their quantities
        foreach ($this->purchaseRequest as $index => $item) {
           
            $requisitionDetail = new RequisitionDetail();
            $requisitionDetail->requisition_info_id = $requisitionInfo->id;
            $requisitionDetail->item_id = $this->purchaseRequest[$index]['id'];
            $requisitionDetail->qty = $this->purchaseRequest[$index]['qty'];
            $requisitionDetail->save();
            // Here you can save the item and quantity to the database or perform any other action
            // For example:
            // PurchaseOrderItem::create([
            //     'item_id' => $itemId,
            //     'quantity' => $quantity,
            // ]);
        }
        $this->reset();
        $this->fetchdata();
        session()->flash('success', 'Purchase Order created successfully.');
        $this->dispatch('refresh');
    }

    public function addToTable()
    {
        $this->validate([
            'selectedItems' => 'required|array',
        ]);

        // Fetch full item details for each selected item
        $this->selectedItems = array_map(function ($itemId) {
            return Item::with('costPrice')->find($itemId);
        }, $this->selectedItems);
    }

    public function addItem($itemId)
    {
        $item = Item::with('costPrice')->find($itemId);

        if (!$item) {
            return;
        }

        // Check if the item is already in the selected items
        foreach ($this->selectedItems as $selectedItem) {
            if ($selectedItem->id === $item->id) {
                return; // Item already exists
            }
        }

        // Add the item to the selected items
        $this->selectedItems[] = $item;

        // Initialize the requested quantity for the item
        $this->purchaseRequest[] = ['id' => $item->id, 'qty' => 1];
    }

   
    public function fetchdata()
    {
    $this->suppliers = Supplier::where([['supplier_status', 'ACTIVE'],['company_id', auth()->user()->emp_id]])->get();
    $this->terms =  Term::all();
    $this->items = Item::with('costPrice')->where('item_status', 'ACTIVE' )->get();
    $this->approver = Signatory::where([['signatory_type', 'APPROVER'],['module','PURCHASING' ],['branch_id', auth()->user()->branch_id]])->get();
    $this->reviewer = Signatory::where([['signatory_type', 'REVIEWER'],['module','PURCHASING' ],['branch_id', auth()->user()->branch_id]])->get();
    }
    public function render()
    {
        return view('livewire.purchase-order-create');
    }
}
