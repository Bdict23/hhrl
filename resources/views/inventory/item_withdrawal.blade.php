@extends('layouts.master')
@section('content')
    <div>
        <form {{-- id="poForm" method="POST" action="{{ route('purchase_order.store') }}" --}}>
            @csrf


            <div class="row me-3 w-100">
                <div class=" col-md-8 card">
                    <div class=" card-body">
                        <header>
                            <h1> Item Withdawal</h1>
                            <div class="me-3">
                                <x-primary-button type="button" data-bs-toggle="modal" data-bs-target="#AddItemModal">+
                                    Add
                                    ITEM</x-primary-button>
                                <x-secondary-button onclick="history.back()"> Back
                                </x-secondary-button>
                            </div>
                        </header>
                        <div class="row me-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="customerName" name="customerName"
                                    placeholder="Enter Item Code">
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" id="address" name="address" placeholder="QTY">
                            </div>
                        </div>
                        <table class="table table-striped table-hover me-3">
                            <thead class="thead-dark me-3">
                                <tr style="font-size: smaller;">
                                    <th>INV. BAL.</th>
                                    <th>AVL. QTY</th>
                                    <th>ITEM CODE</th>
                                    <th>DESCRIPTION</th>
                                    <th>REQ. QTY</th>
                                    <th>COST</th>
                                    <th>TOTAL</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemTableBody">

                                {{--           POPULATE TABLE     --}}

                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="customerName"
                                            class="form-label
                                        ">Total Cost</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" id="customerName" name="customerName">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="alert" style="background-color: #f2f4f7;" role="alert">
                                <h5 class="card-title">Information</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="customerName" class="form-label">Ref. Number</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" id="customerName" name="customerName">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="supp_name" class="form-label"
                                            style="width: 100; font-size: 13px">Department</label>
                                        <select id="department" class="form-select" aria-label="Default select example"
                                            style="width: 100; font-size: 13px">
                                            <option selected>Kitchen Dept.</option>
                                            <option>Cleaning Dept.</option>
                                            <option>Security Dept.</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="usage_date" class="form-label" style="width: 100; font-size: 13px">Usage
                                            Date</label>
                                        <input type="date" class="form-control" id="usage_date" name="usage_date">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="postal_address" class="form-label"
                                            style="width: 100; font-size: 13px">Type</label>
                                        <select id="type" class="form-select" aria-label="Default select example"
                                            style="width: 100; font-size: 13px" onchange="toggleLifespanInput()">
                                            <option selected>Fixed Asset</option>
                                            <option>Raw Materials</option>
                                            <option>Consumables</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6" id="lifespanContainer" style="display: none;">
                                        <label for="lifespan_date" class="form-label"
                                            style="width: 100; font-size: 13px">Lifespan Date</label>
                                        <input type="date" class="form-control" id="lifespan_date" name="lifespan_date"
                                            onchange="calculateLifespan()">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <label for="time" class="form-label" style="font-size: 13px;">Remarks</label>
                                    </div>
                                    <div class="col-md-9">
                                        <textarea type="text" class="form-control" id="remarks" name="remarks"></textarea>
                                    </div>
                                </div>
                                <div class="row mt-2">

                                    <hr class="col-md-6 mt-3">

                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                                            data-bs-target="#AddAccountModal">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>

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
                                <th>INVENTORY BALANCE</th>
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
                                    <td>{{ $cardexBalance[$item->id] ?? 0 }}</td>
                                    <td>{{ $cardexAvailable[$item->id] ?? 0 }}</td>
                                    <td>{{ $item->priceLevel()->latest()->where('price_type', 'cost')->first()->amount }}
                                    </td>
                                    <td>{{ $item->statuses->status_name }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm"
                                            onclick="addToTable({{ json_encode($item) }}, {{ $cardexBalance[$item->id] ?? 0 }}, {{ $cardexAvailable[$item->id] ?? 0 }})">
                                            Add </button>
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

    <!-- Add Account Modal -->
@endsection


@section('script')
    <script>
        function viewBranch(data) {
            console.log(data);
            document.getElementById('branch_id').value = data.id;
        }

        function addToTable(item, balanceQty, availableQty) {
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
                <td class="inventory-balance">${balanceQty}</td>
                <td class="available-qty">${availableQty}</td>
                <td>${item.item_code}</td>
                <td>${item.item_description}</td>
                <td>
                    <input type="number" name="request_qty[]" class="form-control" value="1" min="1" max="${balanceQty}" onchange="updateTotalPrice(this, ${balanceQty})">
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

        function updateTotalPrice(input, balanceQty) {
            console.log(input);
            const row = input.closest('tr');
            const price = parseFloat(row.querySelector('td:nth-child(6)').textContent);
            const requestQty = parseInt(input.value);
            const totalPriceCell = row.querySelector('.total-price');
            const availableQtyCell = row.querySelector('.available-qty');
            totalPriceCell.textContent = (price * requestQty).toFixed(2);
            availableQtyCell.textContent = balanceQty - requestQty;
            // console.log(totalPriceCell);
        }

        function removeRow(button) {
            // Find the row to remove
            const row = button.closest('tr');
            // Remove the row from the table
            row.remove();
        }

        function addCustomer() {
            const selectedCustomer = document.querySelector('input[name="selected_customer"]:checked');
            if (selectedCustomer) {
                const customerName = selectedCustomer.closest('tr').querySelector('td:first-child').textContent;
                document.getElementById('customerName').value = customerName;
                $('#AddAccountModal').modal('hide');
            } else {
                alert('Please select a customer.');
            }
        }

        // Show success or error modal based on the session status
        @if (session('status') === 'success')
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        @elseif (session('status') === 'error')
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        @endif

        function toggleLifespanInput() {
            const typeSelect = document.getElementById('type');
            const lifespanContainer = document.getElementById('lifespanContainer');
            if (typeSelect.value === 'Fixed Asset') {
                lifespanContainer.style.display = 'block';
            } else {
                lifespanContainer.style.display = 'none';
            }
        }

        function calculateLifespan() {
            const usageDate = new Date(document.getElementById('usage_date').value);
            const lifespanDate = new Date(document.getElementById('lifespan_date').value);
            const remarksField = document.getElementById('remarks');

            if (usageDate && lifespanDate) {
                let diffYears = lifespanDate.getFullYear() - usageDate.getFullYear();
                let diffMonths = lifespanDate.getMonth() - usageDate.getMonth();
                let diffDays = lifespanDate.getDate() - usageDate.getDate();

                if (diffDays < 0) {
                    diffMonths--;
                    diffDays += new Date(lifespanDate.getFullYear(), lifespanDate.getMonth(), 0).getDate();
                }

                if (diffMonths < 0) {
                    diffYears--;
                    diffMonths += 12;
                }

                const totalDiffDays = Math.ceil((lifespanDate - usageDate) / (1000 * 60 * 60 * 24));

                remarksField.value =
                    `Lifespan:\n${diffYears}  year(s) and ${diffMonths} month(s) or the total of (${totalDiffDays} days)`;
            }
        }
    </script>
@endsection
