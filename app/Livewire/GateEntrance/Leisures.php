<?php

namespace App\Livewire\GateEntrance;

use Livewire\Component;
use App\Models\Leisure;

class Leisures extends Component
{

    public $leisures;
    public $leisure_id;
    public $name;
    public $description;
    public $amount;
    public $status;
    public $branch_id;

    function mount()
    {
       $this->refresh();
    }
    public function refresh(){
        $this->leisures = Leisure::all();
        $this->status = 1;
        $this->branch_id = 1;
    }
    public function createLeisure()
    {
        try {
            $this->validate([
                'name' => 'required',
                'description' => 'required',
                'amount' => 'required|numeric',
            ]);

            Leisure::create([
                'name' => $this->name,
                'description' => $this->description,
                'amount' => (double) $this->amount,
                'status' => $this->status,
                'branch_id' => $this->branch_id,
            ]);

            $this->reset();
            $this->refresh();

            session()->flash('message', 'Leisure created successfully.');

        }catch (\Exception $e) {
            session()->flash('message', 'Failed to create leisure: ' . $e->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.gate-entrance.leisures');
    }

    public function editLeisure($id)
    {
        $leisure = Leisure::find($id);
        if ($leisure) {
            $this->leisure_id = $leisure->id;
            $this->name = $leisure->name;
            $this->description = $leisure->description;
            $this->amount = $leisure->amount;
            $this->status = $leisure->status;
            $this->branch_id = $leisure->branch_id;
        }
    }
         

    public function updateLeisure()
    {
        try {
            $this->validate([
                'name' => 'required',
                'description' => 'required',
                'amount' => 'required|numeric',
            ]);

            $leisure = Leisure::find($this->leisure_id);
            $leisure->update([
                'name' => $this->name,
                'description' => $this->description,
                'amount' => (double) $this->amount,
                'status' => $this->status,
                'branch_id' => $this->branch_id,
            ]);

            $this->reset();
            $this->refresh();


            session()->flash('message', 'Leisure updated successfully.');

        }catch (\Exception $e) {
            session()->flash('message', 'Failed to update leisure: ' . $e->getMessage());
        }
    }

    public function deactivateLeisure($id)
    {
        try {
            $leisure = Leisure::find($id);
            if ($leisure) {
                $leisure->update(['status' => 0]);
                session()->flash('message', 'Leisure deactivated successfully.');
            } else {
                session()->flash('message', 'Leisure not found.');
            }
        } catch (\Exception $e) {
            session()->flash('message', 'Failed to deactivate leisure: ' . $e->getMessage());
        }
    }
}
