<?php

namespace App\Livewire;

use Illuminate\Http\Request;
use Livewire\Component;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;
use App\Models\Cardex;
use App\Models\Term;
use Illuminate\Support\Facades\DB;


class PurchaseOrderShow extends Component
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
        if(auth()->user()->employee->getModulePermission('Purchase Order') != 2 ){
            $this->requestInfo = $request->session()->get('requestInfo');
            if (empty($this->requestInfo)) {
                $this->loadRequisitionInfo($this->id); // Use $this->id instead of $id

            }
        }else{
            return redirect()->to('dashboard');
        }

    }

    public function loadRequestInfo($id)
    {
        $this->requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails')->where( 'id',  $id)->first();

    }
    public function loadRequisitionInfo($id)
    {
        $this->requestInfo = RequisitionInfo::with('supplier','preparer','reviewer', 'approver','term','requisitionDetails','requisitionDetails.cost')->where( 'id',  $id)->first();
        $this->requisitionDetails = RequisitionDetail::with('cost')->where('requisition_info_id', $id)->get();
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
    public function render()
    {
        return view('livewire.purchase-order-show');
    }
}
