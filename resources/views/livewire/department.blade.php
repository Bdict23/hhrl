<div>
    <div class="tab-content card" id="department-form" style="display: none" wire:ignore.self>
        <form id="departmentForm" wire:submit.prevent="saveDepartment">
            @csrf
            <div class="row">

                <div class="col-md-12 card">
                    <div class="shadow-sm">
                        <div class="card-body">
                            <header class="d-flex justify-content-between align-items-center mb-3">
                                <h1 class="h4">Create Department</h1>
                                <div>
                                    <x-primary-button type="submit"
                                        onclick="sendPersonnelData()">Save</x-primary-button>
                                    <x-secondary-button onclick="showTab('departments-table', this)"
                                        class="ms-2">Summary</x-secondary-button>
                                </div>
                            </header>
                            <div class="card-body">
                                <div class="alert alert-light" role="alert">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="department_name" class="form-label">Department Name <span
                                                        style="color: red;">*</span></label>
                                                <input type="text" class="form-control" id="department_name"
                                                    wire:model="name" placeholder="ex. Security Dept.">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" wire:model="description"></textarea>
                                            @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-end mb-2">
                                                <x-primary-button type="button" data-bs-toggle="modal"
                                                    data-bs-target="#AddPersonnelsModal"
                                                    onclick="changeModule('CREATE-MODULE')">+
                                                    Add Personnel</x-primary-button>
                                            </div>
                                            <div style="max-height: 200px; overflow-y: auto;">
                                                <table class="table table-striped table-sm">
                                                    <thead>
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

    {{-- Department List --}}
    <div class="tab-content card" id="departments-table" style="display: none" wire:ignore.self>
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <header class="d-flex justify-content-between align-items-center mb-3">

                        <div>
                            <x-secondary-button class="mb-3 btn-sm" onclick="showTab('department-form', this)">+
                                New</x-secondary-button>
                        </div>
                        <div>
                            <input type="text" class="form-control" id="searchDepartment" name="searchDepartment"
                                placeholder="Search Department">
                        </div>
                    </header>
                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr style="font-size: smaller;">
                                <th>Department Name</th>
                                <th>Branch</th>
                                <th>Status</th>
                                <th>Personnel</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="departmentTableBody">
                            @forelse ($departments as $department)
                                <tr style="font-size: smaller;">
                                    <td>{{ $department->department_name }}</td>
                                    <td>{{ $department->branch->branch_name }}</td>
                                    <td>{{ $department->department_status }}</td>
                                    <td style="text-align: center">{{ $department->employees->count() }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" wire:click="edit({{ $department->id }})"
                                            onclick="showTab('department-update-form', this), changeModule('UPDATE-MODULE')">Edit</button>
                                        <button class="btn btn-danger btn-sm"
                                            wire:click="deactivate({{ $department->id }})">Delete</button>
                                    </td>
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
                            <select name="branch" class="form-control" id="branch_select"
                                onchange="fetchEmployees()">
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
                                                onclick="selectEmployeeFunction({{ $employee->id }}, '{{ $employee->name }}', '{{ $employee->last_name }}', '{{ $employee->position }}', '{{ $employee->status }}', '{{ $employee->department ? $employee->department->department_name : 'N/A' }}', '{{ $employee->branch->branch_name ?? 'N/A' }}')">Select</button>
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


    {{-- update department form --}}
    <div class="tab-content card" id="department-update-form" style="display: none" wire:ignore.self>
        <form wire:submit.prevent="updateDepartment">
            <input type="hidden" id="department_id" wire:model="department_id">
            @csrf
            <div class="row">
                <div class="col-md-12 card">
                    <div class="shadow-sm">
                        <div class="card-body">
                            <header class="d-flex justify-content-between align-items-center mb-3">
                                <h1 class="h4">Update Department</h1>
                                <div>
                                    <x-primary-button class="mb-3 btn-sm" onclick="showTab('department-form', this)">+
                                        New</x-primary-button>
                                    <x-primary-button type="submit"
                                        onclick="sendPersonnelData()">Update</x-primary-button>
                                    <x-secondary-button type="button"
                                        onclick="showTab('departments-table', this)">Summary</x-secondary-button>
                                </div>
                            </header>
                            <div class="card-body">
                                <div class="alert alert-light" role="alert">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="department_name2" class="form-label">Department Name <span
                                                        style="color: red;">*</span></label>
                                                <input type="text" class="form-control" id="department_name2"
                                                    required value="{{ $name }}">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description2" class="form-label">Description</label>
                                            <textarea class="form-control" id="description2">{{ $description ?? '' }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-end mb-2">
                                                <x-primary-button type="button" data-bs-toggle="modal"
                                                    onclick="changeModule('UPDATE-MODULE')"
                                                    data-bs-target="#AddPersonnelsModal">+
                                                    Add Personnel</x-primary-button>
                                            </div>
                                            <div style="max-height: 200px; overflow-y: auto;">
                                                <table class="table table-striped table-sm">
                                                    <thead>
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
                                                    <tbody id="personnelTableBody2">
                                                        @forelse ($forUpdateEmployees as $personnel)
                                                            <tr style="font-size: smaller;">
                                                                <td hidden>{{ $personnel->id }}</td>
                                                                <td>{{ $personnel->name }}</td>
                                                                <td>{{ $personnel->last_name }}</td>
                                                                <td>{{ $personnel->position }}</td>
                                                                <td>{{ $personnel->status }}</td>
                                                                <td>{{ $personnel->branch->branch_name ?? 'N/A' }}
                                                                </td>
                                                                <td style="text-align: center">
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm"
                                                                        onclick="removePersonnel(this)">Remove</button>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>

                                                            </tr>
                                                        @endforelse
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


    <script>
        function sendPersonnelData() {

            // Get the table body element
            let tableBody = document.getElementById(tableBodyId);
            let rows = tableBody.getElementsByTagName('tr');
            let personnelData = [];

            for (let row of rows) {
                let cells = row.getElementsByTagName('td');
                if (cells.length > 0) {
                    personnelData.push({
                        id: cells[0].innerText.trim(),
                    });
                }
            }
            @this.updatePersonnelData(personnelData);

            if (tableBodyId === 'personnelTableBody2') {
                @this.set('name', document.getElementById('department_name2').value);
                @this.set('description', document.getElementById('description2').value);
            }



        }

        let tableBodyId = '';

        function changeModule(moduleName) {
            tableBodyId = moduleName === 'CREATE-MODULE' ? 'personnelTableBody' : 'personnelTableBody2';
            console.log(`Table Body ID set to: ${tableBodyId}`);
        }

        function selectEmployeeFunction(id, name, lastName, position, status, department, branch) {
            if (department !== 'N/A') {
                alert(`The employee ${name} ${lastName} already belongs to the ${department} department.`);
                return;
            }

            const tableBody = document.getElementById(tableBodyId);
            if (!tableBody) {
                console.error('Table body not found.');
                return;
            }

            const existingRows = Array.from(tableBody.querySelectorAll('tr'));
            if (existingRows.some(row => row.cells[1]?.textContent === name && row.cells[2]?.textContent === lastName)) {
                alert(`The employee ${name} ${lastName} is already selected.`);
                return;
            }

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
            <td hidden>${id}</td>
            <td>${name}</td>
            <td>${lastName}</td>
            <td>${position}</td>
            <td>${status}</td>
            <td>${branch}</td>
            <input type="hidden" name="employees[]" value="${id}">
            <td>
                <button class="btn btn-danger btn-sm" onclick="removePersonnel(this)">Remove</button>
            </td>
            `;
            tableBody.appendChild(newRow);


        }

        function fetchEmployees() {
            let branchId = document.getElementById('branch_select').value;
            @this.fetchEmployees(branchId);
            console.log(branchId);
        }

        function removePersonnel(button) {
            const row = button.closest('tr');
            row.remove();
        }

        function resetForm() {
            document.getElementById('branch_select').value = '';
        }
    </script>
</div>
