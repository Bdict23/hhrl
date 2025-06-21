<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Table;

class TableSelection extends Component
{
    public $selectedTableId;
    public $availableTables = [];
    public function render()
    {
        return view('livewire.restaurant.table-selection');
    }
    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->availableTables = Table::query()
            ->where('availability', 'vacant')
            ->orderBy('table_name')
            ->get();
    }
    public function gotoMenuSelection($tableId)
    {
        if ($tableId) {
            return redirect()->to('/my-menu?table-id=' . $tableId);
        } else {
            session()->flash('error', 'Please select a table before proceeding.');
        }
    }
}
