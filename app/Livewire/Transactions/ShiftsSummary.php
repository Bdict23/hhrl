<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\CashierShift;

class ShiftsSummary extends Component
{
    public $from_date;
    public $to_date;
    public $shifts = [];


    public function render()
    {
        return view('livewire.transactions.shifts-summary');
    }

    public function mount()
    {
        $this->from_date = date('Y-m-d');
        $this->to_date = date('Y-m-d');
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->shifts = CashierShift::where('branch_id', auth()->user()->branch->id)
            ->where('shift_status', 'OPEN')
            ->with('employee', 'cashDrawer')
            ->orderBy('created_at', 'desc')
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
}
