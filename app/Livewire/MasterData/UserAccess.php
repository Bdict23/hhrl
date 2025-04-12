<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Position;
use App\Models\ModulePermission;
use App\Models\Module;


class UserAccess extends Component
{
    public $permissions = [];
    public $userDetails;
    public $employees = [];
    public $branches = [];
    public $positions = [];
    public $position;
    public $modules;
    public $userAccesss = [];
    public $branch; // Define branch property
    public $userAccessId;

    // employee details
    public $employeeId;
    public $employeeName;
    public $employeeBranch;
    public $employeeDepartment;
    public $employeePosition;
    public $employeeStatus;

    protected $listeners = [
        'selectedUser' => 'selectedUser',
        'fetchEmployees' => 'fetchEmployees',
        'setPermission' => 'setPermission',
        'updatedPermissions' => 'updatedPermissions',
    ];
    public function mount()
    {
       $this->fetchData();
    }
    public function fetchData()
    {
        $this->branches = Branch::where('branch_status', 'ACTIVE')->get();
        $this->positions = Position::where('position_status', 'ACTIVE')->get();
        $this->modules = Module::all();



    }

    public function selectedUser($userId)
    {
        $this->userDetails = Employee::with('user')->find($userId);
        $this->userAccess = ModulePermission::where('employee_id', $userId)->get();
        $this->employeeId = $userId;

        foreach ($this->modules as $module) {
            $perm = ModulePermission::where('employee_id', $userId ?? null)
                ->where('module_id', $module->id)
                ->first();

                $this->permissions[$module->id] = [
                    'read_only' => (bool) ($perm->read_only ?? 0),
                    'full_access' => (bool) ($perm->full_access ?? 0),
                    'restrict' => (bool) ($perm->restrict ?? 0),
                ];
        }

    //    dd($this->permissions);

    }

    public function setPermission($moduleId, $type ,$action)
    {
        if($this->employeeId){
            if($action){
                $currentPermission = ModulePermission::where([
                    ['module_id', $moduleId],
                    [$type, '!=', null],
                    ['employee_id',$this->employeeId]
                ])->first();

                if (!$currentPermission && $action) {
                    $currentPermission = new ModulePermission();
                    $currentPermission->module_id = $moduleId;
                    $currentPermission->employee_id = $this->employeeId ?? null;
                    $currentPermission->$type = 1;
                    $currentPermission->save();
                }
                $this->resetExcept('employeeId');
                $this->fetchData();
                $this->selectedUser($this->employeeId);

            }
    }

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
