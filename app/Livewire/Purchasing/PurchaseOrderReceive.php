<?php

namespace App\Livewire\Purchasing;

use App\Models\PriceLevel;
use App\Models\Receiving;
use Livewire\Component;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;
use App\Models\Cardex;
use App\Models\Signatory;
use Illuminate\Support\Facades\DB;
use App\Models\ReceivingAttachment;
use Livewire\WithFileUploads;
use Illuminate\Http\Request;
use App\Models\Backorder;

class PurchaseOrderReceive extends Component
{
    public $toReceiveRequests = [];

    use WithFileUploads;
    public $requestInfo = [];
    public $id;
    public $requisitionInfo = [];
    public $requisitionDetails = [];
    public $qtyAndPrice = [];
    public $receivingId;

    public $cardexSumFinal = [];
    public $cardexSumTemp = [];
    public $cardexSum = [];

    private $receivingOnCardex = [];

public $receivingInfo = [];

    public $paking_list_date;
    public $waybill_no;
    public $delivery_no;
    public $invoice_no;
    public $receiving_no;
    public $delivered_by;
    public $remarks;
    public $attachment;
    public $attachments = [];
    public $finalStatus = false;
    public $isExists = false;
    private $backorderCount = 0;
    private $fulfilled = 0;

    public $totalReceivedAmount = 0;
    public $totalReceived = [];
    public $newAttachment = false;

    protected $listeners = [
        'selectPO' => 'selectPO',
        'loadRequestInfo' => 'loadRequestInfo',
    ];
    protected $rules = [
        'id' => 'required|exists:requisition_infos,id',
        'waybill_no' => 'required_without_all:delivery_no,invoice_no|nullable|max:55',
        'delivery_no' => 'required_without_all:waybill_no,invoice_no|nullable|max:55',
        'invoice_no' => 'required_without_all:waybill_no,delivery_no|nullable|max:55',
        'receiving_no' => 'required|string|max:55|unique:receivings,RECEIVING_NUMBER',
        'delivered_by' => 'nullable|string|max:55',
        'remarks' => 'nullable|string|max:150',
        'qtyAndPrice.*.newCost' => 'required|numeric|min:1',
        'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
    ];
    protected $messages = [
        'id.required' => 'Please choose purchase order, before saving.',
        'user.required' => 'The user field is required.',
        'user.exists' => 'The selected user does not exist.',
        'waybill_no.required_without_all' => 'Either waybill number, delivery number, or invoice number must be provided.',
        'delivery_no.required_without_all' => 'Either waybill number, delivery number, or invoice number must be provided.',
        'invoice_no.required_without_all' => 'Either waybill number, delivery number, or invoice number must be provided.',
        'receiving_no.required' => 'The receiving number field is required.',
        'attachments.*.file' => 'The attachment must be a file.',
        'attachments.*.mimes' => 'The attachment must be a file of type: jpg, jpeg, png, pdf.',
        'attachments.*.max' => 'The attachment may not be greater than 2MB.',
    ];
    public function mount(Request $request = null)
    {
        if(auth()->user()->employee->getModulePermission('Purchase Receive') != 2 ){
            if ($request->has('receiving-number')) {
                //  dd($request->query('requisition-id'));
                $this->editReceiveRequest($request->query('receiving-number'), $request->query('requisition-id'));
            }
            $this->loadReceiveRequest();
        }else{
            return redirect()->to('dashboard');
        }
    }

    public function loadRequestInfo($id)
    {
        $this->requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->where( 'id',  $id)->first();
    }

    // this function will check if all item reqquest are filled and no lacking and will update to complete
    

