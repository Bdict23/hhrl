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
    public $cardexSum = [];

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

        if ($request->has('receiving-number')) {
            //  dd($request->query('requisition-id'));
            $this->editReceiveRequest($request->query('receiving-number'), $request->query('requisition-id'));
        }
        $this->loadReceiveRequest();
    }

    public function loadRequestInfo($id)
    {
        $this->requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->where( 'id',  $id)->first();
    }

    public function selectPO($id)
    {
        $this->id = $id;
        $this->requestInfo = RequisitionInfo::with('supplier', 'preparer', 'reviewer', 'approver', 'term', 'requisitionDetails')
            ->where('id', $id)
            ->first();
        $this->requisitionDetails = RequisitionDetail::with('items', 'cost')
            ->where('requisition_info_id', $id)
            ->get();

        // Use the totalInByRequisition method to calculate total received quantities per item
        $this->cardexSum = [];
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

    public function saveReceiveRequest()
    {
        
       
        $this->validate();
        $this->validate([
            'qtyAndPrice.*.qty' => 'required|integer|min:1',
        ]);

        if ($this->isExists) {
            return $this->updateReceiveRequest();
        }
        $this->requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->where( 'id',  $this->id)->first();

        $newRecieving = new Receiving();
        $newRecieving->REQUISITION_ID = $this->requestInfo->id;
        $newRecieving->RECEIVING_TYPE = 'PO';
        $newRecieving->RECEIVING_NUMBER = $this->receiving_no;
        $newRecieving->WAYBILL_NUMBER = $this->waybill_no;
        $newRecieving->DELIVERY_NUMBER = $this->delivery_no;
        $newRecieving->INVOICE_NUMBER = $this->invoice_no;
        $newRecieving->PREPARED_BY = auth()->user()->emp_id;
        $newRecieving->DELIVERED_BY = $this->delivered_by;
        $newRecieving->RECEIVING_STATUS = $this->finalStatus ? 'FINAL' : 'DRAFT';
        $newRecieving->branch_id = auth()->user()->branch_id;
        $newRecieving->company_id = auth()->user()->branch->company_id;
        $newRecieving->remarks = $this->remarks;
        $newRecieving->save();

        // Save attachments
        if ($this->attachments) {
            foreach ($this->attachments as $attachment) {
                $path = $attachment->store('receiving_attachments', 'public');
                $newRecieving->attachments()->create([
                    'file_path' => $path,
                ]);
            }
        }
        // Save cardex records

        foreach ($this->qtyAndPrice as $index => $value) {
            if ($value['qty'] > 0) {
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
                        $cardex->status =  $this->finalStatus ? 'FINAL' : 'TEMP';
                        $cardex->transaction_type = 'RECEVING';
                        $cardex->price_level_id = $newCostPrice->id;
                        $cardex->receiving_id = $newRecieving->id;
                        $cardex->requisition_id = $this->requestInfo->id;
                        $cardex->final_date = now();
                        $cardex->save();
                }else{
                    $cardex = new Cardex();
                    $cardex->source_branch_id = auth()->user()->branch_id;
                    $cardex->qty_in = $value['qty'];
                    $cardex->item_id = $value['id'];
                    $cardex->status = $this->finalStatus ? 'FINAL' : 'TEMP';
                    $cardex->transaction_type = 'RECEVING';
                    $cardex->price_level_id = $value['costId'];
                    $cardex->receiving_id = $newRecieving->id;
                    $cardex->requisition_id = $this->requestInfo->id;
                    $cardex->final_date = now();
                    $cardex->save();
                }
            }
           
            //create Back-order if the sum of cardex is not equal to the requested quantity
            if((($this->cardexSum[$value['id']] ?? 0 ) + $value['qty']) !== $value['req_qty']){
                
                $backOrder = new Backorder();
                $backOrder->requisition_id = $this->requestInfo->id;
                $backOrder->item_id = $value['id'];
                $backOrder->status = 'ACTIVE';
                $backOrder->cancelled_date = null;
                $backOrder->bo_type = 'PO';
                $backOrder->remarks = $this->requestInfo->supplier_id;
                $backOrder->branch_id = auth()->user()->branch_id;
                $backOrder->company_id = auth()->user()->branch->company_id;
                $backOrder->save();
                $this->backorderCount++;
            }
        }

        session()->flash('success', 'Purchase Order Successfully Received' . ($this->backorderCount > 0 ? " with $this->backorderCount back order(s)." : ""));
        $this->reset();
        $this->loadReceiveRequest();
    }

    public function updateReceiveRequest()
    {
        $this->validate();
        $this->validate([
            'qtyAndPrice.*.qty' => 'required|integer|min:1',
        ]);
        $this->requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->where( 'id',  $this->id)->first();

        $newRecieving = Receiving::find($this->receiving_no);
        $newRecieving->REQUISITION_ID = $this->requestInfo->id;
        $newRecieving->RECEIVING_TYPE = 'PO';
        $newRecieving->RECEIVING_NUMBER = $this->receiving_no;
        $newRecieving->WAYBILL_NUMBER = $this->waybill_no;
        $newRecieving->DELIVERY_NUMBER = $this->delivery_no;
        $newRecieving->INVOICE_NUMBER = $this->invoice_no;
        $newRecieving->PREPARED_BY = auth()->user()->emp_id;
        $newRecieving->DELIVERED_BY = $this->delivered_by;
        $newRecieving->RECEIVING_STATUS = $this->finalStatus ? 'FINAL' : 'DRAFT';
        $newRecieving->branch_id = auth()->user()->branch_id;
        $newRecieving->company_id = auth()->user()->branch_id;
        $newRecieving->remarks = $this->remarks;
        // Save attachments
        if ($this->attachments) {
            foreach ($this->attachments as $attachment) {
                if ($attachment) {
                    // Store the attachment and save the path
                    $path = $attachment->store('receiving_attachments', 'public');
                    ReceivingAttachment::create([
                        'file_path' => $path,
                        'receiving_id' => 1, // Replace with the actual receiving ID
                    ]);
                }
            }
        }
        // Save cardex records

        foreach ($this->qtyAndPrice as $key => $value) {
            if ($value['qty'] > 0) {
                if ($value['oldCost'] != $value['newCost']) {

                    // Create a new cost price record
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
                        $cardex->status =  $this->finalStatus ? 'FINAL' : 'TEMP';
                        $cardex->transaction_type = 'RECEVING';
                        $cardex->price_level_id = $newCostPrice->id;
                        $cardex->receiving_id = $newRecieving->id;
                        $cardex->requisition_id = $this->requestInfo->id;
                        $cardex->final_date = now();
                        $cardex->save();
                }else{
                    // Create a new cardex record
                    $cardex = new Cardex();
                    $cardex->source_branch_id = auth()->user()->branch_id;
                    $cardex->qty_in = $value['qty'];
                    $cardex->item_id = $value['id'];
                    $cardex->status =  $this->finalStatus ? 'FINAL' : 'TEMP';
                    $cardex->transaction_type = 'RECEVING';
                    // Use the existing cost price ID
                    $cardex->price_level_id = $value['costId'];
                    // Set the receiving ID
                    $cardex->receiving_id = 1; // Replace with the actual receiving ID
                    // Set the requisition ID
                    $cardex->requisition_id = 1; // Replace with the actual requisition ID
                    // Set the final date
                    $cardex->final_date = now();
                    // Save the cardex record
                    $cardex->save();
                }
            }
        }

        session()->flash('success', 'Purchase Order Successfully Updated');
        return redirect('/receive_stock');
    }

    public function editReceiveRequest($recNo, $reqId)
    {
        // $this->requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->where( 'id',  $reqId)->first();
        $this->requisitionDetails = RequisitionDetail::with('items', 'cost')
            ->where('requisition_info_id', $reqId)
            ->get();
        $this->loadRequestInfo($reqId);
        $this->receiving_no = $recNo;
        $this->requestInfo = Receiving::with('requisition', 'branch', 'company', 'preparedBy', 'attachments')
            ->where('RECEIVING_NUMBER', $recNo)
            ->first();
        $this->isExists = true;
        $this->waybill_no = $this->requestInfo->WAYBILL_NUMBER;
        $this->delivery_no = $this->requestInfo->DELIVERY_NUMBER;
        $this->invoice_no = $this->requestInfo->INVOICE_NUMBER;
        $this->delivered_by = $this->requestInfo->DELIVERED_BY;
        $this->remarks = $this->requestInfo->remarks;
        $this->attachments = $this->requestInfo->attachments;
        $this->finalStatus = $this->requestInfo->RECEIVING_STATUS == 'FINAL' ? true : false;
        $this->receiving_no = $this->requestInfo->RECEIVING_NUMBER;
        $this->id = $this->requestInfo->REQUISITION_ID;

    }

    public function loadReceiveRequest(){
        $this->toReceiveRequests = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->whereIn('requisition_status', ['TO RECEIVE', 'PARTIALLY FULLFILLED'])->get();
    }
    public function render()
    {
        return view('livewire.purchasing.purchase-order-receive');
    }
}
