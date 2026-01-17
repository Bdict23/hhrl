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
            ->whereBetween('created_at', [now()->toDateString() . ' 00:00:00', now()->toDateString() . ' 23:59:59'])
            ->with('employee', 'cashDrawer')
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function filterShiftsByDate()
    {
        // Logic to filter shifts by date can be implemented here
    }
}
