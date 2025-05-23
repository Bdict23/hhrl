<div>
    <div class="row container">
        
        @if (session()->has('success'))
         <div class="alert alert-success" id="success-message">
             {{ session('success') }}
             <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>
         @endif
    
          {{-- left panel (dept. lists) --}}
        <div 
        @if (auth()->user()->employee->getModulePermission('Departments') == 1)
            class="col-md-6 mt-2"
        @else
            class="col-md-12"
        @endif

        wire:ignore.self>
            <div class="card">
                <div class="shadow-sm">
                    <div class="card-body">
                        <header class="d-flex justify-content-between align-items-center mb-3">
    
                            <h1 class="h4">Department List</h1>
                            <div>
                                <input type="text" class="form-control" id="searchDepartment"
                                    placeholder="Search Department">
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const searchInput = document.getElementById('searchDepartment');
                                    const tableBody = document.getElementById('departmentTableBody');

                                    searchInput.addEventListener('input', function() {
                                        const searchTerm = searchInput.value.toLowerCase();
                                        const rows = tableBody.getElementsByTagName('tr');

                                        Array.from(rows).forEach(row => {
                                            const departmentName = row.cells[0]?.textContent.toLowerCase() || '';
                                            const branchName = row.cells[1]?.textContent.toLowerCase() || '';
                                            const status = row.cells[2]?.textContent.toLowerCase() || '';

                                            if (departmentName.includes(searchTerm) || branchName.includes(searchTerm) || status.includes(searchTerm)) {
                                                row.style.display = '';
                                            } else {
                                                row.style.display = 'none';
                                            }
                                        });
                                    });
                                });
                            </script>
                        </header>
                        <div class="overflow-x-auto" >
                            <table class="table table-striped min-w-full" id="departmentTable">
                                <thead class="table-dark">
                                    <tr style="font-size: smaller;">
                                        <th>Department Name</th>
                                        <th>Branch</th>
                                        <th>Status</th>
                                        <th class="text-center">Personnel</th>
                                        @if (auth()->user()->employee->getModulePermission('Departments') == 1)
                                            <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="departmentTableBody">
                                    @forelse ($departments as $department)
                                        <tr style="font-size: smaller;">
                                            <td>{{ $department->department_name }}</td>
                                            <td>{{ $department->branch->branch_name }}</td>
                                            <td>{{ $department->department_status }}</td>
                                            <td style="text-align: center">{{ $department->employees->count() }}</td>
                                            @if (auth()->user()->employee->getModulePermission('Departments') == 1)
                                                <td class="text-nowrap">
                                                    <a  wire:click="edit({{ $department->id }})"><x-secondary-button><u>Edit</u></x-secondary-button></a>
                                                    <button class="btn btn-danger btn-sm"
                                                        wire:click="deactivate({{ $department->id }})"
                                                        onclick="return confirm('Are you sure you want to deactivate this department?')">Deactivate</button>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No departments found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
       @if (auth()->user()->employee->getModulePermission('Departments') == 1)
        {{-- Right Panel (dept. info) --}}
        <div class="col-md-6 mt-2">
            <form  wire:submit.prevent="saveDepartment" id="departmentForm">
                @csrf
                <div class="row">
    
                    <div class="col-md-12 card">
                        <div class="shadow-sm">
                            <div class="card-body">
                                <header class="d-flex justify-content-between align-items-center mb-3">
                                    <h1 class="h4">Create Department</h1>
                                    <div>
                                        @if ($action === 'update')
                                        <x-primary-button type="submit">
                                            <span wire:loading wire:target="saveDepartment">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Updating...
                                            </span>
                                            <span wire:loading.remove wire:target="saveDepartment">
                                                Update
                                            </span>
                                        </x-primary-button>
                                            <x-secondary-button class="mb-3 btn-sm" wire:click='createNewDepartment'>+ New</x-secondary-button>
                                        @elseif ($action === 'create')
                                            <x-primary-button type="submit">
                                                <span wire:loading wire:target="saveDepartment">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    Saving...
                                                </span>
                                                <span wire:loading.remove wire:target="saveDepartment">
                                                    Save
                                                </span>
                                            </x-primary-button>
                                        @endif
                                    </div>
                                </header>
                                <div class="card-body">
                                    <div class="alert alert-light" role="alert">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="department_name" class="form-label">Department Name <span
                                                            style="color: red;">*</span></label>
                                                    <input  wire:model.live="name" value="{{$name}}" type="text" class="form-control" id="department_name">
                                                    @error('name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="department-description" class="form-label">Description</label>
                                                <textarea  wire:model.live="description" class="form-control" id="department-description">{{$description}}</textarea>
                                                @error('description')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="branch" class="form-label">Branch</label>
                                                <select name="branch" class="form-control" id="branch_select"
                                                    wire:model.live="selectedBranchId">
                                                    <option value="">Select Branch</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}" {{ $selectedBranchId ==  $branch->id ? 'selected' : ''   }}>{{ $branch->branch_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('selectedBranchId')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
    
                                            </div>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-end mb-2">
                                                    <x-primary-button type="button" data-bs-toggle="modal"
                                                        data-bs-target="#AddPersonnelsModal">+
                                                        Add Personnel</x-primary-button>
                                                </div>
                                                <div style="max-height: 200px; overflow-y: auto;">
                                                    <table class="table table-striped table-hover table-sm">
                                                        <thead class="table-dark sticky-top">
                                                            <tr style="font-size: smaller;">
                                                                <th hidden>ID</th>
                                                                <th style="font-size: smaller;">Name</th>
                                                                <th style="font-size: smaller;">Last Name</th>
                                                                <th style="font-size: smaller;">Position</th>
                                                                <th style="font-size: smaller;">Status</th>
                                                                <th style="font-size: smaller;">Registered Branch</th>
                                                                <th style="font-size: smaller; text-align: center;">Actions
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="personnelTableBody">
                                                            {{-- POPULATE PERSONNEL TABLE --}}
                                                            @if ($action === 'update')
                                                                @forelse ($personnels as $index => $personnel)
                                                                    <tr style="font-size: smaller;">
                                                                        <td hidden>{{ $personnel->id }}</td>
                                                                        <td>{{ $personnel->name }}</td>
                                                                        <td>{{ $personnel->last_name }}</td>
                                                                        <td>{{ $personnel->position->position_name }}</td>
                                                                        <td>{{ $personnel->status }}</td>
                                                                        <td>{{ $personnel->branch->branch_name ?? 'N/A' }}
                                                                        </td>
                                                                        <td style="text-align: center">
                                                                            <button wire:click='removeEmployee({{ $index }})' type="button" class="btn btn-danger btn-sm">Remove</button>
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                   
                                                                @endforelse
                                                         
                                                            @endif
                                                            @if ($action === 'create')
                                                                @forelse ($personnels as $index => $personnel)
                                                                    <tr style="font-size: smaller;">
                                                                        <td hidden>{{ $personnel->id }}</td>
                                                                        <td>{{ $personnel->name }}</td>
                                                                        <td>{{ $personnel->last_name }}</td>
                                                                        <td>{{ $personnel->position->position_name }}</td>
                                                                        <td>{{ $personnel->status }}</td>
                                                                        <td>{{ $personnel->branch->branch_name ?? 'N/A' }}
                                                                        </td>
                                                                        <td style="text-align: center">
                                                                            <button wire:click='removeEmployee({{ $index }})' type="button" class="btn btn-danger btn-sm"
                                                                               >Remove</button>
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                   
                                                                @endforelse
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-end">
                                        <!-- Footer content if needed -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @endif

        {{-- Add Personnel Modal --}}
        <div class="modal fade" id="AddPersonnelsModal" tabindex="-1" aria-labelledby="AddPersonnelModalLabel"
            aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="AddPersonnelModalLabel">Employees Lists</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 d-flex row">
                            
                            <div class="col-md-6 justify-content-start">
                                <input type="text" class="form-control" id="searchEmployee"
                                    placeholder="Search Employee">
                            </div>
                            <div class="col-md-6 justify-content-end">
                                @if (session()->has('error'))
                                    <div class="alert alert-danger float-start" id="alert-message">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-striped table-hover table-sm">
                                <thead class="table-dark">
                                    <tr style="font-size: smaller;">
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Branch Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="employeeTableBody">
                                    @forelse ($employees as $employee)
                                        <tr style="font-size: smaller;">
                                            <td>{{ $employee->name }} {{ $employee->middle_name }}
                                                {{ $employee->last_name }}
                                            </td>
                                            <td>{{ $employee->position->position_name }}</td>
                                            <td>{{ $employee->department ? $employee->department->department_name : 'N/A' }}
                                            </td>
                                            <td>{{ $employee->branch->branch_name ?? 'N/A' }}</td>
                                            <td>
                                                    <butto wire:click = "addEmployee({{ $employee->id }})" class="btn btn-primary btn-sm">Select</button>                                     
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No Employees on selected branch</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                      
                        {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <span wire:loading >
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Adding...
                            </span>
                            <span wire:loading.remove>
                                Close
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    
       
    </div>
    
    
    
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('dispatch-success', event => {
    
                   setTimeout(function() {
                    document.getElementById('success-message').style.display = 'none';
                }, 1500);
                
                });
    
                window.addEventListener('dispatch-clearForm', event => {
    
                // Reset the form
                document.getElementById('departmentForm').reset();
    
                });
        });
    
        function removePersonnel(button) {
            const row = button.closest('tr');
            row.remove();
        }
    </script>
    
    
</div>