    public function selectPO($id)
    {
       
        // CHECK RECEIVING IF THERE IS EXISTING RECEIVING WITH DRAFT RECEIVING STATUS WITH THE SAME REQUISITION ID
        if(!$this->isExists){ 
            $this->receivingInfo = Receiving::where('REQUISITION_ID', $id)->where('RECEIVING_STATUS', 'DRAFT')->first();
            if ($this->receivingInfo) {
               session()->flash('error', 'This Purchase Order has an existing draft receiving. Please update the existing receiving.');
                return;
            }
        } 
        $this->id = $id;
        $this->requestInfo = RequisitionInfo::with('supplier', 'preparer', 'reviewer', 'approver', 'term', 'requisitionDetails')
            ->where('id', $id)
            ->first();
        $this->requisitionDetails = RequisitionDetail::with('items', 'cost')
            ->where('requisition_info_id', $id)
            ->get();
            
       

         // Use the totalInByRequisition method to calculate total received quantities per item
        $this->cardexSum = [];
        $this->cardexSumFinal = [];
        $this->cardexSumTemp = [];

        if($this->isExists){
            $this->receivingOnCardex = Cardex::where('receiving_id', $this->receivingInfo->id)->get();
            $firstReceiving = Cardex::where('requisition_id', $this->id)->orderBy('created_at', 'asc')->first();
                foreach($this->receivingOnCardex as $item){
                    $itemId = $item->item_id;
                    $receivingId = $item->receiving_id;
                    $cardex = new Cardex();
                    $this->cardexSum[$itemId] =  $cardex->totalInByRequisition($this->id, $itemId);
                    $this->cardexSumFinal[$itemId] = ($receivingId == $firstReceiving->receiving_id ? 0 : ($this->finalStatus  ? $cardex->totalInByReceivingAsFinal($receivingId, $itemId) : $cardex->totalInByRequisition($this->id, $itemId)));
                    $this->cardexSumTemp[$itemId] =  ($cardex->totalInByReceivingAsTemp($receivingId, $itemId) != null ? $cardex->totalInByReceivingAsTemp($receivingId, $itemId) : 0);
                    $this->totalReceivedAmount += $this->cardexSumFinal[$itemId] * ($item->items->costPrice->amount ?? 0);
                }
                foreach ($this->requisitionDetails as $item) {
                    $itemId = $item->items->id;
                    $costPrice = $item->items->costPrice->amount;
                    $costID = $item->items->costPrice->id;
                    $req_qty = $item->qty;
                    $cardex = new Cardex();
                    $this->qtyAndPrice[] = [
                        'id' => $itemId,
                        'req_qty' => $req_qty,
                        'qty' => (float) ($this->finalStatus ? ($cardex->totalInByReceivingAsFinal($this->receivingId, $itemId) ) : ($this->cardexSumTemp[$itemId] ?? 0)),
                        'oldCost' => $costPrice,
                        'newCost' => $costPrice,
                        'costId' => $costID
                    ];
                }
        }else{

            foreach ($this->requisitionDetails as $item) {
                $itemId = $item->items->id;
                $cardex = new Cardex();
                $this->cardexSum[$itemId] = $cardex->totalInByRequisition($this->id, $itemId);
            }
    
            foreach ($this->requisitionDetails as $item) {
                $itemId = $item->items->id;
                $costPrice = $item->items->costPrice->amount;
                $costID = $item->items->costPrice->id;
                $req_qty = $item->qty;
                $this->qtyAndPrice[] = [
                    'id' => $itemId,
                    'req_qty' => $req_qty,
                    'qty' => 0,
                    'oldCost' => $costPrice,
                    'newCost' => $costPrice,
                    'costId' => $costID
                ];
            }
    
        }

        $this->totalReceived = Cardex::select('item_id', DB::raw('SUM(qty_in) as received_qty'))
            ->where(function($query) use ($id) {
                $query->whereIn('status', ['TEMP', 'FINAL'])
                      ->where('source_branch_id', auth()->user()->branch_id)
                      ->where('requisition_id', $id);
            })
            ->groupBy('item_id')
            ->pluck('received_qty', 'item_id');


       
       
    }

