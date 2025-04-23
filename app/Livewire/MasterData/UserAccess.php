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
    public $modulesWithSignatory = [];
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

    // access count
    public $readOnlyCount = 0;
    public $fullAccessCount = 0;
    public $restrictCount = 0;

    // made changes
    public $hasChanges = false;

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
        $this->modulesWithSignatory = Module::where('has_signatory', 1)->get();



    }

    public function selectedUser($userId)
    {
        $this->reset();
        $this->fetchData();
        $this->userDetails = Employee::with('user')->find($userId);
        $this->userAccess = ModulePermission::where('employee_id', $userId)->get();
        $this->employeeId = $userId;

        foreach ($this->modules as $module) {
            $perm = ModulePermission::where('employee_id', $userId ?? null)
                ->where('module_id', $module->id)
                ->first();

            // access count
            if ($perm->read_only ?? 0) {
                $this->readOnlyCount ++;
            }
            if ($perm->full_access ?? 0) {
                $this->fullAccessCount ++;
            }
            if ($perm->restrict ?? 0) {
                $this->restrictCount ++;
            }
          
                $this->permissions[$module->id] = [
                    'read_only' => (bool) ($perm->read_only ?? 0),
                    'full_access' => (bool) ($perm->full_access ?? 0),
                    'restrict' => (bool) ($perm->restrict ?? 0),
                ];
        }

    //    dd($this->permissions);

    }

    public function setPermission($moduleId, $type)
    {
        if($this->employeeId != null && $this->employeeId != '' ) {
            $this->hasChanges = true;
        }
        $this->permissions[$moduleId] = [
            'read_only' => $type == 'read_only' ? 1 : 0,
            'full_access' => $type == 'full_access' ? 1 : 0,
            'restrict' => $type == 'restrict' ? 1 : 0,
        ];
       
    }

    public function savePersmissions()
    {
        $this->validate([
            'employeeId' => 'required',
        ]);

        foreach ($this->permissions as $moduleId => $permission) {
            $currentPermission = ModulePermission::where([
                ['module_id', $moduleId],
                ['employee_id', $this->employeeId]
            ])->first();

            if ($currentPermission) {
                $currentPermission->delete();
            } 
            ModulePermission::create([
                'module_id' => $moduleId,
                'employee_id' => $this->employeeId,
                'read_only' => $permission['read_only'],
                'full_access' => $permission['full_access'],
                'restrict' => $permission['restrict'],
            ]);
        }

        $this->reset();
        $this->fetchData();
        session()->flash('success', 'Permissions saved successfully.');
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
