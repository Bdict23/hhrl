<div class="overflow-x-auto">
    <div class="container mb-3">
            <div class="row">
                <div class="col-md-6">
                    @if(auth()->user()->employee->getModulePermission('Acknowledgement Receipt') == 1 )
                        <a href="{{ route('acknowledgement_receipt.create') }}" style="text-decoration: none; color: white;"><x-primary-button >+ New AR</x-primary-button></a>
                        <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4 class="text-end">Acknowledgement Receipt Summary <i class="bi bi-file-text"></i></h4>
                </div>
            </div>
        </div>
        <div class="card mt-3 mb-3">  
            <div class=" card-header d-flex justify-content-between mx-2">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="input-group">
                                <label for="CHECK-status" class="input-group-text">Status</label>
                                <select wire:model="statusCheckValue" id="CHECK-status"  class="form-select form-select-sm">
                                    @foreach ($statusCheckOptions as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option>
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
                                <button wire:click="search" class="btn btn-primary input-group-text">
                                    <span wire:loading.remove>Search <i class="bi bi-search"></i></span>
                                    <span wire:loading>Searching&nbsp;<span class="spinner-border spinner-border-sm" role="status"></span></span>
                                </button>  
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
                                <th>REF.</th>
                                <th>AR Status</th>
                                <th>Source</th>
                                <th>CH. Status</th>
                                <th>CH. Date</th>
                                <th>ACCT. NAME</th>
                                <th>CH. NUMBER</th>
                                <th>Amount</th>
                                <th>Bank</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($acknowledgementReceipts as $receipt)
                                <tr>
                                    <td>{{ $receipt->reference }}</td>
                                    <td> <span 
                                        @if( $receipt->status =='OPEN' ) class = "badge bg-warning" 
                                        @elseif($receipt->status =='CANCELLED') class= "badge bg-danger" 
                                        @elseif($receipt->status =='CLOSED') class="badge bg-success"
                                        @else class="badge bg-secondary" 
                                        @endif>{{ $receipt->status }}</span> </td>
                                    <td>{{ $receipt->customer->customer_fname ?? '' }} {{ $receipt->customer->customer_lname ?? '' }}</td>
                                    <td>{{ $receipt->check_status }}</td>
                                    <td>{{ $receipt->check_date }}</td>
                                    <td>{{ $receipt->account_name }}</td>
                                    <td>{{ $receipt->check_number }}</td>
                                    <td>{{ number_format($receipt->check_amount, 2) }}</td>
                                    <td>{{ $receipt->bank->bank_name ?? '' }}</td>
                                    <td>
                                        <a href="\acknowledgement-receipt-view?reference={{ $receipt->reference }}" >
                                           <a href="/acknowledgement-receipt-create?AR-id={{ $receipt->id }}"><x-primary-button class="btn-sm">View</x-primary-button></a>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No acknowledgement receipt found</td>
                                </tr>
                            @endforelse
                    
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>



