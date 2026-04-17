<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Denomination;
use App\Models\Cashflow;
use App\Models\CashflowDetail;
use App\Models\CashflowAccountTitle;
use Illuminate\Http\Request;
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
use App\Models\Payment as Payments;
use App\Models\EventDiscount;
use App\Models\OrderDiscount;
use App\Models\CashReturn;



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
    public $payments; // load all payments for the day
    public $eventDiscounts; // load event discounts
    public $orderDiscounts; // load order discounts
    public $cashPaymentTypeId; // load cash payment type id for loading other payments (non-cash)
    public $cashReturnData;
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
        public $beginningBalance = 0.00;
        public $endingBalance = 0.00;
         public $flowType = 'COLLECTION';
         public $fundStatus = 'VALIDATED';
         public $parentId = null;

        public $restaurantRevenue= 0.00;
        public $beoRevenue = 0.00;
        public $salesOrderRevenue = 0.00;
        public $gateEntrance = 0.00;
        //less
        public $afl = 0.00;
        public $otherPayments = 0.00;
        public $discounts = 0.00;
        public $refund = 0.00;
        public $cashReturnBEO = 0.00;

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

    public function mount(Request $request)
    {
         if ($request->has('cashflow-id')) {
            $id = $request->query('cashflow-id');
        } elseif ($request->route('id')) {
            $id = $request->route('id');
        } else {
            $id = null;
        }
    
        if(auth()->user()->employee->getModulePermission('Cash Flow') == 1) {
            if ($id) {
                $this->exist = true;
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
            return redirect()->route('dashboard')->with('error', 'You do not have access to Cash Flow module.');
        }
       

    }
    
    //INIT
    private function fetchData()
    {
        $savedTitles = CashflowAccountTitle::where('branch_id', auth()->user()->branch_id)->get();
        $this->cashPaymentTypeId = PaymentType::where('payment_type_name', 'CASH')->pluck('id')->first();
        if($this->status != 'NEW'){
            $this->beginningBalance = $this->cashFlow->beginning_balance;
            // get all payments data from cashflow date
            $this->payments = Payments::where('branch_id', auth()->user()->branch_id)->whereDate('created_at', $this->cashFlow->created_at->format('Y-m-d'))->get();
            $this->eventDiscounts = EventDiscount::where('branch_id', auth()->user()->branch_id)->where('status', 'APPLIED')->whereDate('created_at', $this->cashFlow->created_at->format('Y-m-d'))->get();
            $this->orderDiscounts = OrderDiscount::where('branch_id', auth()->user()->branch_id)->where('status', 'APPLIED')->whereDate('created_at', $this->cashFlow->created_at->format('Y-m-d'))->get();
            $this->cashReturnData = CashReturn::where('branch_id', auth()->user()->branch_id)
                                    ->whereDate('updated_at', $this->cashFlow->created_at->format('Y-m-d'))
                                    ->where('event_id', '!=', null)
                                    ->where('status', 'FINAL')->get();

            // Gamit og pluck()->toArray() para makuha tanang IDs, dili lang ang una
            $savedTitleIds = $this->cashFlow->title->pluck('account_title_id')->toArray();
            $titles = $savedTitles->whereIn('id', $savedTitleIds);

            // Siguroha nga dili empty ang array para dili mag-error ang query
            if (!empty($savedTitleIds)) {
                $this->collectionTitles = $titles->where('type', 'COLLECTION');

                $this->lessTitles = $titles->where('type', 'LESS');
            } else {
                // Fallback kung pananglitan naay record pero walay titles (empty collections)
                $this->collectionTitles = collect();
                $this->lessTitles = collect();
            }
        } else {
            
            $this->setBeginningBalance();
            //load payments for today
            $this->payments = Payments::where('branch_id', auth()->user()->branch_id)->whereDate('created_at', Carbon::today()->format('Y-m-d'))->get();
            $this->eventDiscounts = EventDiscount::where('branch_id', auth()->user()->branch_id)->where('status', 'APPLIED')->whereDate('created_at', Carbon::today()->format('Y-m-d'))->get();
            $this->orderDiscounts = OrderDiscount::where('branch_id', auth()->user()->branch_id)->where('status', 'APPLIED')->whereDate('created_at', Carbon::today()->format('Y-m-d'))->get();
            $this->cashReturnData = CashReturn::where('branch_id', auth()->user()->branch_id)
                                    ->whereDate('updated_at', Carbon::today()->format('Y-m-d'))
                                    ->where('event_id', '!=', null)
                                    ->where('status', 'FINAL')->get();

            // Para sa NEW records, i-load tanang ACTIVE titles
            $this->collectionTitles = $savedTitles->where('type', 'COLLECTION')->where('status', 'ACTIVE');
            $this->lessTitles = $savedTitles->where('type', 'LESS')->where('status', 'ACTIVE');
        }

        $this->billDenominations = Denomination::where('type', 'BILL')->orderBy('value', 'desc')->get();
        $this->coinDenominations = Denomination::where('type', 'COIN')->orderBy('value', 'desc')->get();
        $shift = CashierShift::where('branch_id', auth()->user()->branch_id)->where('shift_status', 'OPEN')->first();
        if($shift){
            $this->hasOpenShift = true;
        }

        if($this->status == 'NEW'){
            $loadAcknowledgementCurrentDate = AcknowledgementReceipt::where('branch_id', auth()->user()->branch_id)->whereDate('check_date', Carbon::today())->get();
            $this->curChecks = $loadAcknowledgementCurrentDate->where('status', 'OPEN')
                ->where('check_status', 'CURRENT');
            $this->pdcChecks = $loadAcknowledgementCurrentDate->where('status', 'OPEN')
                ->where('check_status', 'POST-DATED');
        }else{
            $loadAcknowledgementCashflowDate = AcknowledgementReceipt::where('branch_id', auth()->user()->branch_id)->whereDate('check_date', $this->cashFlow->created_at->format('Y-m-d'))->get();
            $this->curChecks = $loadAcknowledgementCashflowDate->where('status', 'CLOSED')
                ->where('check_status', 'CURRENT');
            $this->pdcChecks = $loadAcknowledgementCashflowDate->where('status', 'CLOSED')
                ->where('check_status', 'POST-DATED');

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

    // CALCULATE TOTAL NET COLLECTION 
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

    // CALCULATE TOTAL NET COLLECTION LESS
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

    // CALCULATE CASH ON HAND
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

    private function setBeginningBalance(){
        $lastCashFlow = Cashflow::where('branch_id', auth()->user()->branch_id)->latest()->first();
        if($lastCashFlow){
            $this->beginningBalance = $lastCashFlow->ending_balance;
            return $lastCashFlow->ending_balance;
        }else{
            $this->beginningBalance = 0.00;
            return 0.00;
        }
    }

    // LESS FUNCTIONS
        //SUM AFL
        private function calculateAFL()
        {
            $loadAFL = AdvancesForLiquidation::where('branch_id', auth()->user()->branch_id)
                ->whereDate('created_at', $this->cashFlow->created_at->format('Y-m-d'))
                ->get();
            $returned = $loadAFL->sum('amount_returned') ?? 0;
            $received = $loadAFL->sum('amount_received') ?? 0;

            return $received - $returned;
        }

        //SUM RESTO REVENUE
        private function calculateRestoRevenue()
        {
            $total = Payments::where('branch_id', auth()->user()->branch_id)
                ->where('type', 'RESTO')
                ->whereDate('created_at', $this->cashFlow->created_at->format('Y-m-d'))
                ->sum('amount');
            return $total + $this->orderDiscounts->sum('calculated_amount');
        }

        // SUM OTHER PAYMENTS
        private function calculateOtherPayments(){
            $total = $this->payments->where('payment_type_id', '!=', $this->cashPaymentTypeId)->whereNotIn('type', ['REFUND','VOID'])->sum('amount');
                return $total;
        }

        // SUM DISCOUNTS
        private function calculateDiscounts(){
            $eventDiscounts = $this->eventDiscounts->sum('amount');
            $orderDiscounts = $this->orderDiscounts->sum('calculated_amount');

            return $eventDiscounts + $orderDiscounts;
        }

        // SUM REFUNDS
        private function calculateRefunds(){
            $total = $this->payments->where('type', 'REFUND')->sum('amount');
                return $total;
        }

        // SUM CASH RETURNS BEO
        private function calculateCashReturnsBEO(){
            $total = $this->cashReturnData->sum('amount_returned');
                return $total;
        }


    // COLLECTION FUNCTIONS
        // SUM SALES        
        private function calculateSales(){
            $total = $this->payments->where('type', 'SALES')
                ->sum('amount');
                return $total;
        }
            // SUM GATE ENTRANCE
            private function calculateGateEntrance(){
                $total = $this->payments->where('type', 'ENTRANCE')
                        ->sum('amount');
                    return $total;
            }

        // BEO REVENUE
        private function calculateBEORevenue(){
            $total = $this->payments->where('type', 'BEO')
                ->sum('amount');
                return $total;
        }
        // RESTAURANT REVENUE
        private function calculateRestaurantRevenue(){
            $total = $this->payments->where('type', 'RESTAURANT_REVENUE')
                ->sum('amount');
                return $total;
        }

//SAVE
    public function saveCashflow(){
        if($this->hasOpenShift){
            $this->notify('Error Occured', 'error', 'Cannot save cashflow while there is an open shift.');
            return;
        }
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
            'beginning_balance' => $this->beginningBalance ,
            'ending_balance' => 0.00, 
            'flow_type' => $this->flowType,
            'fund_status' => $this->fundStatus,
            'parent_id' => $this->parentId,
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
        // update cashflow ending balance
        $this->cashFlow->update([
            'ending_balance' => $this->netCollection,
        ]);
        $this->notify('Cashflow Created','success','Cashflow Reference: ' . $this->referenceNumber);


    }

    public function viewCashFlowDetails(){
        $breakdowns = CashflowDenomination::where('cashflow_id', $this->cashFlow->id)->get();
        $afl = $this->calculateAFL();
        $resto = $this->calculateRestoRevenue();
        $beo = $this->calculateBEORevenue();
        $sales = $this->calculateSales();
        $gate = $this->calculateGateEntrance();
        $otherPayments = $this->calculateOtherPayments();
        $discounts = $this->calculateDiscounts();
        $refund = $this->calculateRefunds();
        $cashReturnsBEO = $this->calculateCashReturnsBEO();
        

        if($afl > 0){
            $this->afl = $afl;
        }
        if($resto > 0){
            $this->restaurantRevenue = $resto;
        }
        if($beo > 0){
            $this->beoRevenue = $beo;
        }
        if($sales > 0){
            $this->salesOrderRevenue = $sales;
        }
        if($gate > 0){
            $this->gateEntrance = $gate;
        }
        if($otherPayments > 0){
            $this->otherPayments = $otherPayments;
        }
        if($discounts > 0){
            $this->discounts = $discounts;
        }
        if($cashReturnsBEO > 0){
            $this->cashReturnBEO = $cashReturnsBEO;
        }
        if($refund > 0){
            $this->refund = $refund;
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
            $this->calculateCollectionTotal();
            $this->calculateLessTotal();
            $this->calculateBreakDownTotal();
            $this->grandTotalCollection += $resto + $beo + $sales + $gate +  $this->beginningBalance;
            $this->grandTotalLess += $afl + $otherPayments + $discounts + $refund + $cashReturnsBEO;
            $this->netCollection = ($this->grandTotalCollection - $this->grandTotalLess);
            $this->cashOnHand = $this->billSubTotal + $this->coinSubTotal + $this->beginningBalance;

            if($this->netCollection > $this->cashOnHand) {
                $this->remarks = 'EXCESS : ' . number_format($this->netCollection - $this->cashOnHand, 2);
            } else if($this->netCollection < $this->cashOnHand) {
                $this->remarks = 'SHORT : ' . number_format($this->cashOnHand - $this->netCollection, 2);
            }else {
                $this->remarks = 'BALANCED';
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
