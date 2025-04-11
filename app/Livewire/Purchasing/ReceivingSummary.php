<?php

namespace App\Livewire\Purchasing;

use Livewire\Component;
use App\Models\Receiving as ReceivingModel;

class ReceivingSummary extends Component
{
    public $receivingSummaryList = [];

    public function render()
    {
        return view('livewire.purchasing.receiving-summary');
    }

    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        // Fetch data from the database
        $this->receivingSummaryList = ReceivingModel::with(['requisition',  'branch', 'company','preparedBy'])->where('branch_id', auth()->user()->branch_id)->get();
        // dd($this->receivingSummaryList);
    }
}
