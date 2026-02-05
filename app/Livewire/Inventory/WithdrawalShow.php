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
use App\Models\OtherSetting;


class WithdrawalShow extends Component
{
    

    // Custom Columns Properties
    public $avlBal = false;
    public $avlQty = true;
    public $code = true;
    // public $location = false;
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
    public $overallTotal = 0.00; // overall total of selected items
    public $hasReviewer = false; // check if reviewer is required
    public $eventName = null; // event name for banquet procurement
    public $withdrawalID = null; // withdrawal id for update
    public $eventId = null; // event id for banquet procurement
    public $events = []; // display events on ui
    public $withdrawalTypes = []; // display withdrawal types on ui
    public $selectedWithdrawalType = null; // selected withdrawal type from user


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
        $this->hasReviewer = auth()->user()->branch->getBranchSettingConfig('Allow Reviewer on Withdrawal') == 1 ? true : false;

        if ($request->has('withdrawal-id')) {
            //  dd($request->query('requisition-id'));
            $this->fetchData();
             $this->showWithdrawal($request->query('withdrawal-id'));
        }

        $this->fetchData();
    }

    public function showWithdrawal($id)
    {
  
        $this->withdrawalID = $id;
        $withdrawal = WithdrawalModel::with('department', 'approvedBy', 'reviewedBy', 'cardex.item','withdrawalType')->findOrFail($id);
        $this->hasReviewer = $withdrawal->reviewed_by != null ? true : false;
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
        $this->selectedWithdrawalType = $withdrawal->withdrawalType->id ?? null;
        $this->selectedItems = [];
        

        if ($withdrawal->event_id != null) {
            $this->eventId = $withdrawal->event_id;
            $event = $withdrawal->event;
            $this->eventName = $event->event_name . '  ( ' . \Carbon\Carbon::parse($event->event_date)->format('M-d-Y') . ' )';
        } else {
            $this->eventId = null;
            $this->eventName = null;
        }

        foreach ($withdrawal->cardex as $item) {
            $totalIn = Cardex::where('status', 'final')->where('item_id', $item->item_id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_in');
            $totalOut = Cardex::where('status', 'final')->where('item_id', $item->item_id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_out');
            $totalReserved = Cardex::where('status', 'reserved')->where('item_id', $item->item_id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_out');
            $totalBal = $totalIn - $totalOut;
            $totalAvailable = $totalBal - $totalReserved;
            
            if ($item['qty_out'] > 0) {
                $this->selectedItems[] = [
                    'id' => $item['item_id'],
                    'requested_qty' => (float) $item['qty_out'],
                    'total_balance' =>  $totalBal,
                    'total_available' => $totalAvailable,
                    'code' => $item->item->item_code ?? 'N/A',
                    'name' => $item->item->item_description,
                    'unit' => $item->item->uom->unit_symbol,
                    'category' => $item->item->category->category_name,
                    'classification' => $item->item->classification->classification_name ?? 'N/A',
                    'barcode' => $item->item->item_barcode ?? 'N/A',
                    // 'location' => $item->item->location->location_name ?? 'N/A',
                    'uom' => $item->item->uom->unit_name ?? 'N/A',
                    'brand' => $item->item->brand->brand_name ?? 'N/A',
                    'status' => $item->item->item_status,
                    'cost' => $item->item->costPrice->amount,
                    'costId' => $item->item->costPrice->id,
                    'total' => $item['qty_out'] * ($item->item->costPrice->amount ?? 0),
                ];
            }
            $this->overallTotal += (float) $item['qty_out'] * (float) $item->item->costPrice->amount;

        }
    }

    public function fetchData(){
        $this->withdrawalTypes = OtherSetting::where('setting_key', 'WITHDRAW_TYPE')->where('branch_id', auth()->user()->branch_id)->where('is_active', 1)->get() ?? [];
        $this->departments = Department::where('branch_id', auth()->user()->branch_id)->get();
        $this->events = auth()->user()->branch->banquetEvents()->where('status', 'pending')->get();
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
                // 'location' => $selected->location->location_name ?? 'N/A',
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
    public function updateWithdrawal()
    {
        if ($this->hasReviewer) {
            $this->validate(
                [
                    'reference' => 'required|string|max:25|unique:withdrawals,reference_number,' . $this->withdrawalID,
                    'selectedDepartment' => 'required',
                    'useDate' => 'required',
                    'spanDate' => 'nullable|date|after_or_equal:useDate',
                    'remarks' => 'nullable|string|max:150',
                    'selectedItems' => 'required|array|min:1',
                    'approver' => 'required',
                    'reviewer' => 'required',
                ]);
        } else {
            $this->validate(
                [
                    'reference' => 'required|string|max:25|unique:withdrawals,reference_number,' . $this->withdrawalID,
                    'selectedDepartment' => 'required',
                    'useDate' => 'required',
                    'spanDate' => 'nullable|date|after_or_equal:useDate',
                    'remarks' => 'nullable|string|max:150',
                    'selectedItems' => 'required|array|min:1',
                    'approver' => 'required',
                ]
            );
        }
       
        
        $withdrawal = WithdrawalModel::find($this->withdrawalID);
        $withdrawal->reference_number = $this->reference;
        $withdrawal->department_id = $this->selectedDepartment;
        $withdrawal->prepared_by = auth()->user()->emp_id;
        $withdrawal->reviewed_by  = $this->hasReviewer ? $this->reviewer : null;
        $withdrawal->approved_by = $this->approver;
        $withdrawal->remarks = $this->remarks;
        $withdrawal->withdrawal_status = $this->finalStatus ? $this->hasReviewer ? 'FOR REVIEW' : 'FOR APPROVAL' : 'PREPARING';
        $withdrawal->source_branch_id = auth()->user()->branch_id;
        $withdrawal->usage_date = $this->useDate;
        $withdrawal->useful_date = $this->haveSpan ? $this->spanDate : null;
        $withdrawal->event_id = $this->eventId ?? null; // Set the event ID if available
        $withdrawal->save();
        $withdrawalId = $this->withdrawalID; // Ensure the ID is retrieved after saving

        // Delete existing cardex records for the withdrawal
        Cardex::where('withdrawal_id', $withdrawalId)->delete();
        // Create new cardex records for the withdrawal
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

        session()->flash('success', 'Withdrawal request updated successfully.');
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

    public function selectedEvent($eventId)
    {
        $this->eventId = $eventId;
        $event = auth()->user()->branch->banquetEvents()->find($eventId);
        if ($event) {
            $this->eventName = $event->event_name . '  ( ' . \Carbon\Carbon::parse($event->event_date)->format('M-d-Y') . ' )';
        } else {
            $this->eventName = null;
        }
        $this->dispatch('closeEventModal'); // Close the modal after selection
    }

    public function printPreview()
    {
        // Redirect to the print preview route with the withdrawal ID as a query parameter
        return redirect()->to('/withdrawal-print-preview?withdrawal-id=' . $this->withdrawalID);
    }

   
    public function render()
    {
        return view('livewire.inventory.withdrawal-show');
    }
}
