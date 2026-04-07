<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Denomination;
use App\Models\Cashflow;
use App\Models\CashflowDetail;
use App\Models\CashflowAccountTitle;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentType;
use Carbon\Carbon;
use WireUi\Traits\WireUiActions;
use App\Models\CashierShift;
use App\Models\AdvancesForLiquidation;
use App\Models\AcknowledgementReceipt;
use App\Models\Module;
use App\Models\Signatory;
use App\Models\CashflowDenomination;
use App\Models\Employee;


class CashFlowCreate extends Component
{
    use WireUiActions;

    public $billDenominations = [];
    public $coinDenominations = [];
    public $collectionTitles = [];
    public $lessTitles = [];

    public $denominationCounts = []; // for cash breakdown updated inputs
    public $collectionAmount = []; // for collection titles updated inputs
    public $lessAmount = []; // for less titles updated inputs

    public $collectionSubAmount; // for collection titles total total (display only)
    public $lessSubAmount; // for less titles total (display only)
    public $billSubTotal; // for bill denominations total (display only)
    public $coinSubTotal; // for coin denominations total (display only)

    public $cashFlow;

    //mount data
    public $cashflowDate;
    public $status;
    public $hasOpenShift = false;
    // entries
    public $referenceNumber;
    public $acknowledgementReceipts;
    public $pdcChecks; //not available (provisional receipt)
    public $curChecks; //not available (provisional receipt)
    public $approvers;
    public $approver_id;
    public $notes;
    public $totalEndingBalance = 0;
    public $remarks = null;

    // display titles
        //revenue
        public $restoRevenue= 0.00;
        public $beoRevenue = 0.00;
        public $salesOrderRevenue = 0.00;
        public $gateEntrance = 0.00;
        //less
        public $afl = 0.00;
        public $otherPayments = 0.00;
        public $discounts = 0.00;
        public $refund = 0.00;
        public $crs = 0.00;

    // grand totals
    public $grandTotalCollection = 0.00;
    public $grandTotalLess = 0.00;
    public $netCollection = 0.00;
    public $cashOnHand = 0.00;

    // sub totals (table)
    public $totalLess = 0.00;
    public $totalCollection = 0.00;

    public function render()
    {
        return view('livewire.transactions.cash-flow-create');
    }

    public function mount($id = null)
    {
        if(auth()->user()->employee->getModulePermission('Cash Flow') == 1) {
            if ($id) {
                $this->cashFlow = Cashflow::find($id);
                if (!$this->cashFlow) {
                    // NO ACCESS RIGHTS
                    return redirect()->route('cash_flow.summary');
                }
                // VIEW CASHFLOW DETAILS
                $this->cashflowDate = $this->cashFlow->created_at->format('M. d, Y');
                $this->status = $this->cashFlow->status;
                $this->notes =  $this->cashFlow->notes; 
                $this->approver_id = $this->cashFlow->approver_id;
                $this->remarks = $this->cashFlow->remarks;
                 $this->fetchData();
                 $this->viewCashFlowDetails();
            } else {
                if($this->hasCashflow()){
                   $this->notify('Error Occured', 'error', 'There is currently an open cashflow for today.');
                    return;
                }
                // GENERATE NEW CASHFLOW 
                $this->cashflowDate = date('M. d, Y');
                $this->status = 'NEW';
                $this->fetchData();
            }
        } else {
            return redirect()->route('cash_flow.summary');
        }
       

    }
    
