<?php

namespace App\Livewire\Inventory;

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
use App\Models\ProductionOrderMenu;
use App\Models\BanquetEvent;


class Withdrawal extends Component
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
    public $finalStatus; // selected final status from user
    public $haveSpan = false; // selected span status from user
    public $spanDate = null; // selected span date from user
    public $useDate = null; // selected use date from user
    public $remarks = null; // remarks from user
    public $overallTotal = 0; // overall total of selected items
    public $hasReviewer = false; // check if reviewer is required
    public $events = []; // display events on ui
    public $eventId = null; // selected event from user
    public $eventName = null; // selected event name from use
    public $productionOrders;
    public $productionOrderDetails;
    public $productionOrderMenus;
    public $selectedProductionOrderId = null;
    public $productionOrderName = null;



    protected $rules = [
        'selectedDepartment' => 'required',
        'useDate' => 'required|date',
        'spanDate' => 'nullable|date|after_or_equal:useDate',
        'remarks' => 'nullable|string|max:150',
        'selectedItems' => 'required|array|min:0.01',
        'selectedItems.*.requested_qty' => 'required|numeric|min:0.01',
        'reviewer' => 'required',
        'approver' => 'required',
        'finalStatus' => 'required|in:DRAFT,FINAL',

    ];
    protected $messages = [
        'selectedDepartment.required' => 'The department is required.',
        'selectedItems.required' => 'The item list cannot be empty.',
        'selectedItems.*.requested_qty.required' => 'The requested quantity is required.',
        'selectedItems.*.requested_qty.numeric' => 'The requested quantity must be a number.',
        'selectedItems.*.requested_qty.min' => 'Some withdrawal quantities are invalid.',
        'reviewer.required' => 'The reviewer is required.',
        'approver.required' => 'The approver is required.',
        'useDate.required' => 'The effective date is required.',
        'spanDate.after_or_equal' => 'The restock interval date must be after or equal to the effective date.',
        'finalStatus.required' => 'Select a valid saving option.',
    ];
    protected $listeners = [
        'addItem' => 'addItem',
        'removeItem' => 'removeItem',
        'updatedHaveSpan' => 'updatedHaveSpan',
    ];


    public function mount()
    {
        if(auth()->user()->employee->getModulePermission('Item Withdrawal') == 2 ){
            return redirect()->to('dashboard');
        }
        $this->fetchData();
    }

    public function fetchData(){
        $this->productionOrders = ProductionOrder::where('branch_id', auth()->user()->branch_id)->where('status', 'PENDING')->get();
        $this->hasReviewer = auth()->user()->branch->getBranchSettingConfig('Allow Reviewer on Withdrawal') == 1 ? true : false;
        $this->departments = Department::where('branch_id', auth()->user()->branch_id)->get();
        $myItems = Item::where([['company_id', auth()->user()->branch->company_id],['item_status','ACTIVE']])->get();
        $module = Module::where('module_name', 'Item Withdrawal')->first();

        // Calculate total balance for each item using the Cardex model
        $this->events = auth()->user()->branch->banquetEvents()->where('status', 'CONFIRMED')->where('end_date', '>=', now())->get();
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
        $selected = Item::with('uom','category','brand','classification','costPrice')->where('id',$itemId)->first();
        
        if (!$selected) {
            session()->flash('error', 'Item not found.');
            return;
        }
        
        if ($selected->costPrice == null) {
            session()->flash('error', 'Item has no cost price.');
            return;
        }
       
        // Check if item already selected
        foreach ($this->selectedItems as $selectedItem) {
            if ($selectedItem['id'] === $selected->id) {
                session()->flash('error', 'Item already selected.');
                return;
            }
        }

        $unitOptions = $this->buildUnitOptions($selected);
        $baseUomId = $selected->uom->id ?? null;
        
        $this->selectedItems[] = [
            'id' => $selected->id,
            'code' => $selected->item_code ?? 'N/A',
            'name' => $selected->item_description,
            'unit' => $unitOptions,
            'uom' => $baseUomId,
            'base_uom_id' => $baseUomId,
            'unit_symbol' => $selected->uom->unit_symbol ?? null,
            
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
    public function store()
    {
      
        if($this->hasReviewer){
            $this->validate(
                [
                    'selectedDepartment' => 'required',
                    'useDate' => 'required',
                    'spanDate' => 'nullable|date|after_or_equal:useDate',
                    'remarks' => 'nullable|string|max:150',
                    'selectedItems' => 'required|array|min:1',
                    'selectedItems.*.requested_qty' => 'required|numeric|min:0.01',
                    'reviewer' => 'required',
                    'approver' => 'required',
                    'finalStatus' => 'required|in:DRAFT,FINAL',
                ]
            );
        }else{
            $this->validate([
                'selectedDepartment' => 'required',
                'useDate' => 'required',
                'spanDate' => 'nullable|date|after_or_equal:useDate',
                'remarks' => 'nullable|string|max:150',
                'selectedItems' => 'required|array|min:1',
                'selectedItems.*.requested_qty' => 'required|numeric|min:0.01',
                'approver' => 'required',
                'finalStatus' => 'required|in:DRAFT,FINAL',
            ]);

        }
          try {

        $withdrawal = new WithdrawalModel();
         $yearlyCount = WithdrawalModel::where('source_branch_id', auth()->user()->branch_id)
            ->whereYear('created_at', now()->year)
            ->count() + 1;
        $withdrawal->reference_number =  'IW-' .  auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);
        $withdrawal->event_id = $this->eventId ?? null; // Ensure event_id is nullable
        $withdrawal->department_id = $this->selectedDepartment;
        $withdrawal->prepared_by = auth()->user()->emp_id;
        $withdrawal->reviewed_by = $this->reviewer;
        $withdrawal->approved_by = $this->approver;
        $withdrawal->remarks = $this->remarks;
        $withdrawal->withdrawal_status = $this->finalStatus == 'FINAL' ?  ($this->hasReviewer ? 'FOR REVIEW' : 'FOR APPROVAL') : ('PREPARING');
        $withdrawal->source_branch_id = auth()->user()->branch_id;
        $withdrawal->usage_date = $this->useDate;
        $withdrawal->useful_date = $this->haveSpan ? $this->spanDate : null;
        $withdrawal->production_order_id = $this->selectedProductionOrderId ?? null;
        $withdrawal->save();
        $withdrawalId = $withdrawal->id; // Ensure the ID is retrieved after saving

        foreach ($this->selectedItems as $item) {
            $baseQty = $item['requested_qty_base'] ?? $item['requested_qty'] ?? 0;
            if ($baseQty > 0) {
                $cardex = new Cardex();
                $cardex->source_branch_id = auth()->user()->branch_id;
                $cardex->qty_out = $baseQty;
                $cardex->item_id = $item['id'];
                $cardex->status = 'RESERVED';
                $cardex->transaction_type = 'WITHDRAWAL';
                $cardex->price_level_id = $item['costId'];
                $cardex->withdrawal_id = $withdrawalId; // Use the retrieved ID
                $cardex->save();

            }
        }

        $this->dispatch('showAlert', ['message' => 'Created Successfully', 'type' => 'success']);
        $this->reset();
        $this->fetchData();
        } catch (\Exception $e) {
            $this->dispatch('showAlert', ['message' => 'An error occurred while creating the withdrawal request.', 'type' => 'error']);
        }
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
        $event = BanquetEvent::find($eventId);
        if ($event) {
            $this->eventName = $event->event_name . '  ( ' . \Carbon\Carbon::parse($event->end_date)->format('M-d-Y') . ' )';
            $this->useDate = $event->start_date; // Set useDate to the event start date
        } else {
            $this->eventName = null;
        }
        $this->dispatch('closeEventModal'); // Close the modal after selection
    }

    public function selectedProductionOrder($productionOrderId)
    {
        $this->requestQty = true;
        $this->action = false;
        $this->uom = false;
        $this->selectedProductionOrderId = $productionOrderId;
        $productionOrder = ProductionOrder::find($productionOrderId);
        if ($productionOrder) {
            $this->useDate = $productionOrder->created_at; // Set useDate to the production order date
            $this->productionOrderName = $productionOrder->reference; // Set production order name for display

            // Fetch related details and menus for the selected production order
                $this->productionOrderDetails = ProductionOrderDetail::where('production_order_id', $productionOrderId)->get();
            // insert to selected items array
            $this->selectedItems = [];
            $cardexBalance = Cardex::where('status', 'final')->where('source_branch_id',auth()->user()->branch_id)->whereIn('item_id', $this->productionOrderDetails->pluck('item_id'))->groupBy('item_id')->selectRaw('item_id, SUM(qty_in) as total_in, SUM(qty_out) as total_out')->get()->keyBy('item_id');
            $cardexReserved = Cardex::where('status', 'reserved')->where('source_branch_id',auth()->user()->branch_id)->whereIn('item_id', $this->productionOrderDetails->pluck('item_id'))->groupBy('item_id')->selectRaw('item_id, SUM(qty_out) as total_reserved')->get()->keyBy('item_id');
            
            foreach ($this->productionOrderDetails as $index => $detail) {
                $item = Item::with('uom','category','brand','classification','costPrice')->find($detail->item_id);
                
                if (!$item || !$item->costPrice) {
                    continue;
                }
                
                $balance = $cardexBalance->has($item->id) ? $cardexBalance[$item->id]->total_in - $cardexBalance[$item->id]->total_out : 0;
                $available = $balance - ($cardexReserved->has($item->id) ? $cardexReserved[$item->id]->total_reserved : 0);
                $reqQtyInBaseUnit = number_format($detail->qty / $detail->conversionFactor() , 2);
                $itemUnitLists = $this->buildUnitOptions($item);
                $baseUomId = $item->uom->id ?? null;

                $canServe = $available >= $reqQtyInBaseUnit;
                if (!$canServe) {
                    $reqQtyInBaseUnit = 0;
                }
                
                $this->selectedItems[] = [
                    'id' => $item->id,
                    'code' => $item->item_code ?? 'N/A',
                    'name' => $item->item_description,
                    'unit' => $itemUnitLists,
                    'uom' => $baseUomId,
                    'base_uom_id' => $baseUomId,
                    'unit_symbol' => $item->uom->unit_symbol ?? null,
                    
                    // Base quantities (always in item's base UOM)
                    'base_total_balance' => $balance,
                    'base_total_available' => $available,
                    'requested_qty_base' => $reqQtyInBaseUnit,
                    
                    // Display quantities (convert based on selected UOM)
                    'total_balance' => $balance,
                    'total_available' => $available,
                    'requested_qty' => $reqQtyInBaseUnit  ,
                    
                    // Original request (stays as reference, never changes)
                    'request_qty' => $this->formatQtyWithUnit($detail->qty, $detail->uom->unit_symbol ?? null),
                    
                    'category' => $item->category->category_name,
                    'classification' => $item->classification->classification_name ?? 'N/A',
                    'barcode' => $item->item_barcode ?? 'N/A',
                    'brand' => $item->brand->brand_name ?? 'N/A',
                    'status' => $item->item_status,
                    'cost' => $item->costPrice->amount,
                    'costId' => $item->costPrice->id,
                    'total' => number_format($reqQtyInBaseUnit * $item->costPrice->amount, 2),
                ];
            }
            
            $this->overallTotal = array_sum(array_column($this->selectedItems, 'total'));  
        }
        $this->dispatch('closeProductionOrderModal'); // Close the modal after selection
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

    private function getUnitSymbol(array $item, $uomId): ?string
    {
        if (!isset($item['unit']) || !is_array($item['unit'])) {
            return null;
        }

        foreach ($item['unit'] as $unit) {
            if (($unit['to_uom_id'] ?? null) == $uomId) {
                return $unit['unit_symbol'] ?? null;
            }
        }

        return null;
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

    private function formatQtyWithUnit(float $qty, ?string $unitSymbol): string
    {
        $label = rtrim(rtrim(number_format($qty, 2, '.', ''), '0'), '.');
        if ($unitSymbol) {
            return $label . ' (' . $unitSymbol . ')';
        }
        return $label;
    }

    public function render()
    {
        return view('livewire.inventory.withdrawal');
    }
}
