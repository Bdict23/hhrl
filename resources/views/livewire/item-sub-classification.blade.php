<div>
    <div id="sub-classification-table" class="tab-content card" style="display: none" wire:ignore.self>
        <div class="card-header">
            <h5>Sub Classification Lists</h5>
        </div>
        <div class="card-body">
            <x-primary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('sub-classification-form', document.querySelector('.nav-link.active'))"
                wire:click="showAddSubClassification">+ Add Sub
                Classification</x-primary-button>
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
                                <td class="text-end">{{ $sub_classification->classification->status }}</td>
                                <td class="text-end">
                                    {{ $sub_classification->classification->classification_name ?? 'Not Registered' }}
                                </td>
                                <td class="text-end">
                                    {{ $sub_classification->classification->company->company_name ?? 'Not Registered' }}
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger btn-sm" wire:click="deactivate({{ $sub_classification->id }})">Delete</a>
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
        </div>
    </div>

    {{-- sub-classification Form --}}
    <div id="sub-classification-form" class="tab-content card" style="display: none" wire:ignore.self>
        <div class="card-header">
            <h5>Add Sub Classification</h5>
        </div>
        <div class="card-body">

            <x-secondary-button type="button" class="mb-3"
                onclick="showTab('sub-classification-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
            <form wire:submit.prevent="store">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Parent Classification <span
                                    style="color: red;">*</span></label>
                            <select class="form-control" id="classification_id" wire:model="classification_id">
                                <option value="">
                                    {{ $classification_id == '' ? 'Select' : '' }}
                                </option>
                                @forelse ($classifications as $classification)
                                    <option value="{{ $classification->id }}">
                                        {{ $classification->classification_name }}</option>
                                @empty
                                    <option value="">No Parent Classification Found</option>
                                @endforelse
                            </select>
                            @error('classification_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Sub Classification Name <span
                                    style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="classification_name"
                                wire:model="classification_name">
                            @error('classification_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="description" wire:model="classification_description" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="reg_company" class="form-label">Established to <span
                            style="color: red;">*</span></label>
                    <select class="form-control" id="reg_company" wire:model="company_id">
                        <option value="">{{ $company_id ? '' : 'Select' }}</option>
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
