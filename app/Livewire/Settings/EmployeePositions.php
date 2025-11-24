<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\EmployeePosition;
use App\Models\branch;

class EmployeePositions extends Component

{
    public $position_name_input;
    public $position_description_input;
    public $position_id;
    public $positions = [];


    public function render()
    {
        return view('livewire.settings.employee-positions');
    }

    public function fetchPositions()
    {
        $this->positions = EmployeePosition::where('branch_id', auth()->user()->branch_id)
            ->get();
    }

    public function mount(){
        $this->fetchPositions();
    }
    public function editPosition($positionId)
    {
        $position = EmployeePosition::find($positionId);
        if ($position) {
            $this->position_id = $position->id;
            $this->position_name_input = $position->position_name;
            $this->position_description_input = $position->position_description;
        }
    }

    public function storePosition()
    {
        $this->validate([
            'position_name_input' => 'required|string|max:255',
            'position_description_input' => 'nullable|string|max:500',
        ]);

        EmployeePosition::create([
            'position_name' => $this->position_name_input,
            'position_description' => $this->position_description_input,
            'position_status' => 'ACTIVE',
            'branch_id' => auth()->user()->branch_id,
        ]);

        // // Clear input fields
        // $this->position_name_input = '';
        // $this->position_description_input = '';

        // Refresh the positions list
        $this->reset();
        $this->fetchPositions();
        session()->flash('success', 'Position successfully added');
        $this->dispatch('clearForm');

    }


    public function updatePosition()
    {
        $this->validate([
            'position_name_input' => 'required|string|max:255',
            'position_description_input' => 'nullable|string|max:500',
        ]);

        $position = EmployeePosition::find($this->position_id);
        if ($position) {
            $position->update([
                'position_name' => $this->position_name_input,
                'position_description' => $this->position_description_input,
            ]);

            // Clear input fields
            $this->position_name_input = '';
            $this->position_description_input = '';

            // Refresh the positions list
            $this->reset();
            $this->fetchPositions();
            session()->flash('success', 'Position successfully updated');
            $this->dispatch('clearForm');
            $this->dispatch('hideUpdatePositionModal');


        }
    }

    public function deactivatePosition($positionId)
    {
        $position = EmployeePosition::find($positionId);
        if ($position) {
            $position->update([
                'position_status' => 'INACTIVE',
            ]);

            $this->fetchPositions();
            session()->flash('success', 'Position successfully deactivated');
        }
    }
    public function activatePosition($positionId)
    {
        $position = EmployeePosition::find($positionId);
        if ($position) {
            $position->update([
                'position_status' => 'ACTIVE',
            ]);

            $this->fetchPositions();
            session()->flash('success', 'Position successfully activated');
        }
    }

    public function refresh(){
        $this->reset();
        $this->fetchPositions();
    }
}
