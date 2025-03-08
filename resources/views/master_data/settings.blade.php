@extends('layouts.master')
@section('content')
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <h5 class="text-muted">Item Management</h5>
            <hr>
            <h5 class="text-muted">Item Settings</h5>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="#" class="nav-link active btn-sm" onclick="showTab('category-table', this)"
                        style="background-color: #dddddd">Categories</a></li>
                <li class="nav-item"><a href="#" class="nav-link btn-sm"
                        onclick="showTab('classification-table', this)">Classification</a></li>
                <li class="nav-item"><a href="#" class="nav-link btn-sm"
                        onclick="showTab('sub-classification-table', this)">Sub
                        Classifications</a></li>
                <li class="nav-item"><a href="#" class="nav-link btn-sm"
                        onclick="showTab('types-table', this)">Types</a>
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
                <table class="table table-striped table-sm">
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
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->category_name }}</td>
                                <td>{{ $category->category_description ?? 'N/A' }}</td>
                                <td class="text-end">{{ $category->status }}</td>
                                <td class="text-end">{{ optional($category->company)->company_name ?? 'No Company' }}</td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No category found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Category Form --}}
            <div id="category-form" class="tab-content dashboard" style="display: none;">
                <x-secondary-button type="button" class="mb-3"
                    onclick="showTab('category-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
                <form action="{{ route('settings.category.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="category_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="category_description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="category_status" required>
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
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>DESCRIPTION</th>
                            <th class="text-end">STATUS</th>
                            <th class="text-end">Sub Classes</th>
                            <th class="text-end">REG. COMPANY</th>
                            <th class="text-end">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($classifications as $classification)
                            <tr>
                                <td>{{ $classification->classification_name }}</td>
                                <td>{{ $classification->classification_description }}</td>
                                <td class="text-end">{{ $classification->status }}</td>
                                <td class="text-end">{{ optional($classification->sub_classifications)->count() ?? 0 }}
                                </td>
                                <td class="text-end">{{ $classification->company->company_name ?? 'Not Registered' }}</td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No classification found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Classification Form --}}
            <div id="classification-form" class="tab-content dashboard" style="display: none;">
                <x-secondary-button type="button" class="mb-3"
                    onclick="showTab('classification-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
                <form action="{{ route('settings.classification.store') }}" method="POST">
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
                <x-primary-button type="button" class="mb-3 btn-sm">+ Add Sub Classification</x-primary-button>
                <table class="table table-striped table-sm">
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

                        @forelse ($sub_classifications as $sub_classification)
                            <tr>
                                <td>{{ $sub_classification->classification_name ?? 'Not Registered' }}</td>
                                <td>{{ $sub_classification->classification_description }}</td>
                                <td class="text-end">{{ $classification->status }}</td>
                                <td class="text-end">
                                    {{ $sub_classification->classification->classification_name ?? 'Not Registered' }}</td>
                                <td class="text-end">{{ $classification->company->company_name ?? 'Not Registered' }}
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No sub classification found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Types Tab Content -->
            <div id="types-table" class="tab-content dashboard" style="display: none;">
                <x-primary-button type="button" class="mb-3 btn-sm">+ ADD TYPE</x-primary-button>
                <table class="table table-striped table-sm">
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
                        @forelse ($types as $type)
                            <tr>
                                <td>{{ $type->type_name ?? 'Not Registered' }}</td>
                                <td>
                                    <div class="description-container">
                                        <span
                                            class="short-description">{{ Str::limit($type->type_description, 20) }}</span>
                                        <span class="full-description">{{ $type->type_description }}</span>
                                        @if (strlen($type->type_description) > 20)
                                            <a href="#" class="expand-description"
                                                onclick="showDescriptionModal('{{ $type->type_description }}'); return false;"
                                                style="font-size: smaller">Read</a>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">{{ $type->status }}</td>
                                <td class="text-end">{{ $type->company->company_name ?? 'Not Registered' }}</td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No type found</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            <!-- Unit of measures Tab Content -->
            <div id="unit-of-measures-table" class="tab-content dashboard" style="display: none;">
                <x-primary-button type="button" class="mb-3 btn-sm">+ ADD UNIT OF MEASURE</x-primary-button>
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>DESCRIPTION</th>
                            <th class="text-end">SYMBOL</th>
                            <th class="text-end">REG. COMPANY</th>
                            <th class="text-end">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($unit_of_measures as $unit_of_measure)
                            <tr>
                                <td>{{ $unit_of_measure->unit_name ?? 'Not Registered' }}</td>
                                <td> {{ $unit_of_measure->unit_description }}</td>
                                <td class="text-center">{{ $unit_of_measure->unit_symbol }}</td>
                                <td class="text-end">{{ $unit_of_measure->company->company_name ?? 'Not Registered' }}
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No unit of measure found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Price Levels Tab Content -->
            <div id="price-levels-table" class="tab-content dashboard" style="display: none;">
                <x-primary-button type="button" class="mb-3 btn-sm">+ ADD PRICE LEVEL</x-primary-button>
                <table class="table table-striped table-sm">
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
                <x-primary-button type="button" class="mb-3 btn-sm">+ ADD BRAND</x-primary-button>
                <table class="table table-striped table-sm">
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

    <style>
        .short-description {
            display: inline;
        }

        .full-description {
            display: none;
        }
    </style>

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
