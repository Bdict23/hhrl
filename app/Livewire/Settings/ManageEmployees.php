<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ManageEmployees extends Component
{
    use WithPagination;

    public $branches = [];
    public $departments = [];
    public $positions = [];
    public $editMode = false;
    public $employeeId;
    public $corporate_id;
    public $name;
    public $middle_name;
    public $last_name;
    public $contact_number;
    public $position_id;
    public $religion;
    public $birth_date;
    public $branch_id;
    public $department_id;
    public $status = 'ACTIVE';
    public $search = '';
    public $perPage = 10;
    public $showForm = false;
    public $selectedEmployee = null;

    protected $rules = [
        'corporate_id' => 'nullable|string|max:50|unique:employees,corporate_id',
        'name' => 'required|string|max:50',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'contact_number' => 'nullable|string|max:20',
        'position_id' => 'required|exists:employee_positions,id',
        'religion' => 'nullable|string|max:50',
        'birth_date' => 'nullable|date',
        'department_id' => 'nullable|exists:departments,id',
        'status' => 'required|in:ACTIVE,INACTIVE',
    ];

    public function mount()
    {
        try {
            $this->branches = Branch::orderBy('branch_name')
                ->where('company_id', auth()->user()->employee->branch->company_id)
                ->get(['id', 'branch_name']);
            
            $this->branch_id = auth()->user()->employee->branch_id ?? null;
            $this->departments = Department::orderBy('department_name')->get(['id', 'department_name']);
            $this->positions = Position::orderBy('position_name')->get(['id', 'position_name']);
        } catch (\Exception $e) {
            Log::error("Failed to mount component: {$e->getMessage()}");
            session()->flash('error', 'Failed to load initial data.');
            $this->dispatch('clear-messages');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        Log::info('Create button clicked');
        $this->resetForm();
        $this->editMode = false;
        $this->showForm = true;
        $this->dispatch('open-employee-modal');
    }

    public function edit($id)
    {
        Log::info("Edit button clicked for employee ID: {$id}");
        try {
            $employee = Employee::findOrFail($id);
            $this->employeeId = $id;
            $this->corporate_id = $employee->corporate_id;
            $this->name = $employee->name;
            $this->middle_name = $employee->middle_name;
            $this->last_name = $employee->last_name;
            $this->contact_number = $employee->contact_number;
            $this->position_id = $employee->position_id;
            $this->religion = $employee->religion;
            $this->birth_date = $employee->birth_date ? $employee->birth_date->format('Y-m-d') : null;
            $this->branch_id = auth()->user()->employee->branch_id;
            $this->department_id = $employee->department_id;
            $this->status = $employee->status;
            $this->editMode = true;
            $this->showForm = true;
            $this->dispatch('open-employee-modal');
        } catch (\Exception $e) {
            Log::error("Failed to load employee: {$e->getMessage()}");
            session()->flash('error', 'Failed to load employee data.');
            $this->dispatch('clear-messages');
        }
    }

    public function closeModal()
    {
        Log::info('Close employee modal clicked');
        $this->resetForm();
        $this->editMode = false;
        $this->showForm = false;
        $this->dispatch('close-employee-modal');
    }

    public function openDetailsModal($id)
    {
        Log::info("Open details modal clicked for employee ID: {$id}");
        try {
            $this->selectedEmployee = Employee::with(['branch', 'department', 'position'])->findOrFail($id)->toArray();
            $this->dispatch('open-details-modal');
        } catch (\Exception $e) {
            Log::error("Failed to load details: {$e->getMessage()}");
            session()->flash('error', 'Failed to load employee details.');
            $this->dispatch('clear-messages');
        }
    }

    public function closeDetailsModal()
    {
        Log::info('Close details modal clicked');
        $this->selectedEmployee = null;
        $this->dispatch('close-details-modal');
    }

    public function store()
    {
        $this->validate();

        try {
                Employee::create([
                    'corporate_id' => $this->corporate_id,
                    'name' => $this->name,
                    'middle_name' => $this->middle_name,
                    'last_name' => $this->last_name,
                    'contact_number' => $this->contact_number,
                    'position_id' => $this->position_id,
                    'religion' => $this->religion,
                    'birth_date' => $this->birth_date,
                    'branch_id' => auth()->user()->employee->branch_id,
                    'department_id' => $this->department_id,
                    'status' => $this->status,
                ]);
            session()->flash('success', 'Employee created successfully.');
            $this->closeModal();
            $this->resetPage();
            $this->dispatch('clear-messages');
        } catch (\Exception $e) {
            Log::error("Failed to create employee: {$e->getMessage()}");
            session()->flash('error', 'Failed to create employee: ' . $e->getMessage());
            $this->dispatch('clear-messages');
        }
    }

    public function update()
    {
        $this->validate(array_merge($this->rules, [
            'corporate_id' => "nullable|string|max:50|unique:employees,corporate_id,{$this->employeeId}",
        ]));
        Log::info('Update method called', [
            'employee_id' => $this->employeeId,
            'corporate_id' => $this->corporate_id,
            'name' => $this->name,
        ]);

        try {
            $employee = Employee::findOrFail($this->employeeId);
            $employee->update([
                'corporate_id' => $this->corporate_id,
                'name' => $this->name,
                'middle_name' => $this->middle_name,
                'last_name' => $this->last_name,
                'contact_number' => $this->contact_number,
                'position_id' => $this->position_id,
                'religion' => $this->religion,
                'birth_date' => $this->birth_date,
                'branch_id' => auth()->user()->employee->branch_id,
                'department_id' => $this->department_id,
                'status' => $this->status,
            ]);
            session()->flash('success', 'Employee updated successfully.');
            $this->closeModal();
            $this->dispatch('clear-messages');
        } catch (\Exception $e) {
            Log::error("Failed to update employee: {$e->getMessage()}");
            session()->flash('error', 'Failed to update employee: ' . $e->getMessage());
            $this->dispatch('clear-messages');
        }
    }

    public function fetchFromHris()
    {
        $this->validate(['corporate_id' => 'required|string|max:50']);
        Log::info('Fetch from HRIS called', ['corporate_id' => $this->corporate_id]);

        try {
            $response = Http::get(config('services.hris.url') . '/employees/' . $this->corporate_id, [
                'api_key' => config('services.hris.api_key'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->name = $data['first_name'] ?? '';
                $this->middle_name = $data['middle_name'] ?? '';
                $this->last_name = $data['last_name'] ?? '';
                $this->contact_number = $data['contact_number'] ?? '';
                $this->position_id = $data['position_id'] ?? null;
                $this->religion = $data['religion'] ?? '';
                $this->birth_date = $data['birth_date'] ?? '';
                $this->branch_id = $data['branch_id'] ?? '';
                $this->department_id = $data['department_id'] ?? '';
                $this->status = $data['status'] ?? 'ACTIVE';
                session()->flash('success', 'Data fetched from HRIS.');
                $this->dispatch('clear-messages');
            } else {
                session()->flash('error', 'Unable to fetch HRIS data.');
                $this->dispatch('clear-messages');
            }
        } catch (\Exception $e) {
            Log::error("HRIS fetch failed: {$e->getMessage()}");
            session()->flash('error', 'Error fetching HRIS data: ' . $e->getMessage());
            $this->dispatch('clear-messages');
        }
    }

    #[On('activate')]
    public function activate($id)
    {
        Log::info("Activate called for employee ID: {$id}");
        try {
            $employee = Employee::findOrFail($id);
            $employee->status = 'ACTIVE';
            $employee->save();
            session()->flash('success', 'Employee activated successfully.');
            $this->dispatch('clear-messages');
            $this->resetPage();
        } catch (\Exception $e) {
            Log::error("Failed to activate employee: {$e->getMessage()}");
            session()->flash('error', 'Failed to activate employee: ' . $e->getMessage());
            $this->dispatch('clear-messages');
        }
    }

    #[On('deactivate')]
    public function deactivate($id)
    {
        Log::info("Deactivate called for employee ID: {$id}");
        try {
            $employee = Employee::findOrFail($id);
            $employee->status = 'INACTIVE';
            $employee->save();
            session()->flash('success', 'Employee deactivated successfully.');
            $this->dispatch('clear-messages');
            $this->resetPage();
        } catch (\Exception $e) {
            Log::error("Failed to deactivate employee: {$e->getMessage()}");
            session()->flash('error', 'Failed to deactivate employee: ' . $e->getMessage());
            $this->dispatch('clear-messages');
        }
    }

    private function resetForm()
    {
        $this->reset([
            'employeeId', 'corporate_id', 'name', 'middle_name', 'last_name',
            'contact_number', 'position_id', 'religion', 'birth_date',
            'branch_id', 'department_id', 'status'
        ]);
        $this->status = 'ACTIVE';
        $this->resetErrorBag();
        Log::info('Form reset');
    }

    public function render()
    {
        $employees = Employee::with(['branch', 'department', 'position'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('last_name', 'like', "%{$this->search}%")
                    ->orWhere('corporate_id', 'like', "%{$this->search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
        $this->branch_id = auth()->user()->employee->branch_id;
        return view('livewire.settings.manage-employees', [
            'employees' => $employees,
        ]);
    }
}