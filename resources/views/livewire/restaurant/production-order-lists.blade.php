<div class="overflow-x-auto">
@if (session('status') == 'error')
    <div class="alert alert-danger">
        {{ session('message') ?? 'Something went wrong.' }}
    </div>
@endif
                    <div class="container mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    @if(auth()->user()->employee->getModulePermission('Production Orders') == 1 )
                                        <a href="/create-production" style="text-decoration: none; color: white;"><x-primary-button >+ New Production Order</x-primary-button></a>
                                    @endif
                                    <div class="d-flex justify-content-end">
                                        <span wire:loading class="spinner-border text-primary" role="status"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h4 class="text-end">Production Order Summary <i class="bi bi-basket"> </i></h4>
                                </div>
                            </div>
                        </div>
        <div class="card mt-3 mb-3">  
            <div class=" card-header d-flex justify-content-between mx-2">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="input-group">
                                <label for="PO-status" class="input-group-text">Status</label>
                                <select wire:model="statusPO" id="PO-status"  class="form-select form-select-sm">
                                    <option value="All">All</option>
                                    {{-- @foreach ($statuses as $status)
                                        <option value="{{ $status }}">{{ $status }}</option>
                                    @endforeach --}}
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
                    <table class="table table-hover table-sm " >
                        <thead class="table-dark">
                            <tr>
                                <th>Order Date</th>
                                <th>Order Number</th>
                                <th>Prepared By</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($productionOrders ?? [] as $order)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</td>
                                    <td>{{ $order->reference }}</td>
                                    <td>{{ $order->employee->name }} {{ $order->employee->last_name }}</td>
                                    <td> <i @if($order->status === 'COMPLETED') class="badge bg-success" 
                                        @elseif($order->status === 'PENDING') class=" badge bg-warning"
                                        @elseif($order->status === 'CANCELLED') class=" badge bg-danger"
                                        @else class=" badge bg-secondary" 
                                        @endif>{{ $order->status }}</i></td>
                                    <td>
                                        <a href="/create-production?order_id={{ $order->id }}" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No production order found</td>
                                </tr>
                            @endforelse
                    
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>



