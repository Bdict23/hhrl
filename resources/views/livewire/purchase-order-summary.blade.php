<div class="overflow-x-auto">
@if (session('status') == 'error')
    <div class="alert alert-danger">
        {{ session('message') ?? 'Something went wrong.' }}
    </div>
@endif
    <div class="card mt-3 mb-3">
                <div class=" mb-2 mt-2 d-flex justify-content-start">
                    <div class="col-md-12">
                        @if(auth()->user()->employee->getModulePermission('Purchase Order') == 1 )
                                <a href="{{ route('po.create') }}" type="button" class="btn btn-success btn-sm ml-2">+New Purchase Order</a>
                        @endif
                    </div>
                    <div class="d-flex justify-content-end">
                        <span wire:loading class="spinner-border text-primary" role="status"></span>
                    </div>
                </div>
            <div class="row d-flex justify-content-between mx-2">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="input-group">
                                <label for="PO-status" class="input-group-text">Status</label>
                                <select wire:model="statusPO" id="PO-status"  class="form-select form-select-sm">
                                    <option value="All">All</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="input-group">
                                <label for="from_date" class="input-group-text">From:</label>
                                <input wire:model="fromDate" type="date" id="from_date" name="from_date" value="{{ date('Y-m-d') }}"
                                    class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="input-group">
                                <label for="to_date" class="input-group-text">To:</label>
                                <input wire:model="toDate" type="date" id="to_date" name="to_date" value="{{ date('Y-m-d') }}"
                                    class="form-control form-control-sm">
                                <button wire:click="search" class="btn btn-primary input-group-text">search</button>  
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        <div class="card-body ">
                <div style="height: 500px; overflow-x: auto; display: block;">
                    <table class="table table-striped table-hover table-sm " >
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
                            @forelse ($purchaseOrderSummary as $requisition)
                                <tr>
                                    <td>{{ $requisition->supplier->supp_name ?? 'N/A' }}</td>
                                    <td>{{ $requisition->requisition_number }}</td>
                                    <td>{{ $requisition->trans_date }}</td>
                                    <td>{{ $requisition->preparer->name }}</td>
                                    <td>
                                        <span class="
                                        @if($requisition->requisition_status == 'PREPARING') badge bg-secondary
                                        @elseif($requisition->requisition_status == 'FOR REVIEW') badge bg-dark
                                        @elseif($requisition->requisition_status == 'FOR APPROVAL') badge bg-dark
                                        @elseif($requisition->requisition_status == 'TO RECIEVE') badge bg-primary 
                                        @elseif($requisition->requisition_status == 'PARTIALLY FULFILLED') badge bg-warning text-dark 
                                        @elseif($requisition->requisition_status == 'COMPLETED') badge bg-success 
                                        @elseif($requisition->requisition_status == 'REJECTED') badge bg-danger
                                        @elseif($requisition->requisition_status == 'CANCELLED') badge bg-danger 
                                        @else badge bg-secondary 
                                        @endif"> {{ $requisition->requisition_status  == 'PARTIALLY FULFILLED' ? 'PARTIAL' : $requisition->requisition_status  }}
                                    </span>
                                       </td>
                                    <td>{{ $requisition->remarks }}</td>
                                    <input id="company_id" name='company_id' type="hidden">
                                    <td>
                                        <a style="text-decoration: none" href="{{ route('po.show', ['id' => $requisition->id]) }}">
                                            <x-primary-button class="button-group"><u>View</u></x-primary-button>
                                        <a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No purchase order found</td>
                                </tr>
                            @endforelse
                    
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>



