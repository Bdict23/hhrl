<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\RequisitionInfo;
use App\Models\Item;
use App\Models\SystemParameter;
use App\Models\Module;
use App\Models\Signatory;
use App\Models\Cardex;
use Carbon\Carbon;
use Akira\QrCode\Facades\QrCode;
use App\Models\BatchProperty;
use WireUi\Traits\WireUiActions;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;





class AssetRegisterAction extends Component
{
    use WireUiActions;

    // mounted
    public $moduleId;
    public $reference;
    public $items;
    public $types;
    public $reviewers;
    public $approvers;
    public $purchaseOrders;
    public $isNew = true;
    public $isEditable = true;
    public $existingData;
    public $isSerialized = false;
    public $action = 'create';


    // input
    public $selectedPurchaseOrderId;
    public $selectedTypeId;
    public $dateIssued;
    public $purpose;
    public $note;
    public $selectedItems = [];
    public $approverId;
    public $reviewerId;
    public $saveAs = 'DRAFT';


    // selection
        public $addedItemId;
        public $addedItemSiDr;
        public $addedItemCost;
        public $addedItemSerial;
        public $addedItemCondition;
        public $addedItemLifeSpan;
        public $qrCode;
        public $qty = 1;

    public function render()
    {
        return view('livewire.inventory.asset-register-action');
    }

    protected $messages = [
        'selectedItems.required' => 'Please select one or more items.',
        'reviewerId.required' => 'Reviewer cannot be empty.',
        'approverId.required' => 'Approver cannot be empty.',
    ];

