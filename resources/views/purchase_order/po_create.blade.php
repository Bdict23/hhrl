@extends('layouts.master')
@section('content')
    @livewire('purchase-order-create')
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
            const row = input.closest('tr');
            const priceCell = row.querySelector('td:nth-child(5)');
            const totalPriceCell = row.querySelector('.total-price');

            // Ensure price and quantity are parsed correctly
            const price = parseFloat(priceCell.textContent) || 0;
            const requestQty = parseInt(input.value) || 0;

            // Update the total price for the row
            totalPriceCell.textContent = (price * requestQty).toFixed(2);

            // Update the total amount at the footer
            updateTotalAmount();
        }

        function updateTotalAmount() {
            const tableBody = document.getElementById('itemTableBody');
            const totalAmountElement = document.getElementById('totalAmount');

            // Calculate the total amount by summing all row totals
            const totalAmount = Array.from(tableBody.querySelectorAll('.total-price'))
                .reduce((sum, cell) => sum + parseFloat(cell.textContent || 0), 0);

            // Update the total amount in the footer
            totalAmountElement.textContent = totalAmount.toFixed(2);
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
