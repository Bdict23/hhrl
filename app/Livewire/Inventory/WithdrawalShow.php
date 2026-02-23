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
use App\Models\ProductionOrder;
use App\Models\ProductionOrderDetail;


class WithdrawalShow extends Component
{
    

    // Custom Columns Properties
    public $avlBal = false;
    public $avlQty = true;
    public $code = false;
    public $cost = false;
    public $total = false;
    public $requestQty = false;
    public $action = true;

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
    public $productionRef = null;


    protected $rules = [
        'reference' => 'required|string|max:25|unique:withdrawals,reference_number',
        'selectedDepartment' => 'required',
        'useDate' => 'required',
        'spanDate' => 'nullable|date|after_or_equal:useDate',
        'remarks' => 'nullable|string|max:150',
        'selectedItems' => 'required|array|min:1',
        'reviewer' => 'required',
        'approver' => 'required',
        'finalStatus' => 'required|boolean',
        
    ];
    protected $messages = [
        'reference.required' => 'The reference number is required.',
        'selectedDepartment.required' => 'The department is required.',
        'selectedItems.required' => 'The item list cannot be empty.',
        'reviewer.required' => 'The reviewer is required.',
        'approver.required' => 'The approver is required.',
        'finalStatus.boolean' => 'Select A saving options',
        'finalStatus.required' => 'Select A saving options',
       
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
        $this->selectedItems = [];
        

        if ($withdrawal->event_id != null) {
            $this->eventId = $withdrawal->event_id;
            $event = $withdrawal->event;
            $this->eventName = $event->event_name . '  ( ' . \Carbon\Carbon::parse($event->event_date)->format('M-d-Y') . ' )';
        } else {
            $this->eventId = null;
            $this->eventName = null;
        }
        
        // check if the withdrawal is for Production Order
        if($withdrawal->production_order_id != null){
            $this->requestQty = true;
            $this->action = false;
            $this->uom = false;
            $this->productionRef = $withdrawal->productionOrder->reference;

        }
        
        // mag pluck sa production details para makuha ang item id
        $Productdetail = ProductionOrderDetail::where('production_order_id', $withdrawal->production_order_id)->with('uom')->get();
        
        foreach ($withdrawal->cardex as $item) {
            $totalIn = Cardex::where('status', 'final')->where('item_id', $item->item_id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_in');
            $totalOut = Cardex::where('status', 'final')->where('item_id', $item->item_id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_out');
            $totalReserved = Cardex::where('status', 'reserved')->where('item_id', $item->item_id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_out');
            $balance = $totalIn - $totalOut;
            $totalAvailable = $balance - $totalReserved;
            $reqQtyInBaseUnit = (float) $item['qty_out'];
            $item2 = Item::with('uom','category','location','brand','classification')->where('id',$item->item_id)->first(); 
            $itemUnitLists = $this->buildUnitOptions($item2);
            $baseUomId = $item2->uom_id;

            // get unit Jsymbol from used in production order detail 
            $prdOrderDtl= $Productdetail->where('item_id',$item->item_id)->first();
            if(!$prdOrderDtl){
                $reqQty = $item['qty_out'];
            }else{
                $reqQty = (float) $prdOrderDtl->qty;
            }
            
            
            if ($item['qty_out'] > 0) {
                $this->selectedItems[] = [
                    'id' => $item['item_id'],
                    'code' => $item['item_id'],
                    'name' => $item2->item_description,
                    'unit' => $itemUnitLists,
                    'uom' => $baseUomId,
                    'base_uom_id' => $baseUomId,
                    'unit_symbol' => $item2->uom->unit_symbol ?? 'N/A',

                    // Base quantities (always in item's base UOM)
                    'base_total_balance' => $balance,
                    'base_total_available' => $totalAvailable,
                    'requested_qty_base' => $reqQtyInBaseUnit,

                    // Display quantities (convert based on selected UOM)
                    'total_balance' =>  $balance,
                    'total_available' => $totalAvailable,
                    'requested_qty' => (float) $item['qty_out'],

                    // Original request (stays as reference, never changes)
                    'request_qty' => $this->formatQtyWithUnit($reqQty, $prdOrderDtl->uom->unit_symbol ?? $item2->uom->unit_symbol) ,

                    // Original request (stays as reference, never changes)
                    'code' => $item2->item_code ?? 'N/A',
                    'category' => $item2->category->category_name,
                    'classification' => $item2->classification->classification_name ?? 'N/A',
                    'barcode' => $item2->item_barcode ?? 'N/A',

                    // 'location' => $item2->location->location_name ?? 'N/A',
                    'brand' => $item2->brand->brand_name ?? 'N/A',
                    'status' => $item2->item_status,
                    'cost' => $item2->costPrice->amount,
                    'costId' => $item2->costPrice->id,
                    'total' => $item['qty_out'] * ($item2->costPrice->amount ?? 0),
                ];
            }
            $this->overallTotal += (float) $item['qty_out'] * (float) $item2->costPrice->amount;

        }
    }

     private function formatQtyWithUnit(float $qty, ?string $unitSymbol): string
    {
        $label = rtrim(rtrim(number_format($qty, 2, '.', ''), '0'), '.');
        if ($unitSymbol) {
            return $label . ' (' . $unitSymbol . ')';
        }
        return $label;
    }

    private function buildUnitOptions(Item $item): array
    {
        $unitOptions = [];
        if ($item->uom) {
            $unitOptions[] = [
                'from_uom_id' => $item->uom->id,
                'to_uom_id' => $item->uom->id,
                'unit_symbol' => $item->uom->unit_symbol,
                'unit_name' => $item->uom->unit_name,
                'item_id' => $item->id,
                'conversion_factor' => 1,
            ];
        }

        foreach ($item->units->fromUnits as $fromUnit) {
            $unitOptions[] = [
                'from_uom_id' => $fromUnit->from_uom_id,
                'to_uom_id' => $fromUnit->to_uom_id,
                'unit_symbol' => $item->units->where('id', $fromUnit->to_uom_id)->pluck('unit_symbol')->implode(', '),
                'unit_name' => $item->units->where('id', $fromUnit->to_uom_id)->pluck('unit_name')->implode(', '),
                'item_id' => $item->id,
                'conversion_factor' => $fromUnit->conversion_factor,
            ];
        }

        return $unitOptions;
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
                'code' => $selected->item_code ?? 'N/A',
                'name' => $selected->item_description,
                'unit' => $selected->uom->unit_symbol,
                'uom' => $selected->uom->unit_name ?? 'N/A',
                'base_uom_id' => $selected->uom_id,
                'unit_symbol' => $selected->uom->unit_symbol ?? 'N/A',
    
                // Base quantities (always in item's base UOM)
                'base_total_balance' => $balance,
                'base_total_available' => $available,
                'requested_qty_base' => 0,

                // Display quantities (convert based on selected UOM)
                'total_balance' => $balance,
                'total_available' => $available,
                'requested_qty' => 0,


                'category' => $selected->category->category_name,
                'classification' => $selected->classification->classification_name ?? 'N/A',
                'barcode' => $selected->item_barcode ?? 'N/A',
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
        if($this->finalStatus == "FINAL"){
            $this->finalStatus = true;
        }else{
            $this->finalStatus = false;
        }
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
        // Handle unit dropdown change
        if (str_contains($key, '.uom')) {
            [$index] = explode('.', $key);
            $this->updateItemUnit((int) $index);
            return;
        }
        // Handle withdrawal qty change
        if (str_contains($key, '.requested_qty')) {
            [$index] = explode('.', $key);
            $this->updateRequestedQty((int) $index);
        }
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
    private function updateRequestedQty(int $index): void
    {
        if (!isset($this->selectedItems[$index])) {
            return;
        }
        
        $item = $this->selectedItems[$index];
        
        // Check if requested qty exceeds available
        if (isset($item['requested_qty'], $item['total_available']) && $item['requested_qty'] > $item['total_available']) {
            $this->selectedItems[$index]['requested_qty'] = $item['total_available'];
            session()->flash('error', 'Requested quantity cannot exceed available balance.');
        }
        
        // Convert requested qty to base unit for storage and costing
        $selectedUomId = $item['uom'] ?? null;
        $conversionFactor = $this->getUnitFactor($item, $selectedUomId);
        
        if ($conversionFactor <= 0) {
            $conversionFactor = 1;
        }
        
        $requestedQty = (float) ($item['requested_qty'] ?? 0);
        $this->selectedItems[$index]['requested_qty_base'] = round($requestedQty / $conversionFactor, 4);
        
        // Update total based on base unit qty
        if (isset($item['cost'])) {
            $this->selectedItems[$index]['total'] = round($this->selectedItems[$index]['requested_qty_base'] * $item['cost'], 2);
        }
        
        $this->overallTotal = array_sum(array_column($this->selectedItems, 'total'));
    }
     private function updateItemUnit(int $index): void
    {
        if (!isset($this->selectedItems[$index])) {
            return;
        }

        $item = $this->selectedItems[$index];
        $selectedUomId = $item['uom'] ?? null;
        $conversionFactor = $this->getUnitFactor($item, $selectedUomId);
        
        // Ensure valid conversion factor
        if ($conversionFactor <= 0) {
            $conversionFactor = 1;
        }

        // Convert balance from base unit to selected unit
        if (isset($item['base_total_balance'])) {
            $this->selectedItems[$index]['total_balance'] = round($item['base_total_balance'] * $conversionFactor, 2);
        }
        
        // Convert available from base unit to selected unit
        if (isset($item['base_total_available'])) {
            $this->selectedItems[$index]['total_available'] = round($item['base_total_available'] * $conversionFactor, 2);
        }
        
        // Convert withdrawal qty from base unit to selected unit
        if (isset($item['requested_qty_base'])) {
            $this->selectedItems[$index]['requested_qty'] = round($item['requested_qty_base'] * $conversionFactor, 2);
        }

        // Total always based on base unit qty (no conversion needed)
        if (isset($item['requested_qty_base'], $item['cost'])) {
            $this->selectedItems[$index]['total'] = round($item['requested_qty_base'] * $item['cost'], 2);
        }
        
        $this->overallTotal = array_sum(array_column($this->selectedItems, 'total'));
    }

    private function getUnitFactor(array $item, $uomId): float
    {
        if (!isset($item['unit']) || !is_array($item['unit'])) {
            return 1;
        }

        foreach ($item['unit'] as $unit) {
            if (($unit['to_uom_id'] ?? null) == $uomId) {
                return (float) ($unit['conversion_factor'] ?? 1);
            }
        }

        return 1;
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
