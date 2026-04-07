<div class="overflow-x-auto">
    <div class="container mb-3">
            <div class="row">
                <div class="col-md-6">
                    @if(auth()->user()->employee->getModulePermission('Advances For Liquidation') == 1 )
                        <a href="{{ route('advances-for-liquidation-create') }}" style="text-decoration: none; color: white;"><x-primary-button >+ New AFL</x-primary-button></a>
                        <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4 class="text-end">Advances For Liquidation - Summary <i class="bi bi-file-text"></i></h4>
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
                                <th>Date Created</th>
                                <th>Status</th>
                                <th>Amount Received</th>
                                <th>Amount Returned</th>
                                <th>Prepared By</th>
                                <th>Received By</th>
                                <th>Approved By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($advancesForLiquidation as $afl)
                                <tr>
                                    <td>{{ $afl->reference }}</td>
                                    <td>{{ \Carbon\Carbon::parse($afl->created_at)->format('M. d, Y') }}</td>
                                    <td>
                                        <span @if($afl->status == 'DRAFT') class="badge bg-secondary" 
                                            @elseif($afl->status == 'OPEN') class="badge bg-warning" 
                                            @elseif($afl->status == 'CLOSED') class="badge bg-success" 
                                            @elseif($afl->status == 'CANCELLED') class="badge bg-danger" 
                                            @endif>{{ $afl->status }}</span>
                                        
                                    </td>
                                    <td>{{ number_format($afl->amount_received, 2) }}</td>
                                    <td>{{ number_format($afl->amount_returned, 2) }}</td>
                                    <td>{{ $afl->preparer->name }} {{ $afl->preparer->last_name }}</td>
                                    <td>{{ $afl->disburser->name }} {{ $afl->disburser->last_name }}</td>
                                    <td>{{ $afl->approver->name }} {{ $afl->approver->last_name }}</td>
                                    <td>
                                        <a href="/advances-for-liquidation?AFL-id={{ $afl->id }}"><x-primary-button class="btn-sm">View</x-primary-button></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No advances for liquidation found</td>
                                </tr>
                            @endforelse
                    
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>



