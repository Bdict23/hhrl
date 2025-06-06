<div class="dashboard">
    {{-- Page Header --}}

    {{-- Page Title --}}
    <h2 class="mb-4">Withdrawal Summary</h2>

    {{-- Search Bar --}}
    <div class="row">
        <div class="col-md-6">
            @if (auth()->user()->employee->getModulePermission('Item Withdrawal') == 1 )
                <div>
                <a href="{{ route('withdrawal.index') }}" class="btn btn-primary">Create New Withdrawal</a>
                </div>
            @endif
            
        </div>
        <div class="d-flex justify-content-end mb-3 col-md-6">
            <input type="text" name="search" class="form-control me-2" placeholder="Search..."
                value="{{ request('search') }}">
        </div>
    </div>


    {{-- Withdrawal Summary Table --}}
    <div class="overflow-x-auto">
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