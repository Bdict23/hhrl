<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Withdrawal;
use App\Models\Department;

class WithdrawalSummary extends Component
{
    public $withdrawals = [];
    public $selectedStatus = 'All';
    public $fromDate;
    public $toDate;
    public $statuses = [
        'PREPARING' => 'Preparing',
        'FOR REVIEW' => 'For Review',
        'FOR APPROVAL' => 'For Approval',
        'APPROVED' => 'Approved',
        'REJECTED' => 'Rejected',
        'CANCELLED' => 'Cancelled',
    ];
    public function mount()
    {
        if(auth()->user()->employee->getModulePermission('Item Withdrawal') == 2){
            return redirect()->to('dashboard');
        }
        $this->fetchData();
        // Initialization or data fetching can be done here if needed
    }

    public function fetchData()
    {

        // Fetch data for the withdrawal summary
        $this->withdrawals = Withdrawal::with('department', 'approvedBy', 'reviewedBy', 'preparedBy', 'cardex')
            ->where('source_branch_id', auth()->user()->branch_id)
            ->get();

    }
    public function render()
    {
        return view('livewire.inventory.withdrawal-summary');
    }

    public function viewWithdrawal($id)
    {
        // Redirect to the withdrawal show page with the selected ID
        return redirect()->to('/withdrawal-show?withdrawal-id=' . $id);

    }


     public function search()
    {
        $query = Withdrawal::with('department', 'approvedBy', 'reviewedBy', 'preparedBy', 'cardex')
            ->where('source_branch_id', auth()->user()->branch_id);

        if ($this->selectedStatus !== "All") {
            $query->where('withdrawal_status', $this->selectedStatus);
        }

        if ($this->fromDate && $this->toDate) {
            $query->whereDate('created_at', '>=', $this->fromDate)
                  ->whereDate('created_at', '<=', $this->toDate);
        }

        $this->withdrawals = $query->get();
    }
}
