<div class="content-fluid">
    @if (session()->has('success'))
    <div class="alert alert-success" id="success-message">
        {{ session('success') }}
        <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
     <h5>Merchandise Inventory</h5>
    <div class="card mt-3 mb-3">
        <div class="card-header p-2">
            <div class="row">
                  @if (auth()->user()->employee->getModulePermission('Merchandise Inventory') == 1)
                     <div class=" row col-md-6">
                        <div class="col-md-6 mb-2">
                                <x-primary-button style="text-decoration: none;" wire:click="exportInventory">
                                Export 
                                </x-primary-button>
                                <span wire:loading class="spinner-border text-primary" role="status"></span>
                        </div>
                    </div>
                  @endif
               

                <div class="col-md-6">
                           <div class="input-group">
                            <span class="input-group-text">Search</span>
                             <input type="text" class="form-control form-control-sm" id="searchInput" onkeyup="filterTable()">
                           </div>
                            <script>
                                function filterTable() {
                                    const input = document.getElementById('searchInput');
                                    const filter = input.value.toLowerCase();
                                    const table = document.querySelector('#cardexTable');
                                    const rows = table.getElementsByTagName('tr');

                                    for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
                                        const cells = rows[i].getElementsByTagName('td');
                                        let match = false;

                                        for (let j = 0; j < cells.length; j++) {
                                            if (cells[j]) {
                                                const textValue = cells[j].textContent || cells[j].innerText;
                                                if (textValue.toLowerCase().indexOf(filter) > -1) {
                                                    match = true;
                                                    break;
                                                }
                                            }
                                        }

                                        rows[i].style.display = match ? '' : 'none';
                                    }
                                }
                            </script>
                </div>
            </div>
        </div>


        <div class="card-body overflow-auto" wire:ignore.self>
            <div class="table-responsive-sm h-100">
                <div class="mb-3"  style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-striped table-hover table-bordered table-sm table-responsive">
                        <thead class="table-light me-3 sticky-top">
                            <tr style="font-size: x-small;">
                                @if ($avlBal)
                                    <th>BAL.</th>
                                @endif
                                @if ($avlQty)
                                    <th>AVAIL.</th>
                                @endif
                                @if ($totalReserved)
                                    <th>Reserved</th>
                                @endif
                                @if ($code)
                                    <th>SKU</th>
                                @endif
                                <th>NAME</th>
                                @if ($uom)
                                    <th>UNIT</th>
                                @endif
                                @if ($category)
                                    <th>CATEGORY</th>
                                @endif
                                {{-- @if ($location)
                                    <th>LOCATION</th>
                                @endif --}}
                                @if ($brand)
                                    <th>BRAND</th>
                                @endif
                                @if ($status)
                                    <th>STATUS</th>
                                @endif
                                @if ($classification)
                                    <th>CLASSIFICATION</th>
                                @endif
                                @if ($barcode)
                                    <th>BARCODE</th>
                                @endif
                                @if($sellingPrice)
                                    <th>Sell Price</th>
                                @endif
                                  @if (auth()->user()->employee->getModulePermission('Merchandise Inventory') == 1)
                                    <th>
                                        ACTION
                                            <button type="button"
                                                class="btn btn-sm float-end"
                                                style="background: transparent; border: none; font-size: 1.25rem; padding: 0; line-height: 1;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#customCol"
                                                title="Add or remove column">
                                                +
                                        </button>
                                
                                    
                                    </th>
                                @endif 
                            </tr>
                        </thead>
                        <tbody id="cardexTable">
                            @forelse ($cardex as $index => $item)
                                <tr style="font-size: x-small;">
                                    @if ($avlBal)
                                        <td>{{ $item->total_balance ?? 0 }}</td>
                                    @endif
                                    @if ($avlQty)
                                        <td>{{ $item->total_available ?? 0 }}</td>
                                    @endif
                                    @if ($totalReserved)
                                        <td>{{ $item->total_reserved ?? 0 }}</td>
                                    @endif
                                    @if ($code)
                                        <td>{{ $item->item_code }}</td>
                                    @endif
                                        <td>{{ $item->item_description }}</td>
                                    @if ($uom)
                                        <td>{{ $item->uom->unit_symbol }}</td>
                                    @endif
                                    @if ($category)
                                            <td>{{ $item->category->category_name ?? 'N/A' }}</td>
                                    @endif
                                    {{-- @if ($location)
                                        <td>{{ $item->location ?? 'N/A' }}</td>
                                    @endif --}}
                                    @if ($brand)
                                        <td>{{ $item->brand->brand_name ?? 'N/A' }}</td>
                                    @endif
                                    @if ($status)
                                        <td>{{ $item->item_status }}</td>
                                    @endif
                                    @if ($classification)
                                        <td>{{ $item->classification->classification_name ?? 'N/A' }}</td>
                                    @endif
                                    @if ($barcode)
                                        <td>{{ $item->item_barcode }}</td>
                                    @endif
                                    @if ($sellingPrice)
                                        <td>{{ $item->sellingPrice->amount ?? 0 }}</td>
                                    @endif
                                    @if (auth()->user()->employee->getModulePermission('Merchandise Inventory') == 1)
                                         <td class="w-10 h-5">
                                            <x-primary-button data-bs-target="#editModal{{ $item->id }}">
                                                <i class="fa-solid fa-pen-to-square"><u>view</u></i>
                                            </x-primary-button>
                                        </td>    
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


     <!-- Custom  Columns Modal -->
     <div class="modal fade" id="customCol" tabindex="-1" aria-lablledby="CustomModalLabel" aria-hidden="true"  wire:ignore.self>
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="CustomModalLabel">Custom Columns</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-check">
                        <input wire:model.live="avlBal" class="form-check-input" type="checkbox" value="" id="checkAvlBal">
                        <label class="form-check-label" for="checkAvlBal">
                            Inventory Balance (BAL)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="avlQty" class="form-check-input" type="checkbox" value="" id="checkAvlQty">
                        <label class="form-check-label" for="checkAvlQty">
                            Available Quantity (AVAIL.)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="totalReserved" class="form-check-input" type="checkbox" value="" id="totalReserved">
                        <label class="form-check-label" for="totalReserved">
                            Total Reserved (RESERVED)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="code" class="form-check-input" type="checkbox" value="" id="checkCode">
                        <label class="form-check-label" for="checkCode">
                            SKU/CODE (SKU)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="uom" class="form-check-input" type="checkbox" value="" id="checkUom">
                        <label class="form-check-label" for="checkUom">
                            Unit of Measure (UNIT)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="category" class="form-check-input" type="checkbox" value="" id="checkCategory">
                        <label class="form-check-label" for="checkCategory">
                            Category (CATEGORY)
                        </label>
                    </div>
                    {{-- <div class="form-check">
                        <input wire:model.live="location" class="form-check-input" type="checkbox" value="" id="checkLocation">
                        <label class="form-check-label" for="checkLocation">
                            Location (LOCATION)
                        </label>
                    </div> --}}
                    <div class="form-check">
                        <input wire:model.live="brand" class="form-check-input" type="checkbox" value="" id="checkBrand">
                        <label class="form-check-label" for="checkBrand">
                            Brand (BRAND)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="status" class="form-check-input" type="checkbox" value="" id="checkStatus">
                        <label class="form-check-label" for="checkStatus">
                            Status (STATUS)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="classification" class="form-check-input" type="checkbox" value="" id="checkClassification">
                        <label class="form-check-label" for="checkClassification">
                            Classification (CLASSIFICATION)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="barcode" class="form-check-input" type="checkbox" value="" id="checkBarcode">
                        <label class="form-check-label" for="checkBarcode">
                            Barcode (BARCODE)
                        </label>
                    </div>
                    <div class="form-check">
                        <input wire:model.live="sellingPrice" class="form-check-input" type="checkbox" value="" id="checkSellingPrice">
                        <label class="form-check-label" for="checkSellingPrice">
                            Price (Sell Price)
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>



