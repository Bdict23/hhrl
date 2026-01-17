<div>
    <div id="cash-drawer-lists" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Cash Drawer Lists</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    @if (auth()->user()->employee->getModulePermission('Cash Drawer') == 1 )
                        <x-primary-button type="button" class="mb-3 btn-sm"
                        onclick="showTab('cash-drawer-form', document.querySelector('.nav-link.active'))">+ ADD
                        CASH DRAWER</x-primary-button>
                    @endif
                        <x-secondary-button type="button" class="mb-3 btn-sm"
                        wire:click="fetchCashDrawerData()">Refresh</x-secondary-button>
                </div>
                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Search</span>
                        <input type="text" class="form-control" id="search-cash-drawer"
                            onkeyup="filterCashDrawers()">
                    </div>
                </div>
            </div>
            <script>
                function filterCashDrawers() {
                    const input = document.getElementById('search-cash-drawer');
                    const filter = input.value.toLowerCase();
                    const table = document.querySelector('#cash-drawer-lists table');
                    const trs = table.querySelectorAll('tbody tr');
                    trs.forEach(row => {
                        // Skip "No Service found" row
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
                            <th class="text-xs">DRAWER NAME</th>
                            <th class="text-xs">DRAWER CODE</th>
                            <th class="text-xs">DEPARTMENT</th>
                            <th class="text-center text-xs"  @if (auth()->user()->employee->getModulePermission('Cash Drawer') != 1 ) style="display: none;"  @endif>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                       @forelse ($cashDrawers as $cashDrawer)
                        <tr>
                            <td class="text-xs">{{ $cashDrawer->drawer_name }}</td>
                            <td class="text-xs">{{ $cashDrawer->drawer_code }}</td>
                            <td class="text-xs">{{ $cashDrawer->department ? $cashDrawer->department->department_name : 'N/A' }}</td>
                            <td class="text-end text-xs" @if (auth()->user()->employee->getModulePermission('Cash Drawer') != 1 ) style="display: none;"  @endif>
                                <x-secondary-button class="btn-sm" wire:click="editCashDrawer({{ $cashDrawer->id }})" 
                                    data-bs-toggle="modal" data-bs-target="#UpdateDrawer"><i class="bi bi-pencil-square"></i>&nbsp;Edit</x-secondary-button>
                                <x-danger-button class="btn-sm" wire:click="deactivateCashDrawer({{ $cashDrawer->id }})"><i class="bi bi-slash-circle"></i>&nbsp;Disable</x-danger-button>
                            </td>
                        </tr>
                       @empty
                            {{-- If no services found --}}
                            <tr>
                                <td colspan="7" class="text-center text-muted">No drawers found. &nbsp;<i class="bi bi-dropbox"></i></td>
                            </tr>
                       @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Update Cash Drawer Modal --}}
    <div class="modal fade" id="UpdateDrawer" tabindex="-1" aria-labelledby="updateDrawerModal" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Update Cash Drawer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" wire:submit.prevent="updateDrawer" id="UpdateDrawerForm">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="drawer_name-update" class="form-label">Drawer Name</label>
                                <input type="text" class="form-control" id="drawer_name-update-input" wire:model="drawer_name_input">
                                @error('drawer_name_input')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                           <div class="row col-md-12 mb-3">
                                
                           </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="drawer_code-update" class="form-label">Drawer Code</label>
                                    <input type="text" class="form-control" id="drawer_code-update-input" wire:model="drawer_code_input">
                                    @error('drawer_code_input')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            
                            </div>
                        </div>
                            <div class=" mb-3">
                                <label for="department-select" class="form-label">Department <span style="color: red;">*</span></label>
                                <select class="form-select" id="department-select" wire:model="selected_department_id">
                                    <option value="">Select</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" @if($selected_department_id == $department->id) selected @endif>{{ $department->department_name }}</option>
                                    @endforeach
                                </select>
                                @error('selected_department_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <x-primary-button type="submit"><i class="bi bi-arrow-counterclockwise"></i>&nbsp;Update</x-primary-button>
                        </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Cash Drawer Form --}}
    <div id="cash-drawer-form" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Add Cash Drawer</h5>
        </div>
        <div class="card-body">
            <x-secondary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('cash-drawer-lists', document.querySelector('.nav-link.active'))">Summary</x-secondary-button>
            <form wire:submit.prevent="storeCashDrawer" id="cashDrawerForm">
                @csrf
                <div class="mb-3 row">
                    <div class="col-md-4">
                        <label for="drawer_name-input" class="form-label">Drawer Name <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="drawer_name-input" wire:model="drawer_name_input" >
                        @error('drawer_name_input')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 mb-6">
                        <label for="drawer_code-input" class="form-label">Drawer Code <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="drawer_code-input" wire:model="drawer_code_input" >
                        @error('drawer_code_input')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-8 mb-3">
                        <label for="department-select" class="form-label">Department <span style="color: red;">*</span></label>
                        <select class="form-select" id="department-select" wire:model="selected_department_id">
                            <option value="">Select</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                            @endforeach
                        </select>
                        @error('selected_department_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
               <div class="d-flex justify-content-start">
                 <x-primary-button type="submit">Save</x-primary-button>
               </div>
            </form>
        </div>
    </div>

    <script>
        window.addEventListener('alert', event => {
            const data = event.detail[0];
            Swal.fire({
                icon: data.type,
                title: data.type === 'success' ? 'Success!' : 'Error!',
                text: data.message,
                timer: 3000,
                showConfirmButton: false
            });
             document.getElementById('cashDrawerForm').reset();
        });

        // HIDE UPDATEcATEGORY MODAL
        window.addEventListener('hideUpdateDrawerModal', event => {
            // Reset the form
            document.getElementById('UpdateDrawerForm').reset();
            // Hide the modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('UpdateDrawer'));
            modal.hide();
        });


        // function updateDrawer($data) {
        //     // Set the values of the input fields
        //     console.log($data);
        //     // console.log($data.rate_price.amount);
        //     document.getElementById('drawer_name-update-input').value = $data.drawer_name;
        //     document.getElementById('drawer_code-update-input').value = $data.drawer_code;
        //     document.getElementById('service_description-update-input').value = $data.service_description;
        //     if($data.rate_price){
        //         document.getElementById('service_rate-input-update').value = $data.rate_price.amount;
        //     } else {
        //         document.getElementById('service_rate-input-update').value = '0.00';
        //     }
        //     document.getElementById('service-multiplier-update').checked = $data.has_multiplier;
        //     // Set the selected category
        //     var selectElement = document.getElementById('selectServiceCategoryUpdate');
        //     selectElement.value = $data.category ? $data.category.id : '';
        
        // }

    </script>
</div>
