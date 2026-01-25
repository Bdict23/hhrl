<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\CashierShift;
use App\Models\Denomination;
use App\Models\ShiftDenomination;
use Illuminate\Http\Request;
use App\Models\Payment;

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

    public function render()
    {
        return view('livewire.transactions.close-cashier-shift');
    }


    public function mount( Request $request)
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
            }else{
                $this->cashierShift = CashierShift::find($request->input('shift'));
            }
            
        $this->billDenominations = Denomination::where('type', 'BILL')->orderBy('value', 'desc')->get();
        $this->coinDenominations = Denomination::where('type', 'COIN')->orderBy('value', 'desc')->get();
        }else{
            return redirect()->to('/shifts-summary');
        }
        
        
    }

    public function setBeginningBalance()
    {
        $this->totalBeginningBalance = $this->cashierShift->starting_cash;
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
    }

    public function getPaymentTotal()
    {
        if($this->isClosed){
        $paymentTotal = Payment::where('branch_id', auth()->user()->branch_id)
            ->where('prepared_by', $this->cashierShift->cashier_id)->where('type', 'SALES')
            ->whereBetween('created_at', [$this->cashierShift->shift_started, $this->cashierShift->shift_ended])
            ->sum('amount');
    }else{
        $paymentTotal = Payment::where('branch_id', auth()->user()->branch_id)
            ->where('prepared_by', $this->cashierShift->cashier_id)->where('type', 'SALES')
            ->whereBetween('created_at', [$this->cashierShift->shift_started, now()])
            ->sum('amount');
    }
        return $paymentTotal;
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
        ]);

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Cashier shift closed successfully.']);
    }
    catch (\Exception $e) {
         $this->dispatch('alert', ['type' => 'error', 'message' =>  $e->getMessage()]);
    }
    }
}
