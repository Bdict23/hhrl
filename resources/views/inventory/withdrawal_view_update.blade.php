@extends('layouts.master')
@section('content')
    <div>
        <form id="withdrawalForm" method="POST" action="{{ route('withdrawal.store') }}">
            @csrf
            <div class="row me-3 w-100">
                <div class=" col-md-8 card">
                    <div class=" card-body">
                        <header>
                            <h1> Item Withdawal</h1>
                            <div class="me-3">
                                <button class="btn btn-success btn-sm" type="button" data-bs-toggle="modal"
                                    data-bs-target="#AddItemModal"
                                    {{ $withdrawal->withdrawal_status != 'PREPARING' ? 'hidden' : '' }}>
                                    + Add ITEM</button>
                                </button>
                                <a type="button" class="btn btn-info btn-sm"
                                    href="{{ route('withdrawal.print', $withdrawal->id) }}"
                                    {{ $withdrawal->withdrawal_status != 'PREPARING' ? '' : 'hidden' }}>
                                    &nbsp;Print&nbsp;
                                </a>
                                <button type="button" class="btn btn-secondary btn-sm"
                                    onclick="window.location.href='{{ route('withdrawal.summary') }}'"> Summary
                                </button>
                                <x-secondary-button onclick="history.back()"> Back
                                </x-secondary-button>
                            </div>
                        </header>
                        <div class="row me-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Enter Item Code">
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" placeholder="QTY">
                            </div>
                        </div>
                        <table class="table table-striped table-hover me-3">
                            <thead class="thead-dark me-3">
                                <tr style="font-size: smaller;">
                                    <th {{ $withdrawal->withdrawal_status != 'PREPARING' ? 'hidden' : '' }}>INV. BAL.</th>
                                    <th {{ $withdrawal->withdrawal_status != 'PREPARING' ? 'hidden' : '' }}>AVL. QTY</th>
                                    <th>ITEM CODE</th>
                                    <th>DESCRIPTION</th>
                                    <th>REQ. QTY</th>
                                    <th>COST</th>
                                    <th>TOTAL</th>
                                    <th {{ $withdrawal->withdrawal_status != 'PREPARING' ? 'hidden' : '' }}>Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemTableBody">
                                @forelse ($withdrawal->cardex as $cardex)
                                    <tr>
                                        <td {{ $withdrawal->withdrawal_status != 'PREPARING' ? 'hidden' : '' }}>
                                            {{ $cardex->inventory_balance }}</td>
                                        <td {{ $withdrawal->withdrawal_status != 'PREPARING' ? 'hidden' : '' }}>
                                            {{ $cardex->on_hand_qty }}</td>
                                        <td>{{ $cardex->item->item_code }}</td>
                                        <td>{{ $cardex->item->item_description }}</td>
                                        <td>{{ $cardex->qty_out }}</td>
                                        <td>{{ $cardex->priceLevel->amount }}</td>
                                        <td>{{ $cardex->priceLevel->amount * $cardex->qty_out }}</td>
                                        <td {{ $withdrawal->withdrawal_status != 'PREPARING' ? 'hidden' : '' }}>
                                            <button class="btn btn-danger btn-sm" onclick="removeRow(this)"
                                                {{ $withdrawal->withdrawal_status != 'PREPARING' ? 'disabled' : '' }}>Remove</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">No data available</td>
                                    </tr>
                                @endforelse
                                {{--           POPULATE TABLE   --}}

                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="total_cost" class="form-label">Total Cost</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" id="total_cost" name="total_cost" class="form-control">
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
                                        <label for="reference_number" class="form-label">Ref. Number</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" id="reference_number"
                                            name="reference_number" readonly value="{{ $withdrawal->reference_number }}"
                                            disabled>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="deptartment" class="form-label"
                                            style="width: 100; font-size: 13px">Department</label>
                                        <select id="department" name="department_id" class="form-select"
                                            aria-label="Default select example" style="width: 100; font-size: 13px">
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}"
                                                    {{ $department->id == $withdrawal->department_id ? 'selected' : '' }}>
                                                    {{ $department->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="usage_date" class="form-label" style="width: 100; font-size: 13px">To be
                                            use on</label>
                                        <input type="date" class="form-control" id="usage_date" name="usage_date"
                                            required value="{{ $withdrawal->usage_date }}">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="col-md-6">
                                            <label for="toggleSwitch" class="flex items-center cursor-pointer">
                                                <div class="relative">
                                                    <input type="checkbox" id="toggleSwitch" class="sr-only"
                                                        onchange="toggleLifespanInput()"
                                                        {{ $withdrawal->useful_date ? 'checked' : '' }}>

                                                    <div class="block bg-gray-300 w-10 h-6 rounded-full"></div>
                                                    <div
                                                        class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform">
                                                    </div>
                                                    <style>
                                                        #toggleSwitch:checked+.block {
                                                            background-color: #4caf50;
                                                        }

                                                        #toggleSwitch:checked+.block+.dot {
                                                            transform: translateX(1.25rem);
                                                        }
                                                    </style>
                                                </div>
                                                <span class="ml-3 text-gray-700 text-sm">Useful Date</span>
                                            </label>
                                        </div>
                                        <div class="col-md-6 mt-4">
                                            <label for="toggleSwitch2" class="flex items-center cursor-pointer">
                                                <div class="relative">
                                                    <input type="checkbox" id="toggleSwitch2" class="sr-only"
                                                        {{ $withdrawal->withdrawal_status != 'PREPARING' ? 'disabled' : '' }}
                                                        {{ $withdrawal->withdrawal_status != 'PREPARING' ? 'checked' : '' }}>


                                                    <div class="block bg-gray-300 w-10 h-6 rounded-full"></div>
                                                    <div
                                                        class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform">
                                                    </div>
                                                </div>
                                                <span class="ml-3 text-gray-700 text-sm">Final</span>
                                            </label>
                                            <input type="text" id="finalStatus" name="finalStatus"
                                                value="{{ $withdrawal->withdrawal_status != 'PREPARING' ? 'YES' : 'NO' }}"
                                                hidden>
                                        </div>

                                    </div>

                                    <div class="col-md-6">
                                        <div id="lifespanContainer"
                                            {{ $withdrawal->useful_date ? '' : 'style=display:none' }}>
                                            <label for="lifespan_date" class="form-label"
                                                style="width: 100; font-size: 13px">Lifespan Date</label>
                                            <input type="date" class="form-control" id="lifespan_date"
                                                name="lifespan_date" onchange="calculateLifespan()"
                                                value="{{ $withdrawal->useful_date }}">
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <label for="remarks" class="form-label" style="font-size: 13px;">Remarks</label>
                                        <textarea type="text" class="form-control" id="remarks" name="remarks" style="font-size: 13px; height: 100px">{{ $withdrawal->remarks }}</textarea>
                                    </div>

                                    <div class="row mt-1">
                                        <div class="col-md-6">
                                            <label for="reviewed_to" class="form-label" style="font-size: 13px;">Reviewed
                                                To</label>
                                            <select name="reviewed_to" id="reviewed_to" class="form-select"
                                                aria-label="Default select example">
                                                @if ($reviewers->isEmpty())
                                                    <option style="font-size: 10px">No Reviewer Found</option>
                                                @else
                                                    @foreach ($reviewers as $reviewer)
                                                        <option value="{{ $reviewer->employees->id }}"
                                                            {{ $reviewer->employees->id == $withdrawal->reviewed_to ? 'selected' : '' }}>
                                                            {{ $reviewer->employees->name }}
                                                            {{ $reviewer->employees->last_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="approved_to" class="form-label" style="font-size: 13px;">Approved
                                                To</label>
                                            <select name="approved_to" id="approved_to" class="form-select"
                                                aria-label="Default select example">
                                                @if ($reviewers->isEmpty())
                                                    <option style="font-size: 10px">No Reviewer Found</option>
                                                @else
                                                    @foreach ($approvers as $approver)
                                                        <option value="{{ $approver->employees->id }}"
                                                            {{ $approver->employees->id == $withdrawal->approved_to ? 'selected' : '' }}>
                                                            {{ $approver->employees->name }}
                                                            {{ $approver->employees->last_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                    </div>
                                    <div>
                                        <x-primary-button type="submit" class=" mt-3" data-bs-toggle="modal"
                                            data-bs-target="#AddAccountModal">Save</x-primary-button>
                                        <x-danger-button>Reset</x-danger-button>
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
                    <div class="d-flex justify-content-between mb-3">
                        <select id="categoryFilter" class="form-select w-25" onchange="applyFilters()">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->category_name }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                        <input type="text" id="searchItemInput" class="form-control w-25"
                            placeholder="Search items..." onkeyup="applyFilters()">
                    </div>
                    <!-- Table for Item Selection -->
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ITEM CODE</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>INVENTORY BALANCE</th>
                                <th>AVAILABLE QTY.</th>
                                <th>COST PRICE</th>
                                <th>CATEGORY</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="itemTable">
                            @foreach ($items as $item)
                                @php
                                    $priceLevel = $item->priceLevel()->latest()->where('price_type', 'cost')->first();
                                    $priceAmount = $priceLevel ? $priceLevel->amount : 0.0;
                                    $priceId = $priceLevel ? $priceLevel->id : 0;
                                @endphp
                                <tr>
                                    <td>{{ $item->item_code }}</td>
                                    <td>{{ $item->item_description }}</td>
                                    <td>{{ $cardexBalance[$item->id] ?? 0 }}</td>
                                    <td>{{ $cardexAvailable[$item->id] ?? 0 }}</td>
                                    <td>{{ $priceAmount }}</td>
                                    <td>{{ $item->category->category_name }}</td>
                                    <td>{{ $item->item_status ? 'Active' : 'Inactive' }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm"
                                            onclick="addToTable({{ json_encode($item) }}, {{ $cardexBalance[$item->id] ?? 0 }}, {{ $cardexAvailable[$item->id] ?? 0 }} , {{ $priceId }})">
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

    <!-- Add item Modal -->
@endsection


@section('script')
    <script>
        document.getElementById('toggleSwitch').addEventListener('change', function() {
            const lifespanContainer = document.getElementById('lifespanContainer');
            lifespanContainer.style.display = this.checked ? 'block' : 'none';
        });


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
            // Access the table body
            const tableBody = document.getElementById('itemTableBody');

            // Check if the item already exists in the table
            const existingItem = Array.from(tableBody.querySelectorAll('tr')).find(row => row.querySelector('td')
                .textContent === item.item_code);
            if (existingItem) {
                showAlert('The item already exists in the table.');
                return;
            }

            // Check if available quantity is zero
            if (availableQty <= 0) {
                showAlert('Cannot add this item because there is no available balance.');
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
                    <input type="hidden" name="cost_price[]" value="${priceId}">
                </td>
                <td>${price}</td>
                <td class="total-price">${price}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="removeRow(this)">Remove</button>
                </td>
            `;

            // Append the row to the table
            tableBody.appendChild(newRow);
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

        // Handle the "Final" toggle switch
        document.getElementById('toggleSwitch2').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('finalStatus').value = 'YES';
            } else {
                document.getElementById('finalStatus').value = 'NO';
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Ensure the DOM is fully loaded before attaching the event listener
            const finalToggle = document.getElementById('toggleSwitch2');

            if (finalToggle) {
                console.log('Final toggle element found:', finalToggle); // Debugging log
                finalToggle.addEventListener('change', function() {
                    if (this.checked) {
                        console.log('Final toggle is ON');
                        // Add any additional logic here if needed
                    } else {
                        console.log('Final toggle is OFF');
                        // Add any additional logic here if needed
                    }
                });
            } else {
                console.error('Final toggle (#toggleSwitch2) not found in the DOM.');
            }
        });
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
