<div class="content-fluid">

    <div class="card mt-3 mb-3">
        <div class="card-header p-2">
            <div class="row">
                <div class=" row col-md-6">
                    <div class="col-md-6">
                        <H5>BACK-ORDER LISTS</H5>
                    </div>
                    <div class="col-md-6">
                        <span wire:loading class="spinner-border text-primary" role="status"></span>
                    </div>
                </div>

                <div class="col-md-6">
                <div class="d-flex">
                    <div class="input-group">
                        <label for="PO-status" class="input-group-text">Status</label>
                        <select wire:model="statusPO" id="PO-status"  class="form-select form-select-sm">
                            <option value="all">All</option>
                            <option value="ACTIVE">ACTIVE</option>
                            <option value="FULLFILLED">FULLFILLED</option>
                            <option value="FOR PO">FOR PO</option>
                            <option value="CANCELLED">CANCELLED</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="from_date" class="input-group-text">From:</label>
                        <input wire:model="fromDate" type="date" id="from_date" name="from_date" value="{{ date('Y-m-d') }}"
                            class="form-control form-control-sm">
                    </div>
                    <div class="input-group">
                        <label for="to_date" class="input-group-text">To:</label>
                        <input wire:model="toDate" type="date" id="to_date" name="to_date" value="{{ date('Y-m-d') }}"
                            class="form-control form-control-sm">
                            <button wire:click="search" class="btn btn-primary input-group-text">search</button>
                    </div>
                    <div>
                    </div>
                </div>
            </div>
            </div>
        </div>


        <div class="card-body d-sm-flex">
                <table class="table table-striped table-hover table-responsive-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Reference</th>
                            <th>SKU</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>PO Status</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($backorderList as $backOrder)
                            <tr>
                                <td>{{ $backOrder->requisition->requisition_number }}</td>
                                <td>{{ $backOrder->item->item_code }}</td>
                                <td>{{ $backOrder->bo_type }}</td>
                                <td>{{ $backOrder->created_at }}</td>
                                <td>
                                    <span class="
                                        @if($backOrder->status == 'ACTIVE') badge bg-danger
                                        @elseif($backOrder->status == 'FULLFILLED') badge bg-success
                                        @elseif($backOrder->status == 'FOR PO') badge bg-warning
                                        @elseif($backOrder->status == 'CANCELLED') badge bg-secondary 
                                        @else badge bg-secondary 
                                        @endif">{{ $backOrder->status }} 
                                    </span>
                                </td>
                                <td>{{ $backOrder->remarks }}</td>
                                <td>
                                    <a href="{{ route('po.show', ['id' => $backOrder->id]) }}" class="btn btn-primary btn-sm">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No back orders found.</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
        </div>
    </div>
</div>



