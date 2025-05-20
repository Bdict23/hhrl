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
    public $unit;
    public $branch_id;
    public $isOpen = false;
    public $isdelete = false;



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
                'unit' => $this->unit,
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
        $this->isOpen = true;

    }
    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['name', 'description', 'amount', 'leisure_id']);
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
            if ($leisure) {
                $leisure->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'amount' => (double) $this->amount,
                ]);
            }

            $this->leisures = Leisure::all();
            session()->flash('update_message', 'Leisure updated successfully.');

        } catch (\Exception $e) {
            session()->flash('update_message', 'Failed to update leisure: ' . $e->getMessage());
        }
        $this->validate([
            'name' => 'required',
            'description' => 'required',
            'amount' => 'required|numeric',
        ]);


    }

    public function deleteLeisure($id)
    {
        try {
            $leisure = Leisure::find($id);
            if ($leisure) {
                if ($leisure->status == 1) {
                    $leisure->update([
                        'status' => 0,
                    ]);
                    session()->flash('delete_message', 'Leisure deleted successfully.');
                } else {
                    $leisure->update([
                        'status' => 1,
                    ]);
                    session()->flash('delete_message', 'Leisure restored successfully.');
                }
            }else {
                session()->flash('delete_message', 'Leisure not found.');
            }
            $this->leisures = Leisure::all();
        } catch (\Exception $e) {
            session()->flash('delete_message', 'Failed to delete leisure: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.gate-entrance.leisures');
    }
}
