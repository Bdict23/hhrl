<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Accounting\AccountType;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\COATransactionTemplate;
use App\Models\AcknowledgementReceipt;
use App\Models\PettyCashVoucher;
use App\Models\Employee;
use App\Models\Customer;

class PettyCashVoucherCreate extends Component
{
    public $saveAsStatus; // save as button
    public $currentPCVStatus = 'DRAFT'; // current status of the PCV
    public $isCreate; // check if edit or create new

    // data
    public $particulars; 
    public $acknowledgementReceipts = [];
    public $employees = [];
    public $customers = [];
    public $transactionTypes = []; // account types
    public $transactions = []; // transaction templates

    // calculations and setter
    public $reference;
    public $totalARBalance = 0;
    public $totalAmount = 0;
    public $debitTotal = 0;
    public $creditTotal = 0;
    public $totalDisburseAmount = 0;
    public $payeeName = '';

    //selections
    public $selectedAR;
    public $selectedTransactionTypeID = null;
    public $selectedTemplate = null;
    public $selectedEmployee = null;

    // inputs
    public $voucherSeriesNumber;
    public $note;
    public $employeeId;
    public $customerId;

    public function render()
    {
        return view('livewire.transactions.petty-cash-voucher-create');
    }

    public function mount(){
        $this->fetchData();
        $this->saveAsStatus = 'Save';
        $this->currentPCVStatus = 'Draft';
        $this->isCreate = true;
    }

    public function fetchData(){
        $this->transactionTypes = AccountType::where('company_id', auth()->user()->branch->company_id)->where('is_active', true)->get();
        $this->acknowledgementReceipts = AcknowledgementReceipt::where('branch_id', auth()->user()->branch->id)->where('status', 'OPEN')->get();
        $this->employees = Employee::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->get();
        $this->customers = Customer::where('branch_id', auth()->user()->branch->id)->get();
    }

    
    public function selectAcknowledgementReceipt( $id ){
        $this->selectedAR = AcknowledgementReceipt::find($id);
        $this->calculateARBalance();
        $this->dispatch('closeAcknowledgementReceiptLists');

    }
    public function selectTransaction( $id ){
        $this->selectedTemplate = COATransactionTemplate::find($id);
        foreach($this->selectedTemplate->transactionDetails as $detail){
            $this->particulars[] = [
                'id' => $detail->id,
                'account_title_id' => $detail->account_title_id,
                'account_title_code' => $detail->accountTitle->account_code,
                'account_title' => $detail->accountTitle->account_title,
                'type' => $detail->type,
                'amount' => 0,
            ];
        }
        $this->dispatch('closeTransactionLists');

    }

    public function showTransactions(){
        if($this->selectedTransactionTypeID == null){
             $this->dispatch('showAlert', ['type' => 'warning', 'message' => 'Please select Transaction type first.', 'title' => 'warning']);
        }else{
            $this->transactions = COATransactionTemplate::where('transaction_type', $this->selectedTransactionTypeID)->where('is_active', true)->where('company_id', auth()->user()->branch->company_id)->get();
            $this->dispatch('showTransactionLists');
        }
    }

    public function calculateARBalance(){
        $ARBalanceAmount = $this->selectedAR->check_amount;
        // sum all saved pcv's amount from database connected to the selected AR
        $pcvSum = $this->selectedAR->pettyCashVouchers()->sum('total_amount');
        $this->totalARBalance = $ARBalanceAmount - $pcvSum;
    }


     public function calculateDisbursementAmount(){
        $this->totalDisburseAmount = $this->debitTotal - $this->creditTotal;
    }

    public function selectCustomer( $id ){
        $this->dispatch('showAlert', ['type' => 'warning', 'message' => 'Customer Payee are not yet available.', 'title' => 'Customer Selection']);
    }

    public function selectEmployee( $id ){
        if($this->customerId){
            $this->customerId = null;
        }
        $this->selectedEmployee = Employee::find($id);
        $this->payeeName = $this->selectedEmployee->name . ($this->selectedEmployee->middle_name ? ' ' . $this->selectedEmployee->middle_name : '') . ($this->selectedEmployee->last_name ? ' ' . $this->selectedEmployee->last_name : '');
        $this->dispatch('closePayeeLists');
    }
}
