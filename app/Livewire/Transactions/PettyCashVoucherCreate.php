<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Accounting\AccountType;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\COATransactionTemplate;
use App\Models\AcknowledgementReceipt;
use App\Models\PettyCashVoucher;
use App\Models\PCVDetail;
use App\Models\Employee;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;


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
    public $pcvId; // for edit
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
    public $selectedCustomer = null;

    // inputs
    public $voucherSeriesNumber;
    public $note;
    public $employeeId;
    public $customerId;
    public $arID;


    protected $messages =
    [
        'employeeId.required_without_all' => 'Either Employee or Customer for Payee must be selected.',
        'customerId.required_without_all' => 'Either Employee or Customer for Payee must be selected.',

    ];

    public function render()
    {
        return view('livewire.transactions.petty-cash-voucher-create');
    }

    public function mount(Request $request){
        if($request->has('PCV-id')){
            $this->fetchData();
            $this->loadExistingPCV($request->input('PCV-id'));
            $this->isCreate = false;
        }else{
            $this->fetchData();
            $this->saveAsStatus = 'DRAFT';
            $this->currentPCVStatus = 'DRAFT';
            $this->isCreate = true;
        }
    }

    public function fetchData(){
        $this->transactionTypes = AccountType::where('company_id', auth()->user()->branch->company_id)->where('is_active', true)->get();
        $this->acknowledgementReceipts = AcknowledgementReceipt::where('branch_id', auth()->user()->branch->id)->where('status', 'OPEN')->get();
        $this->employees = Employee::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->get();
        $this->customers = Customer::where('branch_id', auth()->user()->branch->id)->get();
    }

    public function loadExistingPCV($id){
        $pcv = PettyCashVoucher::find($id);
        if(!$pcv){
            $this->dispatch('showAlert', ['type' => 'error','title' => 'Error', 'message' => 'PCV not found.']);
            return;
        }
        $this->pcvId = $id;
        $this->voucherSeriesNumber = $pcv->voucher_number;
        $this->note = $pcv->purpose;
        $this->totalAmount = $pcv->total_amount;
        $this->saveAsStatus = $pcv->status;
        $this->currentPCVStatus = $pcv->status;
        $this->selectedTransactionTypeID = $pcv->account_types_id;
        $this->transactions = COATransactionTemplate::where('transaction_type', $this->selectedTransactionTypeID)->where('is_active', true)->where('company_id', auth()->user()->branch->company_id)->get();
        $this->selectAcknowledgementReceipt($pcv->acknowledgement_receipt_id);
        $this->selectedTemplate = COATransactionTemplate::where('template_name', $pcv->transaction_title)->first();
        if($pcv->paid_to_employee_id){
            $this->selectedEmployee = Employee::find($pcv->paid_to_employee_id);
            $this->employeeId = $pcv->paid_to_employee_id;
            $this->payeeName = $this->selectedEmployee->name . ($this->selectedEmployee->middle_name ? ' ' . $this->selectedEmployee->middle_name : '') . ($this->selectedEmployee->last_name ? ' ' . $this->selectedEmployee->last_name : '');
        }else{
            $this->selectedCustomer = Customer::find($pcv->paid_to_customer_id);
            $this->customerId = $pcv->paid_to_customer_id;
            $this->payeeName = $this->selectedCustomer->customer_fname . ($this->selectedCustomer->customer_mname ? ' ' . $this->selectedCustomer->customer_mname : '') . ($this->selectedCustomer->customer_lname ? ' ' . $this->selectedCustomer->customer_lname: '') . ($this->selectedCustomer->suffix ? ' ' . $this->selectedCustomer->suffix : '');
        }
        $sumDebit = 0;
        $sumCredit = 0;
        foreach($pcv->pcvDetails as $detail){
            $this->particulars[] = [
                'id' => $detail->id,
                'account_title_id' => $detail->transaction_title_id,
                'account_title_code' => ChartOfAccount::find($detail->transaction_title_id)->account_code ?? '',
                'account_title' => ChartOfAccount::find($detail->transaction_title_id)->account_title ?? '',
                'type' => $detail->type,
                'amount' => $detail->amount,
            ];
            if($detail->type == 'DEBIT'){
                $sumDebit += $detail->amount;
            }else{
                $sumCredit += $detail->amount;
            }
        }
        $this->debitTotal = $sumDebit;
        $this->creditTotal = $sumCredit;
        $this->totalDisburseAmount = $sumCredit; // for PCV, disburse amount is based on credit total since it's the amount being given out
    }

    
    public function selectAcknowledgementReceipt( $id ){
        $this->selectedAR = AcknowledgementReceipt::find($id);
        $this->arID = $this->selectedAR->id;
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


    public function selectCustomer( $id ){
        if($this->employeeId != null){
            $this->selectedEmployee = [];
            $this->employeeId = null;
        }
        $this->selectedCustomer = Customer::find($id);
         if(!$this->selectedCustomer){
            $this->dispatch('showAlert', ['type' => 'error','title' => 'Error', 'message' => 'Failed to add Customer.']);
            return;
        }
        $this->customerId = $id;
        $this->payeeName = $this->selectedCustomer->customer_fname . ($this->selectedCustomer->customer_mname ? ' ' . $this->selectedCustomer->customer_mname : '') . ($this->selectedCustomer->customer_lname ? ' ' . $this->selectedCustomer->customer_lname: '');
        $this->dispatch('closePayeeLists');
       
        }

    public function selectEmployee( $id ){
        if($this->customerId){
            $this->customerId = null;
            $this->selectedCustomer = [];

        }
        $this->selectedEmployee = Employee::find($id);
        if(!$this->selectedEmployee){
            $this->dispatch('showAlert', ['type' => 'error','title' => 'Error', 'message' => 'Failed to create Acknowledgement Receipt.']);
            return;
        }
        $this->employeeId = $id;
        $this->payeeName = $this->selectedEmployee->name . ($this->selectedEmployee->middle_name ? ' ' . $this->selectedEmployee->middle_name : '') . ($this->selectedEmployee->last_name ? ' ' . $this->selectedEmployee->last_name : '');
        $this->dispatch('closePayeeLists');
    }


    public function savePCV(){
        $this->validate([
            'selectedTransactionTypeID' => 'required|exists:actng_account_types,id',
            'selectedTemplate' => 'required',
            'arID' => 'required|exists:acknowledgement_receipts,id',
            'voucherSeriesNumber' => 'required',
            'employeeId' => 'required_without_all:selectedCustomer|nullable|exists:employees,id',
            'customerId' => 'required_without_all:selectedEmployee|nullable|exists:customers,id',
            'note' => 'nullable|string|max:500',
            'saveAsStatus' => 'required|in:DRAFT,OPEN,',
            'transactions' => 'required',
            'totalDisburseAmount' => 'required|min:1'
        ]);
        
        $curYear = now()->year;
        $branchId = auth()->user()->branch_id;
        $yearlyCount = PettyCashVoucher::where('branch_id', $branchId)
            ->whereYear('created_at', $curYear)
            ->count() + 1;
        $reference = 'PCV-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);
        $typeName = AccountType::where('id',$this->selectedTransactionTypeID)->first()->type_name;
        
        $pcv = PettyCashVoucher::create([
            'branch_id' => auth()->user()->branch_id,
            'company_id' => auth()->user()->branch->company_id,
            'acknowledgement_receipt_id' => $this->arID,
            'reference' => $reference,
            'voucher_number' => $this->voucherSeriesNumber,
            'paid_to_employee_id' => $this->employeeId,
            'paid_to_customer_id' => $this->customerId,
            'total_amount' => $this->totalDisburseAmount,
            'purpose' => $this->note,
            'status' => $this->saveAsStatus,
            'created_by' => auth()->user()->emp_id,
            'created_at' => Carbon::now('Asia/Manila'),
            'account_types_id' => $this->selectedTransactionTypeID,
            'account_type' => $typeName,
            'transaction_title' => $this->selectedTemplate->template_name,

        ]);

        //saving details
        foreach($this->particulars as $particular){
            $pcvDetail = new PCVDetail();
            $pcvDetail->petty_cash_voucher_id = $pcv->id;
            $pcvDetail->transaction_title = $particular['account_title'];
            $pcvDetail->transaction_title_id = $particular['account_title_id'];
            $pcvDetail->type = $particular['type'];
            $pcvDetail->amount = $particular['amount'];
            $pcvDetail->created_at = Carbon::now('Asia/Manila');
            $pcvDetail->updated_at = Carbon::now('Asia/Manila');
            $pcvDetail->save();
        }

        $this->dispatch('showAlert', ['type' => 'success','title' =>'Success', 'message' => 'PCV saved successfully.']);

    }

    public function updatePCV(){
        $this->validate([
            'selectedTransactionTypeID' => 'required|exists:actng_account_types,id',
            'selectedTemplate' => 'required',
            'arID' => 'required|exists:acknowledgement_receipts,id',
            'voucherSeriesNumber' => 'required',
            'employeeId' => 'required_without_all:selectedCustomer|nullable|exists:employees,id',
            'customerId' => 'required_without_all:selectedEmployee|nullable|exists:customers,id',
            'note' => 'nullable|string|max:500',
            'saveAsStatus' => 'required|in:DRAFT,OPEN,',
            'transactions' => 'required',
            'totalDisburseAmount' => 'required|min:1'
        ]);

        $pcv = PettyCashVoucher::find($this->pcvId)->update([
            'acknowledgement_receipt_id' => $this->arID,
            'voucher_number' => $this->voucherSeriesNumber,
            'paid_to_employee_id' => $this->employeeId,
            'paid_to_customer_id' => $this->customerId,
            'total_amount' => $this->totalDisburseAmount,
            'purpose' => $this->note,
            'status' => $this->saveAsStatus,
            'updated_by' => auth()->user()->emp_id,
            'updated_at' => Carbon::now('Asia/Manila'),
            'account_types_id' => $this->selectedTransactionTypeID,
            'account_type' => AccountType::where('id',$this->selectedTransactionTypeID)->first()->type_name,
            'transaction_title' => $this->selectedTemplate->template_name,

        ]);

        //updating details
        foreach($this->particulars as $particular){
            $pcvDetail = PCVDetail::find($particular['id']);
            if($pcvDetail){
                $pcvDetail->transaction_title = $particular['account_title'];
                $pcvDetail->transaction_title_id = $particular['account_title_id'];
                $pcvDetail->type = $particular['type'];
                $pcvDetail->amount = $particular['amount'];
                $pcvDetail->updated_at = Carbon::now('Asia/Manila');
                $pcvDetail->save();
            }
        }
        $this->currentPCVStatus = $this->saveAsStatus; // update current status to reflect changes in the UI
        $this->dispatch('showAlert', ['type' => 'success','title' =>'Success', 'message' => 'PCV updated successfully.']);
    }

}
