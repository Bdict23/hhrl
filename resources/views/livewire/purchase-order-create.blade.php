<div>
    <div>
        @if (session()->has('success'))
        <div class="alert alert-success" id="success-message">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    </div>
    <div class="container">
        <div class="row">
            <div class=" mt-3 col-md-7">
                <div class='card'>
                    <div class="card-header">
                        <div>
                            <form wire:submit.prevent="store" id="poForm">
                                @csrf
                            <strong>Purchase Order Items</strong>
                            <x-primary-button type="button" data-bs-toggle="modal" data-bs-target="#AddItemModal" style="float: right">+
                                Add
                                ITEM</x-primary-button>
                        </div>
                    </div>
                    <div class="card-body table-responsive-sm">
                        <table class="table table-striped table-hover table-sm table-responsive">
                            <thead class="table-dark">
                                <tr style="font-size: x-small">
                                    <th>ITEM CODE</th>
                                    <th>ITEM NAME</th>
                                    <th>UNIT</th>
                                    <th>REQUEST QTY.</th>
                                    <th>COST</th>
                                    <th>SUB-TOTAL</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemTableBody">
                                {{-- POPULATE TABLE FOR SELECTED ITEMS --}}
                                @forelse ($selectedItems as $index => $item)
                                    <tr>
                                        <td style="font-size: 80%">{{ $item->item_code }}</td>
                                        <td style="font-size: 80%">{{ $item->item_description }}</td>
                                        <td style="font-size: 80%">
                                            {{ $item->uom ? $item->uom->unit_symbol : 'N/A' }}
                                        </td>
                                        <td>
                                            <input wire:model="purchaseRequest.{{ $index }}.qty" type="number" class="form-control" id="qty_{{ $index }}" value="0" min="1" onchange="updateTotalPrice(this)">
                                        </td>
                                        <td style="font-size: 80%">{{ number_format($item->costPrice->amount, 2) }}</td>
                                        <td class="total-price" id="total-price{{ $index }}" style="font-size: 80%">
                                            {{ number_format($item->costPrice->amount * $item->qty, 2) }}
                                        </td>
                                        <td style="font-size: 80%">
                                            <button type="button" class="btn btn-danger btn-sm" wire:click="removeItem({{ $item->id }})">Remove</button>
                                        </td>
                                    </tr>
                    
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No items selected</td>
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
                        <strong style="float: right">Total Cost : <span id="totalAmount">0.00</span></strong>
                    </div>
                </div>
            </div>
            <div class=" mt-3 col-md-5">
                <div class='card p-1'>
                <div class="card-header">
                    <strong>Purchase Order Information</strong>
                </div>
                   
                        <div class="row mb-3">
                            <div class="col-md-6">
                        
                                <label for="options" class="form-label">Select Supplier <span style="color: red; font-size: smaller;"> *</span></label>
                                <select wire:model="supplierId" class="form-control"  style="font-size: x-small">
                                    <option value="" selected>Select Supplier</option>
                                    @foreach ($suppliers as $supp)
                                        <option value="{{ $supp->id }}" style="font-size: x-small">
                                            {{ $supp->supp_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplierId')
                                    <span class="text-danger" style="font-size: x-small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="po_number" class="form-label">PO Number</label>
                                <input wire:model="requisitionNumber" type="text" class="form-control" readonly style="font-size: x-small"placeholder="<AUTO>" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_no_1" class="form-label">M. PO NUMBER</label>
                                <input wire:model="mPoNumber" type="text" class="form-control" id="merchandise_po_number" name="merchandise_po_number" style="font-size: x-small">
                            </div>
                            <div class="col-md-6">
                                <label for="options" class="form-label">Terms<span style="color: red; font-size: smaller;"> *</span></label>
                                <select wire:model="term_id" class="form-control" style="font-size: x-small">
                                    <option value=""  selected>Select Terms</option>
                                    @foreach ($terms as $term)
                                        <option value="{{ $term->id }}" style="font-size: x-small">
                                            {{ $term->term_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('term_id')
                                <span class="text-danger" style="font-size: x-small">{{ $message }}</span>
                            @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <textarea wire:model="remarks" type="text" class="form-control" style="font-size: x-small" placeholder="Remarks"></textarea>
                                @error('remarks')
                                    <span class="text-danger" style="font-size: x-small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="row ">

                                    @if ($hasReviewer)
                                    <div class="col-md-6">
                                        <label for="" class="form-label">Reviewed To <span style="color: red; font-size: x-small;"> *</span></label>
                                        <select wire:model="reviewer_id"  class="form-control" style="font-size: x-small">
                                            <option value="">Select Reviewer</option>
                                            @foreach ($reviewer as $reviewers)
                                                <option value="{{ $reviewers->employees->id }}" style="font-size: x-small">
                                                    {{ $reviewers->employees->name }} {{ $reviewers->employees->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        
                                    </div>
                                    @endif
                                    @error('reviewer_id')
                                            <span class="text-danger" style="font-size: x-small">{{ $message }}</span>
                                    @enderror
                                    
                        
                                    <div
                                    @if (!$hasReviewer)
                                        class="col-md-12"
                                    @else
                                        class="col-md-6"
                                    @endif>
                                        <label for="contact_no_2" class="form-label">Approved To <span style="color: red; font-size: x-small;"> *</span></label>
                                        <select wire:model="approver_id" class="form-control" style="font-size: x-small">
                                            <option value="" selected>Select Approver</option>
                                            @foreach ($approver as $approvers)
                                                <option value="{{ $approvers->employees->id }}" style="font-size: x-small">
                                                    {{ $approvers->employees->name }} {{ $approvers->employees->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('approver_id')
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
                    <h5 class="modal-title" id="AddItemModalLabel">Select Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="card-body">


                <div class="modal-body container">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="searchItemsInput" placeholder="Search items..." style="font-size: x-small" onkeyup="searchingItems()">
                        </div>
                        <div class="col-md-6">
                             <div class="form-check mt-2 float-end">
                                <input wire:click="filterByROP()"  class="form-check-input" type="checkbox" id="filterReorderPoint" 
                                {{-- onchange="filterByROP()" --}}
                                >
                                <label class="form-check-label" for="filterReorderPoint" >
                                    Filter by ROP
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- Table for Item Selection -->
                    <div style="max-height: 400px; overflow-y: auto;">
                        <table class="table  table-hover table-sm table-responsive">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th style="font-size: 10px;">ITEM CODE</th>
                                    <th style="font-size: 10px;" >ITEM NAME</th>
                                    <th style="font-size: 10px;">UNIT</th>
                                    <th style="font-size: 10px;" >INV. COUNT</th>
                                    <th style="font-size: 10px;">AVAILABLE</th>
                                    <th style="font-size: 10px;">ROP</th>
                                    <th style="font-size: 10px;" >COST</th>
                                    <th style="font-size: 10px;">STATUS</th>
                                    <th style="font-size: 10px;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody" >
                                @foreach ($items as $item)
                                    <tr
                                    {{-- @if ($cardexAvailable[$item->id]  <=  $item->orderpoint ?? 0) --}}
                                        style="background-color: #F7CFD8;"
                                    {{-- @endif --}}
                                    >
                                        <td style="font-size: 12px;">{{ $item->item_code }}</td>
                                        <td style="font-size: 12px;">{{ $item->item_description }}</td>
                                        <td style="font-size: 12px;">{{ $item->uom ? $item->uom->unit_symbol : 'N/A' }}</td>
                                        <td style="font-size: 12px;">{{ $cardexBalance[$item->id] ?? 0 }}</td>
                                        <td  
                                        @if ($cardexAvailable[$item->id] ?? 0.00  <  $item->orderpoint ?? 0.00)
                                             style="color: #CF0F47; font-size: 12px;"
                                        @elseif($item->orderpoint == 0)
                                            style="font-size: 12px;"
                                        @else
                                            style="font-size: 12px;"
                                        @endif
                                        >{{ $cardexAvailable[$item->id] ?? 0 }}</td>
                                        <td 
                                        {{-- @if (($cardexAvailable[$item->id]  <=  $item->orderpoint ?? 0) ?? 0 )
                                            style="color: #CF0F47; font-size: 12px;"
                                        @elseif($item->orderpoint == 0)
                                            style="font-size: 12px;"
                                        @else
                                            style="font-size: 12px;"
                                        @endif --}}
                                        >{{ $item->orderpoint }}</td>
                                        <td style="font-size: 12px;">{{ $item->costPrice ? $item->costPrice->amount : 'N/A' }}</td>
                                        <td style="font-size: 12px;">{{ $item->item_status ? 'Active' : 'Inactive' }}</td>
                                        <td>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('success', function (event) {
                // Clear fields
                document.getElementById('poForm').reset();
            });
        });

        function searchingItems() {
            const input = document.getElementById('searchItemsInput').value.toLowerCase();
            const rows = document.querySelectorAll('#itemsTableBody tr');
            rows.forEach(row => {
                const itemCode = row.cells[0].textContent.toLowerCase();
                const itemDescription = row.cells[1].textContent.toLowerCase();
                if (itemCode.includes(input) || itemDescription.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        
        // let filterROP = document.getElementById('filterReorderPoint');
        //     filterROP.addEventListener('click', function() {
        //         filterByROP();
        //     });

        //     // On item select, do not reset input
        //     document.querySelectorAll('.data-item').forEach(item => {
        //         item.addEventListener('click', function(e) {
        //             // Do your selection logic here — BUT don't touch searchInput.value
        //             console.log("Selected: " + this.textContent);
        //         });
        //     });


    </script>
    </div>
