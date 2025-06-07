<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Table;

class TableManagement extends Component
{
    public $tables = [];

    public function render()
    {
        return view('livewire.settings.table-management');
    }
    public function mount()
    {
        // Initialization logic can go here if needed
    }
    public function fetchData()
    {
        $this->tables = Table::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->get();
    }
}
