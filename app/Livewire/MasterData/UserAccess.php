<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\ModulePermission;


class UserAccess extends Component
{
    public $userAccess = [];
    public $userDetails;
    public $employees = [];
    public $branches = [];
    public $branch; // Define branch property
    public $userAccessId;

    // employee details
    public $employeeId;
    public $employeeName;
    public $employeeBranch;
    public $employeeDepartment;
    public $employeePosition;
    public $employeeStatus;

    public function mount()
    {
       $this->fetchData();
    }
    public function fetchData()
    {
        $this->branches = Branch::where('branch_status', 'ACTIVE')->get();
    }

    public function selectedUser($userId)
    {
        $this->userDetails = Employee::with('user')->find($userId);
        $this->userAccess = ModulePermission::where('employee_id', $userId)->get();

    }

    public function fetchEmployees($branch)
    {   $this->branch = $branch;
        $this->employees = Employee::where('status', 'ACTIVE')->where('branch_id', $this->branch)->get();
    }

    public function render()
    {
        return view('livewire.master-data.user-access');
    }
}
