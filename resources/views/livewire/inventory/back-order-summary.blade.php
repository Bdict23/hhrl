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
                            <option value="FULFILLED">FULFILLED</option>
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


        <div class="card-body table-responsive-sm">
                <table class="table table-striped table-hover table-sm">
                    <thead class="table-dark table-sm table-sm">
                        <tr>
                            <th>PO NO.</th>
                            <th>M.PO NO.</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>PO Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($requisitionList as $backOrder)
                            <tr>
                                <td>{{ $backOrder->requisition_number }}</td>
                                <td>{{ $backOrder->merchandise_po_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($backOrder->created_at)->format('d, M, Y') }}</td>
                                <td>{{ $backOrder->category}}</td>
                                <td>
                                    <span class="
                                        @if($backOrder->requisition_status == 'PARTIALLY FULFILLED') badge bg-warning text-dark
                                        @elseif($backOrder->requisition_status == 'COMPLETED') badge bg-success
                                        @elseif($backOrder->requisition_status == 'FOR PO') badge bg-info
                                        @elseif($backOrder->requisition_status == 'CANCELLED') badge bg-secondary 
                                        @else badge bg-secondary 
                                        @endif">{{ $backOrder->requisition_status == 'PARTIALLY FULFILLED' ? 'PARTIAL' : $backOrder->requisition_status }} 
                                    </span>
                                </td>
                                <td>
                                    <a wire:click="showBackorder('{{ $backOrder->requisition_number}}')" ><x-primary-button> <u>View</u> </x-primary-button></a>
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



