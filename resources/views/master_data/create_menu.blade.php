@extends('layouts.master')
@section('content')
    <div>
        <form id="poForm" method="POST" action="{{ route('menu.store') }}" enctype="multipart/form-data">
            @csrf


            <div class="row me-3 w-100">
                <div class=" col-md-8 card">
                    <div class=" card-body">
                        <header>
                            <h4>Create New Recipe</h4>
                            <div class="me-3">
                                <button class="btn btn-success" type="button" data-bs-toggle="modal"
                                    data-bs-target="#AddItemModal">+
                                    Add Ingredients</button>
                                <a class="btn btn-info" type="button" href="/recipe-lists">Recipe Summary</a>
                                <button onclick="history.back()" class="btn btn-primary" type="button"> Back </button>
                            </div>
                        </header>
                        <div class="row me-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="search" name="search"
                                    placeholder="Search">
                            </div>
                        </div>
                        <div style="max-height: 400px; overflow-y: auto;" class="mt-4">
                            <table class="table table-striped table-hover me-3">
                                <thead class="thead-dark me-3">
                                    <TR style="font-size: smaller;">
                                        <th class="text-xs">CODE</th>
                                        <th class="text-xs">DESCRIPTION</th>
                                        <th class="text-xs">QTY</th>
                                        <th class="text-xs">MEASURE</th>
                                        <th class="text-xs">COST</th>
                                        <th class="text-xs">TOTAL COST</th>
                                        <th class="text-xs">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemTableBody">

                                    {{--           POPULATE TABLE     --}}

                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <hr>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end">
                                    <h4 class="alert-heading mr-12">Overall Cost</h4>
                                    <h4 class="alert-heading" id="totalAmount">₱ 0.00</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="container">
                        <form>
                            @csrf
                            <div class="form-group">
                                <img id="imagePreview" src="{{ asset('images/sample_image.jpg') }}" alt="Image Preview"
                                    style="width: 90%; height: 120px; object-fit: cover;" name="image">
                            </div>
                            <div class="form-group mt-1">
                                <label for="recipe_name" style="font-size: 13px;">Recipe Name:</label>
                                <input type="text" class="form-control" id="recipe_name" name="menu_name" required>
                            </div>
                            <div class="form-group mt-1">
                                <label for="recipe_type" style="font-size: 13px;">Type</label>
                                <select name="menu_type" id="recipe_type" class="form-select" aria-label="Default select example">
                                    <option value="APPETIZER">Ala Carte</option>
                                    <option value="MAIN_COURSE">Bangquet</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="recipe_description" style="font-size: 13px;">Description:</label>
                                <textarea class="form-control" id="recipe_description" name="menu_description" rows="3" required
                                    style="height: 30px; width:100%"></textarea>
                            </div>
                            <div class="form-group mt-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="recipe_price" style="font-size: 13px;">CODE:</label>
                                        <input type="text" class="form-control" id="recipe_code" name="menu_code" required
                                            placeholder="ex. CY23">
                                    </div>
                                    <div class="col-md-6">
                                        <label style="font-size : 13px;" for="category">Category:</label>
                                        <select id="category" name="category_id" class="form-select"
                                            aria-label="Default select example">
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="reviewer_select" style="font-size : 13px;">Reviewed By:</label>
                                <select id="reviewer_select" class="form-select" aria-label="Default select example"
                                    name="reviewer_id">
                                    @foreach ($reviewers as $reviewer)
                                        <option value="{{ $reviewer->employees->id }}">
                                            {{ $reviewer->employees->name }} {{ $reviewer->employees->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="approver_select" style="font-size: 13px">Approve By:</label>
                                <div class="col-md-12">
                                    <select id="approver_select" class="form-select" aria-label="Default select example"
                                        name="approver_id">
                                        @foreach ($approvers as $approver)
                                            <option value="{{ $approver->employees->id }}">
                                                {{ $approver->employees->name }} {{ $approver->employees->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="menu_image" style="font-size: 13px">Upload Image:</label>
                                <input class="form-control text-sm" type="file" id="menu_image" name="menu_image" required
                                    onchange="previewImage(event)">
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Create Menu</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="AddItemModal" tabindex="-1" aria-labelledby="AddItemModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="AddItemModalLabel">Select Items</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Table for Item Selection -->
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ITEM CODE</th>
                                        <th>ITEM DESCRIPTION</th>
                                        <th>PRICE</th>
                                        <th>UNIT</th>
                                        <th>SUB-UNIT</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            @php
                                                $itemPrice = $item
                                                    ->priceLevel()
                                                    ->latest()
                                                    ->where('price_type', 'cost')
                                                    ->first()->amount ?? 0;
                                                $itemCode = $item->item_code;
                                                $itemDescription = $item->item_description;
                                                $itemUnit = $item->units->unit_symbol;
                                                $uom_id = $item->uom_id;
                                                $priceID = $item
                                                    ->priceLevel()
                                                    ->latest()
                                                    ->where('price_type', 'cost')
                                                    ->first()->id ?? $item;
                                            @endphp

                                            <td>{{ $itemCode }}</td>
                                            <td>{{ $itemDescription }}</td>
                                            <td>{{ $itemPrice }}</td>
                                            <td>{{ $itemUnit }}</td>
                                            <td>
                                                <!-- Assign a unique ID to the select element -->
                                                <select name="sub_unit" id="sub_unit_{{ $item->id }}"
                                                    class="form-select" onchange="updateData(this, {{ $item->id }})">
                                                    @foreach ($item->units->fromUnits as $unit)
                                                        @php
                                                            $unitSymbol = $item->units
                                                                ->where('id', $unit->to_uom_id)
                                                                ->pluck('unit_symbol')
                                                                ->implode(', ');
                                                            $factor = $unit->conversion_factor;
                                                            $unitId = $unit->to_uom_id;
                                                        @endphp
                                                        <option
                                                            value="{{ json_encode(['id' => $unitId, 'factor' => $factor, 'symbol' => $unitSymbol, 'item_code' => $itemCode, 'item_price' => $itemPrice, 'item_description' => $itemDescription, 'price_id' => $priceID]) }}"
                                                            {{ $unit->to_uom_id == $uom_id ? 'selected' : '' }}>
                                                            {{ $unitSymbol ?? '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <!-- Assign a unique ID to the button -->
                                                <button id="addToTable_{{ $item->id }}"
                                                    class="btn btn-primary btn-sm"
                                                    onclick="addToTable({{ $item->id }}, {{ json_encode(['id' => $uom_id, 'factor' => 1, 'symbol' => $itemUnit, 'item_code' => $itemCode, 'item_price' => $itemPrice, 'item_description' => $itemDescription, 'price_id' => $priceID]) }})">
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

    </div>
    </form>
    </div>

    </div>
    </div>
@endsection


@section('script')
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('imagePreview');
                output.src = reader.result;
                output.style.display = 'block';
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function viewBranch(data) {
            console.log(data);
            document.getElementById('branch_id').value = data.id;
        }

        function addToTable(item, unit) {

            console.log(item);
            console.log(unit);
            const symbol = unit.symbol;

            const tableBody = document.getElementById('itemTableBody');
            const existingItem = Array.from(tableBody.querySelectorAll('tr')).find(row => row.querySelector('td')
                .textContent === item.item_code);
            if (existingItem) {
                alert('The item already exists in the table.');
                return;
            }

            const price = Math.round((1 / unit.factor) * unit.item_price * 100) / 100;
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td style="font-size: 13PX;">${unit.item_code}</td>
                <td style="font-size: 13PX;">${unit.item_description}</td>         
          
                <td>
                    <input type="number" name="qty[]" class="form-control" value="1" min="1" onchange="updateTotalPrice(this)">
                    <input type="hidden" name="uom_id[]" value="${unit.id}">
                    <input type="hidden" name="item_id[]" value="${item}">
                    <input type="hidden" name="price_level_id[]" value="${unit.price_id}">
                </td>
                <td style="font-size: 13PX; text-align: center">${symbol} </td>
                <td style="font-size: 13PX; text-align: center">${price}</td>
                <td class="total-price">${price}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="removeRow(this)">Remove</button>
                </td>
            `;

            tableBody.appendChild(newRow);
            updateOverallCost();
        }

        function updateData(selectElement, itemId) {
            // Get the selected option data
            const selectedOption = JSON.parse(selectElement.value);

            // Find the corresponding button using the unique ID
            const button = document.getElementById(`addToTable_${itemId}`);

            if (button) {
                // Update the button's onclick handler with the selected option
                button.onclick = function() {
                    addToTable(itemId, selectedOption);
                };

                console.log(`Updated button for Item ID: ${itemId} with Selected Option:`, selectedOption);
            } else {
                console.error(`Button with ID addToTable_${itemId} not found.`);
            }
        }


        // Add onclick to the table
        document.querySelectorAll('#itemTableBody tr td:nth-child(5) select').forEach(element => {
            element.onclick = function() {
                updateData(this);
            }
        });

        function updateTotalPrice(input) {
            const row = input.closest('tr');
            const price = parseFloat(row.querySelector('td:nth-child(5)').textContent);
            const requestQty = parseInt(input.value);
            const totalPriceCell = row.querySelector('.total-price');
            totalPriceCell.textContent = (price * requestQty).toFixed(2);
            updateOverallCost();
        }

        function removeRow(button) {
            const row = button.closest('tr');
            row.remove();
            updateOverallCost();
        }

        function updateOverallCost() {
            const totalPrices = document.querySelectorAll('.total-price');
            let overallCost = 0;
            totalPrices.forEach(priceCell => {
                overallCost += parseFloat(priceCell.textContent);
            });
            document.getElementById('totalAmount').textContent = `₱ ${overallCost.toFixed(2)}`;
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
