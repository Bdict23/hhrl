<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\CashierShift;
use App\Models\Denomination;
use App\Models\ShiftDenomination;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentType;

class CloseCashierShift extends Component
{
    public $shiftId;
    public $notes;
    public $billDenominations = [];
    public $coinDenominations = [];
    public $totalBeginningBalance = 0;
    public $totalEndingBalance = 0;
    public $totalSales = 0;
    public $denominationCounts = [];
    public $cashierShift;
    public $isClosed = false;
    public $verified = false;
    public $differenceAmount = 0;

    public $cashRefundTotal = 0;
    public $cashPaymentTotal = 0;

    public function render()
    {
        return view('livewire.transactions.close-cashier-shift');
    }


    public function mount( Request $request)
    {
        if(auth()->user()->employee->getModulePermission('Restaurant - Order Billing') == 1 )
        {
                if($request->has('shift')) {
                    if($request->has('referenced') && $request->input('referenced') == 'current') {
                        $this->cashierShift = CashierShift::where('cashier_id', auth()->user()->employee->id)
                    ->where('shift_status', 'OPEN')
                    ->first();
                    if(!$this->cashierShift){
                        return redirect()->to('/shifts-summary');
                    }
                    if($this->cashierShift->shift_status == 'CLOSED'){
                        $this->isClosed = true;
                    }
                    $this->totalSales = $this->getPaymentTotal();
                    $this->cashRefundTotal = $this->getTotalRefundCash();
                    $this->cashPaymentTotal = $this->getPaymentTotalCash();
                    $this->calculateDifferenceAmount();
                    }else{
                        $this->cashierShift = CashierShift::find($request->input('shift'));
                        $this->shiftId = $this->cashierShift?->id;
                    }
                    
                $this->billDenominations = Denomination::where('type', 'BILL')->orderBy('value', 'desc')->get();
                $this->coinDenominations = Denomination::where('type', 'COIN')->orderBy('value', 'desc')->get();
                }else{
                    return redirect()->to('/shifts-summary');
                }
            }else{
            return abort(403, 'Unauthorized action.');
            }
        
    }

    public function setBeginningBalance()
    {
        $this->totalBeginningBalance = $this->cashierShift->starting_cash;
    }

    public function updated($field)
    {
        
        if (str_starts_with($field, 'denominationCounts.')) {
            $this->calculateTotal();
            $this->calculateDifferenceAmount();
        }
    }

    public function calculateTotal()
    {
        $total = 0;

        // Calculate bills total
        foreach ($this->billDenominations as $denomination) {
            $count = floatval($this->denominationCounts[$denomination->id] ?? 0);
            $total += $count * floatval($denomination->value);
        }

        // Calculate coins total
        foreach ($this->coinDenominations as $denomination) {
            $count = floatval($this->denominationCounts[$denomination->id] ?? 0);
            $total += $count * floatval($denomination->value);
        }

        $this->totalEndingBalance = $total;
        $this->calculateDifferenceAmount();
    }

    public function getPaymentTotal()
    {
       
        $paymentTotal = Payment::where('branch_id', auth()->user()->branch_id)
            ->where('type', 'SALES')
            ->where('shift_id',$this->cashierShift->id)
            ->sum('amount') ?? 0;
    
        return $paymentTotal;
    }
    
    public function getTotalRefundCash()
    {
        $refundTotal = Payment::where('branch_id', auth()->user()->branch_id)
            ->where('type', 'REFUND')
            ->where('payment_type_id', $this->cashierShift->cashPaymentId())
            ->where('shift_id',$this->cashierShift->id)
            ->sum('amount') ?? 0;

        return $refundTotal;
    }
    public function getPaymentTotalCash()
    {
        $paymentTotal = Payment::where('branch_id', auth()->user()->branch_id)
            ->where('type', 'SALES')
            ->where('payment_type_id', $this->cashierShift->cashPaymentId())
            ->where('shift_id',$this->cashierShift->id)
            ->sum('amount') ?? 0;
        return $paymentTotal;
    }

    public function remarks()
    {
        $collection = $this->cashPaymentTotal - $this->cashRefundTotal + $this->cashierShift->starting_cash;
        $result  =   $this->totalEndingBalance - $collection;
        if($result > 0) {
            $remarks = "EXCESS";
        } elseif ($result < 0) {
            $remarks = "SHORT";
        } else {
            $remarks = "NONE";
        }

        return $remarks;
    }

    public function calculateDifferenceAmount()
    {
        $collection = $this->cashPaymentTotal - $this->cashRefundTotal + $this->cashierShift->starting_cash;
        $difference = $this->totalEndingBalance - $collection;
        $this->differenceAmount = $difference;
        
    }
    

    public function closeShift()
    {
        try {
        $this->calculateTotal();
        
        $this->validate([
            'verified' => 'accepted',
        ], [
            'verified.accepted' => 'You must verify that all the information indicated are correct before closing the shift.',
        ]);

        // save bill denominations counts
        foreach ($this->billDenominations as $denomination) {
              $count = $this->denominationCounts[$denomination->id] ?? 0;
              if($count > 0) {
            ShiftDenomination::create([
                'shift_id' => $this->cashierShift->id,
                'counter_type' => 'ENDING_CASH',
                'denomination_id' => $denomination->id,
                'amount' => floatval($denomination->value) * floatval($this->denominationCounts[$denomination->id] ?? 0),
                'quantity' => $this->denominationCounts[$denomination->id] ?? 0,
            ]);
        }
        }
        // save coin denominations counts
        foreach ($this->coinDenominations as $denomination) {
                $count = $this->denominationCounts[$denomination->id] ?? 0;
                if($count > 0) {
            ShiftDenomination::create([
                'shift_id' => $this->cashierShift->id,
                'counter_type' => 'ENDING_CASH',
                'denomination_id' => $denomination->id,
                'amount' => floatval($denomination->value) * floatval($this->denominationCounts[$denomination->id] ?? 0),
                'quantity' => $this->denominationCounts[$denomination->id] ?? 0,
            ]);
            }
        }

        // Update cashier shift
        $this->cashierShift->update([
            'shift_status' => 'CLOSED',
            'shift_ended' => now(),
            'ending_cash' => $this->totalEndingBalance,
            'notes' => $this->notes,
            'total_sales' => $this->totalSales,
            'discrepancy_status' => $this->remarks(),
        ]);

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Cashier shift closed successfully.']);
    }
    catch (\Exception $e) {
         $this->dispatch('alert', ['type' => 'error', 'message' =>  $e->getMessage()]);
    }
    }
}
