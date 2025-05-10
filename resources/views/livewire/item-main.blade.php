<div>

     {{-- return flash message --}}
     @if (session()->has('item-main-success'))
     <div class="alert alert-success" id="success-message">
         {{ session('item-main-success') }}
         <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>
     @endif

     <script>
        document.addEventListener('DOMContentLoaded', function () {
            
                setTimeout(function () {
                       var successMessage = document.getElementById('success-message');
                       if (successMessage) {
                           successMessage.style.display = 'none';
                       }
                   }, 1500);
               // Listen for the clearForm event
               window.addEventListener('saved', function (event) {
                   setTimeout(function () {
                       var successMessage = document.getElementById('success-message');
                       if (successMessage) {
                           successMessage.style.display = 'none';
                       }
                   }, 1500);
               });
               window.addEventListener('propertyAdded', function (event) {
                   setTimeout(function () {
                       var successMessage = document.getElementById('success-message');
                       if (successMessage) {
                           successMessage.style.display = 'none';
                       }
                   }, 1500);
               });

               // Search functionality
               document.getElementById('searchItems').addEventListener('input', function () {
                   const searchValue = this.value.toLowerCase();
                   const rows = document.querySelectorAll('#items-table tbody tr');

                   rows.forEach(row => {
                       const itemCode = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                       const itemName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                       const category = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                       const classification = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                       const subClass = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

                       if (itemCode.includes(searchValue) || itemName.includes(searchValue) || 
                           category.includes(searchValue) || classification.includes(searchValue) || 
                           subClass.includes(searchValue)) {
                           row.style.display = '';
                       } else {
                           row.style.display = 'none';
                       }
                   });
               });
        });
    </script>

    <div id="items-table" class="tab-content card" style="display: block" wire:ignore.self>
        <div class="card-header">
            <h5>Item Lists</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @if (auth()->user()->employee->getModulePermission('Manage Item') == 1)
                    <div class="col-md-6 mb-2">
                        <x-primary-button type="button"
                        onclick="showTab('item-form', document.querySelector('.nav-link.active'))">+ Add
                        Item</x-primary-button>   
                    </div>   
                @endif 
                    <div class="col-md-6 mb-2">
                        <input type="text" class="form-control" id="searchItems"
                            placeholder="Search Item">
                    </div>
            </div>
           
            <div class="table-responsive  mb-3 d-flex justify-content-center"
                style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-sm table-hover small">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>SKU</th>
                            <th>NAME</th>
                            <th class="text-end">CATEGORY</th>
                            <th class="text-end">CLASSIFICATION</th>
                            <th class="text-end">SUB CLASS</th>
                            <th class="text-end"  @if (auth()->user()->employee->getModulePermission('Manage Item') != 1) style="display: none;" @endif
                                >ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>{{ $item->item_code }}</td>
                                <td>{{ $item->item_description }}</td>
                                <td class="text-end">{{ $item->category->category_name ?? 'N/A' }}</td>
                                <td class="text-end">
                                    {{ $item->classification->classification_name ?? 'N/A' }}
                                </td>
                                <td class="text-end">
                                    {{ $item->sub_classification->classification_name ?? 'N/A' }}</td>
                                    @if (auth()->user()->employee->getModulePermission('Manage Item') == 1)
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-primary btn-sm"
                                            wire:click="edit({{ $item->id }})"
                                            onclick="showTab('item-update-form', document.querySelector('.nav-link.active')) , updateItem({{ json_encode($item) }})"
                                            >Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger btn-sm"
                                            wire:click="deactivate({{ $item->id }})" >Delete</a>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No items found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- item form -->
    <div id="item-form" class="tab-content card" style="display: none" wire:ignore.self>
        <div class="card-body">
            <x-secondary-button type="button" class="btn-sm"
                onclick="showTab('items-table', document.querySelector('.nav-link.active'))">Summary</x-secondary-button>

            <form wire:submit.prevent="store" class="submit-form">
                @csrf
                <div class="row">
                    <div class=" col-md-6">
                        <label for="item_code" class="form-label">SKU / Item Code<span
                                style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="item_code" wire:model="item_code">
                        @error('item_code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row col-md-6">
                        <div class=" col-md-6">
                            <label for="uom_id" class="form-label">Unit Symbol <span
                                    style="color: red;">*</span></label>
                            <div class="input-group">
                                <select class="form-control" id="uom_id" wire:model="uom_id"
                                    style="font-size: x-small;" data-live-search="true">
                                    <option value="">Select</option>
                                    @forelse ($uoms as $uom)
                                        <option value="{{ $uom->id }}" style="font-size: x-small;">
                                            {{ $uom->unit_name }}
                                            ( {{ $uom->unit_symbol }} )
                                        </option>
                                    @empty
                                        <option value="">No Symbol</option>
                                    @endforelse
                                </select>
                                {{-- <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        $('#uom_id').selectpicker();
                                    });
                                </script> --}}
                                <button class="input-group-text" type="button"
                                    style="background-color: rgb(190, 243, 217);" data-bs-toggle="modal" data-bs-target="#addUomModal">+</button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="addUomModal" tabindex="-1" aria-labelledby="addUomModalLabel" aria-hidden="true" wire:ignore.self>
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="addUomModalLabel">Add Unit of Measure</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">

                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="unit_symbol" class="form-label">Unit Symbol</label>
                                                                <input type="text" class="form-control" id="unit_symbol" wire:model="unit_symbol">
                                                                @error('unit_symbol')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="unit_name" class="form-label">Unit Name</label>
                                                                <input type="text" class="form-control" id="unit_name" wire:model="unit_name">
                                                                @error('unit_name')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label for="unit_description" class="form-label">Unit Description</label>
                                                            <textarea class="form-control" id="unit_description" wire:model="unit_description" rows="3"></textarea>
                                                            @error('unit_description')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>

                                                        <x-primary-button type="button" wire:click="addUom">Save</x-primary-button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        // document.querySelector('.input-group-text').addEventListener('click', function () {
                                        //     var modal = new bootstrap.Modal(document.getElementById('addUomModal'));
                                        //     modal.show();
                                        // });

                                        window.addEventListener('uomAdded', event => {
                                            var modal = bootstrap.Modal.getInstance(document.getElementById('addUomModal'));
                                            modal.hide();
                                            document.getElementById('unit_symbol').value = '';
                                            document.getElementById('unit_name').value = '';
                                            document.getElementById('unit_description').value = '';
                                        });
                                    </script>
                            </div>

                            @error('uom_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class=" col-md-6">
                            <label for="cost" class="form-label">Cost Price <span
                                    style="color: rgb(129, 127, 127); font-size: x-small;">(optional)</span></label>
                            <input type="number" class="form-control" id="cost" wire:model="cost" step="0.01"
                                placeholder="0.00">
                            @error('cost')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="item_description" class="form-label">Name <span
                            style="color: red;">*</span></label>
                    <textarea class="form-control" id="item_description" wire:model="item_description" rows="3"></textarea>
                    @error('item_description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="item_barcode" class="form-label">Barcode Value <span
                            style="color: rgb(129, 127, 127); font-size: x-small;">(optional)</span></label>
                    <input type="text" class="form-control" id="item_barcode" wire:model="item_barcode"
                        rows="3" />
                    @error('item_barcode')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="row">
                    <div class=" col-md-6">
                        <label for="category_id" class="form-label">Category <span style="color: red;">*</span></label>
                        <div class="input-group">
                            <select class="form-control" id="category_id" wire:model="category_id" data-live-search="true">
                                <option value="">Select</option>
                                @forelse ($categories as $category)
                                    <option value="{{ $category->id }}"> {{ $category->category_name }}
                                    </option>
                                @empty
                                    <option value="">No Symbol</option>
                                @endforelse
                            </select>
                            <button class="input-group-text" type="button"
                                style="background-color: rgb(190, 243, 217);" data-bs-toggle="modal" data-bs-target="#addCategory">+</button>

                           <!-- Modal -->
                           <div class="modal fade" id="addCategory" tabindex="-1" aria-labelledby="addCategoryModal" aria-hidden="true" wire:ignore.self>
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" >Add Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="category_name" class="form-label">Category Name</label>
                                                    <input type="text" class="form-control" id="category_name" wire:model="category_name">
                                                    @error('category_name')

                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                                <div class=" mb-3">
                                                    <label for="category_description" class="form-label">Description</label>
                                                    <textarea class="form-control" id="category_description" wire:model="category_description" rows="3"></textarea>
                                                    @error('category_description')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                            <x-primary-button type="button" wire:click="addCategory">Save</x-primary-button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>

                            window.addEventListener('categoryAdded', event => {
                                var modal = bootstrap.Modal.getInstance(document.getElementById('addCategory'));
                                modal.hide();
                                document.getElementById('category_id').value = '';
                                document.getElementById('category_description').value = '';
                            });
                        </script>
                        </div>
                        @error('category_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class=" col-md-6">
                        <label for="brand_id" class="form-label">Brand <span
                                style="color: rgb(129, 127, 127); font-size: x-small;">(optional)</span></label>
                        <div class="input-group">
                            <select class="form-control" id="brand_id" wire:model="brand_id" data-live-search="true">
                                <option value="">Select</option>
                                @forelse ($brands as $brand)
                                    <option value="{{ $brand->id }}"> {{ $brand->brand_name }}
                                    </option>
                                @empty
                                    <option value="">No Brand</option>
                                @endforelse
                            </select>
                            <button class="input-group-text" type="button"
                                style="background-color: rgb(190, 243, 217);" data-bs-toggle="modal" data-bs-target="#addBrand">+</button>

                            <!-- Modal -->
                            <div class="modal fade" id="addBrand" tabindex="-1" aria-labelledby="addBrandModal" aria-hidden="true" wire:ignore.self>
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" >Add Brand</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <label for="brand_name" class="form-label">Brand Name</label>
                                                        <input type="text" class="form-control" id="brand_name" wire:model="brand_name">
                                                        @error('brand_name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                    <div class=" mb-3">
                                                        <label for="brand_description" class="form-label">Description</label>
                                                        <textarea class="form-control" id="brand_description" wire:model="brand_description" rows="3"></textarea>
                                                        @error('brand_description')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                <x-primary-button type="button" wire:click="addBrand">Save</x-primary-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                window.addEventListener('brandAdded', event => {
                                    var modal = bootstrap.Modal.getInstance(document.getElementById('addBrand'));
                                    modal.hide();
                                    document.getElementById('brand_id').value = '';
                                    document.getElementById('brand_description').value = '';
                                });
                            </script>
                        </div>

                        @error('brand_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="classification_id" class="form-label">Classification<span
                                style="color: red;">*</span></label>
                        <div class="input-group">
                            <select class="form-control" id="classification_id" wire:model="classification_id" data-live-search="true">
                                <option value="">Select</option>
                                @forelse ($classifications as $classification)
                                    <option value="{{ $classification->id }}">
                                        {{ $classification->classification_name }}
                                    </option>
                                @empty
                                    <option value="">No Classification</option>
                                @endforelse
                            </select>
                            <button class="input-group-text" type="button"
                                style="background-color: rgb(190, 243, 217);" data-bs-toggle="modal" data-bs-target="#addClassification">+</button>

                            <!-- Modal -->
                            <div class="modal fade" id="addClassification" tabindex="-1" aria-labelledby="addClassificationModal" aria-hidden="true" wire:ignore.self>
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addClassificationModal">Add Classification</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="classification_name" class="form-label">Classification Name</label>
                                                    <input type="text" class="form-control" id="classification_name" wire:model="classification_name">
                                                    @error('classification_name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class=" mb-3">
                                                <label for="classification_description" class="form-label">Description</label>
                                                <textarea class="form-control" id="classification_description" wire:model="classification_description" rows="3"></textarea>
                                                @error('classification_description')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <x-primary-button type="button" wire:click="addClassification">Save</x-primary-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                window.addEventListener('classificationAdded', event => {
                                    var modal = bootstrap.Modal.getInstance(document.getElementById('addClassification'));
                                    modal.hide();
                                    document.getElementById('classification_id').value = '';
                                    document.getElementById('classification_description').value = '';
                                });
                            </script>
                        </div>
                        @error('classification_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <div>
                        <label for="sub_classification_id" class="form-label">Sub-Class
                            <div class="input-group">
                                <select class="form-control" id="sub_classification_id"
                                    wire:model="sub_classification_id" data-live-search="true">
                                    <option value="">Select</option>
                                    @forelse ($sub_classifications as $subClassification)
                                        <option value="{{ $subClassification->id }}">
                                            {{ $subClassification->classification_name }}
                                        </option>
                                    @empty
                                        <option value="">No Sub-Class</option>
                                    @endforelse
                                </select>
                                <button class="input-group-text" type="button"
                                    style="background-color: rgb(190, 243, 217);" data-bs-toggle="modal" data-bs-target="#addSubClassification"  >+</button>
                            </div>
                        </div>
                        @error('sub_classification_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                         <!-- Modal -->
                         <div class="modal fade" id="addSubClassification" tabindex="-1" aria-labelledby="addSubClassificationModal" aria-hidden="true" wire:ignore.self>
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addSubClassificationModal">Add Sub-Classification</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label for="parent_classification_id" class="form-label">Classification<span style="color: red;">*</span></label>
                                        <div class="input-group">
                                            <select class="form-control" id="parent_classification_id" wire:model="parent_classification_id">
                                                <option value="">Select</option>
                                                @forelse ($classifications as $classification)
                                                    <option value="{{ $classification->id }}">
                                                        {{ $classification->classification_name }}
                                                    </option>
                                                @empty
                                                    <option value="">No Classification</option>
                                                @endforelse
                                            </select>
                                        </div>
                                        @error('parent_classification_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="sub_classification_name" class="form-label">Sub-Classification Name</label>
                                                <input type="text" class="form-control" id="sub_classification_name" wire:model="sub_classification_name">
                                                @error('sub_classification_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="sub_classification_description" class="form-label">Description</label>
                                            <textarea class="form-control" id="sub_classification_description" wire:model="sub_classification_description" rows="3"></textarea>
                                            @error('sub_classification_description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <x-primary-button type="button" wire:click="addSubClassification">Save</x-primary-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            window.addEventListener('subClassificationAdded', event => {
                                var modal = bootstrap.Modal.getInstance(document.getElementById('addSubClassification'));
                                modal.hide();
                                document.getElementById('sub_classification_name').value = '';
                                document.getElementById('sub_classification_description').value = '';
                                document.getElementById('parent_classification_id').value = '';
                            });
                        </script>
                    </div>
                </div>

                <x-primary-button type="submit">Save</x-primary-button>
            </form>
        </div>
    </div>
   

    {{-- update form --}}
    <div id="item-update-form" class="tab-content card" style="display: none" wire:ignore.self>
        <div class="card-header">
            <h5>Update Item</h5>
        </div>
        <div class="card-body">
            <x-secondary-button type="button" class="btn-sm"
                onclick="showTab('items-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
            <x-primary-button type="button"
                onclick="showTab('item-form', document.querySelector('.nav-link.active'))">+ New
                Item</x-primary-button>
            <form wire:submit.prevent="update" class="submit-form">
                @csrf
                <div class="row">
                    <div class=" col-md-6 mt-1">
                        <label for="item_code-update" class="form-label">SKU / Item Code<span
                                style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="item_code-update" wire:model="item_code">
                        @error('item_code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row col-md-6">
                        <div class=" col-md-6">
                            <label for="uom_id-update" class="form-label">Unit Symbol<span
                                    style="color: red;">*</span></label>
                            <div class="input-group">
                                <select class="form-control" id="uom_id-update" wire:model="uom_id"
                                    style="font-size: x-small;">
                                    <option value="">Select</option>
                                    @forelse ($uoms as $uom)
                                        <option value="{{ $uom->id }}" style="font-size: x-small;">(
                                            {{ $uom->unit_symbol }} )
                                            {{ $uom->unit_name }}
                                        </option>
                                    @empty
                                        <option value="">No Symbol</option>
                                    @endforelse
                                </select>
                                <button class="input-group-text" type="button"
                                    style="background-color: rgb(190, 243, 217);">+</button>
                            </div>

                            @error('uom_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="item_description-update" class="form-label">Name<span
                            style="color: red;">*</span></label>
                    <textarea class="form-control" id="item_description-update" wire:model="item_description" rows="3"></textarea>
                    @error('item_description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="item_barcode-update" class="form-label">Barcode Value <span
                            style="color: rgb(129, 127, 127); font-size: x-small;">(optional)</span></label>
                    <input type="text" class="form-control" id="item_barcode-update" wire:model="item_barcode"
                        rows="3" />
                    @error('item_barcode')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="row">
                    <div class=" col-md-6">
                        <label for="category_id" class="form-label-update">Category <span
                                style="color: red;">*</span></label>
                        <div class="input-group">
                            <select class="form-control" id="category_id-update" wire:model="category_id">
                                <option value="">Select</option>
                                @forelse ($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->category_name }}
                                    </option>
                                @empty
                                    <option value="">No Symbol</option>
                                @endforelse
                            </select>
                            <button class="input-group-text" type="button"
                                style="background-color: rgb(190, 243, 217);">+</button>
                        </div>
                        @error('category_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class=" col-md-6">
                        <label for="brand_id-update" class="form-label">Brand <span
                                style="color: rgb(129, 127, 127); font-size: x-small;">(optional)</span></label>
                        <div class="input-group">
                            <select class="form-control" id="brand_id-update" wire:model="brand_id">
                                <option value="" >Select</option>
                                @forelse ($brands as $brand)
                                    <option value="{{ $brand->id }}"> {{ $brand->brand_name }}
                                    </option>
                                @empty
                                    <option value="">No Brand</option>
                                @endforelse
                            </select>
                            <button class="input-group-text" type="button"
                                style="background-color: rgb(190, 243, 217);">+</button>
                        </div>

                        @error('brand_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="classification_id-update" class="form-label">Classification<span
                                style="color: red;">*</span></label>
                        <div class="input-group">
                            <select class="form-control" id="classification_id-update"
                                wire:model="classification_id">
                                <option value="">Select</option>
                                @forelse ($classifications as $classification)
                                    <option value="{{ $classification->id }}">
                                        {{ $classification->classification_name }}
                                    </option>
                                @empty
                                    <option value="">No Classification</option>
                                @endforelse
                            </select>
                            <button class="input-group-text" type="button"
                                style="background-color: rgb(190, 243, 217);">+</button>
                        </div>
                        @error('classification_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="sub_classification_id-update" class="form-label">Sub-Class <span
                                style="color: red;">*</span></label>
                        <div class="input-group">
                            <select class="form-control" id="sub_classification_id-update"
                                wire:model="sub_classification_id">
                                <option value="">Select</option>
                                @forelse ($sub_classifications as $subClassification)
                                    <option value="{{ $subClassification->id }}">
                                        {{ $subClassification->classification_name }}
                                    </option>
                                @empty
                                    <option value="">No Sub-Class</option>
                                @endforelse
                            </select>
                            <button class="input-group-text" type="button"
                                style="background-color: rgb(190, 243, 217);">+</button>
                        </div>
                        @error('sub_classification_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <x-primary-button type="submit">Update</x-primary-button>
            </form>
        </div>
    </div>

    <script>
        window.addEventListener('saved', event => {
            document.getElementById('item_code').value = '';
            document.getElementById('uom_id').value = '';
            document.getElementById('cost').value = '';
            document.getElementById('item_description').value = '';
            document.getElementById('category_id').value = '';
            document.getElementById('brand_id').value = '';
            document.getElementById('classification_id').value = '';
            document.getElementById('sub_classification_id').value = '';
            document.getElementById('item_barcode').value = '';
        });

            window.addEventListener('updated', event => {
            document.getElementById('item_code-update').value = '';
            document.getElementById('uom_id-update').value = '';
            document.getElementById('item_description-update').value = '';
            document.getElementById('category_id-update').value = '';
            document.getElementById('brand_id-update').value = '';
            document.getElementById('classification_id-update').value = '';
            document.getElementById('sub_classification_id-update').value = '';
            document.getElementById('item_barcode-update').value = '';
        });

        function updateItem(item) {
            console.log(item.category_id);
            document.getElementById('item_code-update').value = item.item_code;
            document.getElementById('uom_id-update').value = item.uom_id;
            document.getElementById('item_description-update').value = item.item_description;
            document.getElementById('category_id-update').value = item.category_id ?? '';
            document.getElementById('brand_id-update').value = item.brand_id ?? '';
            document.getElementById('classification_id-update').value = item.classification_id ?? '';
            document.getElementById('sub_classification_id-update').value = item.sub_class_id ?? '';
            document.getElementById('item_barcode-update').value = item.item_barcode;
        }
    </script>
   
</div>
