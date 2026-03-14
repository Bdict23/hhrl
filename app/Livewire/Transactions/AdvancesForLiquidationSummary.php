<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\AdvancesForLiquidation;

class AdvancesForLiquidationSummary extends Component
{
    public $fromDate;
    public $toDate;
    public $advancesForLiquidation = [];


    public function mount(){
        $this->fromDate = now()->startOfMonth()->format('Y-m-d');
        $this->toDate = now()->endOfMonth()->format('Y-m-d');
        $this->fetchData();
    }

    public function render()
    {
        return view('livewire.transactions.advances-for-liquidation-summary');
    }

    public function fetchData(){
        $this->advancesForLiquidation = AdvancesForLiquidation::whereBetween('created_at', [$this->fromDate, $this->toDate])->get();
    }


    public function search(){
        // dd($this->fromDate, $this->toDate);
        $this->validate([
            'fromDate' => 'required|date',
            'toDate' => 'date|after_or_equal:fromDate',
        ]);
            if(!$this->toDate){
                $this->toDate = now()->endOfMonth()->format('Y-m-d');
            }
        if ($this->fromDate && $this->toDate) {
            $this->advancesForLiquidation = AdvancesForLiquidation::whereBetween('created_at', [$this->fromDate, $this->toDate])->get();
        }
    }
}
