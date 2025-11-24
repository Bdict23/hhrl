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


    <div id="employee-positions" class="tab-content card" style="display: none;" wire:ignore.self>
         <div class="card-header">
            <h5>Position Lists</h5>
        </div>
        <div class="card-body">
            <div class="row">
               <div class="col-md-6">
                  @if (auth()->user()->employee->getModulePermission('Employee Positions') == 1 )
                     <x-primary-button type="button" class="mb-3 btn-sm"
                     onclick="showTab('employee-positions-form', document.querySelector('.nav-link.active'))">+ ADD
                     Position</x-primary-button>
                 @endif
                     <x-secondary-button type="button" class="mb-3 btn-sm"
                     wire:click="fetchPositions()">Refresh</x-secondary-button>
               </div>
                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Search</span>
                        <input type="text" class="form-control" id="search-position" placeholder="Search Position Name or Description"
                            onkeyup="filterPositions()">
                    </div>
                </div>
            </div>
            <script>
                function filterVenues() {
                    const input = document.getElementById('search-venue');
                    const filter = input.value.toLowerCase();
                    const table = document.querySelector('#venue-lists table');
                    const trs = table.querySelectorAll('tbody tr');
                    trs.forEach(row => {
                        // Skip "No Venue found" row
                        if (row.children.length < 2) return;
                        const name = row.children[0].textContent.toLowerCase();
                        const code = row.children[1].textContent.toLowerCase();
                        if (name.includes(filter) || code.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }
            </script>
            <div class="table-responsive mt-3 mb-3 d-flex justify-content-center"
                style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-sm small">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Name</th>
                            <th>DESCRIPTION</th>
                            <th>STATUS</th>
                            <th class="text-end"  @if (auth()->user()->employee->getModulePermission('Employee Positions') != 1 ) style="display: none;"  @endif>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody id="position-table-body">
                        @forelse ($positions as $position)
                            <tr>
                                <td>{{ $position->position_name }}</td>
                                <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $position->position_description ?? 'N/A' }}</td>
                                <td>{{ $position->position_status }}</td>
                                @if (auth()->user()->employee->getModulePermission('Employee Positions') == 1 )
                                    @if($position->position_status == 'ACTIVE')
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#UpdatePosition" onclick="updatePosition({{ json_encode($position) }})" wire:click="editPosition({{ $position->id }})">Edit</button>
                                        <a href="#" class="btn btn-sm btn-danger btn-sm" wire:click="deactivatePosition({{ $position->id }})">Deactivate</a>
                                    </td>
                                    @else
                                        <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#UpdatePosition" onclick="updatePosition({{ json_encode($position) }})" wire:click="editPosition({{ $position->id }})">Edit</button>
                                        <a href="#" class="btn btn-sm btn-success btn-sm" wire:click="activatePosition({{ $position->id }})">Activate</a>
                                    </td>
                                    @endif
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No Positions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


       {{-- Update Modal --}}
<div class="modal fade" id="UpdatePosition" tabindex="-1" aria-labelledby="updatePositionModal" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >Update Position</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" wire:submit.prevent="updatePosition" id="UpdatePositionForm">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="position_name-update" class="form-label">Position Name</label>
                            <input type="text" class="form-control" id="position_name-update-input" wire:model="position_name_input">
                            @error('position_name_input')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> 
                    </div>
                        <div class=" mb-3">
                            <label for="position_description-update" class="form-label">Description</label>
                            <textarea class="form-control" id="position_description-update-input" wire:model="position_description_input" rows="3"></textarea>
                            @error('position_description_input')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <x-primary-button type="submit">Update</x-primary-button>
                    </form>
            </div>
        </div>
    </div>
</div>

    {{-- ADD POSITION FORM --}}
    <div id="employee-positions-form" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Add Positions</h5>
        </div>
        <div class="card-body">
            <x-secondary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('employee-positions', document.querySelector('.nav-link.active'))">Summary</x-secondary-button>
            <form wire:submit.prevent="storePosition" id="positionForm">
                @csrf
                <div class="mb-3">
                    <label for="position_name-input" class="form-label">Position Name <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="position_name-input" wire:model="position_name_input" >
                    @error('position_name_input')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="position_description-input" class="form-label">Description <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="position_description-input" wire:model="position_description_input" rows="3" ></textarea>
                    @error('position_description_input')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <x-primary-button type="submit">Save</x-primary-button>
            </form>
        </div>
    </div>




    <script>
        function updatePosition($data) {
                // Set the values of the input fields
                console.log($data);
                document.getElementById('position_name-update-input').value = $data.position_name;
                document.getElementById('position_description-update-input').value = $data.position_description;           
            }
            function filterPositions() {
                    const input = document.getElementById('search-position');
                    const filter = input.value.toLowerCase();
                    const table = document.querySelector('#position-table-body');
                    const trs = table.querySelectorAll('tbody tr');
                    trs.forEach(row => {
                        // Skip "No Venue found" row
                        if (row.children.length < 2) return;
                        const name = row.children[0].textContent.toLowerCase();
                        const code = row.children[1].textContent.toLowerCase();
                        if (name.includes(filter) || code.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }

        // listen for the clearForm event from livewire component
        window.addEventListener('clearForm', event => {
        document.getElementById('positionForm').reset();
        document.getElementById('UpdatePositionForm').reset();
            });

            window.addEventListener('hideUpdatePositionModal', event => {
            // Clear the form fields
            document.getElementById('UpdatePositionForm').reset();
            // Hide the modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('UpdatePosition'));
            modal.hide();

            // Hide the success message after 1 second
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 1500);
        });
    </script>
</div>
