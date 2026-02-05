<?php

namespace App\Livewire\Purchasing;

use Illuminate\Http\Request;
use Livewire\Component;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;
use App\Models\Cardex;
use App\Models\Term;
use Illuminate\Support\Facades\DB;

class PurchaseOrderApprovalShow extends Component
{
    public $requestInfo = [];
    public $id; // Add this public property
    public $requisitionInfo = [];
    public $requisitionDetails = [];
    public $totalReceived = [];
    public $term = [];
    public $terms = [];
    public function mount(Request $request)
    {
        $this->requestInfo = $request->session()->get('requestInfo');
        if (empty($this->requestInfo)) {
            $this->loadRequisitionInfo($this->id); // Use $this->id instead of $id

        }

    }

    public function loadRequisitionInfo($id)
    {
        $this->requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails','orderType')->where( 'id',  $id)->first();
        $this->requisitionDetails = RequisitionDetail::where('requisition_info_id', $id)->get();
        $this->totalReceived = Cardex::select('item_id', DB::raw('SUM(qty_in) as received_qty'))
            ->where(function($query) use ($id) {
                $query->whereIn('status', ['TEMP', 'FINAL'])
                      ->where('source_branch_id', auth()->user()->branch_id)
                      ->where('requisition_id', $id);
            })
            ->groupBy('item_id')
            ->pluck('received_qty', 'item_id');
            // dd($this->totalReceived);
        $this->terms = Term::all();
        $this->term = Term::where('id', $this->requestInfo->term_id)->first();

    }

    public function rejectPO($id){
        DB::transaction(function () use ($id) {
            $requisitionInfo = RequisitionInfo::find($id);
            $requisitionInfo->rejected_date = now();
            $requisitionInfo->requisition_status = 'REJECTED';
            $requisitionInfo->save();
        });
        session()->flash('success', 'Requisition Order Revised Successfully');
        return redirect()->route('approval_request_list');
    }

    public function approvePO($id){
        DB::transaction(function () use ($id) {
            $requisitionInfo = RequisitionInfo::find($id);
            $requisitionInfo->approved_date = now();
            $requisitionInfo->requisition_status = 'TO RECEIVE';
            $requisitionInfo->save();
        });
        session()->flash('success', 'Requisition updated Successfully');
        return redirect()->route('approval_request_list');
    }
    public function render()
    {
        return view('livewire.purchasing.purchase-order-approval-show');
    }
}
