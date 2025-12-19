@extends('layouts.master')
@section('content')
    <div>
        @livewire('restaurant.invoicing')
    </div>
@endsection


@section('script')
    {{-- <script>
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
                    <td style="font-size: smaller; ">
                       ${menu.qty}
                        <input type="hidden" name="menu_id[]" value="${menu.id}">
                    </td>
                    <td style="font-size: smaller; ">${price}</td>
                    <td style="font-size: smaller; ">${menu.menu.menu_code}</td>
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
    </script> --}}
@endsection
