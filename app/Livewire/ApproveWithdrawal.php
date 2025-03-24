<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Withdrawal;
use App\Models\Cardex;

class ApproveWithdrawal extends Component
{

    public $withdrawals = [];
    public $withdrawal ;
    public $withdrawalDetails = [];

    //display data
    public $reference = '';
    public $department = '';
    public $approvedBy = '';
    public $preparedBy = '';
    public $preparedDate = '';
    public $useDate = '';
    public $validityDate = '';
    public $withdrawalRemarks = '';
    public $withdrawalId = '';
    public $overAllCost = 0;

    //display block
    public $showWithdrawalSummary = true;
    public $showViewWithdrawal = false;


    public function mount()
    {
        $this->fetchData();
    }

    public function viewWithdrawalDetails($id)
    {

        $withdrawal = Withdrawal::with('department', 'approvedBy', 'reviewedBy', 'preparedBy', 'cardex')
            ->where('id', $id)
            ->first(); // Retrieve the correct record or fail

            $this->reference = $withdrawal->reference_number;
            $this->department = $withdrawal->department->department_name;
            $this->approvedBy = $withdrawal->approvedBy->name;
            $this->preparedBy = $withdrawal->preparedBy->name;
            $this->preparedDate = $withdrawal->created_at->format('M. d, Y');
            $this->useDate = $withdrawal->usage_date ? \Carbon\Carbon::parse($withdrawal->usage_date)->format('M. d, Y') : null;
            $this->validityDate = $withdrawal->useful_date ? \Carbon\Carbon::parse($withdrawal->useful_date)->format('M. d, Y') : null;
            $this->withdrawalRemarks = $withdrawal->remarks;
            $this->withdrawalId = $withdrawal->id;


        $this->withdrawalDetails = Cardex::with('item', 'priceLevel')
            ->where('withdrawal_id', $id)
            ->get();
            foreach ($this->withdrawalDetails as $withdrawalDetail) {
                $this->overAllCost += $withdrawalDetail->qty_out * $withdrawalDetail->priceLevel->amount;
            }
        $this->showWithdrawalSummary = false;
        $this->showViewWithdrawal = true;
    }

    public function fetchData()
    {
        // Fetch all withdrawals where reviewed_by is the current user's emp_id
        $this->showWithdrawalSummary = true;
        $this->showViewWithdrawal = false;
        $this->withdrawals = Withdrawal::with('department', 'approvedBy', 'reviewedBy', 'cardex.item')->where([['reviewed_by', auth()->user()->emp_id], ['withdrawal_status', 'FOR APPROVAL']])->get();
    }
    public function render()
    {
        return view('livewire.approve-withdrawal', [
            'withdrawals' => $this->withdrawals,
        ]);
    }
    public function backToSummary()
    {
        $this->showWithdrawalSummary = true;
        $this->showViewWithdrawal = false;
        $this->fetchData();
    }
    public function approveWithdrawal($id)
    {
        $withdrawal = Withdrawal::find($id);
        if ($withdrawal) {
            $withdrawal->withdrawal_status = 'APPROVED';
            $withdrawal->approved_date = now();
            $withdrawal->save();
            // Refresh the data
            $this->fetchData();
            return redirect()->route('withdrawal.approval');
        }
    }

    public function rejectWithdrawal($id)
    {
        $withdrawal = Withdrawal::find($id);
        if ($withdrawal) {
            $withdrawal->withdrawal_status = 'REJECTED';
            $withdrawal->reviewed_date = now();
            $withdrawal->save();

            // Update the cardex status to 'CANCELLED'
            Cardex::where('withdrawal_id', $id)->update(['status' => 'CANCELLED']);

            // Refresh the data
            $this->fetchData();
            $this->showWithdrawalSummary = true;
            $this->showViewWithdrawal = false;
            return redirect()->route('withdrawal.approval');
        }
    }
}
