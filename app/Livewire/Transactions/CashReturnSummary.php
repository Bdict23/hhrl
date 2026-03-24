<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\CashReturn;

class CashReturnSummary extends Component
{

// fetched data
public $cashReturns = [];
// filters
 public $statusCheckValue = 'ALL';
 public $fromDate;
 public $toDate;
    public function render()
    {
        return view('livewire.transactions.cash-return-summary');
    }

    public function mount(){
        $this->fetchData();
    }

    public function fetchData(){
        $this->cashReturns = CashReturn::where('branch_id', auth()->user()->branch_id)->get();
    }

    public function search()
    {
        // Implement your search logic here based on the selected status and date range
        // You can emit an event or update a property to trigger the search results to be displayed
    }
}
