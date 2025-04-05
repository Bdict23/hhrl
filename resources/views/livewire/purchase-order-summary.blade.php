<div class="content-fluid">
    
    <div class="card mt-3 mb-3">
        <div class="card-header p-2">
            <div class="row">
                <div class="col-md-6">
                    <x-primary-button style="text-decoration: none;">
                        <a href="{{ route('po.create') }}" style="text-decoration: none; color: inherit;">+ Create Purchase Order</a>
                    </x-primary-button>
                </div>
                <div class="col-md-6">

               
                <div class="d-flex">
                    <div class="input-group">
                        <label for="from_date" class="input-group-text">From:</label>
                        <input type="date" id="from_date" name="from_date" value="{{ date('Y-m-d') }}"
                            class="form-control form-control-sm">
                    </div>
                    <div class="input-group">
                        <label for="to_date" class="input-group-text">To:</label>
                        <input type="date" id="to_date" name="to_date" value="{{ date('Y-m-d') }}"
                            class="form-control form-control-sm">
                            <button class="btn btn-warning input-group-text">search</button>
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
                            <th>Order To</th>
                            <th>Order Number</th>
                            <th>Order Date</th>
                            <th>Prepared By</th>
                            <th>PO Status</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchaseOrderSummary as $requisition)
                            <tr>
                                <td>{{ $requisition->supplier->supp_name ?? 'N/A' }}</td>
                                <td>{{ $requisition->requisition_number }}</td>
                                <td>{{ $requisition->trans_date }}</td>
                                <td>{{ $requisition->preparer->name }}</td>
                                <td>{{ $requisition->requisition_status }}</td>
                                <td>{{ $requisition->remarks }}</td>
                                <input id="company_id" name='company_id' type="hidden">
                                <td>
                                    <x-primary-button class="button-group">
                                        <a style="text-decoration: none"
                                            href="{{ route('po.show', ['id' => $requisition->id]) }}">View<a>
                                    </x-primary-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>    
        </div>
    </div>
</div>



