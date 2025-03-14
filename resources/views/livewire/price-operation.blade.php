<div>
    <div id="price-levels-tables" class="tab-content card" style="display: none;">

        <div class="card-header">
            <h5>Price Levels</h5>
        </div>
        <div class="card-body">
            <x-primary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('costing-form', document.querySelector('.nav-link.active'))">+ Batch
                Pricing</x-primary-button>
            <div class="table-responsive mt-3 mb-3 d-flex justify-content-center"
                style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover table-sm small hover">
                    <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>Description</th>
                            <th>Cost Price</th>
                            <th>Retail Price</th>
                            <th>Markup</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($itemList as $item)
                            <tr>
                                <td>{{ $item->item_code }}</td>
                                <td>{{ $item->item_description }}</td>
                                <td>{{ $item->costPrice ? $item->costPrice->amount : 'N/A' }}</td>
                                <td>{{ $item->sellingPrice ? $item->sellingPrice->amount : 'N/A' }}</td>
                                <td>{{ $item->sellingPrice ? $item->sellingPrice->markup . '%' : 'N/A' }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary btn-sm">Cost</a>
                                    <a href="#" class="btn btn-sm btn-secondary btn-sm">Retail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>



    {{-- Pricing Form --}}
    <div class="tab-content " id="costing-form"
        {{ $AddBatchPricing == 1 ? 'style=display:block' : 'style=display:none' }}>
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert"
                    style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1050;">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form id="departmentForm" wire:submit.prevent="savePricing">
                @csrf
                <div class="row">
                    <div class="col-md-6 card">
                        <div class="shadow-sm">
                            <div class="card-body">
                                <header class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <x-primary-button type="button"
                                            style="background-color: rgb(128, 215, 245);">New</x-primary-button>
                                        <x-primary-button type="submit">Save</x-primary-button>
                                        <x-danger-button type="button" onclick="clearForm()">Clear</x-danger-button>
                                        <x-secondary-button onclick="showTab('price-levels-tables', this)"
                                            class="ms-2">Back</x-secondary-button>
                                    </div>
                                </header>
                                <div class="card-body">
                                    <div class="alert alert-light" role="alert">
                                        <div class="row">
                                            <div>
                                                <div class="mb-3">
                                                    <label class="form-label">Item Code</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="markup"
                                                            name="itemCode" readonly disabled
                                                            value="{{ is_object($itemView) ? $itemView->item_code : '' }}">
                                                        <button class="input-group-text" type="button"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#AddPersonnelModal">Find</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="item_name">Description:</label>
                                                <input type="text" class="form-control" id="markup"
                                                    name="itemCode" readonly Disabled
                                                    value="{{ is_object($itemView) ? $itemView->item_description : '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="department_name" class="form-label">Retail Price</label>
                                                    <input type="number" class="form-control" id="department_name"
                                                        name="department_name" placeholder="0.00" step="0.01">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Markup</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="markup"
                                                            name="markup" placeholder="0.00" step="0.01">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Gross Profit Margin
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="markup"
                                                            name="markup" placeholder="0.00" step="0.01">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="mb-3">
                                                            <label for="margin" class="form-label">Markup
                                                                Amount</label>
                                                            <input type="number" class="form-control" id="margin"
                                                                name="margin" placeholder="0.00" step="0.01"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Tax
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="markup"
                                                            name="markup" placeholder="0.00" step="0.01" readonly
                                                            disabled>
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="mb-3">
                                                            <label for="margin" class="form-label">Total Tax</label>
                                                            <div class="input-group">
                                                                <input type="number" class="form-control"
                                                                    id="totalTax" readonly disabled step="0.01"
                                                                    placeholder="0.00">
                                                                <button class="input-group-text"
                                                                    type="button">?</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">New Retail Price</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="new_retail_price"
                                                        name="new_retail_price" placeholder="0.00" step="0.01"
                                                        readonly disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-end">
                                        <!-- Footer content if needed -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <header class="d-flex justify-content-between align-items-center mb-3">
                                        <h1 class="h4">Batches</h1>
                                    </header>
                                    <table class="table table-striped table-hover table-sm">
                                        <thead class="table-dark">
                                            <tr style="font-size: smaller;">
                                                <th style="font-size: smaller;">Item Code</th>
                                                <th style="font-size: smaller;">Branch</th>
                                                <th style="font-size: smaller;">Retail Price</th>
                                                <th style="font-size: smaller;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="departmentTableBody">
                                        </tbody class="table table-striped table-hover table-sm">
                                        @forelse ($itemBatches as $itemBatch)
                                            <tr>
                                                <td style="font-size: smaller;">{{ $itemBatch->item->item_code }}</td>
                                                <td style="font-size: smaller;">
                                                    {{ $itemBatch->branch->branch_name ?? 'All' }}
                                                </td>
                                                <td style="font-size: smaller;">
                                                    {{ $itemBatch->item->sellingPrice->amount ?? 'N/A' }}</td>
                                                <td style="font-size: smaller;">
                                                    <button class="btn btn-sm btn-primary">View</button>
                                                </td>
                                            </tr>

                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No Batches Found</td>
                                            </tr>
                                        @endforelse
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card shadow-sm mt-3">
                            <div class="card-body">
                                <header class="d-flex justify-content-between align-items-center mb-3">
                                    <h1 class="h4">Apllied Branches</h1>
                                    <div>
                                        <input type="checkbox" id="selectAllBranches"
                                            onclick="toggleSelectAllBranches(this)">
                                        <label for="selectAllBranches">Select All</label>
                                    </div>
                                </header>
                                <table class="table table-striped table-hover table-sm">
                                    <thead class="table-dark">
                                        <tr style="font-size: smaller;">
                                            <th>Branch Name</th>
                                            <th>Company Code</th>
                                            <th>Selected</th>
                                        </tr>
                                    </thead>
                                    <tbody id="departmentTableBody">
                                        @forelse ($branches as $branch)
                                            <tr>
                                                <td>{{ $branch->branch_name }}</td>
                                                <td class="text-center">{{ $branch->company->company_code }}</td>
                                                <td><input type="checkbox" name="branch_ids[]"
                                                        value="{{ $branch->id }}"></td>
                                            </tr>
                                        @empty
                                            <tr class="text-center">
                                                <td colspan="4">No Branches Found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Add Item Modal -->
        <div class="modal fade" id="AddPersonnelModal" tabindex="-1" aria-labelledby="AddPersonnelModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="AddPersonnelModalLabel">SELECT ITEM</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 d-flex justify-content-end">
                            <input type="text" class="form-control w-50" id="searchEmployee"
                                placeholder="Search Item">
                        </div>
                        <div style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-striped table-hover table-sm">
                                <thead class="table-dark">
                                    <tr style="font-size: smaller;">
                                        <th>Item Code</th>
                                        <th>Item Description</th>
                                        <th>Current Cost</th>
                                        <th>Current SRP</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="employeeTableBody">
                                    @forelse ($itemList as $item)
                                        <tr>
                                            <td>{{ $item->item_code }}</td>
                                            <td>{{ $item->item_description }}</td>
                                            <td>{{ $item->costPrice ? $item->costPrice->amount : 'N/A' }}</td>
                                            <td>{{ $item->sellingPrice ? $item->sellingPrice->amount : 'N/A' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary"
                                                    wire:click="selectItem({{ $item->id }})"
                                                    data-bs-dismiss="modal">
                                                    Select
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No Items Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
