<div class="overflow-x-auto">
    <div class="container mb-3">
            <div class="row">
                <div class="col-md-6">
                    @if(auth()->user()->employee->getModulePermission('Acknowledgement Receipt') == 1 )
                        <a href="" style="text-decoration: none; color: white;"><x-primary-button >+ Create</x-primary-button></a>
                        <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4 class="text-end">Cash Flow - Summary <i class="bi bi-file-text"></i></h4>
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
                                    <option value="ALL">ALL</option>
                                    <option value="OPEN">DRAFT</option>
                                    <option value="CLOSED">CLOSED</option>
                                    <option value="CANCELLED">CANCELLED</option>
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
                                <th>Ref.</th>
                                <th>Status</th>
                                <th>CF Date</th>
                                <th>Created By</th>
                                <th>Amount</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cashFlows as $cf)
                                <tr>
                                    <td>{{ $cf->reference }}</td>
                                    <td> <span 
                                        @if( $cf->status =='DRAFT' ) class = "badge bg-secondary" 
                                        @elseif($cf->status =='CANCELLED') class= "badge bg-danger" 
                                        @elseif($cf->status =='FINAL') class="badge bg-success" 
                                        @endif>{{ $cf->status }}</span> </td>
                                    <td>{{ $cf->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $cf->createdBy->name ?? '' }}</td>
                                    <td>{{ number_format($cf->amount, 2) }}</td>
                                    <td>{{ $cf->remarks }}</td>
                                    <td>
                                        <a href="\cashflow-view?reference_id={{ $cf->id }}" >
                                           <a href="\cashflow-view?reference_id={{ $cf->id }}"><x-primary-button class="btn-sm">View</x-primary-button></a>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No records found</td>
                                </tr>
                            @endforelse
                    
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>



