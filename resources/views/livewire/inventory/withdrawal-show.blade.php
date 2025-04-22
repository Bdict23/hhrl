<div>
    <div>
       <div>
            @if (session()->has('success'))
                <div class="alert alert-success mt-1">
                    {{ session('success') }}
                    <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>  
            @endif
       </div>
            <div class="row me-3 w-100">
                <div class=" col-md-8 card">
                    <div class=" card-body">
                        <header>
                            <h1> Item Withdrawal</h1>
                            <div class="me-3" wire:ignore>
                                @if(!$isAlreadyFinal)
                                    <x-primary-button type="button" data-bs-toggle="modal" data-bs-target="#AddItemModal">+ Add ITEM</x-primary-button>
                                @endif
                                <x-secondary-button style="color: rgb(135, 235, 168);" onclick="window.location.href='{{ route('withdrawal.summary') }}'"> Summary </x-secondary-button>
                                <x-secondary-button onclick="history.back()"> Back </x-secondary-button>
                            </div>
                        </header>
                       
                      
                        <table class="table table-striped table-hover me-3">
                           
                           
                            <thead class="table-light me-3">
                                <tr style="font-size: x-small;">
                                    @if ($avlBal)
                                        <th>BAL.</th>
                                    @endif
                                    @if ($avlQty)
                                        <th>AVAIL.</th>
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
                                    @if ($location)
                                        <th>LOCATION</th>
                                    @endif
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
                                    <th>REQ. QTY</th>
                                    <th>COST</th>
                                    <th>TOTAL</th>
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
                                    
                                </tr>
                               
                           
                            </thead>
                            <tbody id="itemTableBody">
                                @forelse ($selectedItems as $index => $item)
                                    <tr style="font-size: x-small;">
                                        @if ($avlBal)
                                            <td>{{ $item['total_balance'] }}</td>
                                        @endif
                                        @if ($avlQty)
                                            <td>{{ $item['total_available'] }}</td>
                                        @endif
                                        @if ($code)
                                            <td>{{ $item['code'] }}</td>
                                        @endif
                                        <td>{{ $item['name'] }}</td>
                                        @if ($uom)
                                            <td>{{ $item['uom'] }}</td>
                                        @endif
                                        @if ($category)
                                            <td>{{ $item['category'] }}</td>
                                        @endif
                                        @if ($location)
                                            <td>{{ $item['location'] }}</td>
                                        @endif
                                        @if ($brand)
                                            <td>{{ $item['brand'] }}</td>
                                        @endif
                                        @if ($status)
                                            <td>{{ $item['status'] }}</td>
                                        @endif
                                        @if ($classification)
                                            <td>{{ $item['classification'] }}</td>
                                        @endif
                                        @if ($barcode)
                                            <td>{{ $item['barcode'] }}</td>
                                        @endif
                                        <td><input type="number" class="form-control" wire:model.live="selectedItems.{{ $index }}.requested_qty"
                                                min="1" max="{{ $item['total_available'] }}"></td>
                                        <td>{{ $item['cost'] }}</td>
                                        <td>{{ $item['total'] }}</td>
                                        <td><button type="button" class="btn btn-danger btn-sm"
                                                wire:click="removeItem({{ $index }})">Remove</button></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="15" class="text-center">No items selected</td>
                                    </tr>
                                @endforelse

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
                                        <strong class="form-control">{{ $overallTotal }}</strong>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <span wire:loading class="mr-2 spinner-border text-primary float-right" role="status"></span>
                                @if(session()->has('error'))
                                <div class="alert alert-danger mt-1">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                @error('selectedItems')
                                    <div class="alert alert-danger mt-1">
                                        {{ $message }}
                                        <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>  
                                    </div> 
                                                                  
                                @enderror
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
                                        <label for="reference" class="form-label">REFERENCE<span style="color: red;">&nbsp;*</span></label>
                                    </div>
                                    <div class="col-md-7">
                                        <input wire:model="reference" type="text" class="form-control" id="reference_number" >
                                        @error('reference')
                                            <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="deptartment" class="form-label"
                                            style="width: 100; font-size: 13px">Department</label><span
                                            style="color: red;">*</span>
                                        <select wire:model="selectedDepartment" id="department"  class="form-select"
                                            aria-label="Default select example" style="width: 100; font-size: 13px">
                                            <option value="">Select Department</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('selectedDepartment')
                                         <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="usage_date" class="form-label" style="width: 100; font-size: 13px">Will use on</label> <span style="color: red;">*</span>
                                        <input  wire:model.live="useDate"  type="date" class="form-control" id="usage_date"
                                           
                                            {{ $isAlreadyFinal ? 'disabled' : '' }}>
                                        @error('useDate')
                                        <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-2">

                                        <div class="col-md-6">
                                            <label for="status" class="form-label">Have Span</label>
                                            <input wire:model.live='haveSpan' type="checkbox" class="form-check-input" id="span-date" {{ $isAlreadyFinal ? 'disabled' : '' }}>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="finalStatus" class="form-label">Set as Final</label>
                                            <input wire:model.live ="finalStatus" type="checkbox" class="form-check-input" id="finalStatus" {{ $isAlreadyFinal ? 'disabled' : '' }}>
                                        </div>
                                    </div>
                                    <div>
                                        <div id="lifespanContainer" {{ $haveSpan ? 'style=display:block' : 'style=display:none' }}>
                                            <label for="lifespan_date" class="form-label"
                                                style="width: 100; font-size: 13px">Lifespan Date</label>
                                            <input wire:model='spanDate' type="date" class="form-control" id="lifespan_date" {{ $isAlreadyFinal ? 'disabled' : '' }}>
                                            @error('spanDate')
                                                <i class="text-danger" style="font-size: 12px">{{ $message }}</i>
                                            @enderror
                                        </div>
                                    <div class="row mt-3">
                                        <label for="remarks" class="form-label" style="font-size: 13px;">Remarks</label>
                                        <textarea wire:model="remarks" type="text" class="form-control" id="remarks" style="font-size: 13px; height: 100px"></textarea>
                                        @error('remarks')
                                            <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-md-6">
                                            <label for="reviewed_to" class="form-label" style="font-size: 13px;">Reviewed
                                                To</label>
                                            <select wire:model="reviewer" id="reviewed_to" class="form-select"
                                                aria-label="Default select example">
                                                <option value="">Select</option>

                                                @if ($reviewers->isEmpty())
                                                    <option style="font-size: 10px">No Reviewer Found</option>
                                                @else
                                                    @foreach ($reviewers as $reviewer)
                                                        <option value="{{ $reviewer->employees->id }}">
                                                            {{ $reviewer->employees->name }}
                                                            {{ $reviewer->employees->last_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('reviewer')
                                                <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="approved_to" class="form-label" style="font-size: 13px;">Approved To</label>
                                            <select wire:model="approver" id="approved_to" class="form-select"
                                                aria-label="Default select example">
                                                <option value="">Select</option>
                                                @if ($reviewers->isEmpty())
                                                    <option style="font-size: 10px">No Reviewer Found</option>
                                                @else
                                                    @foreach ($approvers as $approver)
                                                        <option value="{{ $approver->employees->id }}">
                                                            {{ $approver->employees->name }}
                                                            {{ $approver->employees->last_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('approver')
                                                <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div>
                                        @if (!$isAlreadyFinal)
                                            <x-primary-button wire:click="store" type="button" class=" mt-3">Save</x-primary-button>
                                        @endif
                                    </div>
                                </div>
                            </div>
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
                    <div class="form-check">
                        <input wire:model.live="location" class="form-check-input" type="checkbox" value="" id="checkLocation">
                        <label class="form-check-label" for="checkLocation">
                            Location (LOCATION)
                        </label>
                    </div>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="AddItemModal" tabindex="-1" aria-labelledby="AddItemModalLabel" aria-hidden="true"  wire:ignore>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AddItemModalLabel">Select Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-3">
                        <select id="categoryFilter" class="form-select w-25">
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
                                <th>CODE</th>
                                <th>NAME</th>
                                <th>INVENTORY BALANCE</th>
                                <th>AVAILABLE QTY.</th>
                                <th>COST PRICE</th>
                                <th>CATEGORY</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="itemTable">
                            @foreach ($myCardexItems as $index => $item)
                                <tr>
                                    <td>{{ $item->item_code }}</td>
                                    <td>{{ $item->item_description }}</td>
                                    <td>{{ $item->total_balance }}</td>
                                    <td>{{ $item->total_available}}</td>
                                    <td>{{ $item->costPrice->amount ?? 0 }}</td>
                                    <td>{{ $item->category->category_name ?? 'N/A' }}</td>
                                    <td>{{ $item->item_status }}</td>
                                    <td>
                                        <button wire:click="addItem({{ $item->id }}, {{ $item->total_balance }} , {{ $item->total_available }} )" type="button" class="btn btn-primary btn-sm"> Add </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <span wire:loading class="mr-2 spinner-border text-primary float-left" role="status"></span>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
