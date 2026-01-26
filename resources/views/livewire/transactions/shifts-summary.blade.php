<div>
    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboards') }}
        </h2>
    </x-slot>
    <div class="container mb-3">
        <div class="row">
            <div class="col-md-6">
               
                @if(auth()->user()->employee->getModulePermission('Shift Summary') == 1 )
                     <x-primary-button>Print <i class="bi bi-printer"></i></x-primary-button>
                    <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                @endif
                <x-secondary-button wire:click="fetchData">Refresh &nbsp;<i class="bi bi-arrow-clockwise"></i></x-secondary-button>
            </div>
            <div class="col-md-6">
                <h4 class="text-end">Cashier Shift Summary &nbsp;<i class="bi bi-journal-text"></i></h4>
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
                            <button class="btn btn-warning btn-sm w-9 h-8 " wire:click="filterShiftsByDate"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body overflow-auto">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 table-sm border-radius-15">
                    <thead class="table-dark">
                        <tr>
                            <th style="position: sticky; top: 0; font-size: small;">Reference</th>
                            <th style="position: sticky; top: 0; font-size: small;">Date</th>
                            <th style="position: sticky; top: 0; font-size: small;">Cashier</th>
                            <th style="position: sticky; top: 0; font-size: small;">Drawer</th>
                            <th style="position: sticky; top: 0; font-size: small;">Status</th>
                            <th style="position: sticky; top: 0; font-size: small;">Beginning Balance</th>
                            <th style="position: sticky; top: 0; font-size: small;">Ending Balance</th>
                            <th style="position: sticky; top: 0; font-size: small;">Remarks</th>
                            @if(auth()->user()->employee->getModulePermission('Shift Summary') == 1 )
                                <th style="position: sticky; top: 0; font-size: small;">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shifts as $shift)
                            <tr>
                                <td style="font-size: smaller;">{{ $shift->reference }}</td>
                                <td style="font-size: smaller;">{{ $shift->created_at->format('Y-m-d H:i:s') }}</td>
                                <td style="font-size: smaller;">{{ $shift->employee->name }} {{ $shift->employee->last_name }}</td>
                                <td style="font-size: smaller;">{{ $shift->cashDrawer->drawer_name }}</td>
                                <td style="font-size: smaller;">
                                    @if ($shift->shift_status == 'OPEN')
                                        <span class="badge bg-success">Open</span>
                                    @else
                                        <span class="badge bg-secondary">Closed</span>
                                    @endif
                                </td>
                                <td style="font-size: smaller;">₱ {{ number_format($shift->starting_cash, 2) }}</td>
                                <td style="font-size: smaller;">₱ {{ number_format($shift->ending_cash, 2) ?? 'N/A' }}</td>
                                <td style="font-size: smaller;">{{ $shift->notes ?? 'N/A' }}</td>
                                @if(auth()->user()->employee->getModulePermission('Shift Summary') == 1 )
                                <td style="font-size: smaller;">
                                    <button class="btn btn-info btn-sm">View Details <i class="bi bi-eye"></i></button>
                                </td>
                                @endif
                            </tr>
                            
                        @empty
                            
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
    {{-- <div class="modal fade modal-lg" id="supplierViewModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierModalLabel">Invoice Details &nbsp;<i class="bi bi-info-circle"></i></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                   
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
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#ViewDiscountsModal" title="View Info">
                                  <i class="bi bi-info-lg"></i>
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
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#ViewPaments" title="View Info"> 
                                   <i class="bi bi-info-lg"></i>
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

                                     
                                        
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer text-right">
                                <div class="d-flex justify-content-between">
                                    <h6>AMOUNT DUE:</h6>
                                    <h6>{{ $grossAmount }}</h6>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h6>AMOUNT PAID:</h6>
                                    <h6>{{ $totalAmountDue }}</h6>
                                </div>
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
    </div> --}}
    <!-- End Modal view-->

    {{-- PAYMENTS MODAL --}}
    <div class="modal fade modal-sm " id="ViewPaments" tabindex="-1" aria-labelledby="ViewPaymentsLabel" aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ViewPaymentsLabel">Payments &nbsp;<i class="bi bi-info-circle"></i></h5>
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
                    {{--  --}}
                    </tbody>

                   </table>

                </div>
            </div>
        </div>
    </div>
    {{-- END PAYMENTS MODAL --}}

    {{-- DISCOUNTS MODAL --}}
    <div class="modal fade modal-lg" id="ViewDiscountsModal" tabindex="-1" aria-labelledby="ViewDiscountsLabel" aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ViewDiscountsLabel">Discount Details &nbsp;<i class="bi bi-info-circle"></i></h5>
                    <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#supplierViewModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Discount Details Content -->
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th style="font-size: smaller;">Discount Name</th>
                                <th style="font-size: smaller;">Description</th>
                                <th style="font-size: smaller;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                          {{--  --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- END DISCOUNTS MODAL --}}
</div>