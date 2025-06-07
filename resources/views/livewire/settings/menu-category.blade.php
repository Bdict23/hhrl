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
    <div id="menu-category-lists" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Menu Category Lists</h5>
        </div>
        <div class="card-body">
            {{-- @if (auth()->user()->employee->getModulePermission('Business Venues') == 1 ) --}}
                <x-primary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('menu-category-form', document.querySelector('.nav-link.active'))">+ ADD
                Category</x-primary-button>
            {{-- @endif --}}
                <x-secondary-button type="button" class="mb-3 btn-sm"
                wire:click="fetchData()">Refresh</x-secondary-button>
            <div class="table-responsive mt-3 mb-3 d-flex justify-content-center"
                style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-sm small">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Category Name</th>
                            <th>DESCRIPTION</th>
                            <th>STATUS</th>
                            <th>CREATED</th>
                            <th class="text-end"  @if (auth()->user()->employee->getModulePermission('Business Venues') != 1 ) style="display: none;"  @endif>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($menuCategories as $category)
                            <tr>
                                <td>{{ $category->category_name }}</td>
                                <td>{{ $category->category_description ?? 'N/A' }}</td>
                                <td>{{ $category->status }}</td>
                                <td>{{ $category->created_at->format('M-d-Y') }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateMenuCategoryModal" onclick="updateMenuCategory({{ json_encode($category) }})" wire:click="editMenuCategory({{ $category->id }})">Edit</button>
                                    <a href="#" class="btn btn-sm btn-danger" wire:click="deactivateMenuCategory({{ $category->id }})">Remove</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No Menu Category found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    {{-- Update Modal --}}
    <div class="modal fade" id="updateMenuCategoryModal" tabindex="-1" aria-labelledby="updateMenuCategory" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Update Menu Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" wire:submit.prevent="updateMenuCategory" id="UpdateMenuCategoryForm">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="menu_category_name-update" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="menu_category_name-update-input" wire:model="menu_category_name_input">
                                @error('menu_category_name_input')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                        </div>
                            <div class=" mb-3">
                                <label for="menu_category_description-update" class="form-label">Category Description</label>
                                <textarea class="form-control" id="menu_category_description-update-input" wire:model="menu_category_description_input" rows="3"></textarea>
                                @error('menu_category_description_input')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <x-primary-button type="submit">Update</x-primary-button>
                        </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Category Form --}}
    <div id="menu-category-form" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Add Menu Category</h5>
        </div>
        <div class="card-body">
            <x-secondary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('menu-category-lists', document.querySelector('.nav-link.active'))">Summary</x-secondary-button>
            <form wire:submit.prevent="storeMenuCategory" id="menuCategoryForm">
                @csrf
                <div class="mb-3">
                    <label for="menu_category_name-input" class="form-label"> Category Name <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="menu_category_name-input" wire:model="menu_category_name_input" >
                    @error('menu_category_name_input')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="menu_category_description-input" class="form-label">Category Description <span class="text-muted text-small">(optional)</span></label>
                    <textarea class="form-control" id="menu_category_description-input" wire:model="menu_category_description_input" rows="3"></textarea>
                    @error('menu_category_description_input')
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
            window.addEventListener('clearMenuCategoryForm', event => {
                document.getElementById('menuCategoryForm').reset();
            });

            
            // Listen for the success event
            window.addEventListener('success', event => {
                // Show the success message
                document.getElementById('success-message').style.display = 'block';
                document.getElementById('success-message').innerHTML = event.detail.message;

        // Hide the success message after 1 second
                setTimeout(function() {
        document.getElementById('success-message').style.display = 'none';
                            }, 1500);
        });
        });

        // HIDE UPDATE CATEGORY MODAL
        window.addEventListener('closeEditMenuCategoryModal', event => {
            // Clear the form fields
        document.getElementById('menu_category_name-update-input').value = '';
        document.getElementById('menu_category_description-update-input').value = '';
            // Hide the modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('updateMenuCategoryModal'));
            modal.hide();

            // Hide the success message after 1 second
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 1500);
        });

        function updateMenuCategory($data) {
            // Set the values of the input fields
            console.log($data);
            document.getElementById('menu_category_name-update-input').value = $data.category_name;
            document.getElementById('menu_category_description-update-input').value = $data.category_description;

        }

    </script>
</div>
