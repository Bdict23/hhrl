<div>
    <div id="category-table" class="tab-content card" style="display: none" wire:ignore.self>
        <div class="card-header">
            <h5>Item Category List</h5>
        </div>
        <div class="card-body">
            <x-primary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('category-form', document.querySelector('.nav-link.active'))">+ ADD
                CATEGORY</x-primary-button>
                <x-secondary-button type="button" class="mb-3 btn-sm"
                wire:click="fetchData()">Refresh</x-secondary-button>
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
                        @forelse ($ItemCategories as $category)
                            <tr>
                                <td>{{ $category->category_name }}</td>
                                <td>{{ $category->category_description ?? 'N/A' }}</td>
                                <td class="text-end">{{ $category->status }}</td>
                                <td class="text-end">
                                    {{ optional($category->company)->company_name ?? 'No Company' }}
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger btn-sm" wire:click="deactivate({{ $category->id }})">Delete</a>
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
        </div>
    </div>
    {{-- Category Form --}}
    <div id="category-form" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Add Category</h5>
        </div>
        <div class="card-body">
            <x-secondary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('category-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
            <form wire:submit.prevent="storeCategory">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="name" wire:model="category_name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="description" wire:model="category_description" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="reg_company" class="form-label">Established to<span style="color: red;">*</span></label>
                    <select class="form-control" id="reg_company" wire:model="company_id" required>
                        <option value="">Select</option>
                        @forelse ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                        @empty
                            <option value="">No Company</option>
                        @endforelse
                    </select>
                </div>
                <x-primary-button type="submit">Save</x-primary-button>
            </form>
        </div>
    </div>
</div>
