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
                            <td>{{ $withdrawal->withdrawal_status }}</td>
                            <td>
                                <a href="{{ route('withdrawal.view', $withdrawal->id) }}" class="btn btn-info btn-sm"
                                    id="viewAndEdit">View</a>
                                <a href="{{ route('withdrawal.edit', $withdrawal->id) }}"
                                    class="btn btn-primary btn-sm">Edit</a>
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
@endsection
