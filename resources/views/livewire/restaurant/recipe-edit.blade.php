<div>
   <div>
        <form id="poForm" method="POST" wire:submit.prevent="updateRecipe" enctype="multipart/form-data">
            @csrf
            <div class="row me-3 w-100" wire:ignore.self>
                <div class=" col-md-8 card">
                    <div class=" card-body">
                        <header>
                            <h4>UPDATE RECIPE</h4>
                            <div class="me-3">
                                <button class="btn btn-success" type="button" data-bs-toggle="modal"
                                    data-bs-target="#AddItemModal">+
                                    Add Ingredients</button>
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
                                    @forelse ($recipes as $recipe)
                                        <tr>
                                            <td style="font-size: 13PX;">{{ $recipe->item->item_code}}</td>
                                            <td style="font-size: 13PX;">{{$recipe->item->item_description}}</td>         
                                            <td>
                                                <input type="number" name="qty[]" class="form-control" value="{{ number_format($recipe->qty ?? 0, 0) }}" min="1" onchange="updateTotalPrice(this)" onkeydown="handleEnterKey(event, this)">
                                                <input type="hidden" name="uom_id[]" value="{{$recipe->uom->uom_id}}">
                                                <input type="hidden" name="item_id[]" value="{{$recipe->item_id}}">
                                                <input type="hidden" name="price_level_id[]" value="{{$recipe->price_level->id}}">
                                            </td>
                                            <td style="font-size: 13PX; text-align: center">{{$recipe->uom->unit_symbol}}</td>
                                            <td style="font-size: 13PX; text-align: center">{{number_format(($recipe->latestItemCost?->amount / $recipe->conversionFactor() ) * $recipe->qty ?? 0 , 2) }}</td>
                                            <td class="total-price">{{number_format(($recipe->latestItemCost?->amount / $recipe->conversionFactor() ) * $recipe->qty ?? 0 , 2) }}</td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Remove</button>
                                            </td>
                                        </tr>
                                    @empty
                                        
                                    @endforelse

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
                        <div>
                            <div class="form-group" >
                                @if($hasNewImage)
                                    <img id="imagePreview" src="{{ $menu_image->temporaryUrl() }}" alt="Image Preview"
                                        style="width: 90%; height: 120px; object-fit: cover;" name="image">
                                @else
                                <img id="imagePreview" src="{{ asset('storage/' . $menu->menu_image) }}" alt="{{ $menu->menu_image }}"
                                    style="width: 90%; height: 120px; object-fit: cover;" name="image">
                                @endif
                            </div>
                            <div class="form-group mt-1">
                                <label for="recipe_name" style="font-size: 13px;">Recipe Name:</label>
                                <input type="text" class="form-control" id="recipe_name" name="menu_name" required
                                wire:model="menu_name">
                                @error('menu_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group mt-1">
                                <label for="recipe_type" style="font-size: 13px;">Type</label>
                                <select name="menu_type" id="recipe_type" class="form-select" aria-label="Default select example" wire:model="menu_type">
                                    <option value="Ala Carte" @if ($menu_type == 'Ala Carte') selected @endif>Ala Carte</option>
                                    <option value="Banquet" @if ($menu_type == 'Banquet') selected @endif>Banquet</option>
                                </select>
                                @error('menu_type') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="recipe_description" style="font-size: 13px;">Description:</label>
                                <textarea class="form-control" id="recipe_description" name="menu_description" rows="3" required wire:model="description"
                                    style="height: 30px; width:100%"></textarea>
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group mt-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="recipe_price" style="font-size: 13px;">CODE:</label>
                                        <input type="text" class="form-control" id="recipe_code" name="menu_code" required wire:model="menu_code"
                                            placeholder="ex. CY23">
                                        @error('menu_code') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label style="font-size : 13px;" for="category">Category:</label>
                                        <select id="category" name="category_id" class="form-select"
                                            aria-label="Default select example" wire:model="category_id">
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" @if ($category_id == $category->id) selected @endif>
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                @if ($hasReviewer)
                                    <label for="reviewer_select" style="font-size : 13px;">Reviewed By:</label>
                                    <select id="reviewer_select" class="form-select" aria-label="Default select example"
                                        name="reviewer_id" wire:model="reviewer">
                                        @foreach ($reviewers as $reviewerItem)
                                            <option value="{{ $reviewerItem->employees->id }}" @if($hasReviewer && $reviewerItem->employees->id == $reviewer) selected @endif>
                                                {{ $reviewerItem->employees->name }} {{ $reviewerItem->employees->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('reviewer') <span class="text-danger">{{ $message }}</span> @enderror
                                @endif

                                <label for="approver_select" style="font-size: 13px">Approved By:</label>
                                <div class="col-md-12">
                                    <select id="approver_select" class="form-select" aria-label="Default select example"
                                        name="approver_id" wire:model="approver">
                                        @foreach ($approvers as $approverItem)
                                            <option value="{{ $approverItem->employees->id }}" @if($approverItem->employees->id == $approver) selected @endif>
                                                {{ $approverItem->employees->name }} {{ $approverItem->employees->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('approver') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="menu_image" style="font-size: 13px">Upload Image:</label>
                                <input class="form-control text-sm" type="file" id="menu_image" name="menu_image"
                                    onchange="previewImage(event)" wire:model.live="menu_image">
                                @error('menu_image') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Update Recipe</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="AddItemModal" tabindex="-1" aria-labelledby="AddItemModalLabel"
                aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="AddItemModalLabel">Select Items</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-header">
                            <input type="text" class="form-control" id="modalSearch" placeholder="Search items..." onkeyup="filterModalTable()">
                        </div>
                        <div class="modal-body" style="max-height: 300px; overflow-y: auto; display: block;">
                            <!-- Table for Item Selection -->
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ITEM CODE</th>
                                        <th>ITEM DESCRIPTION</th>
                                        <th>PRICE</th>
                                        <th>UNIT</th>
                                        <th>SCALE</th>
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
                                                $itemUnit = $item->units->unit_symbol ?? 'N/A';
                                                $uom_id = $item->uom_id ;
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
                                                    @foreach ($item->units->fromUnits ?? [] as $unit)
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
                                                    type="button"
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

     <script>

        window.addEventListener('DOMContentLoaded', function(){
             updateOverallCost();
        });

        document.addEventListener('livewire:initialized', function () {
            if (window.Livewire && Livewire.hook) {
                Livewire.hook('message.processed', function () {
                    updateOverallCost();
                });
            }
        });

        function filterModalTable() {
            const searchInput = document.getElementById('modalSearch').value.toLowerCase();
            const table = document.querySelector('#AddItemModal table tbody');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const itemCode = row.cells[0].textContent.toLowerCase();
                const itemDescription = row.cells[1].textContent.toLowerCase();
                
                if (itemCode.includes(searchInput) || itemDescription.includes(searchInput)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }
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
                    <input type="number" name="qty[]" class="form-control" value="1" min="1" onchange="updateTotalPrice(this)" onkeydown="handleEnterKey(event, this)">
                    <input type="hidden" name="uom_id[]" value="${unit.id}">
                    <input type="hidden" name="item_id[]" value="${item}">
                    <input type="hidden" name="price_level_id[]" value="${unit.price_id}">
                </td>
                <td style="font-size: 13PX; text-align: center">${symbol} </td>
                <td style="font-size: 13PX; text-align: center">${price}</td>
                <td class="total-price">${price}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Remove</button>
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

        function handleEnterKey(event, input) {
            if (event.key === 'Enter') {
                event.preventDefault(); // Prevent form submission
                updateTotalPrice(input); // Update the total price
                input.blur(); // Remove focus from the input field
                return false;
            }
        }

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
</div>
