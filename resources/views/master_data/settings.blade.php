@extends('layouts.master')
@section('content')
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">

            <h5 class="text-muted">Item Management</h5>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="#" class="nav-link btn-sm" onclick="showTab('items-table', this)">Item
                        List</a></li>
            </ul>
            <hr>
            <h5 class="text-muted">Item Properties</h5>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="#" class="nav-link active btn-sm"
                        onclick="showTab('category-table', this)" style="background-color: #dddddd">Item Categories</a></li>
                <li class="nav-item"><a href="#" class="nav-link btn-sm"
                        onclick="showTab('classification-table', this)">Classification</a></li>
                <li class="nav-item"><a href="#" class="nav-link btn-sm"
                        onclick="showTab('sub-classification-table', this)">Sub
                        Classifications</a></li>

                <li class="nav-item"><a href="#" class="nav-link btn-sm"
                        onclick="showTab('unit-of-measures-table', this)">Unit of
                        measures</a></li>
                <li class="nav-item"><a href="#" class="nav-link btn-sm"
                        onclick="showTab('price-levels-table', this)">Price
                        Levels</a></li>
                <li class="nav-item"><a href="#" class="nav-link btn-sm"
                        onclick="showTab('brand-table', this)">Brands</a></li>
                </li>
                <hr>

            </ul>
            <h5 class="text-muted">Menu Management</h5>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="#" class="nav-link btn-sm" onclick="showTab('menus-table', this)">Menu
                        List</a></li>
            </ul>
            <hr>
            <h5 class="text-muted">Menu Properties</h5>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="#" class="nav-link btn-sm"
                        onclick="showTab('menu-categories-table', this)">Menu
                        Categories</a></li>
            </ul>
            <hr>
            <h6 class="text-muted">Business</h6>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="#" class="nav-link active">Company</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Branch</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Employees</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Departments</a></li>

            </ul>
        </div>

        <!-- Main Content -->
        <div class="content">

            <!-- item Tab Content -->
            <div>
                @livewire('item-lists')
            </div>

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
            <div id="unit-of-measures-table" class="tab-content dashboard" style="display: none;">
                <x-primary-button type="button" class="mb-3 btn-sm">+ ADD UNIT OF MEASURE</x-primary-button>
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
                        <tr>
                            <td colspan="5" class="text-center">No unit of measure found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Price Levels Tab Content -->
            <div id="price-levels-table" class="tab-content dashboard" style="display: none;">
                <x-primary-button type="button" class="mb-3 btn-sm">+ ADD PRICE LEVEL</x-primary-button>
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
                        <tr>
                            <td colspan="5" class="text-center">No price level found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Brand Tab Content -->
            <div id="brand-table" class="tab-content card" style="display: none;">
                <div class="card-header">
                    <h5>Brand List</h5>
                </div>
                <div class="card-body">
                    <x-primary-button type="button" class="mb-3 btn-sm"
                        onclick="showTab('brand-form', document.querySelector('.nav-link.active'))">+ ADD
                        BRAND</x-primary-button>
                    <div class="table-responsive mt-3 mb-3 d-flex justify-content-center"
                        style="max-height: 400px; overflow-y: auto;">

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
                                @forelse ($brands as $brand)
                                    <tr>
                                        <td>{{ $brand->brand_name ?? 'Not Registered' }}</td>
                                        <td> {{ $brand->brand_description }}</td>
                                        <td class="text-end">{{ $brand->status }}</td>
                                        <td class="text-end">{{ $brand->company->company_name ?? 'Not Registered' }}</td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-primary btn-sm">Edit</a>
                                            <a href="#" class="btn btn-sm btn-danger btn-sm">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No brand found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Brand form -->
            <div id="brand-form" class="tab-content card" style="display: none;">
                <div class="card-header">
                    <h5>Add Brand</h5>
                </div>
                <div class="card-body">

                    <x-secondary-button type="button" class="mb-3"
                        onclick="showTab('brand-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
                    <form>
                        <div class="mb-3">
                            <label for="name" class="form-label">Brand Name <span
                                    style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="name" name="brand_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span
                                    style="color: red;">*</span></label>
                            <textarea class="form-control" id="description" name="brand_description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Belong to Company <span
                                    style="color: red;">*</span></label>
                            <select class="form-control" id="status" name="brand_status" required>
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
            </div>

            <!-- Menu Categories Tab Content -->
            <div id="menu-categories-table" class="tab-content dashboard" style="display: none;">
                <x-primary-button type="button" class="mb-3 btn-sm"
                    onclick="showTab('menu-categories-form', document.querySelector('.nav-link.active'))">+ ADD MENU
                    CATEGORY</x-primary-button>
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
    </script>
@endsection
