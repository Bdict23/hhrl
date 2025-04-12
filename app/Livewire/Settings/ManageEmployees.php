<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Department;
use App\Models\Branch;
use App\Models\CompanyEmployee;
use App\Models\BranchEmployee;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ManageEmployees extends Component
{
    use WithPagination;

    public $companies;
    public $branches;
    public $departments;

    public $showForm = false;
    public $editMode = false;
    public $selectedEmployee = null;

    // Employee fields
    public $employeeId;
    public $corporate_id;
    public $name;
    public $middle_name;
    public $last_name;
    public $contact_number;
    public $position;
    public $religion;
    public $birth_date;
    public $branch_id;
    public $department_id;
    public $status = 'ACTIVE';
    public $selectedCompanyId;
    public $companyDepartmentId;

    // Pagination and search
    public $perPage = 10;
    public $search = '';

    protected $rules = [
        'corporate_id' => 'nullable|string|max:50',
        'name' => 'required|string|max:50',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'contact_number' => 'nullable|string|max:255',
        'position' => 'required|string|max:255',
        'religion' => 'nullable|string|max:255',
        'birth_date' => 'nullable|date',
        'status' => 'required|in:ACTIVE,INACTIVE',
        'companyDepartmentId' => 'nullable|exists:departments,id',
        'selectedCompanyId' => 'nullable|exists:companies,id',
        'branch_id' => 'nullable|exists:branches,id',  
    ];

    protected function rules()
    {
        $baseRules = $this->rules;

        if ($this->editMode) {
            $baseRules['corporate_id'] .= "|unique:employees,corporate_id,{$this->employeeId}";
        } else {
            $baseRules['corporate_id'] .= '|unique:employees,corporate_id';
            $baseRules['selectedCompanyId'] = 'required|exists:companies,id';
        }

        return $baseRules;
    }

    protected function messages()
    {
        return [
            'selectedCompanyId.required' => 'The company field is required.',
            'selectedCompanyId.exists' => 'The selected company does not exist.',
        ];
    }

    public function mount()
    {
        $this->loadStaticData();
    }

    private function loadStaticData()
    {
        $this->companies = Company::orderBy('company_name')->get(['id', 'company_name']);
        $this->branches = Branch::orderBy('branch_name')->get(['id', 'branch_name']);
        $this->departments = Department::orderBy('department_name')->get(['id', 'department_name']);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editMode = false;
        $this->dispatch('open-employee-modal'); // Dispatch event to open modal
    }

    public function edit($id)
    {
        try {
            $employee = Employee::with('companies')->findOrFail($id);

            $this->employeeId = $id;
            $this->corporate_id = $employee->corporate_id;
            $this->name = $employee->name;
            $this->middle_name = $employee->middle_name;
            $this->last_name = $employee->last_name;
            $this->contact_number = $employee->contact_number;
            $this->position = $employee->position;
            $this->religion = $employee->religion;
            $this->birth_date = $employee->birth_date?->format('Y-m-d');
            $this->branch_id = $employee->branch_id;
            $this->department_id = $employee->department_id;
            $this->status = $employee->status;

            $companyAssignment = $employee->companies->first();
            if ($companyAssignment) {
                $this->selectedCompanyId = $companyAssignment->id;
                $this->companyDepartmentId = $companyAssignment->pivot->department_id;
            } else {
                $this->selectedCompanyId = null;
                $this->companyDepartmentId = null;
            }

            $this->showForm = true;
            $this->editMode = true;
            $this->dispatch('open-employee-modal'); //
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load employee data: ' . $e->getMessage());
        }
    }

    public function store()
    {
        $this->validate();

        try {
            \DB::transaction(function () {
                $employee = Employee::create([
                    'corporate_id' => $this->corporate_id,
                    'name' => $this->name,
                    'middle_name' => $this->middle_name,
                    'last_name' => $this->last_name,
                    'contact_number' => $this->contact_number,
                    'position' => $this->position,
                    'religion' => $this->religion,
                    'birth_date' => $this->birth_date,
                    'status' => $this->status,
                ]);

                CompanyEmployee::create([
                    'emp_id' => $employee->id,
                    'company_id' => $this->selectedCompanyId,
                    'department_id' => $this->companyDepartmentId,
                ]);

                if ($this->branch_id) {
                    BranchEmployee::create([
                        'branch_id' => $this->branch_id,
                        'emp_id' => $employee->id,
                    ]);
                }
            });

            $this->resetForm();
            $this->showForm = false;
            $this->resetPage();
            session()->flash('success', 'Employee created successfully.');
            $this->dispatch('close-modal');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create employee: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $employee = Employee::findOrFail($this->employeeId);
            $employee->update([
                'corporate_id' => $this->corporate_id,
                'name' => $this->name,
                'middle_name' => $this->middle_name,
                'last_name' => $this->last_name,
                'contact_number' => $this->contact_number,
                'position' => $this->position,
                'religion' => $this->religion,
                'birth_date' => $this->birth_date,
                'branch_id' => $this->branch_id,
                'department_id' => $this->department_id,
                'status' => $this->status,
            ]);

            if ($this->selectedCompanyId) {
                CompanyEmployee::updateOrCreate(
                    ['emp_id' => $this->employeeId, 'company_id' => $this->selectedCompanyId],
                    ['department_id' => $this->companyDepartmentId]
                );
            }

            $this->resetForm();
            $this->showForm = false;
            session()->flash('success', 'Employee updated successfully.');
            $this->dispatch('close-modal');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update employee: ' . $e->getMessage());
        }
    }

    public function fetchFromHris()
    {
        $this->validate(['corporate_id' => 'required|string|max:50']);

        try {
            $response = Http::get(config('services.hris.url') . '/employees/' . $this->corporate_id, [
                'api_key' => config('services.hris.api_key'),
            ]);

            if ($response->successful()) {
                $employeeData = $response->json();

                $this->name = $employeeData['first_name'] ?? '';
                $this->middle_name = $employeeData['middle_name'] ?? '';
                $this->last_name = $employeeData['last_name'] ?? '';
                $this->contact_number = $employeeData['contact_number'] ?? '';
                $this->position = $employeeData['position'] ?? '';
                $this->religion = $employeeData['religion'] ?? '';
                $this->birth_date = $employeeData['birth_date'] ?? '';
                $this->branch_id = $employeeData['branch_id'] ?? '';
                $this->department_id = $employeeData['department_id'] ?? '';
                $this->status = $employeeData['status'] ?? 'ACTIVE';
                $this->selectedCompanyId = $employeeData['company_id'] ?? '';
                $this->companyDepartmentId = $employeeData['company_department_id'] ?? '';

                session()->flash('success', 'Employee data fetched successfully from HRIS.');
            } else {
                session()->flash('error', 'Unable to fetch employee data from HRIS.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error fetching from HRIS: ' . $e->getMessage());
        }
    }

    public function deactivate($id)
    {
        try {
            Employee::findOrFail($id)->update(['status' => 'INACTIVE']);
            session()->flash('success', 'Employee deactivated successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to deactivate employee: ' . $e->getMessage());
        }
    }

    public function activate($id)
    {
        try {
            Employee::findOrFail($id)->update(['status' => 'ACTIVE']);
            session()->flash('success', 'Employee activated successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to activate employee: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->reset([
            'employeeId', 'corporate_id', 'name', 'middle_name', 'last_name', 'contact_number',
            'position', 'religion', 'birth_date', 'branch_id', 'department_id',
            'status', 'selectedCompanyId', 'companyDepartmentId',
        ]);
        $this->status = 'ACTIVE';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function updatedSearch()
    {
        Log::info("Search updated to: {$this->search}");
        $this->resetPage();
    }

    public function render()
    {
        $employees = Employee::with(['branch', 'department', 'companies'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('position', 'like', '%' . $this->search . '%')
                    ->orWhere('corporate_id', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.settings.manage-employees', [
            'employees' => $employees,
            'companies' => $this->companies,
            'branches' => $this->branches,
            'departments' => $this->departments,
            'selectedEmployee' => $this->selectedEmployee,
        ])->extends('layouts.app')
            ->layout('layouts.app', ['title' => 'Manage Employees']);
    }
    public function updatedShowForm($value)
{
    if ($value) {
        $this->dispatch('open-employee-modal');
    }
}
}