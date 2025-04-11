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
    public $branch; // Define branch property
    public $branches = [];
    public $employees = [];
    public $personnelData = [];

    //
    private $auditCompanies = [];
    private $companyIds = [];
    private $branchIds = [];

    // used by forms
    public $name;
    public $description;

    // for updating department
    public $departmentId;
    public $forUpdateEmployees = [];


    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:255',


    ];

    public function mount()
    {
       $this->fetchDepartments();
    }




    public function saveDepartment()
    {
        $this->validate();

        $dept =  new DepartmentModel();
        $dept->branch_id = $this->branch ?? null; // Ensure branch is set or default to null
        $dept->branch_id = auth()->user()->branch->id;
        $dept->company_id = auth()->user()->branch->company_id;
        $dept->department_name = $this->name;
        $dept->department_description = $this->description;
        $dept->save();

        if (!empty($this->personnelData)) {
            Employee::whereIn('id', $this->personnelData)->update(['department_id' => $dept->id]);
        }
        $this->reset();
        $this->dispatch('saved');
        $this->fetchDepartments();

}



 public function deactivate($id)
    { try {
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
        $this->resetForm();
        $this->fetchDepartments();
    } catch (\Exception $e) {
        return $e->getMessage();
    }

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
    {   $this->branch = $branch;
        $this->employees = Employee::with('position')->where('status', 'ACTIVE')->where('branch_id', $this->branch)->get();
    }



    // for clicking the edit button
    public function edit($id)
    {
        $this->departmentId = $id;
        $this->department = DepartmentModel::findOrFail($id);
        $this->name = $this->department->department_name;
        $this->description = $this->department->department_description;
        $this->forUpdateEmployees = Employee::where('department_id', $id)->get();

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
        $this->resetForm();
        $this->dispatch('saved');
        $this->fetchDepartments();

    }

    public function resetForm()
    {
       // $this->reset(['name', 'description', 'departmentId', 'personnelData', 'branch', 'forUpdateEmployees', 'employees', 'department', 'departments']);
        $this->reset();
        $this->fetchDepartments(); // Fetch departments again to refresh the list
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



