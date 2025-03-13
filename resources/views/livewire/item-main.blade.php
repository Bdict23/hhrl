<div>
    <div id="items-table" class="tab-content card" {{ $ItemListTab == 0 ? 'style=display:none' : 'style=display:block' }}>
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
                                    <a href="#" class="btn btn-sm btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger btn-sm">Delete</a>
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
    <div id="item-form" class="tab-content card" {{ $AddItemTab == 1 ? 'style=display:block' : 'style=display:none' }}>
        <div class="card-header">
            <h5>Add Item</h5>
        </div>
        <div class="card-body">
            <x-secondary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('items-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
            <form wire:submit.prevent="store" class="submit-form">
                @csrf
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="item_code" class="form-label">Item Code <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="item_code" wire:model="item_code">
                        @error('item_code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row mb-3 col-md-6">
                        <div class="mb-3 col-md-6">
                            <label for="uom_id" class="form-label">Unit Symbol <span
                                    style="color: red;">*</span></label>
                            <select class="form-control" id="uom_id" wire:model="uom_id">
                                <option value="">Select</option>
                                @forelse ($uoms as $uom)
                                    <option value="{{ $uom->id }}">( {{ $uom->unit_symbol }} )
                                        {{ $uom->unit_name }}
                                    </option>
                                @empty
                                    <option value="">No Symbol</option>
                                @endforelse
                            </select>
                            @error('uom_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="cost" class="form-label">Cost Price</label>
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

                <div class="mb-3">
                    <label for="item_description" class="form-label">Description <span
                            style="color: red;">*</span></label>
                    <textarea class="form-control" id="item_description" wire:model="item_description" rows="3"></textarea>
                    @error('item_description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="category_id" class="form-label">Category <span style="color: red;">*</span></label>
                        <select class="form-control" id="category_id" wire:model="category_id">
                            <option value="">Select</option>
                            @forelse ($categories as $category)
                                <option value="{{ $category->id }}"> {{ $category->category_name }}
                                </option>
                            @empty
                                <option value="">No Symbol</option>
                            @endforelse
                        </select>
                        @error('category_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="brand_id" class="form-label">Brand <span
                                style="color: rgb(129, 127, 127); font-size: x-small;">(optional)</span></label>
                        <select class="form-control" id="brand_id" wire:model="brand_id">
                            <option value="">Select</option>
                            @forelse ($brands as $brand)
                                <option value="{{ $brand->id }}"> {{ $brand->brand_name }}
                                </option>
                            @empty
                                <option value="">No Brand</option>
                            @endforelse
                        </select>
                        @error('brand_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="classification_id" class="form-label">Classification<span
                                style="color: red;">*</span></label>
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
                        @error('classification_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="sub_classification_id" class="form-label">Sub-Class <span
                                style="color: red;">*</span></label>
                        <select class="form-control" id="sub_classification_id" wire:model="sub_classification_id">
                            <option value="">Select</option>
                            @forelse ($sub_classifications as $subClassification)
                                <option value="{{ $subClassification->id }}">
                                    {{ $subClassification->classification_name }}
                                </option>
                            @empty
                                <option value="">No Sub-Class</option>
                            @endforelse
                        </select>
                        @error('sub_classification_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="company_id" class="form-label">Established to<span
                            style="color: red;">*</span></label>
                    <select class="form-control" id="company_id" wire:model="company_id">
                        <option value="">Select Company</option>
                        @forelse ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                        @empty
                            <option value="">No Company</option>
                        @endforelse
                    </select>
                    @error('company_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <x-primary-button type="submit">Save</x-primary-button>
            </form>
        </div>
    </div>
</div>
