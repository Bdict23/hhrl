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
    <div id="table-lists" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Table Lists</h5>
        </div>
        <div class="card-body">
            {{-- @if (auth()->user()->employee->getModulePermission('Business Venues') == 1 ) --}}
                <x-primary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('table-create-form', document.querySelector('.nav-link.active'))">+ ADD
                New Table</x-primary-button>
            {{-- @endif --}}
                <x-secondary-button type="button" class="mb-3 btn-sm"
                wire:click="fetchData()">Refresh</x-secondary-button>
            <div class="table-responsive mt-3 mb-3 d-flex justify-content-center"
                style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-sm small">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Table Name</th>
                            <th>Capacity</th>
                            <th>Created</th>
                            <th class="text-end"  @if (auth()->user()->employee->getModulePermission('Business Venues') != 1 ) style="display: none;"  @endif>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tables as $table)
                            <tr>
                                <td>{{ $table->table_name }}</td>
                                <td>{{ $table->seating_capacity }}</td>
                                <td>{{ $table->created_at->format('M-d-Y') }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateTableModal" onclick="updateTable({{ json_encode($table) }})" wire:click="editTable({{ $table->id }})">Edit</button>
                                    <a href="#" class="btn btn-sm btn-danger" wire:click="deactivateTable({{ $table->id }})">Remove</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No Table found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    {{-- Update Modal --}}
    <div class="modal fade" id="updateTableModal" tabindex="-1" aria-labelledby="updateMenuCategory" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Update Table</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" wire:submit.prevent="updateTable" id="UpdateTableForm">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="table_name-update" class="form-label">Table Name</label>
                                <input type="text" class="form-control" id="table_name-update-input" wire:model="table_name_input">
                                @error('table_name_input')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                        </div>
                            <div class=" mb-3">
                                <label for="table_capacity-update" class="form-label">Table Capacity</label>
                                <input type="number" class="form-control" id="table_capacity-update-input" wire:model="table_capacity_input">
                                @error('table_capacity_input')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <x-primary-button type="submit">Update</x-primary-button>
                        </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Form --}}
    <div id="table-create-form" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Add New Table</h5>
        </div>
        <div class="card-body">
            <x-secondary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('table-lists', document.querySelector('.nav-link.active'))">Summary</x-secondary-button>
            <form wire:submit.prevent="storeTable" id="tableForm">
                @csrf
                <div class="mb-3">
                    <label for="table_name-input" class="form-label"> Table Name <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="table_name-input" wire:model="table_name_input" >
                    @error('table_name_input')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="table_description-input" class="form-label">Table Capacity</label>
                    <input type="number" class="form-control" id="table_capacity-input" wire:model="table_capacity_input" >
                    @error('table_capacity_input')
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
            window.addEventListener('resetCreateTableForm', event => {
                document.getElementById('tableForm').reset();
            });

            window.addEventListener('success', event => {
                document.getElementById('success-message').style.display = 'block';
                document.getElementById('success-message').innerHTML = event.detail.message;

                setTimeout(function() {
        document.getElementById('success-message').style.display = 'none';
                            }, 1500);
        });
        });

        // HIDE UPDATE CATEGORY MODAL
        window.addEventListener('closeUpdateTableModal', event => {
            document.getElementById('UpdateTableForm').reset();
            var modal = bootstrap.Modal.getInstance(document.getElementById('updateTableModal'));
            modal.hide();

            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 1500);
        });

        function updateTable($data) {
            console.log($data);
            document.getElementById('table_name-update-input').value = $data.table_name;
            document.getElementById('table_capacity-update-input').value = $data.seating_capacity;

        }

    </script>
</div>
