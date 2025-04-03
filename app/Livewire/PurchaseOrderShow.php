<?php

namespace App\Livewire;

use Illuminate\Http\Request;
use Livewire\Component;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;
use App\Models\Term;

class PurchaseOrderShow extends Component
{
    public $requestInfo = [];
    public $id; // Add this public property
    public $requisitionInfo = [];
    public $requisitionDetails = [];
    public $term = [];
    public $terms = [];
    public function mount(Request $request)
    {

        $this->requestInfo = $request->session()->get('requestInfo');
        if (empty($this->requestInfo)) {
            $this->loadRequestInfo($this->id); // Use $this->id instead of $id
        }

    }

    public function loadRequestInfo($id)
    {
        $this->requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->where( 'id',  $id)->first();
        dd($this->requestInfo);
    }
    public function loadRequisitionInfo($id)
    {
        $this->requisitionInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->where( 'id',  $id)->first();
        $this->requisitionDetails = RequisitionDetail::where('requisition_info_id', $id)->get();
        $this->terms = Term::all();
        $this->term = Term::where('id', $this->requestInfo->term_id)->first();
    }
    public function render()
    {
        return view('livewire.purchase-order-show');
    }
}
