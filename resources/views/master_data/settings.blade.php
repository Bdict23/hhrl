@extends('layouts.master')
@section('content')
    <div class="d-flex flex-column">

        {{-- Dropdown for mobile navigation --}}
        <div class="d-md-none text-end mb-3">
            <select class="form-select form-select-sm" onchange="navigateToTab(this.value)">
                <option value="" selected disabled>Select Tab</option>
                @if(auth()->user()->employee->getModulePermission('Manage Item') !=2)
                    <option value="items-table">Item List</option>
                @endif
                @if(auth()->user()->employee->getGroupedModulePermissions('Item Properties') !=2)
                     @if (auth()->user()->employee->getModulePermission('Item Categories') !=2)
                        <option value="category-table">Item Categories</option>
                    @endif
                    @if (auth()->user()->employee->getModulePermission('Item Classifications') !=2)
                        <option value="classification-table">Classification</option>
                    @endif
                    @if (auth()->user()->employee->getModulePermission('Item Sub-Classifications') !=2)
                        <option value="sub-classification-table">Sub Classifications</option>
                    @endif
                    @if (auth()->user()->employee->getModulePermission('Unit of Measures') !=2)
                        <option value="unit-of-measures-table">Unit of Measures</option>
                    @endif
                    @if (auth()->user()->employee->getModulePermission('Item Brands') !=2)
                        <option value="brand-table">Brands</option>
                    @endif
                @endif
                @if (auth()->user()->employee->getGroupedModulePermissions('Price Levels') !=2)
                    @if (auth()->user()->employee->getModulePermission('Item Retail Price') !=2)
                        <option value="price-levels-tables">Retail</option>
                    @endif
                    @if (auth()->user()->employee->getModulePermission('Item Cost Price') !=2)
                        <option value="items-cost">Cost</option>
                    @endif
                @endif
                @if (auth()->user()->employee->getGroupedModulePermissions('Business') !=2)
                    @if (auth()->user()->employee->getModulePermission('Business Venues') !=2)
                        <option value="venue-lists">Venues</option>
                    @endif
                    @if (auth()->user()->employee->getModulePermission('Services') !=2)
                        <option value="service-lists">Services</option>
                    @endif
                    @if (auth()->user()->employee->getModulePermission('Employee') !=2)
                        <option value="employee-management">Employees</option>
                    @endif
                    @if (auth()->user()->employee->getModulePermission('Transfer Employee') !=2)
                        <option value="transfer-employee">Transfer Employee</option>
                    @endif
                    @if (auth()->user()->employee->getModulePermission('Program Settings') !=2)
                        <option value="program-settings">Program Settings</option>
                    @endif
                @endif
            </select>
        </div>

        <div class="d-flex">

            <!-- Sidebar -->
            <div class="sidebar d-none d-md-block" style="overflow-y: auto; max-height: 500px;">
                @if(auth()->user()->employee->getModulePermission('Manage Item') !=2)
                    <h5 class="text-muted">Item Management</h5>
                        <ul class="nav flex-column">
                            <li class="nav-item"><a href="#" class="nav-link active btn-sm" onclick="showTab('items-table', this)"
                                style="background-color: #dddddd">Item List</a></li>
                        </ul>
                        <hr>
                @endif
                
                @if(auth()->user()->employee->getGroupedModulePermissions('Item Properties') !=2)
                    <h5 class="text-muted">Item Properties</h5>
                    <ul class="nav flex-column">
                        @if (auth()->user()->employee->getModulePermission('Item Categories') !=2)
                        <li class="nav-item"><a href="#" class="nav-link btn-sm"
                            onclick="showTab('category-table', this)">Item Categories</a></li>
                        @endif
                    @if (auth()->user()->employee->getModulePermission('Item Classifications') !=2)
                        <li class="nav-item"><a href="#" class="nav-link btn-sm"
                            onclick="showTab('classification-table', this)">Classification</a></li>
                        @endif
                        @if (auth()->user()->employee->getModulePermission('Item Sub-Classifications') !=2)
                            <li class="nav-item"><a href="#" class="nav-link btn-sm"
                                onclick="showTab('sub-classification-table', this)">Sub
                                Classifications</a></li>
                        @endif
                        @if (auth()->user()->employee->getModulePermission('Unit of Measures') !=2)
                            <li class="nav-item"><a href="#" class="nav-link btn-sm"
                                onclick="showTab('unit-of-measures-table', this)">Unit of
                                measures</a></li>
                    @endif
                        @if (auth()->user()->employee->getModulePermission('Item Brands') !=2)
                            <li class="nav-item"><a href="#" class="nav-link btn-sm"
                                onclick="showTab('brand-table', this)">Brands</a></li>
                            </li>
                        @endif
                        <hr>
                    </ul>
                @endif

                @if (auth()->user()->employee->getGroupedModulePermissions('Price Levels') !=2)
                    <h5>Price Level</h5>
                    <ul class="nav flex-column">
                        @if (auth()->user()->employee->getModulePermission('Item Retail Price') !=2)
                            <li class="nav-item"><a href="#" class="nav-link btn-sm"
                                onclick="showTab('price-levels-tables', this)">Retail</a></li>
                        @endif
                        @if (auth()->user()->employee->getModulePermission('Item Cost Price') !=2)
                            <li class="nav-item">
                                <a href="#" class="nav-link btn-sm" onclick="showTab('items-cost', this)">Cost</a>
                            </li>
                        @endif
                    </ul>                                                               
                    <hr>
                @endif
            
                {{-- <h5 class="text-muted">Menu Management</h5>
                <ul class="nav flex-column">
                    <li class="nav-item"><a href="#" class="nav-link btn-sm" onclick="showTab('menus-table', this)">Menu
                            List</a></li>
                </ul>
                <hr> --}}
                {{-- <h5 class="text-muted">Menu Properties</h5>
                <ul class="nav flex-column">
                    <li class="nav-item"><a href="#" class="nav-link btn-sm"
                            onclick="showTab('menu-categories-table', this)">Menu
                            Categories</a></li>
                </ul>
                <hr> --}}

                @if (auth()->user()->employee->getGroupedModulePermissions('Business') !=2)
                    <h6 class="text-muted">Business</h6>
                    @if (auth()->user()->employee->getModulePermission('Business Venues') !=2)
                        <ul class="nav flex-column">
                            <li class="nav-item"><a href="#" class="nav-link btn-sm" onclick="showTab('venue-lists', this)">Venues</a></li>
                        </ul>
                    @endif
                    @if (auth()->user()->employee->getModulePermission('Services') !=2)
                        <ul class="nav flex-column">
                            <li class="nav-item"><a href="#" class="nav-link btn-sm" onclick="showTab('service-lists', this)">Services</a></li>
                        </ul>
                    @endif
                    @if (auth()->user()->employee->getModulePermission('Employee') !=2)
                        <ul class="nav flex-column">
                            {{-- <li class="nav-item"><a href="#" class="nav-link active">Company</a></li>
                            <li class="nav-item"><a href="#" class="nav-link">Branch</a></li>
                            <li class="nav-item"><a href="#" class="nav-link">Department</a></li>
                            --}}
                            <li class="nav-item"><a href="#" class="nav-link btn-sm" onclick="showTab('employee-management', this)">Employees</a></li>
                        </ul>
                    @endif
                    @if (auth()->user()->employee->getModulePermission('Transfer Employee') !=2)
                        <ul class="nav flex-column">
                            <li class="nav-item"><a href="#" class="nav-link btn-sm" onclick="showTab('transfer-employee', this)">Transfer Employee</a></li>
                        </ul>
                    @endif
                    
                    {{-- program settings --}}
                    @if (auth()->user()->employee->getModulePermission('Program Settings') !=2)
                        <ul class="nav flex-column">
                            <li class="nav-item"><a href="#" class="nav-link btn-sm" onclick="showTab('program-settings', this)">Program Settings</a></li>
                        </ul>
                    @endif
                @endif
            </div>

            {{-- navs --}}



            

            <!-- Main Content -->
            <div class="content overflow-auto" style="max-height: 600px;">
                
                <!-- item Tab Content -->
                @if(auth()->user()->employee->getModulePermission('Manage Item') !=2)
                    <div>
                        @livewire('item-main')
                    </div>
                @endif

                <!-- category Tab Content -->
                <div>
                    @livewire('item-category')
                </div>

                <!-- classification Tab Content -->
                <div>
                    @livewire('Item-classification')
                </div>

                <!-- sub-classification Tab Content -->
                <div>
                    @livewire('item-sub-classification')
                </div>

                <!-- Unit of Measures Tab Content -->
                <div>
                    @livewire('item-unit-measure')
                </div>

                <!-- Price Levels Tab Content -->
                <div>
                    @livewire('price-operation')
                </div>

                <!-- Item Cost Tab Content --raldz-->
                <div>
                    @livewire('item-cost')
                </div>


                <!-- Brand Tab Content -->
                <div>
                    @livewire('item-brand')
                </div>


                <!-- Departments Tab Content -->
                {{-- <div>
                    @livewire('department')
                </div> --}}
                <!-- Employees Tab Content -->
                @if (auth()->user()->employee->getModulePermission('Business Venues') !=2)
                    <div>
                        @livewire('settings.venue')
                    </div>
                @endif
               
                

                <div>
                    @livewire('settings.service')
                </div>

                <div>
                    @livewire('settings.manage-employees')
                </div>
                <!-- Menu Categories Tab Content -->
                <div id="menu-categories-table" class="tab-content dashboard" style="display: none;">
                    <x-primary-button type="button" class="mb-3 btn-sm"
                        onclick="showTab('brand-form', document.querySelector('.nav-link.active'))">+ ADD
                        BRAND</x-primary-button>
                    <table class="table table-striped table-sm small">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>DESCRIPTION</th>
                                <th class="text-end">STATUS</th>
                                <th class="text-end">REG. COMPANY</th>
                                <th class="text-end">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($MenuCategories as $category)
                                <tr>
                                    <td>{{ $category->category_name ?? 'Not Registered' }}</td>
                                    <td> {{ $category->category_description }}</td>
                                    <td class="text-end">{{ $category->status }}</td>
                                    <td class="text-end">{{ $category->company->company_name ?? 'Not Registered' }}</td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-primary btn-sm">Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No menu category found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Menu Categories form -->
                <div id="menu-categories-form" class="tab-content dashboard" style="display: none;">
                    <x-secondary-button type="button" class="mb-3"
                        onclick="showTab('menu-categories-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
                    <form>
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name <span style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="name" name="category_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span
                                    style="color: red;">*</span></label>
                            <textarea class="form-control" id="description" name="category_description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Belong to Company <span
                                    style="color: red;">*</span></label>
                            <select class="form-control" id="status" name="category_status" required>
                                @forelse ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @empty
                                    <option value="no_company">No Company Available</option>
                                @endforelse
                            </select>
                        </div>
                        <x-primary-button type="submit">Save</x-primary-button>
                    </form>
                </div>

                <div>
                    @livewire('settings.program-settings')
                </div>
                <div>
                    @livewire('settings.transfer-employee')
                </div>
            </div>
        </div>
    </div>

    <!-- Description Modal -->
    <div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descriptionModalLabel">Full Description</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="descriptionModalBody">
                    <!-- Description will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        function showTab(tabId, element) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(function(tabContent) {
                tabContent.style.display = 'none';
            });

            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(function(navLink) {
                navLink.classList.remove('active');
                navLink.style.backgroundColor = ''; // Reset background color
            });

            // Show the selected tab content
            document.getElementById(tabId).style.display = 'block';

            // Add active class to the selected nav link and set background color
            if (element) {
                element.classList.add('active');
                element.style.backgroundColor = '#dddddd';
            }
        }

        function showDescriptionModal(description) {
            document.getElementById('descriptionModalBody').innerText = description;
            var descriptionModal = new bootstrap.Modal(document.getElementById('descriptionModal'));
            descriptionModal.show();
        }


        // Batch Costing
        function clearForm() {
            document.getElementById('departmentForm').reset();
            document.getElementById('personnelTableBody').innerHTML = '';
        }

        function saveDepartment() {
            document.getElementById('departmentForm').submit();
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

        // document.getElementById('searchDepartment').addEventListener('input', function() {
        //     const searchValue = this.value.toLowerCase();
        //     const rows = document.querySelectorAll('#departmentTableBody tr');
        //     rows.forEach(row => {
        //         const departmentName = row.cells[0].textContent.toLowerCase();
        //         const branchName = row.cells[1].textContent.toLowerCase();
        //         const status = row.cells[2].textContent.toLowerCase();
        //         if (departmentName.includes(searchValue) || branchName.includes(searchValue) || status
        //             .includes(searchValue)) {
        //             row.style.display = '';
        //         } else {
        //             row.style.display = 'none';
        //         }
        //     });
        // });

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

        function navigateToTab(tabId) {
            if (tabId) {
                showTab(tabId, null);
            }
        }
    </script>
@endsection

