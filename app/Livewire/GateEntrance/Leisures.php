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

            session()->flash('message', 'Leisure created successfully.');

        }catch (\Exception $e) {
            session()->flash('message', 'Failed to create leisure: ' . $e->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.gate-entrance.leisures');
    }
}
