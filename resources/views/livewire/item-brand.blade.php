<div>
   {{-- return flash message --}}
   @if (session()->has('success'))
   <div class="alert alert-success" id="success-message">
       {{ session('success') }}
       <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
   </div>
   @endif

    <div id="brand-table" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Brand List</h5>
        </div>
        <div class="card-body">
            @if (auth()->user()->employee->getModulePermission('Item Brands'))
                <x-primary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('brand-form', document.querySelector('.nav-link.active'))">+ ADD
                BRAND</x-primary-button>
            @endif
            <x-secondary-button type="button" class="mb-3 btn-sm"
                wire:click="fetchData()">Refresh</x-secondary-button>
            <div class="table-responsive mt-3 mb-3 d-flex justify-content-center"
                style="max-height: 400px; overflow-y: auto;">

                <table class="table table-striped table-sm small">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Name</th>
                            <th>DESCRIPTION</th>
                            <th class="text-end">STATUS</th>
                            <th class="text-end">REG. COMPANY</th>
                            @if (auth()->user()->employee->getModulePermission('Item Brands'))
                                <th class="text-end">ACTIONS</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($itemBrands as $brand)
                            <tr>
                                <td>{{ $brand->brand_name ?? 'Not Registered' }}</td>
                                <td> {{ $brand->brand_description }}</td>
                                <td class="text-end">{{ $brand->status }}</td>
                                <td class="text-end">{{ $brand->company->company_name ?? 'Not Registered' }}</td>
                                @if (auth()->user()->employee->getModulePermission('Item Brands'))
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-primary btn-sm" onclick="editBrand({{ json_encode($brand) }})" data-bs-toggle="modal" data-bs-target="#updateBrandModal" wire:click="editBrand({{ $brand->id }})">Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger btn-sm" wire:click="deactivate({{ $brand->id }})">Delete</a>
                                    </td>
                                @endif
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
    <div id="brand-form" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Add Brand</h5>
        </div>
        <div class="card-body">

            <x-secondary-button type="button" class="mb-3"
                onclick="showTab('brand-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
            <form wire:submit.prevent="store">
                <div class="mb-3">
                    <label for="brand_name-input_add" class="form-label">Brand Name <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="brand_name-input_add" wire:model="brand_name">
                    @error('brand_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="brand_description-input" class="form-label">Description <span
                            style="color: red;">*</span></label>
                    <textarea class="form-control" id="brand_description-input" wire:model="brand_description" rows="3"></textarea>
                    @error('brand_description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <x-primary-button type="submit">Save</x-primary-button>
            </form>
        </div>
    </div>


    {{-- Update Brand Modal --}}
    <div class="modal fade" id="updateBrandModal" tabindex="-1" aria-labelledby="updateBrandModalLabel" wire:ignore.self
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateBrandModalLabel">Update Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                        <div class="mb-3">
                            <label for="brand_name-input_update" class="form-label">Brand Name <span
                                    style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="brand_name-input_update"
                                wire:model="brand_name">
                            @error('brand_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="brand_description-input_update" class="form-label">Description <span
                                    style="color: red;">*</span></label>
                            <textarea class="form-control" id="brand_description-input_update"
                                wire:model="brand_description" rows="3"></textarea>
                            @error('brand_description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <x-primary-button type="button" wire:click="updateBrand">Update</x-primary-button>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('clearBrandForm', event => {
                // Clear the form fields
                document.getElementById('brand_name-input_add').value = '';
                document.getElementById('brand_description-input').value = '';

                // Hide the success message after 1 second
                setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
                            }, 1500);
                            document.getElementById('brand-table').style.display = 'block';
                            document.getElementById('brand-form').style.display = 'none';
            });

            window.addEventListener('clearBrandUpdateModal', event => {
                document.getElementById('brand_name-input_update').value = '';
                document.getElementById('brand_description-input_update').value = '';

                // Hide the success message after 1 second
                setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
                            }, 1500);
                // Hide the modal
                let myModal = bootstrap.Modal.getInstance(document.getElementById('updateBrandModal'));
                myModal.hide();

            });

        });

        function editBrand($data){
            // Set the form fields with the data
            document.getElementById('brand_name-input_update').value = $data.brand_name;
            document.getElementById('brand_description-input_update').value = $data.brand_description;

        }
    </script>
</div>
