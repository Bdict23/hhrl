<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RequisitionInfo;
use Illuminate\Support\Facades\Auth;


class PurchaseOrderSummary extends Component
{
    public $purchaseOrderSummary = [];
    public $statuses = [
        'PREPARING' => 'Preparing',
        'FOR REVIEW' => 'For Review',
        'FOR APPROVAL' => 'For Approval',
        'TO RECEIVE' => 'To Receive',
        'REJECTED' => 'Rejected',
        'CANCELLED' => 'Cancelled',
        'COMPLETED' => 'Completed',
        'PARTIALLY FULFILLED' => 'Partially Fulfilled',
    ];

    public $fromDate;
    public $toDate;
    public $statusPO = 'All';

    public function mount()
    {
        if(auth()->user()->employee->getModulePermission('Purchase Order') != 2 ){
            $this->fetchData();
        }else{
            return redirect()->to('dashboard');
        }
       
    }

    public function fetchData()
    {
        // Fetching purchase order summary data from the database
        $this->purchaseOrderSummary = RequisitionInfo::with('supplier','preparer', 'approver')
        ->where('CATEGORY', 'PO')
        ->where('requisition_status', '!=', 'CANCELLED')
        ->where('from_branch_id', Auth::user()->branch_id)
        ->where('created_at', '>=', now()->subMonths(1))
        ->get();
        $this->fromDate = now()->startOfMonth()->format('Y-m-d');
        $this->toDate = now()->format('Y-m-d');
    }

    public function redirectToShow($id)
    {
        // Redirecting to the purchase order show page
        return redirect()->route('purchase-order-show', ['id' => $id]);
    }

    public function search()
    {
        $query = RequisitionInfo::with('supplier', 'preparer', 'approver')
            ->where('CATEGORY', 'PO')
            ->where('from_branch_id', Auth::user()->branch_id);

        if ($this->statusPO !== "All") {
            $query->where('requisition_status', $this->statusPO);
        }

        if ($this->fromDate && $this->toDate) {
            $query->whereDate('created_at', '>=', $this->fromDate)
                  ->whereDate('created_at', '<=', $this->toDate);
        }

        $this->purchaseOrderSummary = $query->get();
    }

    public function render()
    {
        return view('livewire.purchase-order-summary');
    }
}
