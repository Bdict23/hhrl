@extends('layouts.master')
@section('content')
    <div class="container mt-5">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert"
                style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1050;">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <form id="departmentForm" method="POST" action="{{ route('departments.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <header class="d-flex justify-content-between align-items-center mb-3">
                                <h1 class="h4">Department Lists</h1>
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
                                                <button class="btn btn-primary btn-sm">Edit</button>
                                                <button class="btn btn-danger btn-sm">Delete</button>
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
                <div class="col-md-6 card">
                    <div class="shadow-sm">
                        <div class="card-body">
                            <header class="d-flex justify-content-between align-items-center mb-3">
                                <h1 class="h4">Create Department</h1>
                                <div>
                                    <x-primary-button type="submit">Save</x-primary-button>
                                    <x-danger-button type="button" onclick="clearForm()">Clear</x-danger-button>
                                    <x-secondary-button onclick="history.back()" class="ms-2">Back</x-secondary-button>
                                </div>
                            </header>
                            <div class="card-body">
                                <div class="alert alert-light" role="alert">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="department_name" class="form-label">Department Name</label>
                                                <input type="text" class="form-control" id="department_name"
                                                    name="department_name" placeholder="ex. Security Dept." required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Branch</label>
                                                <select id="branch" name="branch" class="form-select">
                                                    @forelse ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}
                                                        </option>
                                                    @empty
                                                        <option value="">No branches found</option>
                                                    @endforelse
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-end mb-2">
                                                <x-primary-button type="button" data-bs-toggle="modal"
                                                    data-bs-target="#AddPersonnelModal">+
                                                    Add Personnel</x-primary-button>
                                            </div>
                                            <div style="max-height: 200px; overflow-y: auto;">
                                                <table class="table table-bordered table-sm">
                                                    <thead>
                                                        <tr style="font-size: smaller;">
                                                            <th style="font-size: smaller;">Name</th>
                                                            <th style="font-size: smaller;">Last Name</th>
                                                            <th style="font-size: smaller;">Position</th>
                                                            <th style="font-size: smaller;">Status</th>
                                                            <th style="font-size: smaller;">Registered Branch</th>
                                                            <th style="font-size: smaller; text-align: center;">Actions</th>
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

    {{-- Add Personnel Modal --}}
    <div class="modal fade" id="AddPersonnelModal" tabindex="-1" aria-labelledby="AddPersonnelModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AddPersonnelModalLabel">Add Personnel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 d-flex justify-content-end">
                        <input type="text" class="form-control w-50" id="searchEmployee"
                            placeholder="Search Employee">
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
                                        <td>{{ $employee->name }} {{ $employee->middle_name }} {{ $employee->last_name }}
                                        </td>
                                        <td>{{ $employee->position }}</td>
                                        <td>{{ $employee->department ? $employee->department->department_name : 'N/A' }}
                                        </td>
                                        <td>{{ $employee->branch->branch_name ?? 'N/A' }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm"
                                                onclick="selectEmployee({{ $employee->id }}, '{{ $employee->name }}', '{{ $employee->last_name }}', '{{ $employee->position }}', '{{ $employee->status }}', '{{ $employee->department ? $employee->department->department_name : 'N/A' }}', '{{ $employee->branch->branch_name ?? 'N/A' }}')">Select</button>
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
@endsection

@section('script')
    <script>
        function clearForm() {
            document.getElementById('departmentForm').reset();
            document.getElementById('personnelTableBody').innerHTML = '';
        }

        function saveDepartment() {
            document.getElementById('departmentForm').submit();
        }

        function selectEmployee(id, name, lastName, position, status, department, branch) {
            if (department !== 'N/A') {
                alert(`The employee ${name} ${lastName} already belongs to the ${department} department.`);
                return;
            }

            const tableBody = document.getElementById('personnelTableBody');
            const existingRows = tableBody.querySelectorAll('tr');
            for (let row of existingRows) {
                if (row.cells[0].textContent === name && row.cells[1].textContent === lastName) {
                    alert(`The employee ${name} ${lastName} is already selected.`);
                    return;
                }
            }

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td style="font-size: smaller;">${name}</td>
                <td style="font-size: smaller;">${lastName}</td>
                <td style="font-size: smaller;">${position}</td>
                <td style="font-size: smaller;">${status}</td>
                <td style="font-size: smaller;">${branch}</td>
                <input type="hidden" name="employees[]" value="${id}">
                <td style="font-size: smaller;"><button class="btn btn-danger btn-sm" onclick="removePersonnel(this)">Remove</button></td>
            `;
            tableBody.appendChild(newRow);

            // Close the modal
            $('#AddPersonnelModal').modal('hide');
        }

        function removePersonnel(button) {
            const row = button.closest('tr');
            row.remove();
        }

        document.getElementById('searchEmployee').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#employeeTableBody tr');
            rows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const position = row.cells[1].textContent.toLowerCase();
                const department = row.cells[2].textContent.toLowerCase();
                const branch = row.cells[3].textContent.toLowerCase();
                if (name.includes(searchValue) || position.includes(searchValue) || department.includes(
                        searchValue) || branch.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.getElementById('searchDepartment').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#departmentTableBody tr');
            rows.forEach(row => {
                const departmentName = row.cells[0].textContent.toLowerCase();
                const branchName = row.cells[1].textContent.toLowerCase();
                const status = row.cells[2].textContent.toLowerCase();
                if (departmentName.includes(searchValue) || branchName.includes(searchValue) || status
                    .includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Show success or error modal based on the session status
        @if (session('status') === 'success')
            var successAlert = document.querySelector('.alert-success');
            setTimeout(() => {
                var alert = new bootstrap.Alert(successAlert);
                alert.close();
            }, 2000);
        @elseif (session('status') === 'error')
            var errorAlert = document.querySelector('.alert-danger');
            setTimeout(() => {
                var alert = new bootstrap.Alert(errorAlert);
                alert.close();
            }, 2000);
        @endif
    </script>
@endsection
