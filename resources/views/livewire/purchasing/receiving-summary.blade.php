<div class="content-fluid">

    <div class="card mt-3 mb-3">
        <div class="card-header p-2">
            <div class="row">
                <div class=" row col-md-6">
                    <div class="col-md-6">
                        <h5>RECEVING SUMMARY</h5>
                    </div>
                    <div class="col-md-6">
                        <span wire:loading class="spinner-border text-primary" role="status"></span>
                    </div>
                </div>

                <div class="col-md-6">
                <div class="d-flex">

                    <div class="input-group">
                        <label for="from_date" class="input-group-text">From:</label>
                        <input wire:model="fromDate" type="date" id="from_date" name="from_date" value="{{ date('Y-m-d') }}"
                            class="form-control form-control-sm">
                    </div>
                    <div class="input-group">
                        <label for="to_date" class="input-group-text">To:</label>
                        <input wire:model="toDate" type="date" id="to_date" name="to_date" value="{{ date('Y-m-d') }}"
                            class="form-control form-control-sm">
                            <button wire:click="search" class="btn btn-warning input-group-text">search</button>
                    </div>
                    <div>
                    </div>
                </div>
            </div>
            </div>
        </div>


        <div class="card-body d-sm-flex">
                <table class="table  table-responsive-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Order To</th>
                            <th>Order Number</th>
                            <th>Receiving No.</th>
                            <th>Order Date</th>
                            <th>Prepared By</th>
                            <th>PO Status</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                       @forelse ( $receivingSummaryList as $receivingSummary )
                        <tr>
                            <td>{{ $receivingSummary->requisition->supplier->supp_name }}</td>
                            <td>{{ $receivingSummary->requisition->requisition_number }}</td>
                            <td>{{ $receivingSummary->RECEIVING_NUMBER }}</td>
                            <td>{{ $receivingSummary->created_at }}</td>
                            <td>{{ $receivingSummary->preparedBy->name }}</td>
                            <td>{{ $receivingSummary->RECEIVING_STATUS }}</td>
                            <td>{{ $receivingSummary->remarks }}</td>
                            <td>
                                <button wire:click="openReceivingNumber('{{ $receivingSummary->RECEIVING_NUMBER }}',{{ $receivingSummary->requisition->id }})" class="btn btn-primary btn-sm">View</button>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No data available</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
        </div>
    </div>
</div>



