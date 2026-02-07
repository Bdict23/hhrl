<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\CashierShift;
use App\Models\Denomination;
use App\Models\ShiftDenomination;
use App\Models\Payment;

class ShiftsSummary extends Component
{
    public $from_date;
    public $to_date;
    public $shifts = [];
    public $curShift;


    public $shift_status;
    public $coinDenominations = [];
    public $billDenominations = [];

    public $shiftBillDenominations = [];
    public $shiftCoinDenominations = [];

    public $totalBeginningBalance = 0;
    public $totalEndingBalance = 0;
    public $totalSales = 0;
    public $denominationCounts = [];



    public function render()
    {
        return view('livewire.transactions.shifts-summary');
    }

    public function mount()
    {
        if(auth()->user()->employee->getModulePermission('Shift Summary') != 2 ){
        $this->from_date = date('Y-m-d');
        $this->to_date = date('Y-m-d');
        $this->fetchData();
        }else{
            return abort(403, 'Unauthorized action.');
        }
    }

    public function fetchData()
    {
        $this->shifts = CashierShift::where('branch_id', auth()->user()->branch->id)
            ->whereIn('shift_status', ['OPEN','CLOSED'])
            ->with('employee', 'cashDrawer','openingShiftDenominations')
            ->orderBy('created_at', 'desc')
            ->get();
            $this->billDenominations = Denomination::where('type', 'bill')
                                    ->orderBy('value', 'desc')
                                    ->get();
            $this->coinDenominations = Denomination::where('type', 'coin')
                                    ->orderBy('value', 'desc')
                                    ->get();
    }
    public function filterShiftsByDate()
    {
       
        $this->validate([
                    'from_date' => 'required|date',
                    'to_date' => 'required|date|after_or_equal:from_date',
                ]);
        $this->shifts = CashierShift::where('branch_id', auth()->user()->branch->id)
            ->whereBetween('created_at', [$this->from_date . ' 00:00:00', $this->to_date . ' 23:59:59'])
            ->with('employee', 'cashDrawer')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function viewShiftDetails($shiftId)
    {
        $shift = CashierShift::find($shiftId);

        if ($shift) {
            $this->shift_status = $shift->shift_status;
            $this->curShift = $shift;
            // dd($this->shift->payments->sum('amount'));
            $this->dispatch('showShiftDetails');
            return;
        }
        
    }
}
