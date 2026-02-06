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
        
        <div class="card-body">
            <div class="row">
               <div class="col-md-6">
                 @if (auth()->user()->employee->getModulePermission('Menu Controller') == 1 )
                     <x-primary-button type="button" class="mb-3 btn-sm"
                     onclick="showTab('menu-controller-create-form', document.querySelector('.nav-link.active'))">+ New Menu Control</x-primary-button>
                 @endif
                     <x-secondary-button type="button" class="mb-3 btn-sm"
                     wire:click="fetchData()">Refresh</x-secondary-button>
               </div>
               <div class="col-md-6">
                   <div class="input-group mb-3">
                       <span class="input-group-text">Search</span>
                       <input type="text" class="form-control" id="search-menu-control"
                           onkeyup="filterMenuControls()">
                   </div>
               </div>
            </div>
            <script>
                function filterMenuControls() {
                    const input = document.getElementById('search-menu-control');
                    const filter = input.value.toLowerCase();
                    const table = document.querySelector('#menu-controller-list table');
                    const trs = table.querySelectorAll('tbody tr');

                    trs.forEach(row => {
                        // Skip "No Menu Control found" row
                        if (row.children.length < 2) return;
                        const name = row.children[0].textContent.toLowerCase();
                        const status = row.children[1].textContent.toLowerCase();
                        if (name.includes(filter) || status.includes(filter)) {
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
                            <th>Control Name</th>
                            <th>Status</th>
                            <th>Effective Date</th>
                            <th>End Date</th>
                            <th>Schedule</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($menuControls as $control)
                            <tr>
                                <td>{{ $control->control_name }}</td>
                                <td>{{ $control->is_available  == 0 ? 'Inactive' : 'Active' }}</td>
                                <td>{{ $control->start_date }}</td>
                                <td>{{ $control->end_date }}</td>
                                <td>
                                    <select name="schedule[{{ $control->id }}]" id="" class="form-select form-select-sm">
                                        @if($control->mon)
                                            <option value="monday">Monday</option>
                                        @endif
                                        @if($control->tue)
                                            <option value="tuesday">Tuesday</option>
                                        @endif
                                        @if($control->wed)
                                            <option value="wednesday">Wednesday</option>
                                        @endif
                                        @if($control->thu)
                                            <option value="thursday">Thursday</option>
                                        @endif
                                        @if($control->fri)
                                            <option value="friday">Friday</option>
                                        @endif
                                        @if($control->sat)
                                            <option value="saturday">Saturday</option>
                                        @endif
                                        @if($control->sun)
                                            <option value="sunday">Sunday</option>
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <x-primary-button type="button" class="btn-sm"
                                        onclick="showTab('menu-controller-update-form', document.querySelector('.nav-link.active'))">Edit</x-primary-button>
                                    <x-danger-button type="button" class="btn-sm"
                                        wire:click="deleteMenuControl({{ $control->id }})">Delete</x-danger-button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No menu controls found.</td>
                            </tr>
                        @endforelse
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
            <form wire:submit.prevent="saveMenuControl" id="menuControlForm">
                @csrf
               <div class="row">
                 <div class="col-md-6">
                     <label for="menu_control_name-input" class="form-label"> Menu Control Name <span style="color: red;">*</span></label>
                     <input type="text" class="form-control" id="menu_control_name-input" wire:model="controlNameInput" >
                     @error('controlNameInput')
                         <span class="text-danger">{{ $message }}</span>
                     @enderror
                 </div>
                 <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="effective_date-input" class="form-label">Effective Date <span style="color: red;">*</span></label>
                            <input type="date" class="form-control" id="effective_date-input" wire:model="effectiveDateInput">
                            @error('effectiveDateInput')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                        </div>
                        <div class="col-md-4">
                            <label for="end_date-input" class="form-label">End Date <span style="color: red;">*</span></label>
                            <input type="date" class="form-control" id="end_date-input" wire:model="endDateInput">
                            @error('endDateInput')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="is_available-input" class="form-label">Status <span style="color: red;">*</span></label>
                        <select class="form-select" id="is_available-input" wire:model="isAvailableInput">
                            <option value="">Select Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        @error('isAvailableInput')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                 </div>
               </div>
                <div class="mb-3">
                    <label class="form-label d-block">Days of the Week <span style="color: red;">*</span></label>
                    <div class="d-flex flex-row gap-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                       type="checkbox" wire:model='mondaySelected'>
                                <label class="form-check-label" for="day-monday">Monday</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                       type="checkbox" wire:model='tuesdaySelected'>
                                <label class="form-check-label" for="day-tuesday">Tuesday</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                       type="checkbox" wire:model='wednesdaySelected'>
                                <label class="form-check-label" for="day-wednesday">Wednesday</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                       type="checkbox" wire:model='thursdaySelected'>
                                <label class="form-check-label" for="day-thursday">Thursday</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                       type="checkbox" wire:model='fridaySelected'>
                                <label class="form-check-label" for="day-friday">Friday</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                       type="checkbox" wire:model='saturdaySelected'>
                                <label class="form-check-label" for="day-saturday">Saturday</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                       type="checkbox" wire:model='sundaySelected'>
                                <label class="form-check-label" for="day-sunday">Sunday</label>
                            </div>
                    </div>
                    @error('selected_days')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                    <div>
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
                               @forelse ($selectedRecipe as $index => $recipe)
                                   <tr>
                                       <td>{{ $recipe->category->category_name ?? 'N/A' }}</td>
                                       <td>{{ $recipe->menu_name ?? 'N/A' }}</td>
                                       <td>{{ $recipe->price ?? 'N/A' }}</td>
                                        <td>
                                           <button type="button" class="btn btn-danger btn-sm" wire:click="removeMenuItem({{ $recipe->id }})">Remove</button>
                                       </td>
                                   </tr>
                        
                               @empty
                                   <tr>
                                        <td colspan="4" class="text-center">No menu items selected.</td>
                                   </tr>
                               @endforelse
                            </tbody>
                        </table>
                        <x-primary-button type="submit">Save</x-primary-button>
                    </div>
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
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
                        <div class="col-md-6">
                            <label for="search_menu_item" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search_menu_item" onkeydown="">
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const searchInput = document.getElementById('search_menu_item');
                                    const table = searchInput.closest('.modal-body').querySelector('table');
                                    const tbody = table.querySelector('tbody');

                                    searchInput.addEventListener('input', function () {
                                        const filter = searchInput.value.toLowerCase();
                                        Array.from(tbody.rows).forEach(row => {
                                            const text = row.textContent.toLowerCase();
                                            row.style.display = text.includes(filter) ? '' : 'none';
                                        });
                                    });
                                });
                            </script>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-sm small">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>Recipe Name</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Code</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="menu-items-table-body">
                               {{-- Assuming $menuItems is passed to the component --}}
                               @forelse ($menuItems as $item)
                                   <tr>
                                       <td>{{ $item->menu_name ?? 'N/A' }}</td>
                                       <td>{{ $item->category->category_name ?? 'N/A' }}</td>
                                       <td>{{ $item->recipe_type ?? 'N/A' }}</td>
                                       <td>{{ $item->menu_code ?? 'N/A' }}</td>
                                       <td>
                                           <button type="button" class="btn btn-primary btn-sm" wire:click="selectMenuItem({{ $item->id }})">Add</button>
                                       </td>
                                   </tr>
                               @empty
                                   <tr>
                                       <td colspan="4" class="text-center">No menu items found.</td>
                                   </tr>
                               @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
