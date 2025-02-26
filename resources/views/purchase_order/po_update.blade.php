@extends('layouts.master')
@section('content')
    <div class="dashboard">
        <header>
            <h1>Update Purchase Order</h1>
        </header>
        <form id="poForm" method="POST" action="{{ route('purchase_order.update', $requisitionInfo->id ?? '') }}">
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="options" class="form-label">Select Supplier</label>
                    <select id="options" name="supp_id" class="form-control" required>
                        @foreach ($suppliers ?? [] as $supp)
                            <option value="{{ $supp->id ?? '' }}"
                                {{ $supp->id ?? '' == $requisitionInfo->supplier_id ? 'selected' : '' }}>
                                {{ $supp->supp_name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="po_number" class="form-label">PO Number</label>
                    <input type="text" class="form-control" id="po_number" name="po_number"
                        value="{{ $requisitionInfo->requisition_number ?? '' }}" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="contact_no_1" class="form-label">M. PO NUMBER</label>
                    <input type="text" class="form-control" id="merchandise_po_number" name="merchandise_po_number"
                        value="{{ $requisitionInfo->merchandise_po_number ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label for="options" class="form-label">TYPE</label>
                    <select id="options" name="type_id" class="form-control" required>
                        @foreach ($types ?? [] as $type)
                            <option value="{{ $type->id }}"
                                {{ $type->id ?? '' == $requisitionInfo->requisition_types_id ? 'selected' : '' }}>
                                {{ $type->type_name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="contact_no_2" class="form-label">Remarks</label>
                    <input type="text" class="form-control" id="contact2" name="remarks"
                        value="{{ $requisitionInfo->remarks ?? '' }}">
                </div>
                <div class="col-md-6">
                    <div class="row mv-20">
                        <div class="col-md-6">
                            <label for="contact_no_2" class="form-label">Reviewed To</label>
                            <select id="options" name="reviewer_id" class="form-control" required>
                                @forelse ($reviewer ?? [] as $reviewers)
                                    <option value="{{ $reviewers->employees->id }}"
                                        {{ $reviewers->employees->id ?? '' == $requisitionInfo->reviewer->id ? 'selected' : '' }}>
                                        {{ $reviewers->employees->name ?? '' }}
                                        {{ $reviewers->employees->last_name ?? '' }}

                                    </option>
                                @empty
                                    <option value="">No data available</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="contact_no_2" class="form-label">Approved To</label>
                            <select id="options" name="approver_id" class="form-control" required>
                                @forelse ($approver ?? [] as $approvers)
                                    <option value="{{ $approvers->employees->id }}"
                                        {{ $approvers->employees->id ?? '' == $requisitionInfo->approved_by ? 'selected' : '' }}>
                                        {{ $approvers->employees->name ?? '' }}
                                        {{ $approvers->employees->last_name ?? '' }}
                                    </option>
                                @empty
                                    <option value="">No data available</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <header>
                <h1>Item Lists</h1>
                <div>
                    <button
                        class="btn {{ $requisitionInfo && $requisitionInfo->requisition_status == 'PREPARING' ? 'btn-success' : 'btn-secondary' }}"
                        type="button" data-bs-toggle="modal" data-bs-target="#AddItemModal"
                        {{ $requisitionInfo && $requisitionInfo->requisition_status != 'PREPARING' ? 'disabled' : '' }}>+
                        Add ITEM</button>
                    <button
                        class="btn {{ $requisitionInfo->requisition_status ?? '' == 'PREPARING' ? 'btn-primary' : 'btn-secondary' }}"
                        type="button" onclick="document.getElementById('poForm').submit();"
                        {{ $requisitionInfo && $requisitionInfo->requisition_status != 'PREPARING' ? 'disabled' : '' }}>Update</button>
                    <button onclick="history.back()" class="btn btn-primary" type="button">Back</button>
                </div>
            </header>
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
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
                            <td><input type="number" name="request_qty[]" class="form-control"
                                    value="{{ $reqdetail->qty ?? '' }}" min="1" onchange="updateTotalPrice(this)"
                                    {{ $requisitionInfo && $requisitionInfo->requisition_status != 'PREPARING' ? 'disabled' : '' }}>
                                <input type="hidden" name="item_id[]" value="{{ $reqdetail->items->id ?? '' }}">
                            </td>
                            <td>{{ $reqdetail->items->priceLevel()->latest()->where('price_type', 'cost')->first()->amount ?? '' }}
                            </td>
                            <td class="total-price">{{ $reqdetail->total ?? '' }}</td>
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
        </form>
    </div>
    <!-- Add Item Modal -->
    <div class="modal fade" id="AddItemModal" tabindex="-1" aria-labelledby="AddItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AddItemModalLabel">Select Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ITEM CODE</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>AVAILABLE QTY.</th>
                                <th>PRICE</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items ?? [] as $item)
                                <tr>
                                    <td>{{ $item->item_code ?? '' }}</td>
                                    <td>{{ $item->item_description ?? '' }}</td>
                                    <td>{{ $item->on_hand_qty ?? '' }}</td>
                                    <td>{{ $item->priceLevel()->latest()->where('price_type', 'cost')->first()->amount ?? '' }}
                                    </td>
                                    <td>{{ $item->statuses->status_name ?? '' }}</td>
                                    <td><button class="btn btn-primary btn-sm"
                                            onclick="addToTable({{ $item }})">Add</button></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
@endsection
@section('script')
    <script>
        function addToTable(item) {
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
                <td><input type="number" name="request_qty[]" class="form-control" value="1" min="1" onchange="updateTotalPrice(this)"><input type="hidden" name="item_id[]" value="${item.id}"></td>
                <td>${price}</td>
                <td class="total-price">${price}</td>
                <td><button class="btn btn-danger btn-sm" onclick="removeRow(this)">Remove</button></td>
            `;
            tableBody.appendChild(newRow);
        }

        function updateTotalPrice(input) {
            const row = input.closest('tr');
            const price = parseFloat(row.querySelector('td:nth-child(4)').textContent);
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
