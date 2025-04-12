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

    protected $listeners = [
        'selectPO' => 'selectPO',
        'loadRequestInfo' => 'loadRequestInfo',
    ];
    protected $rules = [
        'id' => 'required|exists:requisition_infos,id',
        'waybill_no' => 'required_without_all:delivery_no,invoice_no|nullable|max:55',
        'delivery_no' => 'required_without_all:waybill_no,invoice_no|nullable|max:55',
        'invoice_no' => 'required_without_all:waybill_no,delivery_no|nullable|max:55',
        'receiving_no' => 'required|string|max:55',
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
    public function mount()
    {
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
            $this->qtyAndPrice[] = [
                'id' => $itemId,
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

        foreach ($this->qtyAndPrice as $key => $value) {
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
        }

        session()->flash('success', 'Purchase Order Successfully Received');
        $this->reset();
        $this->loadReceiveRequest();
    }

    public function loadReceiveRequest(){
        $this->toReceiveRequests = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->whereIn('requisition_status', ['TO RECEIVE', 'PARTIALLY FULLFILLED'])->get();
    }
    public function render()
    {
        return view('livewire.purchasing.purchase-order-receive');
    }
}
