<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Withdrawal;
use App\Models\Department;

class WithdrawalSummary extends Component
{
    public $withdrawals = [];
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
        // $withdrawal = Withdrawal::with('department', 'approvedBy', 'reviewedBy', 'cardex.item')->findOrFail($id);


        // $departments = Department::where([['company_id', auth()->user()->branch->company_id], ['department_status', 'ACTIVE'], ['branch_id', auth()->user()->branch_id]])->get();
        // $approvers = Signatory::where([['signatory_type', 'APPROVER', 'employees'], ['branch_id', auth()->user()->branch_id], ['status', 'ACTIVE'], ['MODULE', 'ITEM_WITHDRAWAL']])->get();
        // $reviewers = Signatory::where([['signatory_type', 'REVIEWER', 'employees'], ['branch_id', auth()->user()->branch_id], ['status', 'ACTIVE'], ['MODULE', 'ITEM_WITHDRAWAL']])->get();
        // $items = Item::with('priceLevel', 'units', 'category', 'classification')->where([['item_status', 'ACTIVE'], ['company_id', auth()->user()->branch->company_id]])->get();
        // $categories = DB::table('categories')->select('category_name')->where([['status', 'ACTIVE'],['category_type', 'ITEM'],['company_id', auth()->user()->branch->company_id]])->get();
    
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
}
