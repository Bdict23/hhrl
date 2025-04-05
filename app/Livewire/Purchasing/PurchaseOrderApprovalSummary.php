<?php

namespace App\Livewire\Purchasing;

use Livewire\Component;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;
use App\Models\Cardex;
use Illuminate\Support\Facades\Auth;
use App\Models\Term;

use Illuminate\Support\Facades\DB;

class PurchaseOrderApprovalSummary extends Component
{
     //REVIEW LIST
     public $review_requests = [];
     public $all_review_requests = [];


     public $requestInfo = [];
     public $id; // Add this public property
     public $requisitionInfo = [];
     public $requisitionDetails = [];
     public $totalReceived = [];
     public $term = [];
     public $terms = [];
    public function mount(){
        $this->forReviewList();
    }

    public function forReviewList(){
        $this->review_requests = RequisitionInfo::with('supplier','preparer','reviewer', 'approver', 'requisitionDetails','term')->where([
            ['requisition_status', 'FOR APPROVAL'],
            ['category', 'PO'],
            ['APPROVED_BY', Auth::user()->emp_id]])->get();
        $this->all_review_requests = RequisitionInfo::with('supplier','preparer','reviewer', 'approver', 'requisitionDetails','term')
            ->whereNotNull('APPROVED_DATE')
            ->where([['APPROVED_BY', Auth::user()->emp_id],['category', 'PO']])
            ->get();

    }
    public function render()
    {
        return view('livewire.purchasing.purchase-order-approval-summary');
    }
}
