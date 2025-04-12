@extends('layouts.master')
@section('content')

    <div>
        @livewire('inventory.withdrawal')
    </div>
    <!-- Add item Modal -->
@endsection


@section('script')
    <script>
       

        function applyFilters() {
            const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
            const searchFilter = document.getElementById('searchItemInput').value.toLowerCase();
            const table = document.getElementById('itemTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                const categoryCell = cells[5]?.textContent.toLowerCase();
                let matchCategory = categoryFilter === '' || categoryCell === categoryFilter;

                let matchSearch = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(searchFilter)) {
                        matchSearch = true;
                        break;
                    }
                }

                rows[i].style.display = matchCategory && matchSearch ? '' : 'none';
            }
        }

        document.getElementById('categoryFilter').addEventListener('change', applyFilters);
        document.getElementById('searchItemInput').addEventListener('keyup', applyFilters);

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('add-item-btn')) {
                const button = event.target;
                const item = JSON.parse(button.getAttribute('data-item'));
                const balanceQty = parseInt(button.getAttribute('data-balance'));
                const availableQty = parseInt(button.getAttribute('data-available'));

                addToTable(item, balanceQty, availableQty);
            }
        });


        function viewBranch(data) {
            console.log(data);
            document.getElementById('branch_id').value = data.id;
        }

        function addToTable(item, balanceQty, availableQty, priceId) {
            const tableBody = document.getElementById('itemTableBody');
            const existingItem = Array.from(tableBody.querySelectorAll('tr')).find(row => row.querySelector('td')
                .textContent === item.item_code);

            if (existingItem) {
                showAlert('The item already exists in the table.');
                return;
            }

            if (availableQty <= 0) {
                showAlert('Cannot add this item because there is no available balance.');
                return;
            }

            const price = item.price_level[0].amount;
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td class="inventory-balance">${balanceQty}</td>
                <td class="available-qty">${availableQty}</td>
                <td>${item.item_code}</td>
                <td>${item.item_description}</td>
                <td>
                    <input type="number" name="request_qty[]" class="form-control" value="1" min="1" max="${balanceQty}" onchange="updateTotalPrice(this, ${balanceQty})">
                    <input type="hidden" name="item_id[]" value="${item.id}">
                    <input type="hidden" name="cost_price[]" value="${priceId}">
                </td>
                <td>${price}</td>
                <td class="total-price">${price}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="removeRow(this)">Remove</button>
                </td>
            `;

            tableBody.appendChild(newRow);
            updateOverallTotal();
        }

        function showAlert(message) {
            const alertContainer = document.getElementById('alertContainer');
            alertContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
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
            updateOverallTotal();
        }

        function removeRow(button) {
            const row = button.closest('tr');
            row.remove();
            updateOverallTotal();
        }

        function updateOverallTotal() {
            const tableBody = document.getElementById('itemTableBody');
            const rows = tableBody.querySelectorAll('tr');
            let totalCost = 0;

            rows.forEach(row => {
                const totalPriceCell = row.querySelector('.total-price');
                totalCost += parseFloat(totalPriceCell.textContent);
            });

            document.getElementById('total_cost').value = totalCost.toFixed(2);
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
    <style>
        /* Ensure the toggle background changes when checked */
        #toggleSwitch2:checked+.block {
            background-color: #4caf50;
        }

        /* Ensure the dot moves when checked */
        #toggleSwitch2:checked+.block+.dot {
            transform: translateX(1.25rem);
        }

        /* Add a smooth transition for the dot */
        .dot {
            transition: transform 0.3s ease;
        }
    </style>
    <div id="alertContainer"></div>
@endsection
