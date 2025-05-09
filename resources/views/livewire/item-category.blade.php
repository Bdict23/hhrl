<div>
     {{-- return flash message --}}
     @if (session()->has('success'))
     <div class="alert alert-success" id="success-message">
         {{ session('success') }}
         <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>
     @endif
    <div id="category-table" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Item Category List</h5>
        </div>
        <div class="card-body">
            @if (auth()->user()->employee->getModulePermission('Item Categories') == 1 )
                <x-primary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('category-form', document.querySelector('.nav-link.active'))">+ ADD
                CATEGORY</x-primary-button>
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
                            <th class="text-end">COMPANY</th>
                            <th class="text-end"  @if (auth()->user()->employee->getModulePermission('Item Categories') != 1 ) style="display: none;"  @endif>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ItemCategories as $category)
                            <tr>
                                <td>{{ $category->category_name }}</td>
                                <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $category->category_description ?? 'N/A' }}</td>
                                <td class="text-end">{{ $category->status }}</td>
                                <td class="text-end">
                                    {{ optional($category->company)->company_name ?? 'No Company' }}
                                </td>
                                @if (auth()->user()->employee->getModulePermission('Item Categories') == 1 )
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#UpdateCategory" onclick="updateCategory({{ json_encode($category) }})" wire:click="editCategory({{ $category->id }})">Edit</button>
                                        <a href="#" class="btn btn-sm btn-danger btn-sm" wire:click="deactivate({{ $category->id }})">Delete</a>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No category found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    {{-- Update Category Modal --}}
<div class="modal fade" id="UpdateCategory" tabindex="-1" aria-labelledby="updateCategoryModal" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >Update Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="category_name-update" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="category_name-update-input" wire:model="category_name_input">
                            @error('category_name_input')

                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                        <div class=" mb-3">
                            <label for="category_description-update" class="form-label">Description</label>
                            <textarea class="form-control" id="category_description-update-input" wire:model="category_description_input" rows="3"></textarea>
                            @error('category_description_input')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    <x-primary-button type="button" wire:click="updateCategory">Update</x-primary-button>
            </div>
        </div>
    </div>
</div>

    {{-- Category Form --}}
    <div id="category-form" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Add Category</h5>
        </div>
        <div class="card-body">
            <x-secondary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('category-table', document.querySelector('.nav-link.active'))">Summary</x-secondary-button>
            <form wire:submit.prevent="storeCategory">
                @csrf
                <div class="mb-3">
                    <label for="category_name-input" class="form-label">Category Name <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="category_name-input" wire:model="category_name_input" >
                    @error('category_name_input')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="category_description-input" class="form-label">Description <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="category_description-input" wire:model="category_description_input" rows="3" ></textarea>
                    @error('category_description_input')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <x-primary-button type="submit">Save</x-primary-button>
            </form>
        </div>
    </div>


    <script>
        // Listen for the DOMContentLoaded event
        document.addEventListener('DOMContentLoaded', function() {
            // Listen wire:success event
            window.addEventListener('clearForm', event => {
                // Clear the form fields
        document.getElementById('category_name-input').value = '';
        document.getElementById('category_description-input').value = '';

        // Hide the success message after 1 second
                setTimeout(function() {
        document.getElementById('success-message').style.display = 'none';
                            }, 1500);
        });
        });

        // HIDE UPDATEcATEGORY MODAL
        window.addEventListener('hideUpdateCategoryModal', event => {
            // Clear the form fields
        document.getElementById('category_name-update-input').value = '';
        document.getElementById('category_description-update-input').value = '';
            // Hide the modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('UpdateCategory'));
            modal.hide();

            // Hide the success message after 1 second
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 1500);
        });

        function updateCategory($data) {
            // Set the values of the input fields
            console.log($data);
            document.getElementById('category_name-update-input').value = $data.category_name;
            document.getElementById('category_description-update-input').value = $data.category_description;

        }

    </script>
</div>
