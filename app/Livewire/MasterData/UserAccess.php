<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Position;
use App\Models\ModulePermission;
use App\Models\Module;
use App\Models\Signatory;


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
    public $assignedSignatory = [];
    public $signatoryRole = [];
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
    public $signatoryChanges = false;

    protected $listeners = [
        'selectedUser' => 'selectedUser',
        'fetchEmployees' => 'fetchEmployees',
        'setPermission' => 'setPermission',
        'updatedPermissions' => 'updatedPermissions',
    ];
    public function mount()
    {
       $this->fetchData();
      
        // dd($this->userDetails);
    }
    public function fetchData()
    {
        $this->branches = Branch::where('branch_status', 'ACTIVE')->get();
        $this->positions = Position::where('position_status', 'ACTIVE')->get();
        $this->modules = Module::all();
        $this->modulesWithSignatory = Module::where('has_signatory', 1)->get();
        $this->employees = Employee::where('status', 'ACTIVE')->get();



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
            if ($perm && $perm->access == 0) {
                $this->readOnlyCount++;
            }
            if ($perm && $perm->access == 1) {
                $this->fullAccessCount++;
            } else {
                $this->restrictCount++;
            }
            // set permission
                // $this->permissions[$module->id] = [
                //     'read_only' => (bool) ($perm->access == 0 ?? 0),
                //     'full_access' => (bool) ($perm->access == 1 ?? 0),
                //     'restrict' => (bool) ($perm->access != 0 && $perm->access != 1 ?? 0),
                // ];
                $this->permissions[$module->id] =  ($perm->access ?? 2 );
        }

        //get the assigned signatory
        foreach ($this->branches as $branch) {
            foreach ($this->modulesWithSignatory as $module) {
            $signatory = Signatory::where('employee_id', $userId)
                ->where('module_id', $module->id)
                ->where('branch_id', $branch->id)
                ->get();

            if ($signatory->isNotEmpty()) {
                foreach ($signatory as $sign) {
                $this->signatoryRole[$sign->branch_id][$sign->module_id][$sign->signatory_type] = [
                    'REVIEWER' => $sign->signatory_type == 'REVIEWER' ? true : false,
                    'APPROVER' => $sign->signatory_type == 'APPROVER' ? true : false,
                ];
                $this->assignedSignatory[$sign->branch_id][$sign->module_id][$sign->signatory_type] = [
                    'module_id' => $module->id,
                    'type' => $sign->signatory_type,
                    'value' => true,
                    'branch' => $sign->branch_id,
                ];
                }
            }
            }
        }

       

    }

    public function setPermission($moduleId, $type)
    {
        if($this->employeeId != null && $this->employeeId != '' ) {
            $this->hasChanges = true;
        }
        // count access
        if ($type == 'read_only') {
            $this->readOnlyCount++;
            $this->fullAccessCount = max(0, $this->fullAccessCount - 1);
            $this->restrictCount = max(0, $this->restrictCount - 1);
        } elseif ($type == 'full_access') {
            $this->fullAccessCount++;
            $this->readOnlyCount = max(0, $this->readOnlyCount - 1);
            $this->restrictCount = max(0, $this->restrictCount - 1);
        } elseif ($type == 'restrict') {
            $this->restrictCount++;
            $this->readOnlyCount = max(0, $this->readOnlyCount - 1);
            $this->fullAccessCount = max(0, $this->fullAccessCount - 1);
        }
        // set permission
        $this->permissions[$moduleId] = $type == 'read_only' ? 0 : ($type == 'full_access' ? 1 : 2);
       
    }

    public function savePersmissions()
    {
        // dd($this->assignedSignatory);
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

            //  dd('module id', $moduleId, 'employee id', $this->employeeId, 'permission', $permission);
            // if persmission is equal to 0 or 1 permission will create alse no permission will be created
          if( $permission == 0 || $permission == 1) {
                ModulePermission::create([
                    'module_id' => $moduleId,
                    'employee_id' => $this->employeeId,
                    'access' => $permission,
                ]);
            }
        }

        if($this->signatoryChanges) {
           
            foreach ($this->assignedSignatory as $branch => $modules) {
                foreach ($modules as $module => $types) {
                    foreach ($types as $data => $value) {
                        if ($value['value'] == false) {
                            // dd('module id', $value['module_id'], 'employee id', $this->employeeId, 'branch id', $value['branch'], 'type', $value['type']);
                            $currentSignatory = Signatory::where([
                                ['module_id', $value['module_id']],
                                ['employee_id', $this->employeeId],
                                ['signatory_type', $value['type']],
                                ['branch_id', $value['branch']]
                            ])->first();
                            // dd($currentSignatory);
                            if ($currentSignatory) {
                                $currentSignatory->delete();
                            }
                            continue;
                        }
                        if ($value['value'] == true) { 
                            $currentSignatory = Signatory::where([
                                ['module_id', $value['module_id']],
                                ['employee_id', $this->employeeId],
                                ['branch_id', $value['branch']],
                                ['signatory_type', $value['type']]
                            ])->first();

                            if ($currentSignatory) {
                                $currentSignatory->delete();
                            }
                                Signatory::create([
                                    'module_id' => $value['module_id'],
                                    'employee_id' => $this->employeeId,
                                    'branch_id' => $value['branch'],
                                    'company_id' => auth()->user()->branch->company_id,
                                    'signatory_type' => $value['type'],
                                ]);
                        }
                    }
                }
            }
        }

        
       
        return redirect('/user-access')->with('success', 'Permission Applied!');
    }


    public function fetchEmployees($branch)
    {   $this->branch = $branch;
        $this->employees = Employee::where('status', 'ACTIVE')->where('branch_id', $this->branch)->get();
    }

    public function render()
    {
        return view('livewire.master-data.user-access');
    }

    public function setSignatoryRole($branch , $moduleId , $type , $value)
    {
        
        if($this->employeeId != null && $this->employeeId != '' ) {
            $this->hasChanges = true;
            $this->signatoryChanges = true;
            $this->assignedSignatory[$branch][$moduleId][$type] = [
                'module_id' => $moduleId,
                'type' => $type,
                'value' => $value,
                'branch' => $branch,
            ];  
            
        }else {
            $this->hasChanges = false;
        }
       
    }
}
