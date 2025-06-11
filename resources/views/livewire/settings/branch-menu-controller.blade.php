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
    <div id="menu-controller-list" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Table Lists</h5>
        </div>
        <div class="card-body">
            {{-- @if (auth()->user()->employee->getModulePermission('Business Venues') == 1 ) --}}
                <x-primary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('menu-controller-create-form', document.querySelector('.nav-link.active'))">+ New Menu Control</x-primary-button>
            {{-- @endif --}}
                <x-secondary-button type="button" class="mb-3 btn-sm"
                wire:click="fetchData()">Refresh</x-secondary-button>
            <div class="table-responsive mt-3 mb-3 d-flex justify-content-center"
                style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-sm small">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Control Name</th>
                            <th>Status</th>
                            <th>Effective Date</th>
                            <th>End Date</th>
                            <th>Schedule</th>
                            <th>Action</th>
                            <th class="text-end"  @if (auth()->user()->employee->getModulePermission('Business Venues') != 1 ) style="display: none;"  @endif>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    {{-- Update Modal --}}
    {{-- <div class="modal fade" id="updateTableModal" tabindex="-1" aria-labelledby="updateMenuCategory" aria-hidden="true" wire:ignore.self>
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
    </div> --}}

    {{-- Menu Control Form --}}
    <div id="menu-controller-create-form" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Add New Menu Control</h5>
        </div>
        <div class="card-body">
            <x-secondary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('menu-controller-list', document.querySelector('.nav-link.active'))">Summary</x-secondary-button>
            <form wire:submit.prevent="storeMenuControl" id="menuControlForm">
                @csrf
                <div class="mb-3">
                    <label for="menu_control_name-input" class="form-label"> Menu Control Name <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="menu_control_name-input" wire:model="menu_control_name_input" >
                    @error('menu_control_name_input')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="menu_control_description-input" class="form-label">Menu Control Description</label>
                    <textarea class="form-control" id="menu_control_description-input" wire:model="menu_control_description_input" rows="3"></textarea>
                    @error('menu_control_description_input')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label d-block">Days of the Week <span style="color: red;">*</span></label>
                    <div class="d-flex flex-row gap-3">
                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="day-{{ strtolower($day) }}"
                                       value="{{ $day }}"
                                       wire:model="selected_days">
                                <label class="form-check-label" for="day-{{ strtolower($day) }}">{{ $day }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('selected_days')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div >
                    <x-secondary-button type="button" class="mb-3 btn-sm"
                        {{-- wire:click="addMenuItem" --}}
                         data-bs-toggle="modal" data-bs-target="#menuItemsModal">+ Add Menu Item</x-secondary-button>
                    <table class="table table-striped table-sm small">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>Menu Category</th>
                                <th>Menu Item</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach($menu_items as $index => $item)
                                <tr>
                                    <td>{{ $item['category'] }}</td>
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ $item['price'] }}</td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" wire:click="removeMenuItem({{ $index }})">Remove</button>
                                    </td>
                                </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                <x-primary-button type="submit">Save</x-primary-button>
            </form>
        </div>
    </div>


    {{-- menu items modal --}}
    <div class="modal fade" id="menuItemsModal" tabindex="-1" aria-labelledby="menuItemsModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuItemsModalLabel">Add Menu Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="menu_category_id" class="form-label">Menu Category</label>
                        <select class="form-select" id="menu_category_id" wire:model="selected_menu_category">
                            <option value="">Select Category</option>
                            {{-- @foreach($menu_categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach --}}
                        </select>
                        @error('selected_menu_category')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="menu_item_id" class="form-label">Menu Item</label>
                        <select class="form-select" id="menu_item_id" wire:model="selected_menu_item">
                            <option value="">Select Item</option>
                            {{-- @foreach($menu_items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach --}}
                        </select>
                        @error('selected_menu_item')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="menu_item_price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="menu_item_price" wire:model="menu_item_price">
                        @error('menu_item_price')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" wire:click="addMenuItemToControl">Add to Control</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm small">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>Menu Category</th>
                                    <th>Menu Item</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach($control_menu_items as $index => $item)
                                    <tr>
                                        <td>{{ $item['category'] }}</td>
                                        <td>{{ $item['name'] }}</td>
                                        <td>{{ $item['price'] }}</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" wire:click="removeMenuItemFromControl({{ $index }})">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="saveMenuControlItems">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</div>
