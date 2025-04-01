<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}

    <div id="unit-of-measures-table" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-body">

            <x-primary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('unit-of-measure-form', document.querySelector('.nav-link.active'))">+ ADD UNIT OF
                MEASURE</x-primary-button>
            <x-secondary-button type="button" class="mb-3 btn-sm"
                wire:click="fetchData()">Refresh</x-secondary-button>
            <table class="table table-striped table-sm small">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>DESCRIPTION</th>
                        <th class="text-center">SYMBOL</th>
                        <th class="text-end">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($unit_of_measures as $uom)
                        <tr>
                            <td>{{ $uom->unit_name }}</td>
                            <td>{{ $uom->unit_description }}</td>
                            <td class="text-center">{{ $uom->unit_symbol }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-primary">Edit</button>
                                <button class="btn btn-sm btn-danger" wire:click="deactivate({{ $uom->id }})">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No unit of measure found</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

    {{--  --}}
    <div id="unit-of-measure-form" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-body">
            <form wire:submit.prevent="store">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="unit_name">Name</label>
                            <input type="text" wire:model="unit_name" class="form-control" id="unit_name"
                                placeholder="Enter unit name">
                            @error('unit_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="unit_symbol">Unit Symbol</label>
                            <input type="text" wire:model="unit_symbol" class="form-control" id="unit_symbol"
                                placeholder="Enter unit symbol">
                            @error('unit_symbol')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <div class="form-group mt-3">
                        <label for="unit_description">Unit description</label>
                        <textarea type="text" wire:model="unit_description" class="form-control" id="unit_description"></textarea>
                        @error('unit_description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="mt-3 mb-3">
                    <div class="form-group">
                        <label for="company_id">Established to</label>
                        <select wire:model="company_id" class="form-control" id="company_id">
                            <option value="">Select company</option>
                            @forelse ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                            @empty
                                <option value="">No company found</option>
                            @endforelse
                        </select>
                        @error('company_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <x-primary-button type="submit">Save</x-primary-button>
            </form>
        </div>
    </div>
</div>
