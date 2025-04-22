<?php

namespace App\Livewire\Inventory;
use Illuminate\Http\Request;
use Livewire\Component;
use App\Models\Department;
use App\Models\Withdrawal as WithdrawalModel;
use App\Models\WithdrawalItem;
use App\Models\Signatory;
use App\Models\Category;
use App\Models\Cardex;
use App\Models\Item;
use App\Models\Module;

class WithdrawalShow extends Component
{
    

    // Custom Columns Properties
    public $avlBal = false;
    public $avlQty = true;
    public $code = true;
    public $location = false;
    public $uom = true;
    public $brand = false;
    public $status = false;
    public $category = false;
    public $classification = false;
    public $barcode = false;


    public $reference;
    public $departments = []; // display dept on ui
    public $selectedDepartment = null; // selected dept from user
    public $myCardexItems = []; // display items on ui particularly on modal
    public $selectedItems = []; // selected items from user and display on ui
    public $reviewers = []; // display reviewers on ui
    public $reviewer = null; // selected reviewer from user
    public $approvers = []; // display approvers on ui
    public $approver = null; // selected approver from user
    public $categories = []; // display categories on ui
    public $finalStatus = false; // selected final status from user
    public $isAlreadyFinal = false; // selected final status from user
    public $haveSpan = false; // selected span status from user
    public $spanDate = null; // selected span date from user
    public $useDate = null; // selected use date from user
    public $remarks = null; // remarks from user
    public $overallTotal = 0; // overall total of selected items
    protected $rules = [
        'reference' => 'required|string|max:25|unique:withdrawals,reference_number',
        'selectedDepartment' => 'required',
        'useDate' => 'required',
        'spanDate' => 'nullable|date|after_or_equal:useDate',
        'remarks' => 'nullable|string|max:150',
        'selectedItems' => 'required|array|min:1',
        'reviewer' => 'required',
        'approver' => 'required',
    ];
    protected $messages = [
        'reference.required' => 'The reference number is required.',
        'selectedDepartment.required' => 'The department is required.',
        'selectedItems.required' => 'The item list cannot be empty.',
        'reviewer.required' => 'The reviewer is required.',
        'approver.required' => 'The approver is required.',
       
    ];
    protected $listeners = [
        'addItem' => 'addItem',
        'removeItem' => 'removeItem',
        'updatedHaveSpan' => 'updatedHaveSpan',
    ];


    public function mount(Request $request = null)
    {
        if ($request->has('withdrawal-id')) {
            //  dd($request->query('requisition-id'));
            $this->fetchData();
             $this->showWithdrawal($request->query('withdrawal-id'));
        }

        $this->fetchData();
    }

    public function showWithdrawal($id)
    {
        $withdrawal = WithdrawalModel::with('department', 'approvedBy', 'reviewedBy', 'cardex.item')->findOrFail($id);
        $this->reference = $withdrawal->reference_number;
        $this->selectedDepartment = $withdrawal->department_id;
        $this->useDate = $withdrawal->usage_date;
        $this->spanDate = $withdrawal->useful_date;
        $this->remarks = $withdrawal->remarks;
        $this->reviewer = $withdrawal->reviewed_by;
        $this->approver = $withdrawal->approved_by;
        $this->isAlreadyFinal = $withdrawal->withdrawal_status != 'PREPARING' ? true : false;
        $this->finalStatus = $this->isAlreadyFinal;
        $this->haveSpan = $withdrawal->useful_date != null ? true : false;
        $this->selectedItems = [];

        foreach ($withdrawal->cardex as $item) {
            if ($item['qty_out'] > 0) {
                $this->selectedItems[] = [
                    'id' => $item['item_id'],
                    'requested_qty' => (float) $item['qty_out'],
                    'total' => (float) $item['qty_out'] * (float) $item['price_level_id'],
                ];
            }
        }
        dd($this->selectedItems);
    }

