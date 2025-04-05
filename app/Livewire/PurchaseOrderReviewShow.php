<?php

namespace App\Livewire;
use Illuminate\Http\Request;
use Livewire\Component;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;
use App\Models\Cardex;
use App\Models\Term;
use Illuminate\Support\Facades\DB;

class PurchaseOrderReviewShow extends Component
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
            // dd($this->totalReceived);
        $this->terms = Term::all();
        $this->term = Term::where('id', $this->requestInfo->term_id)->first();

    }

    public function revisePO($id){
        DB::transaction(function () use ($id) {
            $requisitionInfo = RequisitionInfo::find($id);
            $requisitionInfo->requisition_status = 'PREPARING';
            $requisitionInfo->save();
        });
        session()->flash('success', 'Requisition Order Revised Successfully');
        return redirect()->route('review_request_list');
    }

    public function reviewPO($id){
        DB::transaction(function () use ($id) {
            $requisitionInfo = RequisitionInfo::find($id);
            $requisitionInfo->requisition_status = 'FOR APPROVAL';
            $requisitionInfo->save();
        });
        session()->flash('success', 'Requisition updated Successfully');
        return redirect()->route('review_request_list');
    }

    public function render()
    {
        return view('livewire.purchase-order-review-show');
    }
}
