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
use App\Models\BanquetEvent;
use App\Models\AdvancesForLiquidation;
use App\Models\BanquetProcurement;



class PettyCashVoucherCreate extends Component
{
    public $saveAsStatus; // save as button
    public $currentPCVStatus = 'DRAFT'; // current status of the PCV
    public $isCreate; // check if edit or create new
    public $aflBalance = 0; // balance of the selected advance for liquidation, used to compare with total disburse amount to prevent over-disbursement

    // data
    public $particulars; 
    public $acknowledgementReceipts = [];
    public $validEventIds = [];
    public $events = [];
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
    public $selectedEvent = null;

    // inputs
    public $voucherSeriesNumber;
    public $note;
    public $employeeId;
    public $customerId;
    public $eventId;
    public $advanceForLiquidationId;


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
         $totalReceived = 0;
        $totalReturned = 0;
        $this->transactionTypes = AccountType::where('company_id', auth()->user()->branch->company_id)->where('is_active', true)->get();
        $this->acknowledgementReceipts = AcknowledgementReceipt::where('branch_id', auth()->user()->branch->id)->where('status', 'OPEN')->get();
        $this->employees = Employee::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->get();
        $this->customers = Customer::where('branch_id', auth()->user()->branch->id)->get();
        $this->validEventIds = BanquetProcurement::where('branch_id', auth()->user()->branch_id)->where('status', 'APPROVED')->pluck('event_id')->toArray();
        $this->events = BanquetEvent::whereIn('id', $this->validEventIds)
                                    ->where('end_date', '>=', Carbon::now('Asia/Manila'))->get();
        $this->advanceForLiquidationId = AdvancesForLiquidation::where('branch_id', auth()->user()->branch_id)->where('status', 'OPEN')->where('created_at', '<=', Carbon::now('Asia/Manila'))->pluck('id')->toArray();
        if($this->advanceForLiquidationId){
            $totalReceived = AdvancesForLiquidation::where('branch_id', auth()->user()->branch_id)->whereIn('id', $this->advanceForLiquidationId)->sum('amount_received') ?? 0;
            $totalReturned = AdvancesForLiquidation::where('branch_id', auth()->user()->branch_id)->whereIn('id', $this->advanceForLiquidationId)->sum('amount_returned') ?? 0;
        }
        $this->aflBalance = $totalReceived - $totalReturned; // calculate total balance of all open AFLs to compare with total disburse amount in PCV to prevent over-disbursement
        if($this->aflBalance <= 0){
            $this->dispatch('showAlert', ['timer' => 10000, 'type' => 'warning','title' => 'No AFL Available', 'message' => 'There are no available AFL to be liquidated. Please check your Advances for Liquidation or create a new one.']);
        }
      }

    public function loadExistingPCV($id){
        $pcv = PettyCashVoucher::find($id);
        if(!$pcv){
            $this->dispatch('showAlert', ['timer' => 10000,'type' => 'error','title' => 'Error', 'message' => 'PCV not found.']);
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
        $this->selectEvent($pcv->event_id);
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

    
    public function selectEvent( $id ){
        $this->selectedEvent = BanquetEvent::find($id);
        if(!$this->selectedEvent){
            $this->dispatch('showAlert', ['timer' => 10000,'type' => 'error','title' => 'Error', 'message' => 'Failed to select Event.']);
            return;
        }
        $this->eventId = $this->selectedEvent->id;
        $this->dispatch('closeEventLists');

    }
    public function selectTransaction( $id ){
        $this->selectedTemplate = COATransactionTemplate::find($id);
        $this->particulars = []; // reset particulars
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
             $this->dispatch('showAlert', ['timer' => 10000, 'type' => 'warning', 'message' => 'Please select Transaction type first.', 'title' => 'warning']);
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
            $this->dispatch('showAlert', ['timer' => 10000, 'type' => 'error','title' => 'Error', 'message' => 'Failed to add Customer.']);
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
            $this->dispatch('showAlert', ['timer' => 10000, 'type' => 'error','title' => 'Error', 'message' => 'Failed to create Acknowledgement Receipt.']);
            return;
        }
        $this->employeeId = $id;
        $this->payeeName = $this->selectedEmployee->name . ($this->selectedEmployee->middle_name ? ' ' . $this->selectedEmployee->middle_name : '') . ($this->selectedEmployee->last_name ? ' ' . $this->selectedEmployee->last_name : '');
        $this->dispatch('closePayeeLists');
    }


    public function savePCV(){
        if(!$this->advanceForLiquidationId){
            $this->dispatch('showAlert', ['timer' => 10000, 'type' => 'error', 'title' => 'No Advances for Liquidation', 'message' => 'There are no Advances for Liquidation available. Please create an Advance for Liquidation first before creating a PCV.']);
            return;
        }
        $this->validate([
            'selectedTransactionTypeID' => 'required|exists:actng_account_types,id',
            'selectedTemplate' => 'required',
            'voucherSeriesNumber' => 'required',
            'employeeId' => 'required_without_all:selectedCustomer|nullable|exists:employees,id',
            'customerId' => 'required_without_all:selectedEmployee|nullable|exists:customers,id',
            'note' => 'nullable|string|max:500',
            'saveAsStatus' => 'required|in:DRAFT,OPEN,',
            'transactions' => 'required',
            'totalDisburseAmount' => 'required|min:1',
            'eventId' => 'nullable|exists:banquet_events,id',
        ]);

        if($this->totalDisburseAmount > $this->aflBalance){
            $this->dispatch('showAlert', ['timer' => 10000, 'type' => 'error', 'title' => 'PCV Amount Exceeds AFL Amount', 'message' => 'The total PCV amount (' . $this->totalDisburseAmount . ') exceeds the available AFL amount (' . $this->aflBalance . '.) Please adjust the amount.']);
            return;
        }
        
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
            'template_id' => $this->selectedTemplate->id,
            'account_type' => $typeName,
            'transaction_title' => $this->selectedTemplate->template_name,
            'event_id' => $this->eventId,
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

        $this->dispatch('showAlert', ['timer' => 5000, 'type' => 'success','title' =>'Success', 'message' => 'PCV saved successfully.']);

    }

    public function updatePCV(){
        $this->validate([
            'selectedTransactionTypeID' => 'required|exists:actng_account_types,id',
            'selectedTemplate' => 'required',
            'voucherSeriesNumber' => 'required',
            'employeeId' => 'required_without_all:selectedCustomer|nullable|exists:employees,id',
            'customerId' => 'required_without_all:selectedEmployee|nullable|exists:customers,id',
            'note' => 'nullable|string|max:500',
            'saveAsStatus' => 'required|in:DRAFT,OPEN,',
            'transactions' => 'required',
            'totalDisburseAmount' => 'required|min:1',
            'eventId' => 'nullable|exists:banquet_events,id',
        ]);

        $pcv = PettyCashVoucher::find($this->pcvId)->update([
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
            'event_id' => $this->eventId,
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
        $this->dispatch('showAlert', ['timer' => 5000, 'type' => 'success','title' =>'Success', 'message' => 'PCV updated successfully.']);
    }

}
