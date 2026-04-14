@extends('layouts.master')
@section('content')
<div class="justify-content-end d-flex mb-3">
    <h3>Update Purchas Order &nbsp;<i class="bi bi-cart4"></i></h3>
</div>
    <form id="poForm" method="POST" action="{{ route('purchase_order.update', $requisitionInfo->id ?? '') }}">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-7 mb-2">
                <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Item Lists</h5>
                            <header>
                            </header>
                        </div>
                    <div class="card-body">
                        <div>
                            <x-primary-button
                                type="button" data-bs-toggle="modal" data-bs-target="#AddItemModal">+
                                Add ITEM</x-primary-button>
                            <x-primary-button
                                    onclick="document.getElementById('poForm').submit();">Update</x-primary-button>
                            <x-secondary-button onclick="history.back()" type="button">Back</x-secondary-button>
                        </div>
                        <div class="table-responsive overflow-auto">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ITEM CODE</th>
                                        <th>ITEM DESCRIPTION</th>
                                        <th>UNIT</th>
                                        <th>REQUEST QTY.</th>
                                        <th>PRICE</th>
                                        <th>TOTAL</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemTableBody">
                                    @forelse ($requisitionInfo->requisitionDetails ?? [] as $reqdetail)
                                        <tr data-id="{{ $reqdetail && $reqdetail->requisition_number }}">
                                            <td>{{ $reqdetail->items->item_code ?? '' }}</td>
                                            <td>{{ $reqdetail->items->item_description ?? '' }}</td>
                                            <td>{{ $reqdetail->items->uom->unit_symbol ?? '' }}</td>
                                            <td><input type="number" name="request_qty[]" class="form-control"
                                                    value="{{ $reqdetail->qty ?? '' }}" min="1" onchange="updateTotalPrice(this)"
                                                    {{ $requisitionInfo && $requisitionInfo->requisition_status != 'PREPARING' ? 'disabled' : '' }}>
                                                <input type="hidden" name="item_id[]" value="{{ $reqdetail->items->id ?? '' }}">
                                            </td>
                                            <td>{{ $reqdetail->items->priceLevel()->latest()->where('price_type', 'cost')->first()->amount ?? '' }}
                                            </td>
                                            <td class="total-price">{{ $reqdetail->total ?? $reqdetail->qty * $reqdetail->items->priceLevel()->latest()->where('price_type', 'cost')->first()->amount ?? 0 }}</td>
                                            <td><button
                                                    class="btn {{ $requisitionInfo && $requisitionInfo->requisition_status == 'PREPARING' ? 'btn-danger' : 'btn-secondary' }} btn-sm"
                                                    onclick="removeRow(this)"
                                                    {{ $requisitionInfo && $requisitionInfo->requisition_status != 'PREPARING' ? 'disabled' : '' }}>Remove</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                   </div>
                </div>
            </div>

            <div class="col-md-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Update Purchase Order</h5>
                </div>
               <div class="card-body row">
                
                     <div class="col-md-12 mb-3 row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <label for="po_number" class="input-group-text">PO Number</label>
                                 <input type="text" class="form-control" id="po_number" name="po_number"
                                     value="{{ $requisitionInfo->requisition_number ?? '' }}" readonly>
                            </div>
                            
                        </div>
                         <div class="col-md-6 mb-3 mt-2">
                             <label for="options" class="form-label">Select Supplier</label>
                             <select id="options" name="supp_id" class="form-control" required>
                                 @foreach ($suppliers ?? [] as $supp)
                                     <option value="{{ $supp->id }}"
                                         {{ $supp->id  == $requisitionInfo->supplier_id ? 'selected' : '' }}>
                                         {{ $supp->supp_name ?? '' }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>
                         <div class="col-md-6 mb-3 mt-2">
                             <label for="" class="form-label">Purchase Type</label>
                            <select id="options" name="type_id" class="form-control" required>
                                @foreach ($purchaseTypes ?? [] as $type)
                                    <option value="{{ $type->id }}"
                                        {{ $type->id  == $requisitionInfo->type_id ? 'selected' : '' }}>
                                        {{ $type->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                         </div>
                         <div class="col-md-12">
                            <div class="input-group">
                                <label for="" class="input-group-text">Event</label>
                                <input type="text" class="form-control" value="{{ $requisitionInfo->event->reference ?? 'N/A' }}" readonly disabled id="selected_event_reference">
                                <input type="text" name="event_id" id="selected_event_id" value="{{ $requisitionInfo->event_id ?? '' }}" hidden>
                                <span type="button" class="input-group-text" data-bs-toggle="modal" data-bs-target="#eventModal" style="background-color: rgb(147, 248, 198);"><i class="bi bi-calendar-week"></i></span>
                            </div>
                            <div class="input-group mt-2">
                                <label for="" class="input-group-text">Production</label>
                                <input type="text" class="form-control" value="{{ $requisitionInfo->production->reference ?? 'N/A' }}" readonly disabled id="selected_production_reference">
                                <input type="text" name="production_id" id="selected_production_id" value="{{ $requisitionInfo->production_id ?? '' }}" hidden>
                                <span type="button" class="input-group-text" data-bs-toggle="modal" data-bs-target="#productionModal" style="background-color: rgb(147, 203, 248);"><i class="bi bi-box2"></i></span>
                            </div>
                         </div>
                     </div>
                     <div class="row mb-3 col-md-12">
                         <div class="col-md-6">
                             <label for="contact_no_1" class="form-label">M. PO NUMBER</label>
                             <input type="text" class="form-control" id="merchandise_po_number" name="merchandise_po_number"
                                 value="{{ $requisitionInfo->merchandise_po_number ?? '' }}">
                         </div>
                         <div class="col-md-6">
                             <label for="options" class="form-label">Term</label>
                             <select id="options" name="term_id" class="form-control" required>
                                 @foreach ($terms ?? [] as $term)
                                     <option value="{{ $term->id }}"
                                         {{ $term->id ?? '' == $requisitionInfo->term_id ? 'selected' : '' }}>
                                         {{ $term->term_name ?? '' }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>
                     </div>
                     <div class="row mb-3 col-md-12">
                         <div class="col-md-12">
                             <label for="contact_no_2" class="form-label">Remarks</label>
                             <textarea class="form-control" id="contact2" name="remarks">{{ $requisitionInfo->remarks ?? '' }}</textarea>
                         </div>
                                @if ($hasReviewer)
                                    <div class="col-md-12">
                                        <label for="contact_no_2" class="form-label">Reviewed To</label>
                                        <select id="options" name="reviewer_id" class="form-control">
                                            @forelse ($reviewer ?? [] as $reviewers)
                                                <option value="{{ $reviewers->employees->id }}"
                                                    {{ $reviewers->employees->id == $requisitionInfo->reviewed_by ? 'selected' : '' }}>
                                                    {{ $reviewers->employees->name }}
                                                    {{ $reviewers->employees->last_name }}
                    
                                                </option>
                                            @empty
                                                <option value="">No data available</option>
                                            @endforelse
                                        </select>
                                    </div>
                                @endif
                                 
                                 <div class="col-md-12">
                                     <label for="contact_no_2" class="form-label">Approved To</label>
                                     <select id="options" name="approver_id" class="form-control" required>
                                         @forelse ($approver ?? [] as $approvers)
                                             <option value="{{ $approvers->employees->id }}"
                                                 {{ $approvers->employees->id == $requisitionInfo->approved_by ? 'selected' : '' }}>
                                                 {{ $approvers->employees->name  }}
                                                 {{ $approvers->employees->last_name }}
                                             </option>
                                         @empty
                                             <option value="">No data available</option>
                                         @endforelse
                                     </select>
                                 </div>
                     </div>
               </div>
            </div>
            </div>
        </div>
    </form>
    <!-- Add Item Modal -->
    <div class="modal fade" id="AddItemModal" tabindex="-1" aria-labelledby="AddItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AddItemModalLabel">Select Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >
                    <div class="table-responsive" style="height: 400px; overflow-y: scroll;">
                        <table class="table table-bordered table-hover" >
                            <thead class="table-light sticky-top" style="z-index: 2;">
                                <tr>
                                    <th colspan="7" class="text-center">
                                        <input type="text" class="form-control" id="searchItemInput"
                                            placeholder="Search" onkeyup="filterItems()">
                                    </th>
                                </tr>
                            </thead>
                            <thead class="table-dark sticky-top" style="top: 56px; z-index: 1;">
                                <tr>
                                    <th>ITEM CODE</th>
                                    <th>ITEM DESCRIPTION</th>
                                    <th>UNIT</th>
                                    <th>AVL. QTY.</th>
                                    <th>PRICE</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody id="itemTable">
                                @forelse ($items ?? [] as $item)
                                    <tr>
                                        <td>{{ $item->item_code ?? '' }}</td>
                                        <td>{{ $item->item_description ?? '' }}</td>
                                        <td>{{ $item->uom ? $item->uom->unit_symbol : '' }}</td>
                                        <td>{{ $item->on_hand_qty ?? '' }}</td>
                                        <td>{{ $item->priceLevel()->latest()->where('price_type', 'cost')->first()->amount ?? '' }}</td>
                                        <td>{{ $item->statuses->status_name ?? '' }}</td>
                                        <td><button class="btn btn-primary btn-sm"
                                                onclick="addToTable({{ $item }}, '{{ $item->uom ? $item->uom->unit_symbol : '' }}')">Add</button></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <script>
                    function filterItems() {
                                let input = document.getElementById('searchItemInput');
                                let filter = input.value.toLowerCase();
                                let table = document.querySelector('#itemTable');
                                let rows = table.getElementsByTagName('tr');

                                for (let i = 0; i < rows.length; i++) {
                                    let cells = rows[i].getElementsByTagName('td');
                                    let match = false;

                                    for (let j = 0; j < cells.length; j++) {
                                        if (cells[j]) {
                                            let textValue = cells[j].textContent || cells[j].innerText;
                                            if (textValue.toLowerCase().indexOf(filter) > -1) {
                                                match = true;
                                                break;
                                            }
                                        }
                                    }

                                    rows[i].style.display = match ? '' : 'none';
                                }
                            }
                </script>
            </script>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Purchase order saved successfully!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    An error occurred while saving the purchase order. Please try again.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

     <!-- Event Modal -->
        <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-lg">
                <div class="modal-content card">
                    <div class="modal-header card-header">
                        <h5 class="modal-title" id="eventModalLabel">Select Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="card-body">
                        <input type="text" id="searchEventInput" class="form-control mb-2" placeholder="Search event..." style="font-size: x-small;">
                        <div style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-hover table-sm">
                                <thead class="table-dark sticky-top">
                                    <tr>
                                        <th style="font-size: 12px;">Event Name</th>
                                        <th style="font-size: 12px;">Customer</th>
                                        <th style="font-size: 12px;">Start Date</th>
                                        <th style="font-size: 12px;">End Date</th>
                                        <th style="font-size: 12px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="eventTableBody">
                                    @foreach ($events as $event)
                                        <tr>
                                            <td style="font-size: 12px;">{{ $event->event_name }}</td>
                                            <td style="font-size: 12px;">{{ $event->customer->customer_fname . ' ' . $event->customer->customer_lname }}</td>
                                            <td style="font-size: 12px;">{{ \Carbon\Carbon::parse($event->start_date)->format('M. d, Y') }}</td>
                                            <td style="font-size: 12px;">{{ \Carbon\Carbon::parse($event->end_date)->format('M. d, Y') }}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" onclick="selectEvent({{ $event->id }}, '{{ $event->reference }}')">Select</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Production Order Modal --}}
        <div>
            <div class="modal fade" id="productionModal" tabindex="-1" aria-labelledby="productionModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-lg">
                    <div class="modal-content card">
                        <div class="modal-header card-header">
                            <h5 class="modal-title" id="productionModalLabel">Select Production Order</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="card-body">
                            <input type="text" id="searchProductionInput" class="form-control mb-2" placeholder="Search production order...">
                            <div style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-hover table-sm">
                                    <thead class="table-dark sticky-top">
                                        <tr>
                                            <th style="font-size: 12px;">Reference</th>
                                            <th style="font-size: 12px;">Note</th>
                                            <th style="font-size: 12px;">Order Date</th>
                                            <th style="font-size: 12px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productionTableBody">
                                        @forelse ($productionOrders as $production)
                                            <tr>
                                                <td style="font-size: 12px;">{{ $production->reference }}</td>
                                                <td style="font-size: 12px;">{{ $production->note ?? 'N/A' }}</td>
                                                <td style="font-size: 12px;">{{ $production->created_at->format('M. d, Y') }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="selectProduction({{ $production->id }} , '{{ $production->reference }}')"><i class="bi bi-cursor"></i>&nbsp;Select
                                                        </span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" style="font-size: 12px;">No production orders found.</td>
                                            </tr>
                                        @endforelse
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection
@section('script')
    <script>
        function selectEvent(eventId, eventReference) {
            // Set the selected event ID in a hidden input or directly in the form
            // For example, you can create a hidden input in your form:
            // <input type="hidden" id="selected_event_id" name="event_id" value="">
            document.getElementById('selected_event_id').value = eventId;
            document.getElementById('selected_event_reference').value = eventReference;
            // Close the modal
            var eventModal = bootstrap.Modal.getInstance(document.getElementById('eventModal'));
            eventModal.hide();
        }
        function selectProduction(productionId, productionReference) {
            // Set the selected production ID in a hidden input or directly in the form
            document.getElementById('selected_production_id').value = productionId;
            document.getElementById('selected_production_reference').value = productionReference;
            // Close the modal
            var productionModal = bootstrap.Modal.getInstance(document.getElementById('productionModal'));
            productionModal.hide();
        }
        function addToTable(item,unit) {
            console.log(unit);
            const tableBody = document.getElementById('itemTableBody');
            const existingItem = Array.from(tableBody.querySelectorAll('tr')).find(row => row.querySelector('td')
                .textContent === item.item_code);
            if (existingItem) {
                alert('The item already exists in the table.');
                return;
            }
            const price = item.price_level[0].amount;
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${item.item_code}</td>
                <td>${item.item_description}</td>
                <td>${unit}</td>
                <td><input type="number" name="request_qty[]" class="form-control" value="1" min="1" onchange="updateTotalPrice(this)"><input type="hidden" name="item_id[]" value="${item.id}"></td>
                <td>${price}</td>
                <td class="total-price">${price}</td>
                <td><button class="btn btn-danger btn-sm" onclick="removeRow(this)">Remove</button></td>
            `;
            tableBody.appendChild(newRow);
        }

        function updateTotalPrice(input) {
            const row = input.closest('tr');
            const price = parseFloat(row.querySelector('td:nth-child(5)').textContent);
            const requestQty = parseInt(input.value);
            const totalPriceCell = row.querySelector('.total-price');
            totalPriceCell.textContent = (price * requestQty).toFixed(2);
        }

        function removeRow(button) {
            const row = button.closest('tr');
            row.remove();
        }
        @if (session('status') === 'success')
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        @elseif (session('status') === 'error')
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        @endif
    </script>
@endsection
