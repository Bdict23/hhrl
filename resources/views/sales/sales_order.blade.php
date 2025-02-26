@extends('layouts.master')
@section('content')
    <div>
        <form {{-- id="poForm" method="POST" action="{{ route('purchase_order.store') }}" --}}>
            @csrf


            <div class="row me-3 w-100">
                <div class=" col-md-8 card">
                    <div class=" card-body">
                        <header>
                            <h1>SALES ORDER</h1>
                            <div class="me-3">
                                <button class="btn btn-success" type="button" data-bs-toggle="modal"
                                    data-bs-target="#AddItemModal">+
                                    Add
                                    ITEM</button>
                                <button class="btn btn-secondary" type="button"
                                    onclick="document.getElementById('poForm').submit();">
                                    Save
                                </button>
                                <button onclick="history.back()" class="btn btn-primary" type="button"> Back </button>
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
                                    <th>AVL.</th>
                                    <th>CODE</th>
                                    <th>DESCRIPTION</th>
                                    <th>DISCOUNT</th>
                                    <th>QTY</th>
                                    <th>PRICE</th>
                                    <th>SUB TOTAL</th>
                                    <th>TOTAL</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemTableBody">

                                {{--           POPULATE TABLE     --}}

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card me-3">
                        <div class="card-body">
                            <div class="alert alert-primary" role="alert">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="alert-heading">Grand Total</h4>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-end">
                                            <h4 class="alert-heading" id="totalAmount">₱ 0.00</h4>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="discount" class="form-label">Discount</label>
                                        <input class="form-control" id="discount" name="discount" min="0"
                                            onchange="updateGrandTotal()" readonly value="0%" disabled
                                            style="text-align: center;">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="grandTotal" class="form-label">Total</label>
                                        <input type="text" class="form-control" id="grandTotal" name="grandTotal"
                                            readonly value="₱ 0.00" disabled>
                                    </div>
                                </div>
                                <script>
                                    function updateGrandTotal() {
                                        const discount = parseFloat(document.getElementById('discount').value) || 0;
                                        let grandTotal = 0;
                                        document.querySelectorAll('.total-price').forEach(cell => {
                                            grandTotal += parseFloat(cell.textContent);
                                        });
                                        grandTotal -= discount;
                                        document.getElementById('grandTotal').value = grandTotal.toFixed(2);
                                    }
                                </script>

                            </div>


                            <div class="alert" style="background-color: #f2f4f7;" role="alert">
                                <h5 class="card-title">Information</h5>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="customerName" class="form-label">Name</label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="customerName" name="customerName">
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <label for="time" class="form-label">Address</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="address" name="address">
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <label class="form-label">Representative</label>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="orderNo">Order</label>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-9">
                                            <select id="customer" class="form-select" aria-label="Default select example">
                                                <option selected>BENEDICT</option>
                                                <option value="1">PARTSMAN</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">

                                            <select id="orderNo" name="orderNo" class="form-select">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>

                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                                            data-bs-target="#AddAccountModal">Add Account</button>
                                    </div>
                                    <div class="col-md-6">
                                        <div class= "row">
                                            <div class="col-md-3 mt-3">
                                                <label for="orderNo" class="form-label mt-1">Type</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control mt-3" id="custType"
                                                    name="CustomerType" readonly value="RETAIL" disabled>
                                            </div>

                                        </div>
                                    </div>
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
    {{-- <div class="modal fade" id="AddItemModal" tabindex="-1" aria-labelledby="AddItemModalLabel" aria-hidden="true">
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
                                    <td>{{ $item->on_hand_qty }}</td>
                                    </td>
                                    <td>{{ $item->priceLevel()->latest()->where('price_type', 'cost')->first()->amount }}
                                    </td>
                                    <td>{{ $item->statuses->status_name }}</td>
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
    </div> --}}

    <!-- Add Account Modal -->
    <div class="modal fade" id="AddAccountModal" tabindex="-1" aria-labelledby="AddAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AddAccountModalLabel">Add Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="newCustomerName" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="newCustomerName"
                            placeholder="Enter Customer Name">
                    </div>
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Select</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>BENEDICT LU</td>
                                <td>WHOLESALE</td>
                                <td>
                                    <div style="display: flex; justify-content: center" class="mt-1">
                                        <input type="checkbox">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>BENEDICT LU</td>
                                <td>KEY ACCOUNT</td>
                                <td>
                                    <div style="display: flex; justify-content: center" class="mt-1">
                                        <input type="checkbox">
                                    </div>
                                </td>
                            </tr>
                            {{-- @foreach ($customers as $customer)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->type }}</td>
                                    <td>
                                        <input type="checkbox" name="selected_customer" value="{{ $customer->id }}">
                                    </td>
                                </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addCustomer()">Add</button>
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
    </script>
@endsection
