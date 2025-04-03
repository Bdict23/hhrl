<div>
    <div class="container">
        <div class="row"> 
            <div class="card mt-3 col-md-7">
                <div class="card-header">
                    <div>
                        <form id="poForm" method="POST" action="{{ route('purchase_order.store') }}">
                            @csrf
                        <strong>Purchase Order Items</strong>
                        <x-primary-button type="button" data-bs-toggle="modal" data-bs-target="#AddItemModal" style="float: right">+
                            Add
                            ITEM</x-primary-button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover table-sm table-responsive">
                        <thead class="table-dark">
                            <tr style="font-size: x-small">
                                <th>ITEM CODE</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>REQUEST QTY.</th>
                                <th>PRICE</th>
                                <th>TOTAL</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemTableBody">
    
                            {{--           POPULATE TABLE     --}}
    
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer">
                    <strong style="float: right">Total Amount: <span id="totalAmount">0.00</span></strong>
                </div>
            </div>
            <div class="card mt-3 col-md-5">
                <div class="card-header">
                    <strong>Purchase Order Information</strong>
                </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
        
                            <label for="options" class="form-label">Select Supplier</label>
                            <select id="options" name="supp_id" class="form-control" required onchange="fetchcompany(this.value)" style="font-size: x-small">
                                @foreach ($suppliers as $supp)
                                    <option value="{{ $supp->id }}" style="font-size: x-small">
                                        {{ $supp->supp_name }}
                                    </option>
                                @endforeach
                            </select>
        
                        </div>
                        <div class="col-md-6">
                            <label for="po_number" class="form-label">PO Number</label>
                            <input type="text" class="form-control" id="po_number" name="po_number" value = "<AUTO>" readonly style="font-size: x-small">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="contact_no_1" class="form-label">M. PO NUMBER</label>
                            <input type="text" class="form-control" id="merchandise_po_number" name="merchandise_po_number" style="font-size: x-small">
                        </div>
                        <div class="col-md-6">
                            <label for="options" class="form-label">TYPE</label>
                            <select id="options" name="type_id" class="form-control" required onchange="fetchcompany(this.value)" style="font-size: x-small">
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}" style="font-size: x-small">
                                        {{ $type->type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="contact_no_2" class="form-label">Remarks</label>
        
                            <input type="text" class="form-control" id="contact2" name="remarks" style="font-size: x-small">
                        </div>
                        <div class="col-md-6">
                            <div class="row mv-20">
                                <div class="col-md-6">
                                    <label for="contact_no_2" class="form-label">Reviewed To</label>
                                    <select id="options" name="reviewer_id" class="form-control" required style="font-size: x-small">
                                        @foreach ($reviewer as $reviewers)
                                            <option value="{{ $reviewers->employees->id }}" style="font-size: x-small">
                                                {{ $reviewers->employees->name }} {{ $reviewers->employees->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
        
                                <div class="col-md-6">
                                    <label for="contact_no_2" class="form-label">Approved To</label>
                                    <select id="options" name="approver_id" class="form-control" required
                                        onchange="fetchcompany(this.value)" style="font-size: x-small">
                                        @foreach ($approver as $approvers)
                                            <option value="{{ $approvers->employees->id }}" style="font-size: x-small">
                                                {{ $approvers->employees->name }} {{ $approvers->employees->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <x-primary-button type="button" onclick="document.getElementById('poForm').submit();">
                            Save
                        </x-primary-button>
                        <x-secondary-button onclick="history.back()" type="button"> Back </x-secondary-button>
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
                    <!-- Table for Item Selection -->
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ITEM CODE</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>AVAILABLE QTY.</th>
                                <th>PRICE</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->item_code }}</td>
                                    <td>{{ $item->item_description }}</td>
                                    <td>{{ $item->on_hand_qty }} 0</td>
                                    </td>
                                    <td>{{ $item->priceLevel()->latest()->where('price_type', 'cost')->first()->amount ?? 0.0 }}
                                    </td>
                                    <td>{{ $item->item_status ? 'Active' : 'Inactive' }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="addToTable({{ $item }})">
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
