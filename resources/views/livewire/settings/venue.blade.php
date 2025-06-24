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
    <div id="venue-lists" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Venue Lists</h5>
        </div>
        <div class="card-body">
            <div class="row">
               <div class="col-md-6">
                 @if (auth()->user()->employee->getModulePermission('Business Venues') == 1 )
                     <x-primary-button type="button" class="mb-3 btn-sm"
                     onclick="showTab('venue-form', document.querySelector('.nav-link.active'))">+ ADD
                     Venue</x-primary-button>
                 @endif
                     <x-secondary-button type="button" class="mb-3 btn-sm"
                     wire:click="fetchVenues()">Refresh</x-secondary-button>
               </div>
                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Search</span>
                        <input type="text" class="form-control" id="search-venue"
                            onkeyup="filterVenues()">
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
                            <th>CODE</th>
                            <th>DESCRIPTION</th>
                            <th>CAPACITY</th>
                            <th>PRICE</th>
                            <th class="text-end"  @if (auth()->user()->employee->getModulePermission('Business Venues') != 1 ) style="display: none;"  @endif>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($venues as $venue)
                            <tr>
                                <td>{{ $venue->venue_name }}</td>
                                <td>{{ $venue->venue_code }}</td>
                                <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $venue->description ?? 'N/A' }}</td>
                                <th>{{ $venue->capacity}}</th>
                                <td>{{ $venue->ratePrice ? '₱' . number_format($venue->ratePrice->amount, 2) : 'N/A' }}</td>
                                @if (auth()->user()->employee->getModulePermission('Item Categories') == 1 )
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#UpdateVenue" onclick="updateVenue({{ json_encode($venue) }})" wire:click="editVenue({{ $venue->id }})">Edit</button>
                                        <a href="#" class="btn btn-sm btn-danger btn-sm" wire:click="deactivate({{ $venue->id }})">remove</a>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No Venue found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    {{-- Update Modal --}}
<div class="modal fade" id="UpdateVenue" tabindex="-1" aria-labelledby="updateVenueModal" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >Update Venue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" wire:submit.prevent="updateVenue" id="UpdateVenueForm">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="venue_name-update" class="form-label">Venue Name</label>
                            <input type="text" class="form-control" id="venue_name-update-input" wire:model="venue_name_input">
                            @error('venue_name_input')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="venue_code-update" class="form-label">Venue Code</label>
                                <input type="text" class="form-control" id="venue_code-update-input" wire:model="venue_code_input">
                                @error('venue_code_input')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="capacity-update" class="form-label">Capacity</label>
                                <input type="number" class="form-control" id="capacity-update-input" wire:model="capacity_input">
                                @error('capacity_input')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="venue_rate-update" class="form-label">Rate <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="venue_rate-update-input" wire:model="venue_rate_input">
                                @error('venue_rate_input')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                    </div>
                        <div class=" mb-3">
                            <label for="venue_description-update" class="form-label">Description</label>
                            <textarea class="form-control" id="venue_description-update-input" wire:model="venue_description_input" rows="3"></textarea>
                            @error('category_description_input')
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
    <div id="venue-form" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Add Venue</h5>
        </div>
        <div class="card-body">
            <x-secondary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('venue-lists', document.querySelector('.nav-link.active'))">Summary</x-secondary-button>
            <form wire:submit.prevent="storeVenue" id="venueForm">
                @csrf
                <div class="mb-3">
                    <label for="venue_name-input" class="form-label">Venue Name <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="venue_name-input" wire:model="venue_name_input" >
                    @error('venue_name_input')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <label for="venue_code-input" class="form-label">Venue Code <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="venue_code-input" wire:model="venue_code_input" >
                        @error('venue_code_input')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="capacity-input" class="form-label">Capacity <span style="color: red;">*</span></label>
                        <input type="number" class="form-control" id="capacity-input" wire:model="capacity_input" >
                        @error('capacity_input')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                                <label for="venue_rate" class="form-label">Rate Price</label>
                                <input type="number" step="0.01" class="form-control" id="venue_rate-input" wire:model="venue_rate_input">
                                @error('venue_rate_input')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                </div>
                <div class="mb-3">
                    <label for="venue_description-input" class="form-label">Description <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="venue_description-input-update" wire:model="venue_description_input" rows="3" ></textarea>
                    @error('venue_description_input')
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
            // Listen wire:success event
            window.addEventListener('clearForm', event => {
                // Clear the form fields
        document.getElementById('venueForm').reset();
            });

            // Show the venue lists tab by default
            // showTab('venue-lists', document.querySelector('.nav-link.active'));

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

        // HIDE UPDATEcATEGORY MODAL
        window.addEventListener('hideUpdateVenueModal', event => {
            // Clear the form fields
        document.getElementById('venue_name-update-input').value = '';
        document.getElementById('venue_description-update-input').value = '';
        document.getElementById('venue_code-update-input').value = '';
        document.getElementById('capacity-update-input').value = '';
        document.getElementById('venue_rate-update-input').value = '';
            // Hide the modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('UpdateVenue'));
            modal.hide();

            // Hide the success message after 1 second
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 1500);
        });

        function updateVenue($data) {
            // Set the values of the input fields
            console.log($data);
            document.getElementById('venue_name-update-input').value = $data.venue_name;
            document.getElementById('venue_description-update-input').value = $data.description;
            document.getElementById('venue_code-update-input').value = $data.venue_code;
            document.getElementById('capacity-update-input').value = $data.capacity;
            document.getElementById('venue_rate-update-input').value = $data.rate_price ? $data.rate_price.amount : '';
        

        }

    </script>
</div>
