{{-- filepath: c:\xampp\htdocs\training2024\resources\views\inventory\withdrawal_summary.blade.php --}}
@extends('layouts.master')

@section('content')
    <div class="container dashboard">

        {{-- Page Title --}}
        <h2 class="mb-4">Withdrawal Summary</h2>

        {{-- Search Bar --}}
        <div class="row">
            <div class="col-md-6">
                <div>
                    <a href="{{ route('withdrawal.index') }}" class="btn btn-primary">Create New Withdrawal</a>
                </div>
            </div>
            <div class="d-flex justify-content-end mb-3 col-md-6">
                <input type="text" name="search" class="form-control me-2" placeholder="Search..."
                    value="{{ request('search') }}">
            </div>
        </div>


        {{-- Withdrawal Summary Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Reference Number</th>
                        <th>Department</th>
                        <th>Usage Date</th>
                        <th>Approved By</th>
                        <th>Reviewed By</th>
                        <th>Prepared By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($withdrawals as $withdrawal)
                        <tr>
                            <td>{{ $withdrawal->reference_number }}</td>
                            <td>{{ $withdrawal->department->department_name ?? 'N/A' }}</td>
                            <td>{{ $withdrawal->usage_date }}</td>
                            <td>{{ $withdrawal->approvedBy->name ?? 'N/A' }}</td>
                            <td>{{ $withdrawal->reviewedBy->name ?? 'N/A' }}</td>
                            <td>{{ $withdrawal->preparedBy->name ?? 'N/A' }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#viewWithdrawalModal{{ $withdrawal->id }}">View</button>
                                <a href="{{ route('withdrawal.edit', $withdrawal->id) }}"
                                    class="btn btn-primary btn-sm">Edit</a>
                            </td>
                        </tr>

                        {{-- View Modal --}}
                        <div class="modal fade" id="viewWithdrawalModal{{ $withdrawal->id }}" tabindex="-1"
                            aria-labelledby="viewWithdrawalModalLabel{{ $withdrawal->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewWithdrawalModalLabel{{ $withdrawal->id }}">
                                            Withdrawal Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body ">
                                        {{-- <p><strong>Reference Number:</strong> {{ $withdrawal->reference_number }}</p>
                                        <p><strong>Department:</strong> {{ $withdrawal->department->name ?? 'N/A' }}</p>
                                        <p><strong>Usage Date:</strong> {{ $withdrawal->usage_date }}</p>
                                        <p><strong>Approved By:</strong> {{ $withdrawal->approvedBy->name ?? 'N/A' }}</p>
                                        <p><strong>Reviewed By:</strong> {{ $withdrawal->reviewedBy->name ?? 'N/A' }}</p>
                                        <p><strong>Prepared By:</strong> {{ $withdrawal->preparedBy->name ?? 'N/A' }}</p>
                                        <hr>
                                        <h5>Items</h5>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Item Name</th>
                                                    <th>Quantity</th>
                                                    <th>Cost Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                 @foreach ($withdrawal->cardex->item as $itemx)
                                                    <tr>
                                                        <td>{{ $itemx->name }}</td>
                                                        <td>{{ $itemx->pivot->qty_out }}</td>
                                                        <td>{{ $itemx->pivot->price_level_id }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table> --}}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No withdrawals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
