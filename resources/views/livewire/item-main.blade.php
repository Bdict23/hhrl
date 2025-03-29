<div>
    <div id="items-table" class="tab-content card" style="display: block" wire:ignore.self>
        <div class="card-header">
            <h5>Item List</h5>
        </div>
        <div class="card-body">
            <x-primary-button type="button" class="mb-3"
                onclick="showTab('item-form', document.querySelector('.nav-link.active'))">+ Add
                Item</x-primary-button>
            <div class="table-responsive  mb-3 d-flex justify-content-center"
                style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-sm table-hover small">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>DESCRIPTION</th>
                            <th class="text-end">CATEGORY</th>
                            <th class="text-end">CLASSIFICATION</th>
                            <th class="text-end">SUB CLASS</th>
                            <th class="text-end">ACTIONS</th>
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
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-primary btn-sm"
                                        wire:click="edit({{ $item->id }})"
                                        onclick="showTab('item-update-form', document.querySelector('.nav-link.active')) , updateItem({{ json_encode($item) }})">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger btn-sm"
                                        wire:click="deactivate({{ $item->id }})">Delete</a>
                                </td>
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
                    <label for="item_description" class="form-label">Description <span
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
                            <select class="form-control" id="category_id" wire:model="category_id">
                                <option value="">Select</option>
                                @forelse ($categories as $category)
                                    <option value="{{ $category->id }}"> {{ $category->category_name }}
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
                        <label for="brand_id" class="form-label">Brand <span
                                style="color: rgb(129, 127, 127); font-size: x-small;">(optional)</span></label>
                        <div class="input-group">
                            <select class="form-control" id="brand_id" wire:model="brand_id">
                                <option value="">Select</option>
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
                        <label for="classification_id" class="form-label">Classification<span
                                style="color: red;">*</span></label>
                        <div class="input-group">
                            <select class="form-control" id="classification_id" wire:model="classification_id">
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
                        <label for="sub_classification_id" class="form-label">Sub-Class <span
                                style="color: red;">*</span></label>
                        <div class="input-group">
                            <select class="form-control" id="sub_classification_id"
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
                    <div class=" col-md-6">
                        <label for="item_code-update" class="form-label">SKU / Item Code<span
                                style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="item_code-update" wire:model="item_code">
                        @error('item_code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row col-md-6">
                        <div class=" col-md-6">
                            <label for="uom_id-update" class="form-label">Unit Symbol {{ $item_description }}<span
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
                    <label for="item_description-update" class="form-label">Description <span
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
                                <option value="">Select</option>
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
        });

        window.addEventListener('updated', event => {
            document.getElementById('item_code-update').value = '';
            document.getElementById('uom_id-update').value = '';
            // document.getElementById('item_description-update').value = '';
            document.getElementById('category_id-update').value = '';
            document.getElementById('brand_id-update').value = '';
            document.getElementById('classification_id-update').value = '';
            document.getElementById('sub_classification_id-update').value = '';
        });

        function updateItem(item) {
            console.log(item);
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
