<div>
    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboards') }}
        </h2>
    </x-slot>
    <div class="container mb-3">
        <div class="row">
            <div class="col-md-6">
                <x-primary-button>Print <i class="bi bi-printer"></i></x-primary-button>
                <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                <x-secondary-button wire:click="fetchData">Refresh &nbsp;<i class="bi bi-arrow-clockwise"></i></x-secondary-button>
            </div>
            <div class="col-md-6">
                <h4 class="text-end">Daily Sales Report</h4>
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
                            <button class="btn btn-warning btn-sm w-9 h-8 " wire:click="filterInvoicesByDate"><i class="bi bi-search"></i></button>
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
                            <th style="position: sticky; top: 0; font-size: small;">Date</th>
                            <th style="position: sticky; top: 0; font-size: small;">Invoice No</th>
                            <th style="position: sticky; top: 0; font-size: small;">Customer Name</th>
                            <th style="position: sticky; top: 0; font-size: small;">Amount</th>
                            <th style="position: sticky; top: 0; font-size: small;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td style="font-size: small;">{{ $invoice->created_at->format('m/d/Y') }}</td>
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
                                            wire:click="viewInvoiceDetails({{ $invoice->order->id }})">
                                            <i class="bi bi-binoculars"></i>
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



    <!-- Modal view -->
    <div class="modal fade modal-lg" id="supplierViewModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierModalLabel">Invoice Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form -->
                    <form>
                        @csrf
                        <div class="row mb-1">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="customer_name" wire:model="customer_name">
                            </div>
                            <div class="col-md-6">
                                <label for="discount" class="form-label"
                                    style="font-size: smaller;">Total Discount Applied</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm" id="discount" name="discount"
                                        min="0"  readonly value="₱ {{ number_format($totalDiscountAmount, 2) }}" disabled
                                        style="text-align: center;">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#ViewDiscountsModal">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="table" class="form-label">Table</label>
                                <input type="text" class="form-control" id="table_name" wire:model="table_name">
                            </div>
                            <div class="col-md-4">
                                <label for="order_number" class="form-label">Order No</label>
                                <input type="text" class="form-control text-center" id="order_number" wire:model="order_number">
                            </div>
                             <div class="col-md-4">
                                <label for="discount" class="form-label"
                                    style="font-size: smaller;">Mode of Payment</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm" id="paymentMethod" name="mode_of_payment"
                                        min="0"  readonly disabled style="text-align: center;" wire:model="paymentMethod">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#ViewPaments"> 
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                Order Details
                            </div>
                            <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                                <table class="table table-striped table-hover me-3">
                                    <thead class="thead-dark me-3">
                                        <tr style="font-size: smaller;">
                                            <th>Menu Name</th>
                                            <th>QTY</th>
                                            <th>PRICE</th>
                                            <th>SUB TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemTableBody">

                                        @foreach ($selectedOrderDetails ?? [] as $details)
                                            <tr>
                                                <td style="font-size: smaller;">{{ $details->menu->menu_name }}</td>
                                                <td style="font-size: smaller;">{{ $details->qty }}</td>
                                                <td style="font-size: smaller;">{{ number_format($details->priceLevel->amount, 2) }}</td>
                                                <td style="font-size: smaller;">
                                                    {{ number_format($details['sub_total'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                        
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer text-right">
                                <h6>Total : 0.00</h6>
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <div wire:loading wire:target="viewInvoiceDetails" class="me-auto">
                        <span class="spinner-border text-primary" role="status"></span>
                        &nbsp; Please Wait...
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
            </form>
        </div>
    </div>
    <!-- End Modal view-->

    {{-- PAYMENTS MODAL --}}
    <div class="modal fade modal-sm " id="ViewPaments" tabindex="-1" aria-labelledby="ViewPaymentsLabel" aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ViewPaymentsLabel">Payments</h5>
                    <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#supplierViewModal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="">
                   <table class="table table-bordered table-hover align-middle mb-0 table-sm">
                    <thead>
                        <tr>
                            <th style="font-size: smaller;">Payment Type</th>
                            <th style="font-size: smaller;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                      @forelse ($payments ?? [] as $payment)
                        <tr>
                            <td style="font-size: smaller;">{{ $payment->payment_type->payment_type_name }}</td>
                            <td style="font-size: smaller;">{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                          
                      @empty
                        <tr>
                            <td colspan="2" class="text-center" style="font-size: smaller;">No payment details available.</td>
                        </tr>
                      @endforelse
                    </tbody>

                   </table>

                </div>
            </div>
        </div>
    </div>
    {{-- END PAYMENTS MODAL --}}

    {{-- DISCOUNTS MODAL --}}
    <div class="modal fade modal-sm" id="ViewDiscountsModal" tabindex="-1" aria-labelledby="ViewDiscountsLabel" aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ViewDiscountsLabel">Discount Details</h5>
                    <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#supplierViewModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Discount Details Content -->
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th style="font-size: smaller;">Discount Name</th>
                                <th style="font-size: smaller;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- END DISCOUNTS MODAL --}}
</div>