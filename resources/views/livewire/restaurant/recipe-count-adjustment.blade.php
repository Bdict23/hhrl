<div>
    <div class="container">
        <div class="row">
            <div class=" mt-3 col-md-7">
                <div class='card'>
                    <div class="card-header">
                        <div>
                            <form wire:submit.prevent="store" id="poForm">
                                @csrf
                            <strong>Recipe Count Adjustment</strong>
                            <x-primary-button type="button" data-bs-toggle="modal" data-bs-target="#AddItemModal" style="float: right">+Add</x-primary-button>
                        </div>
                    </div>
                    <div class="card-body table-responsive-sm">
                        <table class="table table-striped table-hover table-sm table-responsive">
                            <thead class="table-dark">
                                <tr style="font-size: x-small">
                                    <th>Recipe Name</th>
                                    <th>Recipe Type</th>
                                    <th>Category</th>
                                    <th>Code</th>
                                    <th>Adj. Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemTableBody">
                                {{-- POPULATE TABLE FOR SELECTED ITEMS --}}
                                @forelse ($selectedItems as $index => $item)
                                    <tr>
                                        <td style="font-size: 80%">{{ $item->menu_name }}</td>
                                        <td style="font-size: 80%">{{ $item->recipe_type }}</td>
                                        <td style="font-size: 80%">
                                            {{ $item->code }}
                                        </td>
                                        <td style="font-size: 80%">
                                            {{ $item->item_code }}
                                        </td>
                                        <td style="font-size: 80%">
                                            <input wire:model.live="recipeCount.{{ $index }}.qty" type="number" class="form-control" id="qty_{{ $index }}" value="0" min="1" step="1" >
                                        </td>
                                        <td style="font-size: 80%">
                                            <button type="button" class="btn btn-danger btn-sm" wire:click="removeItem({{ $item->id }})">Remove</button>
                                        </td>
                                    </tr>
                    
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No Recipe selected</td>
                                    </tr>
                    
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer">
                        <div>
                            @error('selectedItems')
                                <span class="text-danger" style="font-size: x-small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div wire:loading>
                            Saving Please Wait...
                        </div>
                    </div>
                </div>
            </div>
            <div class=" mt-3 col-md-5">
                <div class='card p-1'>
                <div class="card-header">
                    <strong>Adjustment Information</strong>
                </div>
                   
                        <div class="row mb-3">
                            <div class="col-md-6">
                        
                                <label for="options" class="form-label">Adjustment Type <span style="color: red; font-size: smaller;"> *</span></label>
                                <select wire:model="adjustmentType" class="form-control"  style="font-size: x-small">
                                    <option value="" selected>Select Adjustment Type</option>
                                    @foreach (['INCREASE', 'DECREASE'] as $type)
                                        <option value="{{ $type }}" style="font-size: x-small">
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('adjustmentType')
                                    <span class="text-danger" style="font-size: x-small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="adjustment_number" class="form-label">Adjustment Number</label>
                                <input wire:model="adjustmentRefNumber" type="text" class="form-control" readonly style="font-size: x-small"placeholder="<AUTO>" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row ">
                                    <div class="col-md-12">
                                        <label for="contact_no_2" class="form-label">Approved To <span style="color: red; font-size: x-small;"> *</span></label>
                                        <select wire:model="selected_approver_id" class="form-control" style="font-size: x-small">
                                            <option value="" selected>Select Approver</option>
                                            @foreach ($approver ?? [] as $approvers)
                                                <option value="{{ $approvers->employees->id }}" style="font-size: x-small">
                                                    {{ $approvers->employees->name }} {{ $approvers->employees->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>           
                                     <div class="col-md-12">
                                        @error('selected_approver_id')
                                            <span class="text-danger" style="font-size: x-small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <x-primary-button type="submit">
                                Save
                            </x-primary-button>
                            <a href="/purchase_order"><x-secondary-button type="button"> Summary </x-secondary-button></a>
                        </div>
                    </div>
            </div>
        </div>

        


        <!-- Add Item Modal -->
    <div class="modal fade" id="AddItemModal" tabindex="-1" aria-labelledby="AddItemModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content card">
                <div class="modal-header card-header">
                    <h5 class="modal-title" id="AddItemModalLabel">Select Recipe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="card-body">


                <div class="modal-body container">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="searchItemsInput" placeholder="Search items..." style="font-size: x-small" onkeyup="searchingItems()">
                        </div>
                    </div>
                    <!-- Table for Item Selection -->
                    <div style="max-height: 400px; overflow-y: auto;">
                        <table class="table  table-hover table-sm table-responsive">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th style="font-size: 10px;" >RECIPE NAME</th>
                                    <th style="font-size: 10px;" >DESCIPTION</th>
                                    <th style="font-size: 10px;">TYPE</th>
                                    <th style="font-size: 10px;">CODE</th>
                                    <th style="font-size: 10px;" >CATEGORY</th>
                                    <th style="font-size: 10px;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody" >
                                @foreach ($recipes as $item)
                                    <tr>
                                        <td style="font-size: 80%">{{ $item->menu_name }}</td>
                                        <td style="font-size: 80%">{{ $item->description }}</td>
                                        <td style="font-size: 80%">{{ $item->type }}</td>
                                        <td style="font-size: 80%">{{ $item->code }}</td>
                                        <td style="font-size: 80%">{{ $item->category }}</td>
                                        <td style="font-size: 80%">
                                            <button type="button" class="btn btn-primary btn-sm" wire:click="addItem({{ $item->id }})">Add</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
                <div class="modal-footer">
                    <div>
                        @error('selectedItems')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div>
                            @if (session()->has('error'))
                            <span class="alert text-danger float-left">
                                {{ session('error') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
