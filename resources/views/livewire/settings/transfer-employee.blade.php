<div>
     {{-- return flash message --}}
     @if (session()->has('success'))
     <div class="alert alert-success" id="success-message">
         {{ session('success') }}
         <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>
     @endif


    <div id="transfer-employee" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Employee Lists</h5>
        </div>
        <div class="card-body">
            {{-- @if (auth()->user()->employee->getModulePermission('Item Categories') == 1 ) 
                <x-primary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('category-form', document.querySelector('.nav-link.active'))">+ ADD
                CATEGORY</x-primary-button>
            @endif--}}
               <div class="row">
                    <div class="col-md-6">
                        <x-secondary-button type="button" class=" btn-sm" wire:click="fetchData()">Refresh</x-secondary-button>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1">Search</span>
                            <input type="text" class="form-control" placeholder="Search Employee" onkeyup="filterTable()" id="search-input" aria-label="Search" aria-describedby="basic-addon1">
                        </div>
                            <script>
                                function filterTable() {
                                    const input = document.querySelector('#search-input');
                                    const filter = input.value.toLowerCase();
                                    const table = document.querySelector('#employee-table');
                                    const rows = table.getElementsByTagName('tr');

                                    for (let i = 0; i < rows.length; i++) {
                                        const cells = rows[i].getElementsByTagName('td');
                                        let found = false;

                                        for (let j = 0; j < cells.length; j++) {
                                            if (cells[j]) {
                                                const txtValue = cells[j].textContent || cells[j].innerText;
                                                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                                                    found = true;
                                                    break;
                                                }
                                            }
                                        }

                                        rows[i].style.display = found ? "" : "none";
                                    }
                                }
                            </script>
                    </div>
                </div>
                
            <div class="table-responsive mt-3 mb-3 d-flex justify-content-center" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-sm small">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>NAME</th>
                            <th>POSITION</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-center">COMPANY</th>
                            <th class="text-center">BRANCH</th>
                             @if (auth()->user()->employee->getModulePermission('Transfer Employee') == 1 )
                                <th class="text-end" >ACTIONS</th>
                             @endif
                        </tr>
                    </thead>
                    <tbody id="employee-table">
                        @forelse ($employees as $employee)
                            <tr>
                                <td>{{ ($employee->name ?? '') . ($employee->middle_name ?? '') . ' ' . ($employee->last_name ?? '') }}</td>
                                <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $employee->position->position_name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $employee->status }}</td>
                                <td class="text-center">
                                    {{ optional($employee->branch->company)->company_name ?? 'No Company' }}
                                </td>
                                <td class="text-center">
                                    {{ optional($employee->branch)->branch_name ?? 'No Branch' }}
                                </td>
                                @if (auth()->user()->employee->getModulePermission('Transfer Employee') == 1 )
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#transferEmployeeModal" onclick="updateCategory({{ json_encode($employee) }})" wire:click="selectEmployee({{ $employee->id }})">Transfer</button>
                                    </td>
                                @endif
                            </tr>
                            
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No employee found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

     {{-- Modal --}}
    <div class="modal fade" id="transferEmployeeModal" tabindex="-1" aria-labelledby="updateCategoryModal" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Transfer Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" wire:submit.prevent="confirmTransferEmployee">
                            <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="category_name-update" class="form-label">Employee Name</label>
                                <input value="{{ ($selectedEmployee->name ?? '') . ' ' . ($selectedEmployee->middle_name ?? '') . ' ' . ($selectedEmployee->last_name ?? '' ) }}" type="text" class="form-control" id="category_name-update-input" readonly>
                                @error('selectedEmployee')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class=" mb-3">
                                <label class="form-label">Company</label>
                                <select class="form-control" id="" wire:change="fetchBranches($event.target.value)">
                                    <option value="">Select Company</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedCompany')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="category_name-update" class="form-label">Branch</label>
                                    <select wire:change='selectBranch($event.target.value)' class="form-control">
                                        <option value="">Select Branch</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                        @endforeach
                                    </select>
                                @error('selectedBranch')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <x-primary-button type="submit">Confirm Transfer</x-primary-button>

                    </form>
                </div>
            </div>
        </div>
    </div> 
    <script>
        window.addEventListener('close-modal', function () {
            const modal = bootstrap.Modal.getInstance(document.getElementById('transferEmployeeModal'));
            if (modal) {
                modal.hide();
            }
        });
    </script> 
</div>
