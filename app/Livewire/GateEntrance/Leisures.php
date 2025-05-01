<?php

namespace App\Livewire\GateEntrance;

use Livewire\Component;
use App\Models\Leisure;

class Leisures extends Component
{

    public $leisures;
    public $leisure;

    public $leisure_id;
    public $name;
    public $description;
    public $amount;
    public $status;
    public $branch_id;

    function mount()
    {
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


            $this->reset(['name', 'description', 'amount']);
            $this->leisures = Leisure::all();

            session()->flash('create_message', 'Leisure created successfully.');

        } catch (\Exception $e) {
            session()->flash('create_message', 'Failed to create leisure: ' . $e->getMessage());
        }
    }

    public function editLeisure($id)
    {
        $this->leisure = Leisure::find($id);
        if ($this->leisure) {
            $this->leisure_id = $this->leisure->id;
            $this->name = $this->leisure->name;
            $this->description = $this->leisure->description;
            $this->amount = $this->leisure->amount;
            $this->status = $this->leisure->status;
        }

    }

    public function updateLeisure()
    {
        $this->validate([
            'name' => 'required',
            'description' => 'required',
            'amount' => 'required|numeric',
        ]);

        $leisure = Leisure::find($this->leisure_id);
        if ($leisure) {
            $leisure->update([
                'name' => $this->name,
                'description' => $this->description,
                'amount' => (double) $this->amount,
                'status' => $this->status,
                'branch_id' => $this->branch_id,
            ]);
        }

        $this->reset(['name', 'description', 'amount', 'leisure_id']);
        $this->leisures = Leisure::all();

        session()->flash('create_message', 'Leisure updated successfully.');
    }

    public function render()
    {
        return view('livewire.gate-entrance.leisures');
    }
}
