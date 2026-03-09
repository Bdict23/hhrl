<div class="overflow-x-auto">
    <div class="container mb-3">
            <div class="row">
                <div class="col-md-6">
                    @if(auth()->user()->employee->getModulePermission('Petty Cash Voucher') == 1 )
                        <a href="{{ route('petty_cash_voucher.create') }}" style="text-decoration: none; color: white;"><x-primary-button >+ New PCV</x-primary-button></a>
                        <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4 class="text-end">Petty Cash Voucher - Summary <i class="bi bi-file-text"></i></h4>
                </div>
            </div>
        </div>
        <div class="card mt-3 mb-3">  
            <div class=" card-header d-flex justify-content-between mx-2">
                <div class="col-md-12">
                    <div class="row">
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
                                <th>PCV No.</th>
                                <th>AR Ref.</th>
                                <th>Paid To</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Prepared By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pettyCashVouchers as $pcv)
                                <tr>
                                    <td>{{ $pcv->reference }}</td>
                                    <td>{{ $pcv->voucher_number }}</td>
                                    <td>{{ $pcv->acknowledgementReceipt->reference }}</td>
                                    <td>{{ $pcv->customer->customer_fname . ' ' . $pcv->customer->customer_lname ?? $pcv->employee->name . ' ' . $pcv->employee->last_name }}</td>
                                    <td>{{ number_format($pcv->total_amount, 2) }}</td>
                                    <td><span 
                                        @if( $pcv->status =='OPEN' ) class = "badge bg-warning" 
                                        @elseif($pcv->status =='CANCELLED') class= "badge bg-danger" 
                                        @elseif($pcv->status =='CLOSED') class="badge bg-success"
                                        @else class="badge bg-secondary" 
                                        @endif>{{ $pcv->status }}</span> 
                                    </td>
                                    <td>{{ $pcv->created_at->format('M. d, Y') }}</td>
                                    <td>{{ $pcv->preparedBy->name . ' ' . $pcv->preparedBy->last_name ?? '' }}</td>
                                    <td>
                                        <a href="/pcv-edit-view?PCV-id={{ $pcv->id }}"><x-primary-button class="btn-sm">View</x-primary-button></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No petty cash voucher found</td>
                                </tr>
                            @endforelse
                    
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>



