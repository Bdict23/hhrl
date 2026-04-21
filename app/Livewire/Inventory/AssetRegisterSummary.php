<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\BatchProperty;


class AssetRegisterSummary extends Component
{
    public $statuses = [
        'DRAFT' => 'Draft',
        'OPEN' => 'Open',
        'CLOSED' => 'Closed',
    ];
    public $fromDate;
    public $toDate;
    public $batches;

    public function render()
    {
        return view('livewire.inventory.asset-register-summary');
    }

    public function mount(){
        $this->fetchData();
    }


    public function fetchData()
    {
        // Fetching purchase order summary data from the database
        $this->batches = BatchProperty::
        where('branch_id', auth()->user()->branch_id)
        ->where('created_at', '>=', now()->subMonths(1))
        ->get();
        $this->fromDate = now()->startOfMonth()->format('Y-m-d');
        $this->toDate = now()->format('Y-m-d');
    }


     public function search()
    {
        $query = BatchProperty::where('branch_id', auth()->user()->branch_id);

        if ($this->statuses !== "All") {
            $query->where('status', $this->statuses);
        }

        if ($this->fromDate && $this->toDate) {
            $query->whereDate('created_at', '>=', $this->fromDate)
                  ->whereDate('created_at', '<=', $this->toDate);
        }

        $this->batches = $query->get();
    }
}