    public function fetchData(){

        $this->departments = Department::where('branch_id', auth()->user()->branch_id)->get();
        $myItems = Item::where([['company_id', auth()->user()->branch->company_id],['item_status','ACTIVE']])->get();
        $module = Module::where('module_name', 'Item Withdrawal')->first();

        // Calculate total balance for each item using the Cardex model
        $this->myCardexItems = $myItems->map(function ($item) {
            $totalIn = Cardex::where('status', 'final')->where('item_id', $item->id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_in');
            $totalOut = Cardex::where('status', 'final')->where('item_id', $item->id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_out');
            $totalReserved = Cardex::where('status', 'reserved')->where('item_id', $item->id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_out');
            $item->total_balance = $totalIn - $totalOut;
            $item->total_reserved = $totalReserved;
            $item->total_available = $item->total_balance - $totalReserved;

            return $item;
        });
        $this->reviewers = Signatory::with('employees')->where([['module_id', $module->id ],['signatory_type', 'reviewer'],['branch_id',auth()->user()->branch_id]])->get();
        $this->approvers = Signatory::with('employees')->where([['module_id', $module->id ],['signatory_type', 'approver'],['branch_id',auth()->user()->branch_id]])->get();
        $this->categories = Category::where([['company_id', auth()->user()->branch->company_id],['status','ACTIVE'],['category_type','ITEM']])->get();
    }

    public function addItem($itemId, $balance, $available)
    {
        $selected = Item::with('uom','category','location','brand','classification','costPrice')->where('id',$itemId)->first();
        if (!$selected) {
            session()->flash('error', 'Item not found.');
            return;
        }
        if ($selected->costPrice == null) {
            session()->flash('error', 'Item has no cost price.');
            return;
        }
       
        // Check if the item is already in the selected items
        foreach ($this->selectedItems as $selectedItem) {
            if ($selectedItem['id'] === $selected->id) {
                session()->flash('error', 'Item already selected.');
                return;
            }
            }

            $this->selectedItems[] = [
                'id' => $selected->id,
                'total_balance' => $balance,
                'total_available' => $available,
                'code' => $selected->item_code ?? 'N/A',
                'name' => $selected->item_description,
                'unit' => $selected->uom->unit_symbol,
                'category' => $selected->category->category_name,
                'classification' => $selected->classification->classification_name ?? 'N/A',
                'barcode' => $selected->item_barcode ?? 'N/A',
                'requested_qty' => 0,
                'location' => $selected->location->location_name ?? 'N/A',
                'uom' => $selected->uom->unit_name ?? 'N/A',
                'brand' => $selected->brand->brand_name ?? 'N/A',
                'status' => $selected->item_status,
                'cost' => $selected->costPrice->amount,
                'costId' => $selected->costPrice->id,
                'total' => 0,
            ];
        
    }
    public function removeItem($index)
    {
        unset($this->selectedItems[$index]);
        $this->selectedItems = array_values($this->selectedItems);
    }
    public function store()
    {
        $this->validate();

        $withdrawal = new WithdrawalModel();
        $withdrawal->reference_number = $this->reference;
        $withdrawal->department_id = $this->selectedDepartment;
        $withdrawal->prepared_by = auth()->user()->emp_id;
        $withdrawal->reviewed_by = $this->reviewer;
        $withdrawal->approved_by = $this->approver;
        $withdrawal->remarks = $this->remarks;
        $withdrawal->withdrawal_status = $this->finalStatus ? 'FOR REVIEW' : 'PREPARING';
        $withdrawal->source_branch_id = auth()->user()->branch_id;
        $withdrawal->usage_date = $this->useDate;
        $withdrawal->useful_date = $this->haveSpan ? $this->spanDate : null;
        $withdrawal->save();
        $withdrawalId = $withdrawal->id; // Ensure the ID is retrieved after saving

        foreach ($this->selectedItems as $item) {
            if ($item['requested_qty'] > 0) {
                $cardex = new Cardex();
                $cardex->source_branch_id = auth()->user()->branch_id;
                $cardex->qty_out = $item['requested_qty'];
                $cardex->item_id = $item['id'];
                $cardex->status = 'RESERVED';
                $cardex->transaction_type = 'WITHDRAWAL';
                $cardex->price_level_id = $item['costId'];
                $cardex->withdrawal_id = $withdrawalId; // Use the retrieved ID
                $cardex->save();

            }
        }

        session()->flash('success', 'Withdrawal request created successfully.');
        $this->reset();
        $this->fetchData();
    }

    public function updatedSelectedItems($value, $key)
    {
       // Mag Check if ang requested quantity mag exceed sa available balance
        if (str_contains($key, 'requested_qty')) {
            foreach ($this->selectedItems as $index => $item) {
                if (isset($item['requested_qty']) && $item['requested_qty'] > $item['total_available']) {
                    $this->selectedItems[$index]['requested_qty'] = $item['total_available'];
                    session()->flash('error', 'Requested quantity cannot exceed available balance.');
                }
            }
        }

        //mag update sa overall total para naay ma display
        if (str_contains($key, 'requested_qty')) {
            foreach ($this->selectedItems as $index => $item) {
                if (isset($item['requested_qty'])) {
                    $this->selectedItems[$index]['total'] = (float) $item['requested_qty'] * (float) $item['cost'];
                    $this->overallTotal = array_sum(array_column($this->selectedItems, 'total'));
                }
            }
        }
    }
    public function updatedHaveSpan($value)
    {

        $this->spanDate = null;
        $this->useDate = null;
        if ($value) {
            $this->useDate = now();
        }
    }



   
    public function render()
    {
        return view('livewire.inventory.withdrawal-show');
    }
}
