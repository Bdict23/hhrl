<div class="overflow-x-auto">

    <div class="card mt-3 mb-3">
        <div class="card-header p-2 ">
            <div class="row">
                <div class=" row col-md-6">
                    <div class="col-md-6">
                        @if(auth()->user()->employee->getModulePermission('Purchase Order') == 1 )
                            <x-primary-button style="text-decoration: none;">
                                <a href="{{ route('po.create') }}" style="text-decoration: none; color: inherit;">+ Create Purchase Order</a>
                            </x-primary-button>
                        @endif
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
                            <option value="All">All</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
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


        <div class="card-body ">
                <div class="overflow-x-auto" style="display: height: 400px; overflow-x: auto;">
                    <table class="table min-w-full table-striped table-hover">
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



