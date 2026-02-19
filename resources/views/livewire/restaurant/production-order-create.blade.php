<div>
   
    <div class="row">
      <h4 class="text-end">Production Order <i class="bi bi-boxes"></i></h4>
            <div class="col-md-12 row ">
                <div class="col-md-3 mb-2">
                    <div class="input-group rounded shadow-sm">
                        <select name="" id="" class="form-select form-select-sm" wire:model="saveAs" @if($orderStats == 'FINAL') disabled @endif>
                            <option value="">SAVE AS</option>
                            <option value="DRAFT">DRAFT</option>
                            <option value="FINAL">FINAL</option>
                        </select>
                        <button class="btn btn-primary btn-sm" wire:click="productonOrderSave"  @if($orderStats == 'FINAL') disabled @endif>Save</button>
                    </div>
                    @error('saveAs')
                        <div class="text-danger" style="font-size: smaller;">{{ $message }}</div>
                    @enderror
                        <a href="/production-orders" class="col-md-12 d-grid gap-2" style="text-decoration: none;">
                            <x-secondary-button class="mt-2 btn-sm">Summary &nbsp; <i class="bi bi-card-list"></i></x-secondary-button>
                        </a>
                    <div class="col-md-12 d-grid gap-2">
                        <x-secondary-button class="mt-2 btn-sm">Print &nbsp; <i class="bi bi-printer"></i></x-secondary-button>
                    </div>
                </div>
               <div class="col-md-9 mb-2">
                 <div class="card">
                     <div class="card-body row">
                         <div class="col-md-4">
                             <div class="col-md-12 row">
                                 <div class="input-group mb-2 mt-2">
                                     <label for="reference" class="input-group-text">Reference</label>
                                     <input type="text" id="reference" class="form-control" style="font-size: smaller; text-align: center;" placeholder="<AUTO>" readonly disabled wire:model="reference">
                                 </div>
                             </div>
                             <div class="col-md-12 input-group">
                                 <label for="prepared_by" class="input-group-text">Prepared By</label>
                                 <input type="text" id="prepared_by" class="form-control" wire:model="prepared_by" readonly disabled>
                             </div>
                         </div>
                         <div class="col-md-8 row">
                             <div class="col-md-12">
                                 <textarea id="notes" class="form-control" wire:model="notes" placeholder="Note"
                                 wire:model="notes"></textarea>
                                    @error('notes')
                                        <div class="text-danger" style="font-size: smaller;">{{ $message }}</div>
                                    @enderror
                             </div>
                         </div>
                     </div>
                 </div>
               </div>
            </div>
        <div class="col-md-3 card mr-2"  style="height: 600px; max-height:600px; overflow: auto;">
              <hr class="text-secondary">
              @error('selectedRecipes')
                    <div class="text-danger" style="font-size: smaller;">{{ $message }}</div>
                @enderror
              <table class="table table-hover  table-sm">
                 <thead class="sticky-top bg-light">
                    <tr>
                        <th></th>
                        <th>Recipe</th>
                        <th >Qty</th>
                        <th @if($orderStats == 'FINAL') hidden @endif>Action</th>
                    </tr>
                 </thead>
                    <tbody>
                       @forelse($selectedRecipes ?? [] as $index => $recipe)
                            <tr  wire:click="$set('selectedRecipeIdTab', {{ $recipe['id'] }})" style="cursor: pointer;" class="@if($selectedRecipeIdTab === $recipe['id']) table-primary @endif">
                                <td>@if($selectedRecipeIdTab === $recipe['id'])
                                    <i class="bi bi-arrow-right-circle-fill" style="color: yellowgreen"
                                     wire:loading.class="spinner-border spinner-border-sm" 
                                     wire:loading.remove.class="bi bi-arrow-right-circle-fill"
                                     ></i>
                                    @endif 
                                </td>
                                <td>{{ $recipe['menu_name'] }}</td>
                                <td><input type="number" class="form-control" 
                                    wire:model.live="selectedRecipes.{{ $index }}.qty_requested"
                                    min="1"
                                     @if($orderStats == 'FINAL') disabled value="{{ $recipe['qty_requested'] }}" @endif></td>
                                </td>

                                <td>
                                    <button class="btn btn-danger btn-sm" @if($orderStats == 'FINAL') hidden @endif
                                        onclick="event.preventDefault(); confirmRemoveRecipe({{ $recipe['id'] }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td @if($orderStats == 'FINAL') colspan="3" @else colspan="4" @endif class="text-center">No recipes added</td>
                            </tr>
                        @endforelse
                    </tbody>
              </table>
             <div class="d-grid gap-2 sticky-bottom bg-light">
                 <button @if($orderStats == 'FINAL') hidden @endif class="m-2 text-center btn " style="background-color: rgb(47, 47, 46); color: white;" data-bs-toggle="modal" data-bs-target="#addRecipeModal">+ Add</button>
             </div>
        </div>
    
          <div class="col-md-9 row" wire:ignore.self> 

             <div wire:ignore.self>
                <ul class="nav nav-tabs mb-3" id="ingredientsTabs" role="tablist" wire:ignore>
                    <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="selected-recipe-tab" data-bs-toggle="tab" data-bs-target="#selected-recipe" type="button" role="tab" aria-controls="selected-recipe" aria-selected="true">Selected Recipe Ingredients</button>
                    </li>
                    <li class="nav-item" role="presentation">
                            <button class="nav-link" id="overall-ingredients-tab" data-bs-toggle="tab" data-bs-target="#overall-ingredients" type="button" role="tab" aria-controls="overall-ingredients" aria-selected="false">Overall Ingredients</button>
                    </li>
                </ul>
                 <div class="tab-content" id="ingredientsTabsContent" wire:ignore.self>

                    {{-- 1. Selected Recipe Ingredients Tab --}}
                    <div class="card tab-pane fade show active" id="selected-recipe" role="tabpanel" aria-labelledby="selected-recipe-tab" wire:ignore.self>
                         @error('recipeIngredients')
                            <div class="text-danger" style="font-size: smaller;">{{ $message }}</div>
                        @enderror
                        <div class="card-body" style="height: 600px; max-height: 600px; overflow: auto;">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Req. Qty</th>
                                        <th>Invt. Bal.</th>
                                        <th>Avl.</th>
                                        <th>Status</th>
                                        <th @if($orderStats == 'FINAL') hidden @endif>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $filteredIngredients = collect($recipeIngredients)->where('recipe_id', $selectedRecipeIdTab);
                                    @endphp

                                    @forelse($filteredIngredients as $ingredient)
                                        @php
                                            // Kuhaon ang aggregated data gikan sa Computed Property 'overall'
                                            $summary = $this->overall[$ingredient['item_id']] ?? null;
                                            $isShort = ($summary['available'] ?? 0) < 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $ingredient['item']['item_code'] }}</td>
                                            <td>{{ $ingredient['item']['item_description'] }}</td>
                                            <td>{{ number_format($ingredient['qty'], 2) . '('.($ingredient['uom']['unit_symbol'] ?? 'N/A').')' }}</td>
                                            <td>{{ number_format($ingredient['balance'], 2) . '('.($ingredient['item']['uom']['unit_symbol'] ?? 'N/A').')' }}</td>
                                            
                                            {{-- Available Qty from Computed Property --}}
                                            <td style="{{ $isShort ? 'color: red; font-weight: bold;' : '' }}">
                                                {{ number_format($summary['available'] ?? 0, 2) . '('.($ingredient['uom']['unit_symbol'] ?? 'N/A').')'}}
                                            </td>

                                            {{-- Status Badge Logic --}}
                                            <td>
                                                @if(($summary['available'] ?? 0) >= 0)
                                                    <span class="badge rounded-pill bg-success text-white">Available</span>
                                                @elseif(($ingredient['balance'] ?? 0) > 0)
                                                    <span class="badge rounded-pill bg-warning text-dark">Insufficient</span>
                                                @else
                                                    <span class="badge rounded-pill bg-danger text-white">Out of Stock</span>
                                                @endif
                                            </td>
                                            <td @if($orderStats == 'FINAL') hidden @endif>
                                                <button class="btn btn-danger btn-sm" @if($orderStats == 'FINAL') disabled @endif
                                                    onclick="event.preventDefault(); confirmRemoveIngredient({{ $ingredient['item_id'] }}, {{ $ingredient['recipe_id'] }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td @if($orderStats == 'FINAL') colspan="7" @else colspan="8" @endif  class="text-center">No items added</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 2. Overall Ingredients Tab (Aggregated List) --}}
                    <div class="card tab-pane fade" id="overall-ingredients" role="tabpanel" aria-labelledby="overall-ingredients-tab" wire:ignore.self>
                        <div class="card-header">
                            <div class="input-group mb-2">
                                <label for="searchOverall" class="input-group-text">Search &nbsp;<i class="bi bi-search"></i></label>
                                <input type="text" id="searchOverallInput" class="form-control" placeholder="Search ingredients..." onkeypress="searchFromOverall()">
                                <script>
                                    function searchFromOverall() {
                                        const searchTerm = document.getElementById('searchOverallInput').value.toLowerCase();
                                        const rows = document.querySelectorAll('#overallIngredientsTableBody tr');
                                    
                                        rows.forEach(row => {
                                            const itemName = row.cells[1].textContent.toLowerCase(); // Assuming Item Name is in the second column
                                            if (itemName.includes(searchTerm)) {
                                                row.style.display = ''; // Show row
                                            } else {
                                                row.style.display = 'none'; // Hide row
                                            }
                                        });
                                    }
                                </script>
                            </div>
                        </div>
                        <div class="card-body" style="height: 600px; max-height: 600px; overflow: auto;">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Total Req. Qty</th>
                                        <th>Unit</th>
                                        <th>Invt. Balance</th>
                                        <th>Projected Avl.</th>
                                        <th>Status</th>
                                        <th @if($orderStats == 'FINAL') hidden @endif>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="overallIngredientsTableBody">
                                    @forelse($this->overall as $itemId => $item)
                                        @php 
                                            $isShort = $item['available'] < 0; 
                                            // Pangitaon nato ang index sa manual entry para ani nga item_id
                                            $manualIndex = collect($this->recipeIngredients)->search(fn($ing) => $ing['item_id'] == $itemId && $ing['recipe_id'] === null);
                                        @endphp
                                        <tr wire:key="overall-{{ $itemId }}">
                                            <td>{{ $item['item_code'] }}</td>
                                            <td>{{ $item['item_description'] }}</td>
                                            <td>
                                                @if($manualIndex !== false)
                                                    {{-- Kung manual item, pwede i-edit ang qty --}}
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" 
                                                            class="form-control" 
                                                            wire:model.live="recipeIngredients.{{ $manualIndex }}.qty"
                                                            min="0.01" step="0.01" @if($orderStats == 'FINAL') disabled @endif>
                                                        @if($item['qty'] > $this->recipeIngredients[$manualIndex]['qty'])
                                                            <span class="input-group-text bg-light" title="Total including recipes">
                                                                Σ {{ number_format($item['qty'], 2) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    {{-- Kung gikan ra sa Recipe, readonly ang display --}}
                                                    <input type="text" class="form-control form-control-sm bg-light" value="{{ number_format($item['qty'], 2) }}" readonly>
                                                @endif
                                            </td>
                                            <td>{{ $item['uom'] }}</td>
                                            <td>{{ number_format($item['balance'], 2) }} ({{ $item['base_uom'] }})</td>
                                            <td style="{{ $isShort ? 'color: red; font-weight: bold;' : '' }}">
                                                {{ number_format($item['available'], 2) }} ({{ $item['uom'] }})
                                            </td>
                                            <td>
                                                <span class="badge {{ $item['available'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $item['available'] >= 0 ? 'Ready' : 'Critical' }}
                                                </span>
                                            </td>
                                            <td @if($orderStats == 'FINAL') hidden @endif>
                                                <button class="btn btn-danger btn-sm" @if($orderStats == 'FINAL') disabled @endif
                                                       onclick="event.preventDefault(); confirmRemoveOverallItem({{ $itemId }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td @if($orderStats == 'FINAL') colspan="7" @else colspan="8" @endif class="text-center">No summarized items</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="d-grid gap-2 sticky-bottom bg-light" >
                                    <button  @if($orderStats == 'FINAL') hidden @endif class="m-2 text-center btn " style="background-color: rgb(47, 47, 46); color: white;" data-bs-toggle="modal" data-bs-target="#addItemModal">+ Add Item</button>
                            </div>
                        </div>
                        
                    </div>
                </div>
             </div>
          </div>
    </div>

   

    {{-- ADD RECIPE MODAL --}}
    <div class="modal fade" id="addRecipeModal" tabindex="-1" aria-labelledby="addRecipeModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRecipeModalLabel">Add Recipe 🍽</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-2">
                        <label for="search" class="input-group-text">Search &nbsp;<i class="bi bi-search"></i></label>
                        <input type="text" id="searchRecipeInput" class="form-control" onkeypress="searchFromRecipes()">
                        <script>
                            function searchFromRecipes() {
                                const searchTerm = document.getElementById('searchRecipeInput').value.toLowerCase();
                                const rows = document.querySelectorAll('#recipesTableBody tr');
                            
                                rows.forEach(row => {
                                    const recipeName = row.cells[0].textContent.toLowerCase(); // Assuming Recipe Name is in the first column
                                    if (recipeName.includes(searchTerm)) {
                                        row.style.display = ''; // Show row
                                    } else {
                                        row.style.display = 'none'; // Hide row
                                    }
                                });
                            }
                            </script>
                    </div>
                    <div style="height: 400px; overflow-x: auto;">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>Recipe Name</th>
                                   <th>Type</th>
                                    <th>Category</th>
                                    <th>Code</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="recipesTableBody">
                                @forelse($recipes ?? [] as $recipe)
                                    <tr>
                                       <td>{{ $recipe->menu_name }}</td>
                                        <td>{{ $recipe->menu_type ?? 'N/A' }}</td>
                                        <td>{{ $recipe->category->category_name ?? 'N/A' }}</td>
                                        <td>{{ $recipe->menu_code ?? 'N/A' }}</td>
                                        <td>{{ $recipe->status ?? 'N/A' }}</td>
                                        <td><button id="addRecipeButton{{ $recipe->id }}" class="btn btn-primary btn-sm" wire:click="addRecipe({{ $recipe->id }})">
                                            <i class="bi bi-plus" 
                                                wire:loading.class="spinner-border spinner-border-sm" 
                                                wire:target="addRecipe({{ $recipe->id }})"
                                                wire:loading.remove.class="bi bi-plus"
                                                wire:loading.attr="disabled">
                                            </i>
                                        </button></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No recipes available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ADD ITEM MODAL --}}
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Add Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-2">
                        <label for="search" class="input-group-text">Search &nbsp;<i class="bi bi-search"></i></label>
                        <input type="text" id="searchItemInput" class="form-control" onkeypress="searchFromItems()">
                        <script>
                            function searchFromItems() {
                                const searchTerm = document.getElementById('searchItemInput').value.toLowerCase();
                                const rows = document.querySelectorAll('#itemsTableBody tr');
                            
                                rows.forEach(row => {
                                    const itemName = row.cells[0].textContent.toLowerCase(); // Assuming Item Name is in the first column
                                    if (itemName.includes(searchTerm)) {
                                        row.style.display = ''; // Show row
                                    } else {
                                        row.style.display = 'none'; // Hide row
                                    }
                                });
                            }
                        </script>
                    </div>
                    <div style="height: 400px; overflow-x: auto;">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Unit</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                @forelse($items ?? [] as $item)
                                    <tr>
                                        <td>{{ $item->item_description }}</td>
                                        <td>{{ $item->uom->unit_symbol ?? 'N/A' }}</td>
                                        <td><button class="btn btn-primary btn-sm" wire:click="addItem({{ $item->id }})">
                                             <i class="bi bi-plus"  wire:loading.class="spinner-border spinner-border-sm" 
                                             wire:target="addItem({{ $item->id }})"
                                             wire:loading.remove.class="bi bi-plus"
                                             wire:loading.attr="disabled"></i>
                                        </button></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No items available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('showAlert', event => {
            const data = event.detail[0];
            // Close any open SweetAlert first
            Swal.close();
            
            // Use SweetAlert or any other alert library to display the message
           if(data.type === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                });
            } else if(data.type === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                });
            }else if(data.type === 'warning') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: data.message,
                });
            } else if(data.type === 'info') {
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });

        function confirmRemoveRecipe(recipeId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to remove this recipe?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Removing...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    @this.call('removeRecipe', recipeId);
                }
            });
        }

        function confirmRemoveIngredient(itemId, recipeId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to remove this ingredient?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Removing...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    @this.call('removeIngredientItem', itemId, recipeId);
                }
            });
        }
        function confirmRemoveOverallItem(itemId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to remove this item from overall summary?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Removing...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    @this.call('removeOverallIngredientItem', itemId);
                }
            });
        }
    </script>
</div>