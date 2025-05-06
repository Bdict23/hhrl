<div>
     {{-- return flash message --}}
     @if (session()->has('success'))
     <div class="alert alert-success" id="success-message">
         {{ session('success') }}
         <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>
     @endif

    <div id="unit-of-measures-table" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-body">
            @if (auth()->user()->employee->getModulePermission('Unit of Measures') == 1 )
                <x-primary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('unit-of-measure-form', document.querySelector('.nav-link.active'))">+ ADD UNIT OF
                MEASURE</x-primary-button>
            @endif
           
            <x-secondary-button type="button" class="mb-3 btn-sm"
                wire:click="fetchData()">Refresh</x-secondary-button>
            <div style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-sm small">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Name</th>
                            <th>DESCRIPTION</th>
                            <th class="text-center">SYMBOL</th>
                            @if (auth()->user()->employee->getModulePermission('Unit of Measures') == 1 )
                                <th class="text-end">ACTIONS</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($unit_of_measures as $uom)
                            <tr>
                                <td>{{ $uom->unit_name }}</td>
                                <td>{{ $uom->unit_description }}</td>
                                <td class="text-center">{{ $uom->unit_symbol }}</td>
                                @if (auth()->user()->employee->getModulePermission('Unit of Measures') == 1 )
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateUOM" onclick="updateUOMField({{ json_encode($uom)}})" wire:click="editUOM({{ $uom->id }})">Edit</button>
                                        <button class="btn btn-sm btn-danger" wire:click="deactivate({{ $uom->id }})">Delete</button>
                                    </td>
                                @endif
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
    </div>

    {{-- create new uom  --}}
    <div id="unit-of-measure-form" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-body">
            <form wire:submit.prevent="store">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="unit_name-input">Name</label>
                            <input type="text" wire:model="unit_name" class="form-control" id="unit_name-input"
                                placeholder="Enter unit name">
                            @error('unit_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="unit_symbol-input">Unit Symbol</label>
                            <input type="text" wire:model="unit_symbol" class="form-control" id="unit_symbol-input"
                                placeholder="Enter unit symbol">
                            @error('unit_symbol')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <div class="form-group mt-3 mb-3">
                        <label for="unit_description-input">Unit description</label>
                        <textarea type="text" wire:model="unit_description" class="form-control" id="unit_description-input"></textarea>
                        @error('unit_description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <x-primary-button type="submit">Save</x-primary-button>
                <x-secondary-button type="button" onclick="showTab('unit-of-measures-table', document.querySelector('.nav-link.active'))">Summary</x-secondary-button>
            </form>
        </div>
    </div>


    {{-- Update Unit of Measure Modal --}}

<div class="modal fade" id="updateUOM" tabindex="-1" aria-labelledby="updateUOMModal" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >Update Unit Of Measure Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="unit_name-update_input">Name</label>
                            <input type="text" wire:model="unit_name" class="form-control" id="unit_name-update_input"
                                placeholder="Enter unit name">
                            @error('unit_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="unit_symbol-update_input">Unit Symbol</label>
                            <input type="text" wire:model="unit_symbol" class="form-control" id="unit_symbol-update_input"
                                placeholder="Enter unit symbol">
                            @error('unit_symbol')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <div class="form-group mt-3 mb-3">
                        <label for="unit_description-update_input">Unit description</label>
                        <textarea type="text" wire:model="unit_description" class="form-control" id="unit_description-update_input"></textarea>
                        @error('unit_description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                    <x-primary-button type="button" wire:click="updateUOM">Update</x-primary-button>
            </div>
        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for addUnitOfMeasure event
            window.addEventListener('clearUOMForm', function() {
                document.getElementById('unit_name-input').value = '';
                document.getElementById('unit_symbol-input').value = '';
                document.getElementById('unit_description-input').value = '';
                document.getElementById('unit-of-measure-form').style.display = 'none';
                document.getElementById('unit-of-measures-table').style.display = 'block';

                //Hide flash message in 3 seconds
                setTimeout(function() {
                    var successMessage = document.getElementById('success-message');
                    if (successMessage) {
                        successMessage.style.display = 'none';
                    }
                }, 1500);
            });

            // Listen for the updateUOM event
            window.addEventListener('clearUOMModalFormUpdate', function() {

                document.getElementById('unit_name-update_input').value = '';
                document.getElementById('unit_symbol-update_input').value = '';
                document.getElementById('unit_description-update_input').value = '';
                document.getElementById('unit-of-measure-form').style.display = 'none';
                document.getElementById('unit-of-measures-table').style.display = 'block';
                // Hide the modal
                let modal = bootstrap.Modal.getInstance(document.getElementById('updateUOM'));
                modal.hide();
                //Hide flash message in 3 seconds
                setTimeout(function() {
                    var successMessage = document.getElementById('success-message');
                    if (successMessage) {
                        successMessage.style.display = 'none';
                    }
                }, 3000);
            });


        });

        function updateUOMField($data) {
            // Set the values of the input fields
            document.getElementById('unit_name-update_input').value = $data.unit_name;
            document.getElementById('unit_symbol-update_input').value = $data.unit_symbol;
            document.getElementById('unit_description-update_input').value = $data.unit_description;

            // Set the id of the unit of measure to be updated
            @this.set('uom_id', id);
        }
    </script>
</div>
