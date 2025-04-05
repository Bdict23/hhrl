<?php

namespace App\Livewire\Purchasing;


use Livewire\Component;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;
use App\Models\Cardex;
use App\Models\Signatory;
use Illuminate\Support\Facades\DB;

class PurchaseOrderReceive extends Component
{
    public $toReceiveRequests = [];


    public $requestInfo = [];
    public $id; // Add this public property
    public $requisitionInfo = [];
    public $requisitionDetails = [];
    public $totalReceived = [];
    public $users = [];
    public $user;


    public function mount()
    {
        $this->loadReceiveRequest();
        $this->users = Signatory::with('employees')->where([['signatory_type', 'RECEIVER'], ['branch_id', auth()->user()->branch_id]])->get();

    }

    public function loadRequestInfo($id)
    {
        $this->requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->where( 'id',  $id)->first();


    }

    public function loadRequisitionInfo($id)
    {
        $this->requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->where( 'id',  $id)->first();
        $this->requisitionDetails = RequisitionDetail::where('requisition_info_id', $id)->get();
        $this->totalReceived = Cardex::select('item_id', DB::raw('SUM(qty_in) as received_qty'))
            ->where(function($query) use ($id) {
                $query->whereIn('status', ['TEMP', 'FINAL'])
                      ->where('source_branch_id', auth()->user()->branch_id)
                      ->where('requisition_id', $id);
            })
            ->groupBy('item_id')
            ->pluck('received_qty', 'item_id');

    }

    public function selectPO($id)
    {
        $this->loadRequisitionInfo($id);
    }

    public function loadReceiveRequest(){
        $this->toReceiveRequests = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->whereIn('requisition_status', ['TO RECEIVE', 'PARTIALLY FULLFILLED'])->get();
    }
    public function render()
    {
        return view('livewire.purchasing.purchase-order-receive');
    }
}