    public function mount(Request $request = null){
        if($request->has('id')){
            $this->existingData = BatchProperty::find($request->query('id'));
            if($this->existingData){
                $this->action = $request->query('action');
                $this->isNew = false;
                $this->getExistingData($this->existingData);
            }
        }else{
            $this->isNew = true;
            $this->dateIssued = Carbon::now();
        }
        $this->fetchData();
    }
    public function fetchData(){
        
        $module = Module::where('module_name', 'Fixed Asset')->first();
        $this->moduleId = $module->id;
        $this->types = SystemParameter::where('module_id', $module->id)->where('branch_id', auth()->user()->branch_id)->where('status', 'ACTIVE')->where('key', 'batch_type')->get();
        $this->reviewers = Signatory::with('employees')
            ->where('branch_id', auth()->user()->branch_id)
            ->where('module_id', $module->id )
            ->where('signatory_type', 'REVIEWER')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->employees->id,
                    'full_name' => $user->employees->name . ' ' . $user->employees->middle_name. ' ' . $user->employees->last_name,
                ];
            });
         $this->approvers = Signatory::with('employees')
            ->where('branch_id', auth()->user()->branch_id)
            ->where('module_id', $module->id )
            ->where('signatory_type', 'APPROVER')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->employees->id,
                    'full_name' => $user->employees->name . ' ' . $user->employees->middle_name. ' ' . $user->employees->last_name,
                ];
            });

            $this->items = Item::with('costPrice')->where('item_status', 'ACTIVE' )->where('company_id', auth()->user()->branch->company_id)->get();
            $this->purchaseOrders = RequisitionInfo::where('requisition_status','COMPLETED')->where('from_branch_id',auth()->user()->branch_id)->get();
    }

    public function updatedAddedItemId(){
        $cost = $this->items->where('id', $this->addedItemId)->first();
        if($this->addedItemId != null && $cost != null){
            $this->addedItemCost = $cost->costPrice->amount ?? 0.00;
        }
    }

    public function addItem(){
        $selectedItem = $this->items->where('id', $this->addedItemId)->first();
        if($this->isSerialized){
            $this->validate([
                'addedItemSerial' => 'required|unique:batch_property_details,serial'],[
                'addedItemSerial.unique' => 'This serial number has already been taken.',
            ]);
        }else{
            $this->validate([
                'qty' => 'required|numeric|min:1'],[
                'qrt.required' => 'Enter a valid quantity.',
            ]);
        }
        if($this->isNew){
            $this->validate([
            'addedItemId'=> 'required',
            'addedItemLifeSpan'=> 'required|numeric|min:1',
            'addedItemCost'=> 'required|numeric|min:0.01',
            'addedItemCondition'=> 'required|in:USED,NEW',
            'addedItemSerial' => 'nullable|unique:batch_property_details,serial'],[
            'addedItemSerial.unique' => 'This serial number has already been taken.',
            ]);
        }else{
            $this->validate([
                'addedItemId'=> 'required',
                'addedItemLifeSpan'=> 'required|numeric|min:1',
                'addedItemCost'=> 'required|numeric|min:0.01',
                'addedItemCondition'=> 'required|in:USED,NEW',
                ]);
        }
        
        $this->selectedItems [] = [
            'id'=> $selectedItem->id,
            'itemCode' => $selectedItem->item_code,
            'itemName' => $selectedItem->item_description,
            'serial'    => $this->addedItemSerial,
            'sidr' => $this->addedItemSiDr,
            'cost' => $this->addedItemCost,
            'qty' => $this->qty,
            'span' => $this->addedItemLifeSpan,
            'condition' => $this->addedItemCondition,
        ];
        $this->resetAddItemForm();
    }

    public function resetAddItemForm(){
        $this->addedItemId = null;
        $this->addedItemSiDr = null;
        $this->addedItemCost = null;
        $this->addedItemLifeSpan = null;
        $this->addedItemCondition = null;
        $this->qty = null;
    }
    public function resetBatchForm(){
        $this->selectedPurchaseOrderId = null;
        $this->selectedTypeId = null;
        $this->dateIssued = Carbon::now();
        $this->purpose = null;
        $this->note = null;
        $this->selectedItems = [];
        $this->approverId = null;
        $this->reviewerId = null;
        $this->saveAs = 'DRAFT';
    }
    public function removeItem($index){
        
        unset($this->selectedItems[$index]);
        $this->selectedItems = array_values($this->selectedItems);
    }

    public function save(){
        $this->validate([
            'dateIssued'=> 'required|date',
            'purpose'=> 'nullable|string|max:150',
            'note'=> 'nullable|string|max:150',
            'selectedItems'=> 'required|array|min:1',
            'approverId' => 'required',
            'reviewerId' => 'required',
            'saveAs' => 'required|in:DRAFT,OPEN'
        ]);

        $yearlyCount = BatchProperty::where('branch_id', auth()->user()->branch_id)
            ->whereYear('created_at', now()->year)
            ->count() + 1;
        $batch =  BatchProperty::create([
        'reference' => 'FAB-' .  auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT),
        'status' => $this->saveAs,
        'type_id' => $this->selectedTypeId,
        'requisition_id' => $this->selectedPurchaseOrderId,
        'branch_id' => auth()->user()->branch_id,
        'note' => $this->note,
        'purpose' => $this->purpose,
        'prepared_by' => auth()->user()->emp_id,
        'approved_by' => $this->approverId,
        'reviewed_by' => $this->reviewerId,
        'issued_date' => $this->dateIssued,
        'created_at' => Carbon::now('Asia/Manila')
       ]);
       $count = 1;
        foreach ($this->selectedItems as $item) {
            $generatedItemCode = $batch->reference . '-' . $item['itemCode']. '-'. str_pad($count, 3, '0', STR_PAD_LEFT);
            $spanEndedDate = now()->addYears((int)($item['span'] ?? 0));
            $batch->batchItems()->create([
            'code' => $generatedItemCode,
            'item_id'  => $item['id'],
            'branch_id'     => auth()->user()->branch_id,
            'serial'     => $item['serial'] ,
            'cost'     => $item['cost'] ,
            'lifespan'     => $item['span'] ,
            'span_ended'     => $spanEndedDate ,
            'condition'     => $item['condition'] ,
            'created_at'     => Carbon::now('Asia/Manila'),
            'updated_at'     => null,
        ]);
        $count++;
        }
        $this->resetBatchForm();
        $this->notify('Saved!', 'success', 'The fixed asset batch has been successfully processed.');
        
    }
    public function updateBatch(){
        $batch = $this->existingData;
        $batch->update([
            'type_id'=> $this->selectedTypeId,
            'status'=> $this->saveAs,
            'requisition_id'=> $this->selectedPurchaseOrderId,
            'note'=> $this->note,
            'purpose'=> $this->purpose,
            'prepared_by'=> auth()->user()->emp_id,
            'approved_by'=> $this->approverId,
            'reviewed_by'=> $this->reviewerId,
            'issued_date'=> $this->dateIssued,
            'updated_at'=> Carbon::now('Asia/Manila'),
            ]);
        $this->isEditable = $this->saveAs == 'DRAFT';
        $batch->batchItems()->delete();
        $count = 1;
        foreach ($this->selectedItems as $item) {
            $generatedItemCode = $batch->reference . '-' . $item['itemCode']. '-'. str_pad($count, 3, '0', STR_PAD_LEFT);
            $spanEndedDate = now()->addYears((int)($item['span'] ?? 0));
            $batch->batchItems()->create([
            'code' => $generatedItemCode,
            'item_id'  => $item['id'],
            'branch_id'     => auth()->user()->branch_id,
            'serial'     => $item['serial'] ,
            'cost'     => $item['cost'] ,
            'lifespan'     => $item['span'] ,
            'span_ended'     => $spanEndedDate ,
            'condition'     => $item['condition'] ,
            'created_at'     => Carbon::now('Asia/Manila'),
            'updated_at'     => Carbon::now('Asia/Manila'),
        ]);
        $count++;
        }
        $this->notify('Updated!', 'success','The fixed asset batch has been successfully updated.');
    }

    public function reviewAction(){
       if($this->checkAuthorization('reviewer', auth()->user()->emp_id)){
        $batch = $this->existingData;
        if($this->saveAs != 'REVISE'){
        $batch->update([
            'reviewed_date'=> Carbon::now('Asia/Manila'),
            ]);
       }else{
        dd('false');
            $batch->update([
            'status'=> 'DRAFT',
            ]);
       }
        $this->notify('Updated!', 'success','The fixed asset batch has been successfully updated.');
       }else{
        $this->notify('Access Denied!', 'error','You have no authorize to change the status.');
       }
    }
    public function approvalAction(){
        if($this->checkAuthorization('approver', auth()->user()->emp_id)){
            $batch = $this->existingData;
            if($this->saveAs != 'REVISE'){
            $batch->update([
                'approved_date'=> Carbon::now('Asia/Manila'),
                'status' => 'CLOSED',
                ]);
                $this->addToCardex();
            }else{
                    $batch->update([
                    'status'=> 'DRAFT',
                    ]);
            }
             $this->notify('Updated!', 'success','The fixed asset batch has been successfully updated.');
        }else{
            $this->notify('Access Denied!', 'error','You have no authorize to change the status.');
        }
    }
    public function checkAuthorization($role, $id)
{
    return match($role) {
        'reviewer' => $this->reviewers->where('id', $id)->count() > 0,
        'approver' => $this->approvers->where('id', $id)->count() > 0,
        default    => false
    };
}

    public function notify($title, $icon, $description) : void
    {
        $this->notification()->send([
            'icon' => $icon,
            'title' => $title,
            'description' => $description,
        ]);
    }

    public function getExistingData($data){
        $this->reference = $data->reference;
        $this->selectedTypeId = $data->type_id;
        $this->selectedPurchaseOrderId = $data->requisition_id;
        $this->dateIssued = $data->issued_date;
        $this->purpose = $data->purpose;
        $this->saveAs = $data->status;
        $this->note = $data->note;
        $this->approverId = $data->approved_by;
        $this->reviewerId = $data->reviewed_by;
        $this->isEditable = $data->status == 'DRAFT' ;
        

        foreach($data->batchItems as $item){
           $this->selectedItems [] = [
            'id'=> $item->item_id,
            'itemCode' => $item->itemDetail->item_code,
            'itemName' => $item->itemDetail->item_description,
            'serial'    => $item->serial,
            'sidr' => $item->sidr_no,
            'cost' => $item->cost,
            'span' => $item->lifespan,
            'condition' => $item->condition,
           ];
        }
    }

    public function exportToPdf(){
        $batch = $this->existingData;
        $items = $batch?->batchItems()->with('itemDetail')->get() ?? collect();

        $pdf = Pdf::loadView('export.pdf.batch-items-qr-view', [
            'batch' => $batch,
            'items' => $items,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'fixed-asset-batch.pdf');
    }

    public function addToCardex(){
        $batch = $this->existingData;
        foreach($batch->batchItems as $item){
            Cardex::create([
                'item_id' => $item->item_id,
                'source_branch_id' => auth()->user()->branch_id,
                'reference' => $batch->reference,
                'qty_out' => 1,
                'status' => 'FINAL',
                'transaction_type' => 'ADJUSTMENT',
                'price_level_id' => null,
                'batch_id' => $batch->id,
                'created_at' => Carbon::now('Asia/Manila')
            ]);
        }
    }

}
