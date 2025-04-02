<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RequisitionInfo;
use Illuminate\Support\Facades\Auth;


class PurchaseOrderSummary extends Component
{
    public $purchaseOrderSummary = [];

    public function mount()
    {
        // Initialization code can go here if needed
        $this->fetchData();
    }

    public function fetchData()
    {
        // Fetching purchase order summary data from the database
        $this->purchaseOrderSummary = RequisitionInfo::with('supplier','preparer', 'approver')
        ->where('CATEGORY', 'PO')
        ->where('requisition_status', '!=', 'CANCELLED')
        ->where('from_branch_id', Auth::user()->branch_id)
        ->get();
    }

    public function redirectToShow($id)
    {
        // Redirecting to the purchase order show page
        return redirect()->route('purchase-order-show', ['id' => $id]);
    }
    public function render()
    {
        return view('livewire.purchase-order-summary');
    }
}
