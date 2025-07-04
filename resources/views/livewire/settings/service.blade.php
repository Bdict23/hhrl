<div>
     {{-- return flash message --}}
     @if (session()->has('success'))
        <div class="alert alert-success" id="success-message">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
     @endif
     @if (session()->has('error'))
        <div class="alert alert-danger" id="success-message">
            {{ session('error') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
     @endif
    <div id="service-lists" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Services Lists</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    @if (auth()->user()->employee->getModulePermission('Services') == 1 )
                        <x-primary-button type="button" class="mb-3 btn-sm"
                        onclick="showTab('service-form', document.querySelector('.nav-link.active'))">+ ADD
                        SERVICE</x-primary-button>
                    @endif
                        <x-secondary-button type="button" class="mb-3 btn-sm"
                        wire:click="fetchData()">Refresh</x-secondary-button>
                </div>
                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Search</span>
                        <input type="text" class="form-control" id="search-service"
                            onkeyup="filterServices()">
                    </div>
                </div>
            </div>
            <script>
                function filterServices() {
                    const input = document.getElementById('search-service');
                    const filter = input.value.toLowerCase();
                    const table = document.querySelector('#service-lists table');
                    const trs = table.querySelectorAll('tbody tr');
                    trs.forEach(row => {
                        // Skip "No Service found" row
                        if (row.children.length < 2) return;
                        const name = row.children[0].textContent.toLowerCase();
                        const code = row.children[1].textContent.toLowerCase();
                        if (name.includes(filter) || code.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }
            </script>
            <div class="table-responsive mt-3 mb-3 d-flex justify-content-center"
                style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-sm small">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th class="text-xs">NAME</th>
                            <th class="text-xs">CODE</th>
                            <th class="text-xs">TYPE</th>
                            <th class="text-xs">DESCRIPTION</th>
                            <th class="text-xs">CATEGORY</th>
                            <th class="text-xs">MULTIPLIER</th>
                            <th class="text-xs">PRICE</th>
                            <th class="text-end text-xs"  @if (auth()->user()->employee->getModulePermission('Services') != 1 ) style="display: none;"  @endif>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                       @forelse ($services as $service)
                        <tr>
                            <td class="text-xs">{{ $service->service_name }}</td>
                            <td class="text-xs">{{ $service->service_code }}</td>
                            <td class="text-xs">{{ $service->service_type }}</td>
                            <td class="text-xs">{{ $service->service_description }}</td>
                            <td class="text-xs">{{ $service->category ? $service->category->category_name : 'N/A' }}</td>
                            <td class="text-xs">
                                @if ($service->has_multiplier)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-info">No</span>
                                @endif
                            </td>
                            <td class="text-xs">
                                @if ($service->ratePrice)
                                    {{ $service->ratePrice->amount }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="text-end text-xs" @if (auth()->user()->employee->getModulePermission('Services') != 1 ) style="display: none;"  @endif>
                                <x-secondary-button class="btn-sm" wire:click="editService({{ $service->id }})" onclick="updateService({{ json_encode($service) }})"
                                    data-bs-toggle="modal" data-bs-target="#UpdateService">Edit</x-secondary-button>
                                <x-danger-button class="btn-sm" wire:click="deactivateService({{ $service->id }})">remove</x-danger-button>
                            </td>
                        </tr>
                       @empty
                            {{-- If no services found --}}
                            <tr>
                                <td colspan="7" class="text-center text-muted">No services found.</td>
                            </tr>
                       @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Update Category Modal --}}
    <div class="modal fade" id="UpdateService" tabindex="-1" aria-labelledby="updateServiceModal" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Update Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" wire:submit.prevent="updateService" id="UpdateServiceForm">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="service_name-update" class="form-label">Service Name</label>
                                <input type="text" class="form-control" id="service_name-update-input" wire:model="service_name_input">
                                @error('service_name_input')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                           <div class="row col-md-12 mb-3">
                                
                                <div class="col-md-6 mb-3">
                                    <label for="service_type-update" class="form-label">Service Type</label>
                                    <select name="" id="selectServiceTypeUpdate" class="form-control" wire:model="service_type_input">
                                        <option value="">Select</option>
                                        <option value="INTERNAL">Internal</option>
                                        <option value="EXTERNAL">External</option>
                                    </select>
                                    @error('service_type_input')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="catgory-update" class="form-label">Category</label>
                                    <div class="input-group">
                                        <select name="" id="selectServiceCategoryUpdate" class="form-control" wire:model="selectedCategoryId">
                                            <option value="">Select</option>
                                            @forelse ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @empty
                                                
                                            @endforelse
                                        </select>
                                        <button type="button"  style="background-color: rgb(190, 243, 217);" class="input-group-text" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                            Add
                                        </button>
                                    </div>
                                </div>
                           </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="service_code-update" class="form-label">Service Code</label>
                                    <input type="text" class="form-control" id="service_code-update-input" wire:model="service_code_input">
                                    @error('service_code_input')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                               <div class="col-md-6 mb-3">
                                    <label for="service_rate-input-update" class="form-label">Rate Price <span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" id="service_rate-input-update" wire:model="service_rate_input">
                                        <div class="input-group-text" style="background-color: rgb(230, 225, 225)">
                                            <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="service-multiplier-update" wire:model="service_multiplier_input">
                                                    <label class="form-check-label text-xs" for="service-multiplier-update"><strong>Multiplier</strong></label>
                                            </div>
                                        </div>
                                        @error('service_rate_input')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        @error('service_multiplier_input')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @if ($service_type_input == 'EXTERNAL')
                                <div class="col-md-6 mb-3">
                                    {{-- Show service cost input only for EXTERNAL service type --}}
                                    <label for="service_cost-update" class="form-label">Service Cost</label>
                                    <input type="number" step="0.01" class="form-control" id="service_cost-update-input" wire:model="service_cost_input">
                                    @error('service_cost_input')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                            <div class=" mb-3">
                                <label for="service_description-update" class="form-label">Description</label>
                                <textarea class="form-control" id="service_description-update-input" wire:model="service_description_input" rows="3"></textarea>
                                @error('category_description_input')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <x-primary-button type="submit">Update</x-primary-button>
                        </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Service Form --}}
    <div id="service-form" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Add Service</h5>
        </div>
        <div class="card-body">
            <x-secondary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('service-lists', document.querySelector('.nav-link.active'))">Summary</x-secondary-button>
            <form wire:submit.prevent="storeService" id="serviceForm">
                @csrf
                <div class="mb-3 row">
                    <div class="col-md-4">
                        <label for="service_name-input" class="form-label">Service Name <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="service_name-input" wire:model="service_name_input" >
                        @error('service_name_input')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="service-category" class="form-label">Category <span style="color: red;">*</span></label>
                        <div class="input-group">
                            <select name="" id="" class="form-control" wire:model="selectedCategoryId">
                                <option value="">Select Category</option>
                                @forelse ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @empty
                                    
                                @endforelse
                            </select>
                                <button type="button"  style="background-color: rgb(190, 243, 217);" class="input-group-text" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                   Add
                                </button>
                        </div>
                        @error('selectedCategoryId')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="service_type-input" class="form-label">Service Type <span style="color: red;">*</span></label>
                        <select name="" id="service_type-input" class="form-control" wire:model.live="service_type_input">
                            <option value="">Select</option>
                            <option value="INTERNAL">Internal</option>
                            <option value="EXTERNAL">External</option>
                        </select>
                        @error('service_type_input')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 mb-6">
                        <label for="service_code-input" class="form-label">Service Code <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="service_code-input" wire:model="service_code_input" >
                        @error('service_code_input')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-6">
                        <label for="service_rate" class="form-label">Rate Price <span style="color: red;">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control" id="service_rate" wire:model="service_rate_input">
                            <div class="input-group-text" style="background-color: rgb(230, 225, 225)">
                               <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="service-multiplier" wire:model="service_multiplier_input">
                                    <label class="form-check-label text-xs" for="service-multiplier"><strong>Multiplier</strong></label>
                               </div>
                            </div>
                            @error('service_rate_input')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            @error('service_multiplier_input')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    @if ($service_type_input == 'EXTERNAL')
                        <div class="col-md-4 mb-6">
                            {{-- Show service cost input only for EXTERNAL service type --}}
                            <label for="service_cost-input" class="form-label">Service Cost</label>
                            <input type="number" step="0.01" class="form-control" id="service_cost-input" wire:model="service_cost_input">
                            @error('service_cost_input')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        @endif
                <div class="mb-3">
                    <label for="service_description-input" class="form-label">Description <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="service_description-input" wire:model="service_description_input" rows="3" ></textarea>
                    @error('service_description_input')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
               <div class="d-flex justify-content-end">
                 <x-primary-button type="submit">Save</x-primary-button>
               </div>
            </form>
        </div>
    </div>

    {{-- modal --}}
        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">Add Service Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="storeServiceCategory" id="addCategoryForm">
                            <div class="mb-3">
                                <label for="service_category_input" class="form-label">Category Name <span style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="service_category_input" wire:model="service_category_add_input">
                                @error('service_category_add_input')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="service_category_description_input" class="form-label">Description</label>
                                <textarea class="form-control" id="service_category_description_input" wire:model="service_category_description_input" rows="3"></textarea>
                                @error('service_category_description_input')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <x-primary-button type="submit">Add Category</x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <script>
        // Listen for the DOMContentLoaded event
        document.addEventListener('DOMContentLoaded', function() {
            // Listen wire:success event
            window.addEventListener('clearForm', event => {
                document.getElementById('serviceForm').reset();
            });

            // Show the venue lists tab by default
            // showTab('venue-lists', document.querySelector('.nav-link.active'));
        });

        // HIDE UPDATEcATEGORY MODAL
        window.addEventListener('hideUpdateServiceModal', event => {
            // Reset the form
            document.getElementById('UpdateServiceForm').reset();
            // Hide the modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('UpdateService'));
            modal.hide();

            // Hide the success message after 1 second
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 1500);
        });

        window.addEventListener('clearCategoryForm', event => {
            // Reset the form
            document.getElementById('addCategoryForm').reset();
            //hide the modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
            modal.hide();
            // Hide the success message after 1 second
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 1500);
        });

        function updateService($data) {
            // Set the values of the input fields
            console.log($data);
            // console.log($data.rate_price.amount);
            document.getElementById('service_name-update-input').value = $data.service_name;
            document.getElementById('service_code-update-input').value = $data.service_code;
            document.getElementById('service_description-update-input').value = $data.service_description;
            if($data.rate_price){
                document.getElementById('service_rate-input-update').value = $data.rate_price.amount;
            } else {
                document.getElementById('service_rate-input-update').value = '0.00';
            }
            document.getElementById('service-multiplier-update').checked = $data.has_multiplier;
            // Set the selected category
            var selectElement = document.getElementById('selectServiceCategoryUpdate');
            selectElement.value = $data.category ? $data.category.id : '';
        
        }

    </script>
</div>
