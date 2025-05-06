<div>

   <div class="container row">
    @if (session()->has('success'))
    <div class="alert alert-success" id="success-message">
        {{ session('success') }}
        <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                  <div class="d-flex justify-content-end">
                    <x-primary-button type="button" data-bs-toggle="modal"
                    data-bs-target="#AddPersonnelsModal">Find User</x-primary-button>
                    <button wire:click = "savePersmissions" type="button" class="btn btn-sm btn-success ms-2" {{ $hasChanges ? '' : 'disabled'}} wire:loading.attr="disabled">
                        <span wire:loading wire:target="savePersmissions">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Saving...
                    </span>
                    <span wire:loading.remove wire:target="savePersmissions">
                        Save Changes
                    </span></button>
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

        <div class=" col-md-6" wire:ignore.self>
            <ul class="nav nav-tabs" id="jobOrderTabs" role="tablist" wire:ignore>
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="invoice-tab" data-bs-toggle="tab" data-bs-target="#invoice" type="button"
                        role="tab" aria-controls="invoice" aria-selected="true">Permision</button>
                </li>
    
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pcv-tab" data-bs-toggle="tab" data-bs-target="#pcv" type="button"
                        role="tab" aria-controls="pcv" aria-selected="false">Signatory</button>
                </li>
            </ul>

            <div class="tab-content" id="jobOrderTabContent" wire:ignore.self>
                
                <div class="tab-pane fade show active" id="invoice" role="tabpanel" aria-labelledby="invoice-tab" wire:ignore.self>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">User Access</h5>
                        </div>
                        <div class="card-body table-responsive-sm">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th colspan="1" class="text-center">User Access</th>
                                        <th colspan="1" class="text-center">Read Only : {{ $readOnlyCount }}</th>
                                        <th colspan="1" class="text-center">Full Access : {{$fullAccessCount}} </th>
                                        <th colspan="1" class="text-center">Restrict : {{ $restrictCount }} </th>
                    
                                    </tr>
                                    <tr>
                                        <th>Module</th>
                                        <th class="text-center" colspan="3">Access</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                  
                                    @foreach ($modules as $module)
                                        <tr>
                                            <td>{{ $module->module_name }}</td>
                                            <td colspan="3" class="text-center">
                                                <select wire:change="setPermission('{{ $module->id }}', $event.target.value)"  class="form-select" aria-label="Default select example">
                                                    <option value="">Select Access</option>
                                                    <option value="read_only" {{ $permissions[$module->id]['read_only'] ?? false ? 'selected' : '' }}>
                                                      Read Only</option>
                                                    <option value="full_access" {{ $permissions[$module->id]['full_access'] ?? false ? 'selected' : '' }}>Full Access</option>
                                                    <option value="restrict" {{ $permissions[$module->id]['restrict'] ?? false ? 'selected' : '' }}>Restrict</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="tab-pane fade" id="pcv" role="tabpanel" aria-labelledby="pcv-tab" wire:ignore.self>

                    <!-- Signatory TAB -->
                    <div class="card">
                        <header class="card-header">
                            <h6 >Assign Signatory Role</h6>
                        </header>
                        <div class="card-body table-responsive-sm" style="height: 300px; overflow-y: auto;">                           
                             <table class="table table-striped table-sm">
                                <thead class="table-dark sticky-top">
                                    <tr class="text-smaller">
                                        <th>Module</th>
                                        <th>Reviewer</th>
                                        <th>Approver</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($branches as $branch)
                                        <tr> 
                                            <td colspan="4" class="text-center" style="background-color: #c9f5df;"><strong>{{ $branch->branch_name }}</strong></td>
                                        </tr>
                                    
                                        <tr>
                                            @foreach ($modulesWithSignatory as $module )
                                                <tr>
                                                    <td>{{ $module->module_name }}</td>
                                                    <td>
                                                    <input class="form-check-input" type="checkbox" wire:click="setSignatoryRole({{ $branch->id}},{{ $module->id }}, 'REVIEWER', $event.target.checked)" {{ $signatoryRole[$branch->id][$module->id]['REVIEWER'] ?? false ? 'checked' : '' }} >   
                                                    </td>
                                                    <td>
                                                    <input class="form-check-input" type="checkbox" wire:click="setSignatoryRole({{$branch->id}},{{ $module->id }}, 'APPROVER', $event.target.checked )" {{ $signatoryRole[$branch->id][$module->id]['APPROVER'] ?? false ? 'checked' : '' }} >
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
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
