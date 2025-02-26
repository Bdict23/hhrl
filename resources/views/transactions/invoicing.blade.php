@extends('layouts.master')
@section('content')
    <div>
        <form id="poForm" method="POST" action="{{ route('store.payment') }}">
            @csrf
            <div class="row me-3 w-100">
                <div class=" col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <header>
                                <h1>SALES ORDER</h1>
                                <div class="me-3">
                                    <x-primary-button type="button" data-bs-toggle="modal" data-bs-target="#AddOrderModal">+
                                        Order Number</x-primary-button>
                                    <x-danger-button type="button"> Clear </x-danger-button>
                                    <x-secondary-button onclick="history.back()" type="button"> Back
                                    </x-secondary-button>
                                </div>
                            </header>
                        </div>
                        <div class=" card-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="search" name="search"
                                        placeholder="Search">
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="orderNo">Order</label>
                                        </div>
                                        <div class="col-md-6">

                                            <select id="orderNo" class="form-select">
                                                @forelse ($orders as $order)
                                                    <option value="{{ $order->id }}">{{ $order->order_number }}</option>
                                                @empty
                                                    <option value="">No Orders</option>
                                                @endforelse

                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="me-3" style="height: 320px; overflow-y: auto;">
                                <table class="table table-striped table-hover me-3">
                                    <thead class="thead-dark me-3">
                                        <tr style="font-size: smaller;">
                                            <th>Menu Name</th>
                                            <th>CODE</th>
                                            <th>QTY</th>
                                            <th>PRICE</th>
                                            <th>SUB TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemTableBody">

                                        {{--           POPULATE TABLE     --}}
                                    </tbody>
                                </table>
                            </div>


                            <div class="card-footer row mt-3">
                                <div class="col-md-2">
                                    <label for="customerName" class="form-label">Customer</label>
                                    <label for="table_id" class="form-label">Table</label>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control form-control-sm" id="customerName"
                                        name="customer" readonly>
                                    <input type="text" class="form-control form-control-sm" id="order_id"
                                        name="order_id" hidden>
                                    <input type="text" class="form-control form-control-sm mt-2" id="table_id"
                                        name="table_id" readonly>
                                </div>
                                <div class="col-md-5 alert alert-secondary text-end" role="alert">
                                    <h4 class="alert-heading " id="sumTotal">₱ 0.00</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-5">
                    <div class="card me-3">
                        <div class="card-body">
                            <div class="alert alert-primary" role="alert"
                                style="background-color: #f2f4f7; height: 100%;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="alert-heading" style="font-size: smaller;">Invoice Number</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-end">
                                            <input class="form-control form-control-sm text-center" id="invoiceNumber"
                                                name="invoiceNumber" style="font-size: smaller;"
                                                placeholder="Enter Invoice Number" required>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="discount" class="form-label"
                                            style="font-size: smaller;">Discount</label>
                                        <input class="form-control form-control-sm" id="discount" name="discount"
                                            min="0" onchange="updateGrandTotal()" readonly value="0%" disabled
                                            style="text-align: center;">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="grandTotal" class="form-label" style="font-size: smaller;">Total
                                            Payable</label>
                                        <input type="text" class="form-control form-control-sm text-center"
                                            id="grandTotal" name="grandTotal" readonly value="₱ 0.00" disabled>
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
                                <h5 class="card-title">Payment Details</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="paymentMethod" class="form-label">Payment Method</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select name="paymentMethod" id="paymentMethod" class="form-control text-center"
                                            required>
                                            <option value="CASH">Cash</option>
                                            <option value="CC">Credit Card</option>
                                            <option value="ONLINE">Online</option>
                                            <option value="CHEQUE">Cheque</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-5">
                                        <label for="time" class="form-label">Amount Received</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" id="amountReceived"
                                            name="amountReceived" required>
                                    </div>
                                </div>

                                <div class="row mt-2">

                                    <div class="col-md-3">
                                        <label class="form-label">Change</label>
                                    </div>

                                    <div class="col-md-9 text-center">
                                        <input type="text" class="form-control text-center" id="change"
                                            name="change" readonly value="₱ 0.00" disabled onchange="updateChange()">
                                    </div>
                                </div>

                            </div>
                            <div class="row mt-2 d-flex justify-content-end">
                                <div class="col-md-6">
                                    <div class= "row">

                                        <div class="col-md-8 mt-3">
                                            <x-primary-button type="submit">Save Payment</x-primary-button>
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




    <!-- Add Order Modal -->
    <div class="modal fade" id="AddOrderModal" tabindex="-1" aria-labelledby="AddOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AddOrderModalLabel">Add Order Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body card">
                    <div class="card-header">
                        <h5>Customer List</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">

                            <input type="text" class="form-control" id="newCustomerName" placeholder="Search">
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Order Number</th>
                                    <th>Table</th>
                                    <th>Customer</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td style="text-align: center;">{{ $order->order_number }}</td>
                                        <td style="text-align: center;">{{ $order->tables->table_name }}</td>
                                        <td style="text-align: center;">{{ $order->customer_name ?? 'N/A' }}</td>
                                        @php
                                            $latestSrpPrice = [];
                                            foreach ($order->order_details as $detail) {
                                                $latestSrpPrice[] =
                                                    $detail->menu
                                                        ->price_levels()
                                                        ->latest()
                                                        ->where('price_type', 'SRP')
                                                        ->first()->amount ?? '0.00';
                                            }
                                        @endphp
                                        <td style="text-align: center;"><x-primary-button type="button"
                                                onclick="selectOrder({{ json_encode($order) }}, {{ json_encode($latestSrpPrice) }})">Select</x-primary-button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <x-danger-button data-bs-dismiss="modal">Close</x-d>
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

        function updateChange() {
            const totalAmount = parseFloat(document.getElementById('grandTotal').value) || 0;
            const cashReceived = parseFloat(document.getElementById('cashReceived').value) || 0;
            const change = cashReceived - totalAmount;
            document.getElementById('change').value = change.toFixed(2);
        }


        function addToTable(menu) {
            console.log(menu);
            // Access the table body
            const tableBody = document.getElementById('itemTableBody');

            // Check if the item already exists in the table
            const existingItem = Array.from(tableBody.querySelectorAll('tr')).find(row => row.querySelector('td')
                .textContent === menu.item_code);
            if (existingItem) {
                alert('The item already exists in the table.');
                return;
            }

            // Extract the price from the price_level array
            const price = menu.price_level[0].amount;

            // Create ug new row
            const newRow = document.createElement('tr');

            // Mag Populate sa row with item data
            newRow.innerHTML = `
                <td>${menu.item_code}</td>
                <td>${menu.item_description}</td>
                <td>
                    <input type="number" name="request_qty[]" class="form-control"  value="1" min="1" onchange="updateTotalPrice(this)">
                    <input type="hidden" name="menu_id[]" value="${menu.id}">
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
                $('#AddOrderModal').modal('hide');
            } else {
                alert('Please select a customer.');
            }
        }

        function selectOrder(order, prices) {
            console.log(order);
            if (!order.order_details || !Array.isArray(order.order_details)) {
                console.error('Invalid order details:', order.order_details);
                return;
            }

            // Access the table body
            const tableBody = document.getElementById('itemTableBody');
            tableBody.innerHTML = ''; // Clear existing rows

            let sumTotal = 0;

            // Populate the table with order items
            order.order_details.forEach(menu => {
                const price = prices.shift();
                const subTotal = price * menu.qty;
                sumTotal += subTotal;

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td style="font-size: smaller; ">${menu.menu.menu_name}</td>
                    <td style="font-size: smaller; ">${menu.menu.menu_code}</td>
                    <td style="font-size: smaller; ">
                       ${menu.qty}
                        <input type="hidden" name="menu_id[]" value="${menu.id}">
                    </td>
                    <td style="font-size: smaller; ">${price}</td>
                    <td class="total-price" style="font-size: smaller; ">${subTotal.toFixed(2)}</td>
                `;
                tableBody.appendChild(newRow);
            });

            // Update customer name
            document.getElementById('customerName').value = order.customer_name || 'N/A';

            // Update sum total
            document.getElementById('sumTotal').textContent = `₱ ${sumTotal.toFixed(2)}`;
            document.getElementById('grandTotal').value = sumTotal.toFixed(2);
            document.getElementById('order_id').value = order.id;
            document.getElementById('table_id').value = order.tables.table_name;

            // Close the modal
            $('#AddOrderModal').modal('hide');
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
