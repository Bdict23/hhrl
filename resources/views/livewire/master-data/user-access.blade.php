<div>
   <div class="container row">
      
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                  <div class="d-flex justify-content-end">
                    <x-primary-button type="button" data-bs-toggle="modal"
                    data-bs-target="#AddPersonnelsModal"
                    onclick="changeModule('CREATE-MODULE')">Find User</x-primary-button>
                    <button type="button" class="btn btn-sm btn-success ms-2">Save Changes</button>
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
                        <input type="email" class="form-control" id="userEmail" value="{{ $userDetails ? $userDetails->user->email : '' }}">
                    </div>
                    <div class="mb-3">
                        <label for="userRole" class="form-label">Position</label>
                        <select class="form-select" id="userRole" wire:model="userRole">
                            <option value="">Select</option>
                            
                            {{-- @foreach($roles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="userStatus" class="form-label">User Status</label>
                        <select class="form-select" id="userStatus" wire:model="userStatus">
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
                    <table>
                        <thead>
                            <tr>
                                <th>Module</th>
                                <th>Read Only</th>
                                <th>Full Access</th>
                                <th>Restrict</th>
                            </tr>
                        </thead>
                        <tbody>
                          
                                <tr>
                                    <td>(module name)</td>
                                    <td><input type="checkbox"></td>
                                    <td><input type="checkbox"></td>
                                    <td><input type="checkbox" checked></td>
                                </tr>
                           
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
                                    <td>{{ $employee->position }}</td>
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
