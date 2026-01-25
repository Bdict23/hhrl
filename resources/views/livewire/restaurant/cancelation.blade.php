<div>
    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboards') }}
        </h2>
    </x-slot>
    <div class="container mb-3">
        <div class="row">
            <div class="col-md-6">
                <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                <x-secondary-button wire:click="fetchData">Refresh &nbsp;<i class="bi bi-arrow-clockwise"></i></x-secondary-button>
            </div>
            <div class="col-md-6">
                <h4 class="text-end">Cancelation Summary</h4>
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
                            <button class="btn btn-warning btn-sm w-9 h-8 " wire:click="filterCancelationByDate"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body overflow-auto">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th style="position: sticky; top: 0; font-size: small;">Created</th>
                            <th style="position: sticky; top: 0; font-size: small;">Sys. Reference</th>
                            <th style="position: sticky; top: 0; font-size: small;">Type</th>
                            <th style="position: sticky; top: 0; font-size: small;">Canceled By</th>
                            <th style="position: sticky; top: 0; font-size: small;">Amount</th>
                            <th style="position: sticky; top: 0; font-size: small;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices ?? [] as $invoice)
                            <tr>
                                <td style="font-size: small;">{{ $invoice->created_at->format('m/d/Y') }}</td>
                                <td style="font-size: small;">{{ $invoice->reference ?? '' }}</td>
                                <td style="font-size: small;">{{ $invoice->invoice_number }}</td>
                                <td style="font-size: small;">
                                    {{ $invoice->customer->customer_name ?? $invoice->customer_name }}
                                </td>
                                <td style="font-size: small;"  @if ($invoice->amount <= 0) class="text-danger" @endif>
                                    {{ number_format($invoice->amount, 2) }}
                                <td> 
                                    @php
                                        $latestSrpPrice = [];
                                        foreach ($invoice->order->ordered_items as $detail) {
                                            $latestSrpPrice[] =
                                                $detail->menu
                                                    ->price_levels()
                                                    ->latest()
                                                    ->where('price_type', 'SRP')
                                                    ->first()->amount ?? '0.00';
                                        }
                                    @endphp

                                    <div class="button-group">
                                        <x-primary-button
                                            {{-- onclick="selectOrder({{ json_encode($invoice) }},{{ json_encode($latestSrpPrice) }})" --}}
                                            data-bs-target="#supplierViewModal" data-bs-toggle="modal"
                                            wire:click="viewInvoiceDetails({{ $invoice->order->id }}, {{ $invoice->id }})"
                                            title="View Invoice Details">
                                            <i class="bi bi-info-circle"></i>
                                        </x-primary-button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center" style="font-size: small;">No sales transactions found for the selected date range.</td>
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

     <div wire:loading wire:target="filterInvoicesByDate" class="me-auto" style="margin-top: 10px;">
        <span class="spinner-border text-primary" role="status"></span>
        &nbsp;&nbsp; Fetching Data Please Wait...
    </div>
    <div wire:loading wire:target="fetchData" class="me-auto" style="margin-top: 10px;">
        <span class="spinner-border text-primary" role="status"></span>
        &nbsp;&nbsp; Refreshing Please Wait...
    </div>



    
</div>