    //INIT
    private function fetchData()
    {
        if($this->status != 'NEW'){
            // Gamit og pluck()->toArray() para makuha tanang IDs, dili lang ang una
            $savedTitleIds = $this->cashFlow->title->pluck('account_title_id')->toArray();

            // Siguroha nga dili empty ang array para dili mag-error ang query
            if (!empty($savedTitleIds)) {
                $this->collectionTitles = CashflowAccountTitle::where('branch_id', auth()->user()->branch_id)
                    ->where('type', 'COLLECTION')
                    ->whereIn('id', $savedTitleIds)
                    ->get();

                $this->lessTitles = CashflowAccountTitle::where('branch_id', auth()->user()->branch_id)
                    ->where('type', 'LESS')
                    ->whereIn('id', $savedTitleIds)
                    ->get();
            } else {
                // Fallback kung pananglitan naay record pero walay titles (empty collections)
                $this->collectionTitles = collect();
                $this->lessTitles = collect();
            }
        } else {
            // Para sa NEW records, i-load tanang ACTIVE titles
            $this->collectionTitles = CashflowAccountTitle::where('branch_id', auth()->user()->branch_id)
                ->where('type', 'COLLECTION')
                ->where('status', 'ACTIVE')
                ->get();

            $this->lessTitles = CashflowAccountTitle::where('branch_id', auth()->user()->branch_id)
                ->where('type', 'LESS')
                ->where('status', 'ACTIVE')
                ->get();
        }

        $this->billDenominations = Denomination::where('type', 'BILL')->orderBy('value', 'desc')->get();
        $this->coinDenominations = Denomination::where('type', 'COIN')->orderBy('value', 'desc')->get();
        $shift = CashierShift::where('branch_id', auth()->user()->branch_id)->where('shift_status', 'OPEN')->first();
        if($shift){
            $this->hasOpenShift = true;
        }
        if($this->status == 'NEW'){
            $this->curChecks = AcknowledgementReceipt::where('branch_id', auth()->user()->branch_id)
                ->where('status', 'OPEN')
                ->where('check_status', 'CURRENT')
                ->whereDate('check_date', Carbon::today())->get();
            $this->pdcChecks = AcknowledgementReceipt::where('branch_id', auth()->user()->branch_id)
                ->where('status', 'OPEN')
                ->where('check_status', 'POST-DATED')
                ->whereDate('check_date', Carbon::today())->get();
        }else{
            $this->curChecks = AcknowledgementReceipt::where('branch_id', auth()->user()->branch_id)
                ->where('status', 'CLOSED')
                ->where('check_status', 'CURRENT')
                ->whereDate('check_date', $this->cashFlow->created_at->format('Y-m-d'))->get();
            $this->pdcChecks = AcknowledgementReceipt::where('branch_id', auth()->user()->branch_id)
                ->where('status', 'CLOSED')
                ->where('check_status', 'POST-DATED')
                ->whereDate('check_date', $this->cashFlow->created_at->format('Y-m-d'))->get();

        }

            $moduleid = Module::where('module_name', 'Cash Flow')->first()->id;
            $this->approvers = Signatory::with('employees')
            ->where('branch_id', auth()->user()->branch_id)
            ->where('module_id', $moduleid)
            ->where('signatory_type', 'APPROVER')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->employees->id,
                    'full_name' => $user->employees->name . ' ' . $user->employees->middle_name. ' ' . $user->employees->last_name,
                ];
            });
        
    }

    // UPDATED FUNCTION
    public function updated($field)
    {
        
        if (str_starts_with($field, 'denominationCounts.')) {
            $this->calculateBreakDownTotal();
        }
         if (str_starts_with($field, 'collectionAmount.')) {
            $this->calculateCollectionTotal();
        }
        if (str_starts_with($field, 'lessAmount.')) {
            $this->calculateLessTotal();
        }

    }

    private function calculateCollectionTotal()
    {
     $totalCollection = 0;
        foreach ($this->collectionTitles as $title) {
            $amount = floatval($this->collectionAmount[$title->id] ?? 0);
            $totalCollection += $amount;
        }
        $this->collectionSubAmount = $totalCollection;
        $this->grandTotalCollection = $this->collectionSubAmount;
        $this->netCollection = $this->grandTotalCollection - $this->grandTotalLess;
    }
    private function calculateLessTotal()
    {
        $totalLess = 0;
        foreach ($this->lessTitles as $title) {
            $amount = floatval($this->lessAmount[$title->id] ?? 0);
            $totalLess += $amount;
        }
        $this->lessSubAmount = $totalLess;
        $this->grandTotalLess = $this->lessSubAmount;
        $this->netCollection = $this->grandTotalCollection - $this->grandTotalLess;

    }
    private function calculateBreakDownTotal()
    {
        $total = 0;
        $billTotal = 0;
        $coinTotal = 0;

        foreach ($this->billDenominations as $denomination) {
            $count = floatval($this->denominationCounts[$denomination->id] ?? 0);
            $billTotal += $count * floatval($denomination->value);
        }
        $this->billSubTotal = $billTotal;
        

        // Calculate coins total
        foreach ($this->coinDenominations as $denomination) {
            $count = floatval($this->denominationCounts[$denomination->id] ?? 0);
            $coinTotal += $count * floatval($denomination->value);
        }
        $this->coinSubTotal = $coinTotal;
        $this->billSubTotal = $billTotal;
    }



    //SUM AFL
    private function calculateAFL()
    {
        $returned = AdvancesForLiquidation::where('branch_id', auth()->user()->branch_id)
            ->where('status', 'CLOSED')
            ->whereDate('created_at', $this->cashFlow->created_at->format('Y-m-d'))
            ->sum('amount_returned');

        $received = AdvancesForLiquidation::where('branch_id', auth()->user()->branch_id)
            ->where('status', 'CLOSED')
            ->whereDate('created_at', $this->cashFlow->created_at->format('Y-m-d'))
            ->sum('amount_received');
        return $received - $returned;
    }

    //SUM SALES
    private function calculatePayments()
    {
        $total = Payment::where('branch_id', auth()->user()->branch_id)
            ->where('type', 'SALES')
            ->whereDate('created_at', $this->cashFlow->created_at->format('Y-m-d'))
            ->sum('amount');
        return $total;
    }

    // SUM ONLINE PAYMENTS
    private function calculateOnlinePayments(){
        $total = Payments::where('branch_id', auth()->user()->branch_id)
            ->where('type', 'SALES')
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');
            return $total;
    }

    //SAVE
    public function saveCashflow(){
        
        if($this->hasCashflow()){
           $this->notify('Cannot Save', 'warning', 'Only one cashflow per day can be saved.');
            return;
        }
        $this->validate([
            'approver_id' => 'required|exists:employees,id',
            'notes'       => 'nullable|string|max:255',

        ]);
        $currentYear = now()->year;
        $branchId = auth()->user()->branch_id;
        $yearlyCount = Cashflow::where('branch_id', $branchId)
            ->whereYear('created_at', $currentYear)
            ->count();
        $this->referenceNumber = 'CF-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);
        $cashflow = Cashflow::create([
            'branch_id' => auth()->user()->branch_id,
            'reference' =>   $this->referenceNumber,
            'status' => 'OPEN',
            'created_by' => auth()->user()->emp_id,
            'amount' => 0,
            'approver_id' => $this->approver_id,
            'notes'        => $this->notes,
            'created_at' => Carbon::now('Asia/Manila'),
            
        ]);
        // record collections
        foreach($this->collectionTitles as $title){
            if(floatval($this->collectionAmount[$title->id] ?? 0) > 0){
            $cfDetail = CashflowDetail::create([
                'branch_id' => auth()->user()->branch_id,
                'cashflow_id' => $cashflow->id,
                'account_title_id' => $title->id,
                'amount' => floatval($this->collectionAmount[$title->id] ?? 0),
                'created_at' => Carbon::now('Asia/Manila'),
            ]);}
        }
        // record less
        foreach($this->lessTitles as $title){
            if(floatval($this->lessAmount[$title->id] ?? 0) > 0){
            $cfDetail = CashflowDetail::create([
                'branch_id' => auth()->user()->branch_id,
                'cashflow_id' => $cashflow->id,
                'account_title_id' => $title->id,
                'amount' => floatval($this->lessAmount[$title->id] ?? 0),
                'created_at' => Carbon::now('Asia/Manila'),
            ]);
            }
        }

        // record breakdown
        foreach($this->billDenominations as $denomination){
            $count = $this->denominationCounts[$denomination->id] ?? 0;
            if($count > 0){
                $cfDetail = CashflowDenomination::create([
                    'cashflow_id' => $cashflow->id,
                    'denomination_id' => $denomination->id,
                    'quantity' => $count,
                    'amount' => $count * floatval($denomination->value),
                    'created_at' => Carbon::now('Asia/Manila'),
                ]);
            }
           
        }
        foreach($this->coinDenominations as $denomination){
            $count = $this->denominationCounts[$denomination->id] ?? 0;
            if($count > 0){
                $cfDetail = CashflowDenomination::create([
                    'cashflow_id' => $cashflow->id,
                    'denomination_id' => $denomination->id,
                    'quantity' => $count,
                    'amount' => $count * floatval($denomination->value),
                    'created_at' => Carbon::now('Asia/Manila'),
                ]);
            }
            
        }
        //update Acknowledgement
        $updateAR = AcknowledgementReceipt::where('branch_id', auth()->user()->branch_id)
            ->where('status', 'OPEN')
            ->whereDate('check_date', Carbon::today())->first();
            if($updateAR){
        $updateAR->update([
            'status' => 'CLOSED'
        ]);
            }
        //UPDATE AFL
        $updateAFL = AdvancesForLiquidation::where('branch_id', auth()->user()->branch_id)
            ->where('status', 'OPEN')
            ->whereDate('created_at', Carbon::today())->first();
            if($updateAFL){
                $updateAFL->update([
                    'status' => 'CLOSED'
                ]);
            }

        $this->status = 'OPEN';
        $this->cashFlow = CashFlow::where('id', $cashflow->id)->first();
        $this->fetchData();
        $this->viewCashFlowDetails();
        $this->notify('Cashflow Created','success','Cashflow Reference: ' . $this->referenceNumber);


    }

    public function viewCashFlowDetails(){
        $breakdowns = CashflowDenomination::where('cashflow_id', $this->cashFlow->id)->get();
        $afl = $this->calculateAFL();
        $resto = $this->calculatePayments();
        

        if($afl > 0){
            $this->afl = $afl;
        }
        if($resto > 0){
            $this->restoRevenue = $resto;
        }
        foreach($this->billDenominations as $bill){
            $this->denominationCounts[$bill->id] = $breakdowns->where('denomination_id', $bill->id)->pluck('quantity')->first() ?? 0;
        }
        foreach($this->coinDenominations as $coin){
                $this->denominationCounts[$coin->id] = $breakdowns->where('denomination_id', $coin->id)->pluck('quantity')->first() ?? 0;

        }
        foreach($this->collectionTitles as $title){
            $this->collectionAmount[$title->id] = $this->cashFlow->title->where('account_title_id', $title->id)->pluck('amount')->first() ?? 0;
        }
        foreach($this->lessTitles as $title){
            $this->lessAmount[$title->id] = $this->cashFlow->title->where('account_title_id', $title->id)->pluck('amount')->first() ?? 0;
        }
    }


    public function notify( $title , $icon, $description){
            $this->notification()->send([
                'title'       => $title,
                'description' => $description,
                'icon'        => $icon,
            ]);
    }


    public function hasCashflow(){
        $checkCashFlow = CashFlow::whereDate('created_at', Carbon::today() )->where('branch_id', auth()->user()->branch_id)->first();
        if($checkCashFlow){
            return true;
         }else{
            return false;
         }
    }

    
}
