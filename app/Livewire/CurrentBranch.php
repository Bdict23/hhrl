<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AssignedBranch;
use App\Models\Employee;

class CurrentBranch extends Component
{
    public $branches = [];
    public $currentBranch = '';
    public $currentSwitch = '';


    public function render()
    {
        return view('livewire.current-branch');
    }

    public function mount()
    {
        $this->fetchdata();
    }
    public function fetchdata()
    {
        $this->branches = AssignedBranch::with('branch')
            ->where('employee_id', auth()->user()->id)
            ->get();
        $this->currentBranch = auth()->user()->branch->branch_name;
    }

    public function updatedcurrentSwitch($branchId)
    {  
        $this->currentSwitch = $branchId;
    }

    // public function switchBranch()
    // {
    //     $this->currentBranch = AssignedBranch::find($branchId)->branch->branch_name;
    //     auth()->user()->update(['branch_id' => $branchId]);
    //     session()->flash('message', 'Branch switched successfully.');
    // }

    public function switchBranch()
    {
        // $employee = Employee::find(auth()->user()->emp_id);
        // $employee->update(['branch_id' => $this->currentSwitch]);

        //update usre table
        $user = auth()->user();
        $user->update(['branch_id' => $this->currentSwitch]);
        // dd($employee);
        session()->flash('message', 'Branch switched successfully.');
    }
}
