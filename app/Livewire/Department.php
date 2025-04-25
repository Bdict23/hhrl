<?php

namespace App\Livewire;
use Livewire\Withpagination;
use Livewire\Component;
use App\Models\Department as DepartmentModel;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Audit;


class Department extends Component
{

    // used by tables and forms
    public $departments = [];
    public $department = [];
    public $branches = [];
    public $employees = [];
    public $personnelData = [];

    // for initializing the data
    private $auditCompanies = [];
    private $companyIds = [];
    private $branchIds = [];
    public $getBranchEmployees;

    // used by forms
    public $name = '';
    public $description = '';
    public $action = 'create'; // Default action is create
    public $personnels = []; // for assigning employees to department
    public $selectedBranchId; // Variable to store the branch ID selected by the user

    // for updating department
    public $departmentId;
    public $forUpdateEmployees = [];


    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:255',
        'selectedBranchId' => 'required|exists:branches,id', // Ensure branch is selected and exists in the database


    ];

    protected $messages = [
        'name.required' => 'The department name is required.',
        'description.required' => 'The department description is required.',
        'selectedBranchId.required' => 'Please select a branch.',
        'selectedBranchId.exists' => 'The selected branch does not exist.',
    ];

    public function mount()
    {
       $this->fetchDepartments();
    }




    public function saveDepartment()
    {
      if($this->action == 'update'){
        return $this->updateDepartment();
        }
        $this->validate();

        $dept =  new DepartmentModel();
        $dept->branch_id = $this->branch ?? null; // Ensure branch is set or default to null
        $dept->branch_id = $this->selectedBranchId ?? auth()->user()->branch->id; // Use selected branch ID or default to user's branch ID
        $dept->company_id = auth()->user()->branch->company_id;
        $dept->department_name = $this->name;
        $dept->department_description = $this->description;
        $dept->save();

        //save sa department_id sa mga employees nga gi assign sa department
        if (!empty($this->personnels)) {
            foreach ($this->personnels as  $employeeData) {
              
                $employee = Employee::find($employeeData->id);
                if ($employee) {
                    $employee->department_id = $dept->id; // Assign the department ID to the employee
                    $employee->save();
                }
            }
        }

        $this->reset();
        $this->fetchDepartments();
        session()->flash('success', 'Department created successfully!' );
        $this->dispatch('dispatch-success');
        $this->dispatch('dispatch-clearForm');


    }

    // remove employee from personnel list
    public function removeEmployee($index)
    {
       
        unset($this->personnels[$index]);
        // Re-index the array}
        if($this->action == 'create'){ 
            $this->personnels = array_values($this->personnels);
        }elseif($this->action =='update'){ 
            $this->personnels = array_values($this->personnels->toArray());
        }
    }


 public function deactivate($id)
    {
        $department = DepartmentModel::find($id);
        $department->department_status = 'INACTIVE';
        $department->save();

        $this->employees = Employee::where('department_id', $id)->get();
        if ($this->employees->isNotEmpty()) {
            foreach ($this->employees as $employee) {
                $employee->department_id = null; // Set department_id to null pag ma deactivate ang department
                $employee->save();
            }
        }
        $this->reset();
        $this->fetchDepartments();
        $this->dispatch('dispatch-success');
        session()->flash('success', 'Department deactivated successfully!' );

 }




    public function fetchDepartments()
    {
        $this->auditCompanies = Audit::with('company')->where('created_by', auth()->user()->emp_id)->get();
        $this->companyIds = $this->auditCompanies->pluck('company.id')->toArray();
        $this->branches = Branch::where('branch_status', 'ACTIVE')->whereIn('company_id', $this->companyIds)->get();
        $this->branchIds = $this->branches->pluck('id')->toArray();

        $this->departments = DepartmentModel::where('department_status', 'ACTIVE')->whereIn('branch_id', $this->branchIds)->get();


    }

    public function fetchEmployees($branch)
    {
        $this->employees = Employee::with('position')->where('status', 'ACTIVE')->where('branch_id', $branch)->get();
    }



    // for clicking the edit button
    public function edit($id)
    {
        $this->departmentId = $id;
        $this->department = DepartmentModel::findOrFail($id);
        $this->name = $this->department->department_name;
        $this->description = $this->department->department_description;
        $this->selectedBranchId = $this->department->branch_id; 
        $this->action = 'update'; // Set action to update
        $this->personnels = Employee::where('department_id', $id)->get();
        $this->employees = Employee::with('position') ->where('status', 'ACTIVE')->where('branch_id', $this->selectedBranchId)->get();

    }

    public function addEmployee($employeeId)
    {
        $employee = Employee::find($employeeId);
        if ($employee) {
            if($this->action == 'create'){
                if (in_array($employee, $this->personnels)) {
                    session()->flash('error', 'The employee is already selected.');
                    return;
                }
            }elseif($this->action == 'update'){
                if (in_array($employee, $this->personnels->toArray())) {
                    session()->flash('error', 'The employee is already selected.');
                    return;
                }
            }
            $this->personnels[] = $employee;
        }
    }

    
    public function updatedSelectedBranchId($branchId)
    {
        $this->personnels = []; // Reset personnel data when branch is changed
        $this->branch = $branchId;
        $this->employees = Employee::with('position')
            ->where('status', 'ACTIVE')
            ->where('branch_id', $this->branch)
            ->get();
        $this->dispatch('branchEmployeesFetched', ['employees' => $this->employees]);
    }

    public function createNewDepartment()
    {
        $this->reset();
        $this->action = 'create'; // Reset action to create
        $this->fetchDepartments();
        $this->dispatch('dispatch-clearForm');
    }


    public function updateDepartment()
    {

        $this->validate();
        $department = DepartmentModel::find($this->departmentId);
        $department->department_name = $this->name;
        $department->department_description = $this->description;
        $department->save();
        // Reset the departmentId to null sa tanan employee nga assign sa department before mag update sa new data
            Employee::where('department_id', $this->departmentId)->update(['department_id' => null]);
        if (!empty($this->personnelData)) {
            Employee::whereIn('id', $this->personnelData)->update(['department_id' => $this->departmentId]);
        }
        $this->forUpdateEmployees = Employee::where('department_id', $this->departmentId)->get();
        $this->reset();
        session()->flash('success', 'Department updated successfully!' );
        $this->dispatch('dispatch-clearForm');
        $this->fetchDepartments();

    }


    public function updatePersonnelData($data)
    {
        $this->personnelData = $data; // Update the Livewire property with the received data
    }

    public function render()
    {
        return view('livewire.department', [
            'employees' => $this->employees,
        ]);
    }
}



