<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Branch;
use App\Models\User;

class TransferEmployee extends Component
{
    public $employees = [];
    public $companies = [];
    public $selectedCompany;
    public $branches = [];
    public $selectedBranch;
    public $selectedEmployee;

    protected $rules = [
        'selectedCompany' => 'required|exists:companies,id',
        'selectedBranch' => 'required|exists:branches,id',
    ];

    public function render()
    {
        return view('livewire.settings.transfer-employee');
    }

    public function mount()
    {
       $this->fetchData();
    }
    public function fetchData()
    {
        $this->employees = Employee::with('branch','position')->get();
        // dd($this->employees->toArray());
        $this->companies = Company::where('company_status', 'ACTIVE')->get();
        
    }
    public function selectEmployee($employeeId)
    {
        $this->selectedEmployee = Employee::find($employeeId);
        // dd($this->selectedEmployee->toArray());
    }

    public function fetchBranches($companyID)
    {
        $this->selectedCompany = $companyID;
        $this->branches = Branch::where([['company_id', $companyID],['branch_status', 'ACTIVE']])->get();
    }
    public function selectBranch($branchID)
    {
        $this->selectedBranch = $branchID;
    }
    public function confirmTransferEmployee()
    {
        $this->validate();
        $employee = Employee::find($this->selectedEmployee->id);
        $user = User::where('emp_id', $this->selectedEmployee->id)->first();
        if ($user) {
            $user->branch_id = $this->selectedBranch;
            $user->save();
        }
        $employee->branch_id = $this->selectedBranch;
        $employee->save();

        session()->flash('success', 'Employee transferred successfully.');
        $this->fetchData();
        $this->dispatch('close-modal');
    }


}
