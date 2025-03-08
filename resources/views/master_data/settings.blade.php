@extends('layouts.master')
@section('content')
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <h5 class="text-muted">Item Management</h5>
            <hr>
            <h5 class="text-muted">Item Settings</h5>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="" class="nav-link active" onclick="showTab('category-table', this)"
                        style="background-color: #dddddd">Categories</a></li>
                <li class="nav-item"><a href="#" class="nav-link"
                        onclick="showTab('classification-table', this)">Classification</a></li>
                <li class="nav-item"><a href="#" class="nav-link"
                        onclick="showTab('sub-classification-table', this)">Sub
                        Classifications</a></li>
                <li class="nav-item"><a href="#" class="nav-link" onclick="showTab('types-table', this)">Types</a>
                </li>
                <li class="nav-item"><a href="#" class="nav-link"
                        onclick="showTab('unit-of-measures-table', this)">Unit of
                        measures</a></li>
                <li class="nav-item"><a href="#" class="nav-link" onclick="showTab('price-levels-table', this)">Price
                        Levels</a></li>
                <li class="nav-item"><a href="#" class="nav-link" onclick="showTab('brand-table', this)">Brand</a>
                </li>
            </ul>
            <hr>
            <h6 class="text-muted">Business</h6>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="#" class="nav-link active">Company</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Branch</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="content">
            <!-- category Tab Content -->
            <div id="category-table" class="tab-content dashboard">
                <x-primary-button type="button" class="mb-3"
                    onclick="showTab('category-form', document.querySelector('.nav-link.active'))">+ ADD
                    CATEGORY</x-primary-button>
                <table class="table table-striped">
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
                            <td colspan="5" class="text-center">No category found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- Category Form --}}
            <div id="category-form" class="tab-content dashboard" style="display: none;">
                <x-secondary-button type="button" class="mb-3"
                    onclick="showTab('category-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
                <form>
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reg_company" class="form-label">Registered Company</label>
                        <select class="form-control" id="reg_company" name="reg_company" required>
                            <option value="active">No Company Registered</option>
                        </select>
                    </div>
                    <x-primary-button type="submit">Save Category</x-primary-button>
                </form>
            </div>

            <!-- classification Tab Content -->
            <div id="classification-table" class="tab-content dashboard" style="display: none;">
                <x-primary-button type="button" class="mb-3"
                    onclick="showTab('classification-form', document.querySelector('.nav-link.active'))">+ Add
                    Classification</x-primary-button>
                <table class="table table-striped">
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
                            <td colspan="5" class="text-center">No classification found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- Classification Form --}}
            <div id="classification-form" class="tab-content dashboard" style="display: none;">
                <x-secondary-button type="button" class="mb-3"
                    onclick="showTab('classification-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
                <form>
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Classification Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reg_company" class="form-label">Registered Company</label>
                        <select class="form-control" id="reg_company" name="reg_company" required>
                            <option value="active">No Company Registered</option>
                        </select>
                    </div>
                    <x-primary-button type="submit">Save Classification</x-primary-button>
                </form>
            </div>

            <!-- sub-classification Tab Content -->
            <div id="sub-classification-table" class="tab-content dashboard" style="display: none;">
                <x-primary-button type="button" class="mb-3">+ Add Sub Classification</x-primary-button>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>DESCRIPTION</th>
                            <th class="text-end">STATUS</th>
                            <th class="text-end">Parent Class</th>
                            <th class="text-end">REG. COMPANY</th>
                            <th class="text-end">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center">No sub-classification found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Types Tab Content -->
            <div id="types-table" class="tab-content dashboard" style="display: none;">
                <x-primary-button type="button" class="mb-3">+ ADD TYPE</x-primary-button>
                <table class="table table-striped">
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
                            <td colspan="5" class="text-center">No item type found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Unit of measures Tab Content -->
            <div id="unit-of-measures-table" class="tab-content dashboard" style="display: none;">
                <x-primary-button type="button" class="mb-3">+ ADD UNIT OF MEASURE</x-primary-button>
                <table class="table table-striped">
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
                <x-primary-button type="button" class="mb-3">+ ADD PRICE LEVEL</x-primary-button>
                <table class="table table-striped">
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
            <div id="brand-table" class="tab-content dashboard" style="display: none;">
                <x-primary-button type="button" class="mb-3">+ ADD BRAND</x-primary-button>
                <table class="table table-striped">
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
                            <td colspan="5" class="text-center">No brand found</td>
                        </tr>
                    </tbody>
                </table>
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
    </script>
@endsection
