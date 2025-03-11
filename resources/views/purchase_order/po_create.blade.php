@extends('layouts.master')
@section('content')
    <div class="card mt-3 mb-3 p-3">
        <header>
            <h1>Purchase Order Form</h1>
        </header>
        <form id="poForm" method="POST" action="{{ route('purchase_order.store') }}">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">

                    <label for="options" class="form-label">Select Supplier</label>
                    <select id="options" name="supp_id" class="form-control" required onchange="fetchcompany(this.value)">
                        @foreach ($suppliers as $supp)
                            <option value="{{ $supp->id }}">
                                {{ $supp->supp_name }}
                            </option>
                        @endforeach
                    </select>

                </div>
                <div class="col-md-6">
                    <label for="po_number" class="form-label">PO Number</label>
                    <input type="text" class="form-control" id="po_number" name="po_number" value = "<AUTO>" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="contact_no_1" class="form-label">M. PO NUMBER</label>
                    <input type="text" class="form-control" id="merchandise_po_number" name="merchandise_po_number">
                </div>
                <div class="col-md-6">
                    <label for="options" class="form-label">TYPE</label>
                    <select id="options" name="type_id" class="form-control" required onchange="fetchcompany(this.value)">
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}">
                                {{ $type->type_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="contact_no_2" class="form-label">Remarks</label>

                    <input type="text" class="form-control" id="contact2" name="remarks">
                </div>
                <div class="col-md-6">
                    <div class="row mv-20">


                        <div class="col-md-6">
                            <label for="contact_no_2" class="form-label">Reviewed To</label>

                            <select id="options" name="reviewer_id" class="form-control" required>
                                @foreach ($reviewer as $reviewers)
                                    <option value="{{ $reviewers->employees->id }}">
                                        {{ $reviewers->employees->name }} {{ $reviewers->employees->last_name }}
                                    </option>
                                @endforeach
                            </select>


                        </div>

                        <div class="col-md-6">
                            <label for="contact_no_2" class="form-label">Approved To</label>
                            <select id="options" name="approver_id" class="form-control" required
                                onchange="fetchcompany(this.value)">
                                @foreach ($approver as $approvers)
                                    <option value="{{ $approvers->employees->id }}">
                                        {{ $approvers->employees->name }} {{ $approvers->employees->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                    </div>
                </div>

            </div>


            <header>
                <h1>Item Lists</h1>
                <div>
                    <x-primary-button type="button" data-bs-toggle="modal" data-bs-target="#AddItemModal">+
                        Add
                        ITEM</x-primary-button>
                    <x-primary-button type="button" onclick="document.getElementById('poForm').submit();">
                        Save
                    </x-primary-button>
                    <x-secondary-button onclick="history.back()" type="button"> Back </x-secondary-button>
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

                    {{--           POPULATE TABLE     --}}

                </tbody>
            </table>
        </form>

    </div>
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
                    <!-- Table for Item Selection -->
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
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->item_code }}</td>
                                    <td>{{ $item->item_description }}</td>
                                    <td>{{ $item->on_hand_qty }} 0</td>
                                    </td>
                                    <td>{{ $item->priceLevel()->latest()->where('price_type', 'cost')->first()->amount ?? 0.0 }}
                                    </td>
                                    <td>{{ $item->item_status ? 'Active' : 'Inactive' }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="addToTable({{ $item }})">
                                            Add
                                        </button>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
        function viewBranch(data) {
            console.log(data);
            document.getElementById('branch_id').value = data.id;
        }

        function addToTable(item) {
            console.log(item);
            // Access the table body
            const tableBody = document.getElementById('itemTableBody');

            // Check if the item already exists in the table
            const existingItem = Array.from(tableBody.querySelectorAll('tr')).find(row => row.querySelector('td')
                .textContent === item.item_code);
            if (existingItem) {
                alert('The item already exists in the table.');
                return;
            }

            // Extract the price from the price_level array
            const price = item.price_level[0].amount;

            // Create a new row
            const newRow = document.createElement('tr');

            // Populate the row with item data
            newRow.innerHTML = `
                <td>${item.item_code}</td>
                <td>${item.item_description}</td>
                <td>
                    <input type="number" name="request_qty[]" class="form-control"  value="1" min="1" onchange="updateTotalPrice(this)">
                    <input type="hidden" name="item_id[]" value="${item.id}">
                </td>
                <td>${price}</td>
                <td class="total-price">${price}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="removeRow(this)">Remove</button>
                </td>
            `;

            // Append the row to the table
            tableBody.appendChild(newRow);

            // Close the modal
            //$('#AddItemModal').modal('hide');
        }

        function updateTotalPrice(input) {
            console.log(input);
            const row = input.closest('tr');
            const price = parseFloat(row.querySelector('td:nth-child(4)').textContent);
            const requestQty = parseInt(input.value);
            const totalPriceCell = row.querySelector('.total-price');
            totalPriceCell.textContent = (price * requestQty).toFixed(2);
            // console.log(totalPriceCell);
        }

        function removeRow(button) {
            // Find the row to remove
            const row = button.closest('tr');
            // Remove the row from the table
            row.remove();
        }

        // Show success or error modal based on the session status
        @if (session('status') === 'success')
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        @elseif (session('status') === 'error')
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        @endif
    </script>
@endsection
