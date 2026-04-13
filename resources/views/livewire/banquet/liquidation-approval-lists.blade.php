<div>
   <div  wire:ignore.self>
            <x-slot name="header">

                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboards') }}
                </h2>
            </x-slot>
            <div class="container mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <x-secondary-button wire:click="fetchData">Refresh &nbsp;<i class="bi bi-arrow-clockwise"></i></x-secondary-button>
                    </div>
                    <div class="col-md-6">
                        <h4 class="text-end">BEO Liquidation - Approval lists</h4>
                    </div>
                </div>
            </div>
            

            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="row g-2"> <!-- Add 'g-2' for spacing -->
                                <div class="col-md-4 d-flex align-items-center">
                                    <label for="from_date" class="me-2">From:</label>
                                    <input type="date" id="from_date" name="from_date" value="{{ date('Y-m-d') }}"
                                        class="form-control form-control-sm" wire:model="from_date">
                                </div>
                                <div class="col-md-4 d-flex align-items-center">
                                    <label for="to_date" class="me-2">To:</label>
                                    <input type="date" id="to_date" name="to_date" value="{{ date('Y-m-d') }}"
                                        class="form-control form-control-sm" wire:model="to_date">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-warning btn-sm w-9 h-8 " wire:click="filterLiquidationByDate"><i class="bi bi-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body overflow-auto" style="max-height: 450px;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0 table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th style="font-size: smaller;">Status</th>
                                    <th style="font-size: smaller;">Reference</th>
                                    <th style="font-size: smaller;">Liq. Date</th>
                                    <th style="font-size: smaller;">Created By</th>
                                    <th style="font-size: smaller;">Event Name</th>
                                    <th style="font-size: smaller;">Validated</th>
                                    <th style="font-size: smaller;">Approved Date</th>
                                    <th style="font-size: smaller;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($liquidationData as $liquidation)
                                    <tr>
                                        <td style="font-size: smaller;">
                                            @if ($liquidation->status == 'DRAFT')
                                                <span class="badge bg-secondary">DRAFT</span>
                                            @elseif ($liquidation->status == 'OPEN')
                                                <span class="badge bg-success">OPEN</span>
                                            @elseif ($liquidation->status == 'CLOSED')
                                                <span class="badge bg-primary">CLOSED</span>
                                            @elseif ($liquidation->status == 'CANCELLED')
                                                <span class="badge bg-danger">CANCELLED</span>
                                            @endif
                                        </td>
                                        <td style="font-size: smaller;">{{ $liquidation->reference }}</td>
                                        <td style="font-size: smaller;">{{ \Carbon\Carbon::parse($liquidation->created_at)->format('M. d, Y') }}</td>
                                        <td style="font-size: smaller;">{{ $liquidation->creator ? $liquidation->creator->name : 'N/A' }}</td>
                                        <td style="font-size: smaller;">{{ $liquidation->event ? $liquidation->event->event_name : 'N/A' }}</td>
                                        <td style="font-size: smaller;">{{ $liquidation->reviewed_date ? \Carbon\Carbon::parse($liquidation->reviewed_date)->format('M. d, Y') : 'N/A' }}</td>
                                        <td style="font-size: smaller;">{{ $liquidation->approved_date ? \Carbon\Carbon::parse($liquidation->approved_date)->format('M. d, Y') : 'N/A' }}</td>
                                        <td style="font-size: smaller;">
                                            <a href="{{ route('beo.liquidation.approval.view', ['id' => $liquidation->id]) }}" class="btn btn-sm btn-info">View</a>
                                            @if($liquidation->status == 'DRAFT' && auth()->user()->employee->emp_id == $liquidation->created_by)
                                                <a href="{{ route('beo.liquidation.edit', ['id' => $liquidation->id]) }}" class="btn btn-sm btn-primary">Edit</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center" style="font-size: small;">No liquidation transactions found for the selected date range.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <li style="font-size: small; color: red;">{{ $error }}</li>
                @endforeach
            @endif

            <div wire:loading wire:target="filterLiquidationByDate" class="me-auto" style="margin-top: 10px;">
                <span class="spinner-border text-primary" role="status"></span>
                &nbsp;&nbsp; Fetching Data Please Wait...
            </div>
            <div wire:loading wire:target="fetchData" class="me-auto" style="margin-top: 10px;">
                <span class="spinner-border text-primary" role="status"></span>
                &nbsp;&nbsp; Refreshing Please Wait...
            </div>
        </div>
</div>
