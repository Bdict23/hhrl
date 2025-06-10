<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Table;

class TableManagement extends Component
{
    public $tables = [];
    public $table_name_input ;
    public $table_capacity_input = 0;
    public $table_id;

    protected $rules = [
        'table_name_input' => 'required|string|max:255',
        'table_capacity_input' => 'required|integer|min:1',
    ];
    public function render()
    {
        return view('livewire.settings.table-management');
    }
    public function mount()
    {
        // Initialization logic can go here if needed
        $this->fetchData();
    }
    public function fetchData()
    {
        $this->tables = Table::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->get();
    }
    public function storeTable()
    {
        $this->validate();

        Table::create([
            'table_name' => $this->table_name_input,
            'seating_capacity' => $this->table_capacity_input,
            'branch_id' => auth()->user()->branch->id,
            'status' => 'ACTIVE',
        ]);

        $this->reset(['table_name_input', 'table_capacity_input']);
        $this->fetchData();
        $this->dispatch('resetCreateTableForm');
        session()->flash('success', 'Table created successfully.');
    }

    public function editTable($tableId)
    {
        $table = Table::findOrFail($tableId);
        $this->table_name_input = $table->table_name;
        $this->table_capacity_input = $table->seating_capacity;
        $this->table_id = $tableId;
    }

    public function updateTable()
    {
        $this->validate(
            [
                'table_name_input' => 'required|string|max:255',
                'table_capacity_input' => 'required|integer|min:1',
            ]
        );

        $table = Table::findOrFail($this->table_id);
        $table->update([
            'table_name' => $this->table_name_input,
            'seating_capacity' => $this->table_capacity_input,
        ]);

        $this->reset(['table_name_input', 'table_capacity_input']);
        $this->fetchData();
        $this->dispatch('closeUpdateTableModal');
        session()->flash('success', 'Table updated successfully.');
    }
}
