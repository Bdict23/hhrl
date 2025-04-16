<div id="employee-management" class="tab-content card dashboard" style="display: none;" wire:ignore.self>
    <div class="card-header">
        <h5 class="card-title mb-0">Employee Management</h5>
    </div>
    <div class="card-body">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" id="success-message">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" id="error-message">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Employees Table -->
            <div class="col-md-12">
                <div class="mb-3">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search employees..." 
                        class="form-control" 
                    />
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Employees</h5>
                            <button 
                                wire:click="create" 
                                class="btn btn-primary btn-sm">
                                Add Employee
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Branch</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $employee)
                                        <tr wire:key="employee-{{ $employee->id }}">
                                            <td>{{ $employee->corporate_id ?? 'N/A' }}</td>
                                            <td>{{ $employee->name }} {{ $employee->middle_name ? $employee->middle_name . ' ' : '' }}{{ $employee->last_name }}</td>
                                            <td>{{ $employee->position->position_name ?? 'Not assigned' }}</td>
                                            <td>{{ $branches->firstWhere('id', $employee->branch_id)?->branch_name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge {{ $employee->status === 'ACTIVE' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $employee->status }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button 
                                                    wire:click="edit({{ $employee->id }})" 
                                                    class="btn btn-primary btn-sm">
                                                    Edit
                                                </button>
                                                <button 
                                                    wire:click="openDetailsModal({{ $employee->id }})" 
                                                    class="btn btn-info btn-sm">
                                                    View
                                                </button>
                                                @if($employee->status === 'ACTIVE')
                                                    <button 
                                                        wire:click.prevent="deactivate({{ $employee->id }})" 
                                                        class="btn btn-danger btn-sm">
                                                        Deactivate
                                                    </button>
                                                @else
                                                    <button 
                                                        wire:click.prevent="activate({{ $employee->id }})" 
                                                        class="btn btn-success btn-sm">
                                                        Activate
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No employees found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{ $employees->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Form Modal -->
        @if($showForm)
        <div 
            class="modal fade" 
            id="employeeModal" 
            tabindex="-1" 
            data-bs-backdrop="static" 
            aria-labelledby="employeeModalLabel" 
            aria-hidden="true" 
            wire:ignore.self>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="employeeModalLabel">
                            {{ $editMode ? 'Update Employee Details' : 'Create New Employee' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="corporate_id_input" class="form-label">Corporate ID</label>
                                    <div class="input-group">
                                        <input 
                                            type="text" 
                                            wire:model="corporate_id" 
                                            id="corporate_id_input" 
                                            class="form-control">
                                        {{-- <button 
                                            type="button" 
                                            wire:click="fetchFromHris" 
                                            class="btn btn-outline-primary">
                                            Fetch from HRIS
                                        </button> --}}
                                    </div>
                                    @error('corporate_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="name_input" class="form-label">First Name</label>
                                    <input type="text" wire:model="name" id="name_input" class="form-control" required>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="middle_name_input" class="form-label">Middle Name</label>
                                    <input type="text" wire:model="middle_name" id="middle_name_input" class="form-control">
                                    @error('middle_name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="last_name_input" class="form-label">Last Name</label>
                                    <input type="text" wire:model="last_name" id="last_name_input" class="form-control" required>
                                    @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="contact_number_input" class="form-label">Contact Number</label>
                                    <input type="text" wire:model="contact_number" id="contact_number_input" class="form-control">
                                    @error('contact_number') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="position_id_input" class="form-label">Position</label>
                                    <select wire:model="position_id" id="position_id_input" class="form-select" required>
                                        <option value="">Select Position</option>
                                        @foreach($positions as $position)
                                            <option value="{{ $position->id }}">{{ $position->position_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('position_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="religion_input" class="form-label">Religion</label>
                                    <input type="text" wire:model="religion" id="religion_input" class="form-control">
                                    @error('religion') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="birth_date_input" class="form-label">Birth Date</label>
                                    <input type="date" wire:model="birth_date" id="birth_date_input" class="form-control">
                                    @error('birth_date') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="status_input" class="form-label">Status</label>
                                    <select wire:model="status" id="status_input" class="form-select" required>
                                        <option value="ACTIVE">Active</option>
                                        <option value="INACTIVE">Inactive</option>
                                    </select>
                                    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="department_id_input" class="form-label">Department</label>
                                    <select wire:model="department_id" id="department_id_input" class="form-select">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="branch_id_input" class="form-label">Branch</label>
                                    <select wire:model="branch_id" id="branch_id_input" class="form-select" required>
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('branch_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading wire:target="{{ $editMode ? 'update' : 'store' }}">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Saving...
                                    </span>
                                    {{ $editMode ? 'Update' : 'Save' }}
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="closeModal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Employee Details Modal -->
        <div 
            class="modal fade" 
            id="detailsModal" 
            tabindex="-1" 
            aria-labelledby="detailsModalLabel" 
            aria-hidden="true"
            wire:ignore.self>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsModalLabel">
                            @if($selectedEmployee)
                                {{ $selectedEmployee['name'] }} {{ $selectedEmployee['middle_name'] ? $selectedEmployee['middle_name'] . ' ' : '' }}{{ $selectedEmployee['last_name'] }}
                            @endif
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="closeDetailsModal"></button>
                    </div>
                    <div class="modal-body">
                        @if($selectedEmployee)
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Corporate ID</label>
                                    <p>{{ $selectedEmployee['corporate_id'] ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Position</label>
                                    <p>{{ $selectedEmployee['position']['position_name'] ?? 'Not assigned' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <p>{{ $selectedEmployee['status'] ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Branch</label>
                                    <p>{{ $branches->firstWhere('id', $selectedEmployee['branch_id'])?->branch_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Department</label>
                                    <p>{{ $departments->firstWhere('id', $selectedEmployee['department_id'])?->department_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Number</label>
                                    <p>{{ $selectedEmployee['contact_number'] ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Religion</label>
                                    <p>{{ $selectedEmployee['religion'] ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Birth Date</label>
                                    <p>{{ $selectedEmployee['birth_date'] ? \Carbon\Carbon::parse($selectedEmployee['birth_date'])->format('Y-m-d') : 'N/A' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button 
                            type="button" 
                            class="btn btn-secondary" 
                            data-bs-dismiss="modal" 
                            wire:click="closeDetailsModal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:init', function () {
    Livewire.on('open-employee-modal', () => {
        const modalElement = document.getElementById('employeeModal');
        if (modalElement) {
            const bsModal = new bootstrap.Modal(modalElement);
            bsModal.show();
        }
    });

    Livewire.on('close-employee-modal', () => {
        const modalElement = document.getElementById('employeeModal');
        if (modalElement) {
            const bsModal = bootstrap.Modal.getInstance(modalElement);
            if (bsModal) bsModal.hide();
        }
    });

    Livewire.on('open-details-modal', () => {
        const modalElement = document.getElementById('detailsModal');
        if (modalElement) {
            const bsModal = new bootstrap.Modal(modalElement);
            bsModal.show();
        }
    });

    Livewire.on('close-details-modal', () => {
        const modalElement = document.getElementById('detailsModal');
        if (modalElement) {
            const bsModal = bootstrap.Modal.getInstance(modalElement);
            if (bsModal) bsModal.hide();
        }
    });

    Livewire.on('clear-messages', () => {
        setTimeout(() => {
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            if (successMessage) successMessage.style.display = 'none';
            if (errorMessage) errorMessage.style.display = 'none';
        }, 1500);
    });
});
</script>