<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <div id="brand-table" class="tab-content card"
        {{ $BrandListTab == 1 ? 'style=display:block' : 'style=display:none' }}>
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
                        @forelse ($itemBrands as $brand)
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
    <div id="brand-form" class="tab-content card"
        {{ $AddBrandTab == 1 ? 'style=display:block' : 'style=display:none' }}>
        <div class="card-header">
            <h5>Add Brand</h5>
        </div>
        <div class="card-body">

            <x-secondary-button type="button" class="mb-3"
                onclick="showTab('brand-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
            <form wire:submit.prevent="store">
                <div class="mb-3">
                    <label for="brand_name" class="form-label">Brand Name <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="brand_name" wire:model="brand_name">
                    @error('brand_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="brand_description" class="form-label">Description <span
                            style="color: red;">*</span></label>
                    <textarea class="form-control" id="brand_description" wire:model="brand_description" rows="3"></textarea>
                    @error('brand_description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="company_id" class="form-label">Established to<span style="color: red;">*</span></label>
                    <select class="form-control" id="company_id" wire:model="company_id">
                        <option value="">Select</option>
                        @forelse ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                        @empty
                            <option value="no_company">No Company Available</option>
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