    public function saveReceiveRequest()
    {
        // dd($this->attachments);
            // if isExists is true, update the receiving request
            if ($this->isExists) {
                 $this->updateReceiveRequest();
                 return;
            }
        $this->validate([
            'id' => 'required|exists:requisition_infos,id',
            'waybill_no' => 'required_without_all:delivery_no,invoice_no|nullable|max:55',
            'delivery_no' => 'required_without_all:waybill_no,invoice_no|nullable|max:55',
            'invoice_no' => 'required_without_all:waybill_no,delivery_no|nullable|max:55',
            'receiving_no' => 'required|string|max:55|unique:receivings,RECEIVING_NUMBER',
            'delivered_by' => 'nullable|string|max:55',
            'remarks' => 'nullable|string|max:150',
            'qtyAndPrice.*.newCost' => 'required|numeric|min:1',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
            'attachments' => 'required',
            'attachments.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        $this->requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->where( 'id',  $this->id)->first();

        //create receiving
        $newRecieving = new Receiving();
        $newRecieving->REQUISITION_ID = $this->requestInfo->id;
        $newRecieving->RECEIVING_TYPE = 'PO';
        $newRecieving->RECEIVING_NUMBER = $this->receiving_no;
        $newRecieving->WAYBILL_NUMBER = $this->waybill_no;
        $newRecieving->DELIVERY_NUMBER = $this->delivery_no;
        $newRecieving->INVOICE_NUMBER = $this->invoice_no;
        $newRecieving->PREPARED_BY = auth()->user()->emp_id;
        $newRecieving->DELIVERED_BY = $this->delivered_by;
        $newRecieving->RECEIVING_STATUS = $this->finalStatus ? 'FINAL' : 'DRAFT'; // set status based on finalStatus
        $newRecieving->branch_id = auth()->user()->branch_id;
        $newRecieving->company_id = auth()->user()->branch->company_id;
        $newRecieving->remarks = $this->remarks;
        $newRecieving->save();

        // Save attachments
        foreach ($this->attachments as $attachment) {
            if ($attachment) {
                // Store the attachment and save the path
                $path = $attachment->store('receiving_attachments', 'public');
                ReceivingAttachment::create([
                    'file_path' => $path,
                    'receiving_id' => $newRecieving->id, // Replace with the actual receiving ID
                ]);
            }
        }
        // Save cardex records logic

        foreach ($this->qtyAndPrice as $index => $value) {
            $maxIndex = count($this->qtyAndPrice) - 1; // Get the maximum index

            if ((($this->cardexSum[$value['id']] ) + $value['qty']) != $value['req_qty'] && $this->finalStatus) {

                //check if has existing backorder for a specific item
                $hasBackorder = Backorder::where('requisition_id', $this->requestInfo->id)
                    ->where('item_id', $value['id'])
                    ->where('bo_type', 'PO')
                    ->first();

                if ($hasBackorder && $this->finalStatus ) {
                    $hasBackorder->update([
                        'remarks' => $hasBackorder->remarks .' , '. $this->receiving_no,
                        'receiving_attempt' => $hasBackorder->receiving_attempt + 1,
                    ]);
                    
                    $this->backorderCount++;
                    // dd('cardersum is '.($this->cardexSum[$value['id']] ?? 0 ).' + qty is '.$value['qty'].' = req_qty is '.$value['req_qty']);

                } else {

                    // create new backorder and update receiving status to 'PARTIALLY FULLFILLED'

                    $this->requestInfo->update(['requisition_status' => 'PARTIALLY FULFILLED']);
                    $this->createBackorder($value['id']);

                    $this->backorderCount++;


                }
            }else if((($this->cardexSum[$value['id']] ?? 0 ) + $value['qty']) == $value['req_qty'] && $this->finalStatus){

                $hasBackorder = Backorder::where('requisition_id', $this->requestInfo->id)
                    ->where('item_id', $value['id'])
                    ->where('bo_type', 'PO')
                    ->first();

                if ($hasBackorder && $this->finalStatus) {
                    $hasBackorder->update([
                        'status' => 'FULFILLED',
                    ]);
                    $hasBackorder->save();
                   
                }
                
            }

            
            // Check if the quantity is greater than 0 to create a cardex record
            if ($value['qty'] > 0 || !$this->finalStatus) {
                
                // Create a new cost price record if the cost has changed
                if ($value['oldCost'] != $value['newCost']) {
                    $newCostPrice = new PriceLevel();
                    $newCostPrice->price_type = 'COST';
                    $newCostPrice->amount = $value['newCost'];
                    $newCostPrice->item_id = $value['id'];
                    $newCostPrice->created_by = auth()->user()->emp_id;
                    $newCostPrice->company_id = auth()->user()->branch->company_id;
                    $newCostPrice->supplier_id = $this->requestInfo->supplier_id;
                    $newCostPrice->branch_id = auth()->user()->branch_id;
                    $newCostPrice->save();
    
                        $cardex = new Cardex();
                        $cardex->source_branch_id = auth()->user()->branch_id;
                        $cardex->qty_in = $value['qty'];
                        $cardex->item_id = $value['id'];
                        $cardex->status =  $this->finalStatus ? 'FINAL' : 'TEMP'; // set status based on finalStatus
                        $cardex->transaction_type = 'RECEVING';
                        $cardex->price_level_id = $newCostPrice->id;
                        $cardex->receiving_id = $newRecieving->id;
                        $cardex->requisition_id = $this->requestInfo->id;
                        $cardex->final_date = now();
                        $cardex->save();
                } else {
                    $cardex = new Cardex();
                    $cardex->source_branch_id = auth()->user()->branch_id;
                    $cardex->qty_in = $value['qty'];
                    $cardex->item_id = $value['id'];
                    $cardex->status = $this->finalStatus ? 'FINAL' : 'TEMP'; // set status based on finalStatus
                    $cardex->transaction_type = 'RECEVING';
                    $cardex->price_level_id = $value['costId'];
                    $cardex->receiving_id = $newRecieving->id;
                    $cardex->requisition_id = $this->requestInfo->id;
                    $cardex->final_date = now();
                    $cardex->save();
                }
            }
            
            if ($index === $maxIndex) {
            
                if($this->backorderCount > 0 && $this->finalStatus){
                    $this->requestInfo->update(['requisition_status' => 'PARTIALLY FULFILLED']); 
                }else if($this->finalStatus){
                    // update requisition status to COMPLETED
                    $this->requestInfo->update(['requisition_status' => 'COMPLETED']);
                }
            }
            
        }
    

        session()->flash('success', 'Purchase Order Successfully ' . ($this->finalStatus ? "Saved as Draft" : "Received") . ($this->backorderCount > 0 ? " with $this->backorderCount active back order(s)." : ""));
        $this->reset();
        $this->loadReceiveRequest();
    }

    public function updateReceiveRequest()
    {
        $this->validate([
            'waybill_no' => 'required_without_all:delivery_no,invoice_no|nullable|max:55',
            'delivery_no' => 'required_without_all:waybill_no,invoice_no|nullable|max:55',
            'invoice_no' => 'required_without_all:waybill_no,delivery_no|nullable|max:55',
            'delivered_by' => 'nullable|string|max:55',
            'remarks' => 'nullable|string|max:150',
            'qtyAndPrice.*.newCost' => 'required|numeric|min:1',

        ]);

        $this->requestInfo = RequisitionInfo::with('supplier', 'preparer', 'reviewer', 'approver', 'term', 'requisitionDetails')
            ->where('id', $this->id)
            ->where('requisition_status', '!=', 'COMPLETED')
            ->first();
        if(!$this->requestInfo){
            
            session()->flash('error', 'This Purchase Order has been completed. You cannot update it.');
            return;
        }
         //update receiving
        $updateRecieving = Receiving::where('REQUISITION_ID', $this->requestInfo->id)->where('RECEIVING_NUMBER', $this->receiving_no)->first();
        if ($updateRecieving) {
            $updateRecieving->WAYBILL_NUMBER = $this->waybill_no;
            $updateRecieving->DELIVERY_NUMBER = $this->delivery_no;
            $updateRecieving->INVOICE_NUMBER = $this->invoice_no;
            $updateRecieving->PREPARED_BY = auth()->user()->emp_id;
            $updateRecieving->DELIVERED_BY = $this->delivered_by;
            $updateRecieving->RECEIVING_STATUS = $this->finalStatus ? 'FINAL' : 'DRAFT'; // set status based on finalStatus
            $updateRecieving->branch_id = auth()->user()->branch_id;
            $updateRecieving->company_id = auth()->user()->branch->company_id;
            $updateRecieving->remarks = $this->remarks;
            $updateRecieving->save();
        }

       if($this->newAttachment){
            // delete the images on the storage
            $existingAttachments = ReceivingAttachment::where('receiving_id', $updateRecieving->id)->get();
            foreach ($existingAttachments as $attachment) {
                if ($attachment) {
                    $filePath = storage_path('app/public/' . $attachment->file_path);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
            // delete the existing attachments from the database
            ReceivingAttachment::where('receiving_id', $updateRecieving->id)->delete();

            // Save attachments
        if ($this->attachments) {
            foreach ($this->attachments as $attachment) {
                if ($attachment) {
                    // Store the attachment and save the path
                    $path = $attachment->store('receiving_attachments', 'public');
                    ReceivingAttachment::create([
                        'file_path' => $path,
                        'receiving_id' => $updateRecieving->id, // Replace with the actual receiving ID
                    ]);
                }
            }
        }
        }
      
        
       
        // delete existing cardex records
        $this->receivingOnCardex = Cardex::where('receiving_id', $this->receivingInfo->id)->get();
        foreach($this->receivingOnCardex as $item){
            $itemId = $item->item_id;
            $receivingId = $item->receiving_id;
            $cardex = new Cardex();
            $cardex->where('receiving_id', $receivingId)->delete();
        }
      

        foreach ($this->qtyAndPrice as $index => $value) {
            $maxIndex = count($this->qtyAndPrice) - 1; // Get the maximum index
            // dd('cardersum is '.($this->cardexSum[$value['id']] ?? 0 ).' + qty is '.$value['qty'].' = req_qty is '.$value['req_qty']);

            if ((($this->cardexSum[$value['id']] ?? 0 ) + $value['qty']) != $value['req_qty'] && $this->finalStatus) {
                //check if has existing backorder for a specific item
                $hasBackorder = Backorder::where('requisition_id', $this->requestInfo->id)
                    ->where('item_id', $value['id'])
                    ->where('bo_type', 'PO')
                    ->first();

                if ($hasBackorder && $this->finalStatus ) {
                    $hasBackorder->update([
                        'remarks' => $hasBackorder->remarks .' , '. $this->receiving_no,
                        'receiving_attempt' => $hasBackorder->receiving_attempt + 1,
                    ]);
                    
                    $this->backorderCount++;
                    // dd('cardersum is '.($this->cardexSum[$value['id']] ?? 0 ).' + qty is '.$value['qty'].' = req_qty is '.$value['req_qty']);

                } else {

                    // create new backorder and update receiving status to 'PARTIALLY FULFILLED'

                    $this->requestInfo->update(['requisition_status' => 'PARTIALLY FULFILLED']);
                    $this->createBackorder($value['id']);
                    $this->backorderCount++;
                }
            }else if((($this->cardexSum[$value['id']] ?? 0 ) + $value['qty']) == $value['req_qty'] && $this->finalStatus){
                $this->fulfilled++;
                $hasBackorder = Backorder::where('requisition_id', $this->requestInfo->id)
                    ->where('item_id', $value['id'])
                    ->where('bo_type', 'PO')
                    ->first();

                if ($hasBackorder && $this->finalStatus) {
                    $hasBackorder->update([
                        'status' => 'FULFILLED',
                    ]);
                    $hasBackorder->save();
                }
            }

            
            // Check if the quantity is greater than 0 to create a cardex record
            if ($value['qty'] > 0) {
                
                // Create a new cost price record if the cost has changed
                if ($value['oldCost'] != $value['newCost']) {
                    $newCostPrice = new PriceLevel();
                    $newCostPrice->price_type = 'COST';
                    $newCostPrice->amount = $value['newCost'];
                    $newCostPrice->item_id = $value['id'];
                    $newCostPrice->created_by = auth()->user()->emp_id;
                    $newCostPrice->company_id = auth()->user()->branch->company_id;
                    $newCostPrice->supplier_id = $this->requestInfo->supplier_id;
                    $newCostPrice->branch_id = auth()->user()->branch_id;
                    $newCostPrice->save();
    
                        $cardex = new Cardex();
                        $cardex->source_branch_id = auth()->user()->branch_id;
                        $cardex->qty_in = $value['qty'];
                        $cardex->item_id = $value['id'];
                        $cardex->status =  $this->finalStatus ? 'FINAL' : 'TEMP'; // set status based on finalStatus
                        $cardex->transaction_type = 'RECEVING';
                        $cardex->price_level_id = $newCostPrice->id;
                        $cardex->receiving_id = $updateRecieving->id;
                        $cardex->requisition_id = $this->requestInfo->id;
                        $cardex->final_date = now();
                        $cardex->save();
                }else{
                    $cardex = new Cardex();
                    $cardex->source_branch_id = auth()->user()->branch_id;
                    $cardex->qty_in = $value['qty'];
                    $cardex->item_id = $value['id'];
                    $cardex->status = $this->finalStatus ? 'FINAL' : 'TEMP'; // set status based on finalStatus
                    $cardex->transaction_type = 'RECEVING';
                    $cardex->price_level_id = $value['costId'];
                    $cardex->receiving_id = $updateRecieving->id;
                    $cardex->requisition_id = $this->requestInfo->id;
                    $cardex->final_date = now();
                    $cardex->save();
                }
            }

            
            
            if ($index == $maxIndex) {
            
                if($this->backorderCount > 0 && $this->finalStatus){
                    $this->requestInfo->update(['requisition_status' => 'PARTIALLY FULFILLED']); 
                }else if($this->finalStatus && $this->requisitionDetails->count()== $this->fulfilled){
                    // update requisition status to COMPLETED
                    $this->requestInfo->update(['requisition_status' => 'COMPLETED']);
                }
            }
            
        }

        session()->flash('success', 'Purchase Order Successfully Updated');
        return redirect('/receive_stock');
    }

    public function editReceiveRequest($recNo, $reqId)
    {
        $this->isExists = true; 
        $this->receiving_no = $recNo;
        $this->receivingInfo = Receiving::where('RECEIVING_NUMBER', $recNo)->first();

        $this->receivingId = $this->receivingInfo->id;
        $this->waybill_no = $this->receivingInfo->WAYBILL_NUMBER ?? '';
        $this->delivery_no = $this->receivingInfo->DELIVERY_NUMBER ?? '';
        $this->invoice_no = $this->receivingInfo->INVOICE_NUMBER ?? '';
        $this->delivered_by = $this->receivingInfo->DELIVERED_BY  ?? '';
        $this->remarks = $this->receivingInfo->remarks ?? '';
        $this->finalStatus = $this->receivingInfo->RECEIVING_STATUS == 'FINAL' ? true : false;
        $this->receiving_no = $this->receivingInfo->RECEIVING_NUMBER;
        $this->id = $this->receivingInfo->REQUISITION_ID;

        // Retrieve the file paths from ReceivingAttachment and load the files into $this->attachments
        $imagePaths = ReceivingAttachment::where('receiving_id', $this->receivingInfo->id)->get('file_path')->toArray();
        $this->attachments = [];
        foreach ($imagePaths as $imagePath) {
            $filePath = storage_path('app/public/' . $imagePath['file_path']);
            // dd($filePath);
            if (file_exists($filePath)) {
                $this->attachments[] = $imagePath['file_path'];
            }
        }
        // dd($this->attachments);
        $this->selectPO($reqId);
    }

    public function loadReceiveRequest(){
        $this->toReceiveRequests = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->whereIn('requisition_status', ['TO RECEIVE', 'PARTIALLY FULFILLED'])->where('from_branch_id', auth()->user()->branch_id)->get();
    }
 

    public function render()
    {
        return view('livewire.purchasing.purchase-order-receive');
    }

    public function updatedAttachments()
    {
        $this->validate([
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        $this->Attachments = [];
        $this->newAttachment = true;
    }

    private function createBackorder($itemId){
        $backOrder = new Backorder();
        $backOrder->requisition_id = $this->requestInfo->id;
        $backOrder->item_id = $itemId; //value['id']
        $backOrder->status = 'ACTIVE';
        $backOrder->cancelled_date = null;
        $backOrder->bo_type = 'PO';
        $backOrder->remarks = $this->receiving_no;
        $backOrder->branch_id = auth()->user()->branch_id;
        $backOrder->company_id = auth()->user()->branch->company_id;
        $backOrder->save();
    }
}
