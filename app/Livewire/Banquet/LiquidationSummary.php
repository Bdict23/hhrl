<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use WireUi\Traits\WireUiActions;
use App\Models\EventLiquidation;

class LiquidationSummary extends Component
{
    use WireUiActions;

    public $liquidationData = [];
    public $from_date;
    public $to_date;

    public function render()
    {
        return view('livewire.banquet.liquidation-summary');
    }

    public function mount()
    {
        $this->fetchData();
    }
    public function fetchData()
    {
        // Fetch liquidation data based on the user's branch and permissions
        $this->liquidationData = EventLiquidation::where('branch_id', auth()->user()->branch_id)->get();
    }

    public function filterLiquidationByDate()
    {
        $query = EventLiquidation::where('branch_id', auth()->user()->branch_id);

        if ($this->from_date) {
            $query->whereDate('created_at', '>=', $this->from_date);
        }

        if ($this->to_date) {
            $query->whereDate('created_at', '<=', $this->to_date);
        }

        $this->liquidationData = $query->get();
    }
}
