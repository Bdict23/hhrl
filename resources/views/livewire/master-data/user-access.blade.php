<div>
   <div class="container row">
      
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                  <div class="d-flex justify-content-end">
                    <x-primary-button type="button" data-bs-toggle="modal"
                    data-bs-target="#AddPersonnelsModal">Find User</x-primary-button>
                    <button type="button" class="btn btn-sm btn-success ms-2" disabled>Save Changes</button>
                </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="userName" class="form-label">First Name</label>
                            <input value="{{ $userDetails ? $userDetails->name : '' }}" type="text" class="form-control" id="userName">
                        </div>
                        <div class="col-md-6">
                            <label for="userId" class="form-label">Lastname</label>
                            <input type="text" class="form-control" id="userId" value="{{ $userDetails ? $userDetails->last_name : '' }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">User Email</label>
                        <input type="email" class="form-control" id="userEmail" value="{{ $userDetails && $userDetails->user ? $userDetails->user->email : '' }}">
                    </div>
                    <div class="mb-3">
                        <label for="userRole" class="form-label">Position</label>
                        <select class="form-select" id="userRole" wire:model="position">
                            <option value="">Select</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}"  {{ $userDetails && $userDetails->position_id == $position->id ? 'SELECTED' : '' }}>{{ $position->position_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="userStatus" class="form-label">User Status</label>
                        <select class="form-select" id="userStatus" wire:model="employeeStatus">
                            <option value="">Select Status</option>
                            <option value="ACTIVE" {{ $userDetails && $userDetails->status == 'ACTIVE' ? 'SELECTED' : '' }}>Active</option>
                            <option value="INACTIVE" {{ $userDetails && $userDetails->status == 'INACTIVE' ? 'SELECTED' : '' }}>Inactive</option>
                        </select>

                </div>
            </div>
            </div>
        </div>

        <div class=" col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">User Access</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover table-sm table-responsive-sm">
                        <thead class="table-dark">
                            <tr>
                                <th colspan="1" class="text-center">User Access</th>
                                <th colspan="1" class="text-center">Read Only : 0</th>
                                <th colspan="1" class="text-center">Full Access : 0</th>
                                <th colspan="1" class="text-center">Restrict : 0</th>

                            </tr>
                            <tr>
                                <th>Module</th>
                                <th class="text-center">Read Only</th>
                                <th class="text-center">Full Access</th>
                                <th class="text-center">Restrict</th>   
                            </tr>
                        </thead>
                        <tbody>
                          
                            @foreach ($modules as $module)
                            <tr>
                                <td>{{ $module->module_name }}</td>
                
                                @foreach (['read_only', 'full_access', 'restrict'] as $type)
                                    <td>
                                        <input 
                                         wire:click="setPermission( '{{$module->id}}','{{ $type }}',$event.target.checked)" 
                                        type="checkbox" class="form-check-input" id="flexCheckDefault" 
                                        {{-- value=""> --}} value="1"
                                        {{ $permissions[$module->id][$type] ?? false ? 'checked' : '' }} >
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                           
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card" style="display: none">
                <div class="card-header">
                    <h5 class="card-title">User Access</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover table-sm table-responsive-sm">
                        <thead class="table-dark">
                            <tr>
                                <th colspan="1" class="text-center">User Access</th>
                                <th colspan="1" class="text-center">Read Only : 0</th>
                                <th colspan="1" class="text-center">Full Access : 0</th>
                                <th colspan="1" class="text-center">Restrict : 0</th>

                            </tr>
                            <tr>
                                <th>Module</th>
                                <th class="text-center">Read Only</th>
                                <th class="text-center">Full Access</th>
                                <th class="text-center">Restrict</th>
                            </tr>
                        </thead>
                        <tbody>
                          
                            @foreach ($modules as $module)
                            <tr>
                                <td>{{ $module->module_name }}</td>
                
                                @foreach (['read_only', 'full_access', 'restrict'] as $type)
                                    <td>
                                        <input 
                                         {{-- wire:model.live="permissions.{{ $module->id }}.{{ $type }}"  --}}
                                        type="checkbox" class="form-check-input" id="flexCheckDefault" 
                                        {{ $permissions[$module->id][$type] ?? false ? 'checked' : '' }} disabled>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
   </div>


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
                    <div class="col-md-4 justify-content-start ">
                        <select name="branch" class="form-control" id="branch_select" onchange="fetchEmployees()">
                            <option value="">Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 justify-content-end">
                        <input type="text" class="form-control" id="searchEmployee"
                            placeholder="Search Employee">
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
                                        <button class="btn btn-primary btn-sm" value="{{ $employee->id }}"
                                            wire:click="selectedUser({{ $employee->id }})" data-bs-dismiss="modal" aria-label="Close">Select</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No employees found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    </div>
  
    <script>
         function fetchEmployees() {
            let branchId = document.getElementById('branch_select').value;
            @this.fetchEmployees(branchId);
            console.log(branchId);
    }
    </script>
</div>
