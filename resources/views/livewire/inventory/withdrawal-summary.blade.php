<div>
    <div class="container mb-3">
        <div class="row">
            <div class="col-md-6">
               @if (auth()->user()->employee->getModulePermission('Item Withdrawal') == 1 )
                    <a href="{{ route('withdrawal.index') }}" style="text-decoration: none; color: white;"><x-primary-button >+ New Withdrawal</x-primary-button></a>
                    <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                @endif
                <div class="d-flex justify-content-end">
                    <span wire:loading class="spinner-border text-primary" role="status"></span>
                </div>
            </div>
            <div class="col-md-6">
                <h4 class="text-end">Withdrawal Summary <i class="bi bi-dropbox"> </i></h4>
            </div>
        </div>
    </div>


    {{-- Withdrawal Summary Table --}}
    <div class="card">
        
            <div class=" card-header d-flex justify-content-between mx-2">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="input-group">
                                <label for="PO-status" class="input-group-text">Status</label>
                                <select wire:model="selectedStatus" id="PO-status"  class="form-select form-select-sm">
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
           <div class="card-body overflow-x-auto" style="max-height: 400px;">
             <table class="table table-hover table-bordered table-striped table-sm" style="display: height : 400px; overflow-x: auto;">
                 <thead class="table-dark table-hover ">
                     <tr>
                         <th>Reference Number</th>
                         <th>Department</th>
                         <th>Usage Date</th>
                         <th>Approved By</th>
                         <th>Reviewed By</th>
                         <th>Prepared By</th>
                         <th>Status</th>
                         <th>Actions</th>
                     </tr>
                 </thead>
                 <tbody>
                     @forelse ($withdrawals as $withdrawal)
                         <tr>
                             <td>{{ $withdrawal->reference_number }}</td>
                             <td>{{ $withdrawal->department->department_name ?? 'N/A' }}</td>
                             <td>{{ \Carbon\Carbon::parse($withdrawal->usage_date)->format('M. d, Y') }}</td>
                             <td>{{ $withdrawal->approvedBy->name ?? 'N/A' }}</td>
                             <td>{{ $withdrawal->reviewedBy->name ?? 'N/A' }}</td>
                             <td>{{ $withdrawal->preparedBy->name ?? 'N/A' }}</td>
                             <td>
                                 <span class="badge bg-{{ $withdrawal->withdrawal_status == 'APPROVED' ? 'success' : ($withdrawal->withdrawal_status == 'PREPARING' ? 'secondary' : 'info') }}">
                                     {{ ucfirst($withdrawal->withdrawal_status) }}
                                 </span>
                             </td>
                             <td>
                                 <a wire:click="viewWithdrawal({{$withdrawal->id}})" class="btn btn-info btn-sm"
                                     id="viewAndEdit">View</a>
                                 
                             </td>
                         </tr>
                     @empty
                         <tr>
                             <td colspan="8" class="text-center">No withdrawals found.</td>
                         </tr>
                     @endforelse
                 </tbody>
             </table>
           </div>
    </div>
</div>