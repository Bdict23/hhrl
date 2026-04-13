<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use WireUi\Traits\WireUiActions;
use App\Models\EventLiquidation;

class LiquidationApprovalLists extends Component
{
    use WireUiActions;


    public $liquidationData = [];
    public $from_date;
    public $to_date;

    public function render()
    {
        return view('livewire.banquet.liquidation-approval-lists');
    }

       public function mount()
    {
        $this->fetchData();
    }
    public function fetchData()
    {
        // Fetch liquidation data based on the user's branch and permissions
        $this->liquidationData = EventLiquidation::where('branch_id', auth()->user()->branch_id)->where('approved_by', auth()->user()->emp_id)->where('approved_date', null)->get();
    }

    public function filterLiquidationByDate()
    {
        $query = EventLiquidation::where('branch_id', auth()->user()->branch_id)->where('approved_date', null)->where('approved_by', auth()->user()->emp_id);

        if ($this->from_date) {
            $query->whereDate('created_at', '>=', $this->from_date);
        }

        if ($this->to_date) {
            $query->whereDate('created_at', '<=', $this->to_date);
        }

        $this->liquidationData = $query->get();
    }
}
