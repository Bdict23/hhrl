<?php

namespace App\Livewire;
use App\Models\Withdrawal;
use App\Models\Cardex;
use Livewire\Component;

class ReviewWithdrawal extends Component
{

    public $withdrawals = [];
    public $withdrawal;
    public $withdrawalDetails = [];
    public $overAllCost = 0;

    //display block
    public $showWithdrawalSummary = true;
    public $showViewWithdrawal = false;


    public function mount()
    {
        $this->fetchData();
    }

    public function viewWithdrawal($id)
    {
        $this->withdrawal = Withdrawal::with('department', 'approvedBy', 'reviewedBy', 'preparedBy', 'cardex')->where('id', $id)->first();
        $this->withdrawalDetails = Cardex::with('item','priceLevel')->where('withdrawal_id', $id)->get();
        $this->showWithdrawalSummary = false;
        $this->showViewWithdrawal = true;
        foreach ($this->withdrawalDetails as $withdrawalDetail) {
            $this->overAllCost += $withdrawalDetail->qty_out * $withdrawalDetail->priceLevel->amount;
        }
    }

    public function fetchData()
    {
        // Fetch all withdrawals where reviewed_by is the current user's emp_id
        $this->showWithdrawalSummary = true;
        $this->showViewWithdrawal = false;


        $this->withdrawals = Withdrawal::with('department', 'approvedBy', 'reviewedBy', 'cardex.item')->where([['reviewed_by', auth()->user()->emp_id], ['withdrawal_status', 'FOR REVIEW']])->get();
    }
    public function render()
    {
        return view('livewire.review-withdrawal', [
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
        $withdrawal->withdrawal_status = 'FOR APPROVAL';
        $withdrawal->reviewed_date = now();
        $withdrawal->save();
        // Refresh the data
        $this->fetchData();
    }
    public function rejectWithdrawal($id)
    {
        $withdrawal = Withdrawal::find($id);
        $withdrawal->withdrawal_status = 'PREPARING';
        $withdrawal->reviewed_date = now();
        $withdrawal->save();
        // Refresh the data
        $this->fetchData();
    }

}
