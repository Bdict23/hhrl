<div>
   <ul class="nav nav-underline" id="accountingTab" role="tablist" wire:ignore>
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="billing-tab" data-bs-toggle="tab" data-bs-target="#BillingTab" type="button"
                role="tab" aria-controls="BillingTab" aria-selected="true">Payment &nbsp;&nbsp;</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#PaymentsTab" type="button"
                role="tab" aria-controls="PaymentsTab" aria-selected="false">
                Open Tab &nbsp;&nbsp;</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payments-summary-tab" data-bs-toggle="tab" data-bs-target="#PaymentsSummaryTab" type="button"
                role="tab" aria-controls="PaymentsSummaryTab" aria-selected="false">
                Payment Summary</button>
        </li>
    </ul>


     <div class="tab-content" id="accountingTabContent">

        {{-- Payment Tab --}}
        <div class="container tab-pane fade show active" id="BillingTab" role="tabpanel" aria-labelledby="billing-tab" wire:ignore.self>
            <div class="d-flex justify-content-end">
                 <h4 class="text-end">BEO - Payment <i class="bi bi-cash-coin"></i></h4>
            </div>
           <div class="container">
               <div class="row">
                    {{-- left --}}
                    <div class="col-md-6"> 
                        <x-select
                            label="Event" 
                            placeholder="Select event ..."
                            :options="$events"
                            option-value="id"
                            :min-items-for-search="0"
                            option-label="event_name"
                            wire:model.live="selectedEventId"
                        />
                        <div class=" m-1 mt-4" wire:loading.class="opacity-50">
                            <div class="card overflow-auto" style="height: 300px; max-height: 300px; ">
                                <table class="table table-bordered mt-3">
                                        <tbody>
                                            <tr><td colspan="5" class="text-center"><strong>Food</strong></td></tr>
                                                <tr>
                                                <th class="text-xs">Title</th>
                                                <th class="text-xs">Qty</th>
                                                <th class="text-xs">Amount</th>
                                                <th class="text-xs">Less</th>
                                                <th class="text-xs">Total</th>
                                            </tr>
                                            @if($selectedEvent)
                                                @foreach($selectedEvent->eventMenus as $eventMenu)
                                                    @php
                                                        $foodLineTotal = ($eventMenu->price->amount ?? 0) * ($eventMenu->qty ?? 0);
                                                        $foodLineDiscount = $this->getFoodDiscountByMenuId($eventMenu->id);
                                                    @endphp
                                                    <tr>
                                                        <td class="text-xs">{{ $eventMenu->menu->menu_name }}</td>
                                                        <td class="text-xs">{{ $eventMenu->qty }}</td>
                                                        <td class="text-xs">₱ {{ number_format($eventMenu->price->amount, 2) }}</td>
                                                        <td class="text-xs" style="cursor: pointer; text-decoration: underline;" wire:click="setDiscountedFood({{ $eventMenu->id }})">
                                                            ₱ {{ number_format($foodLineDiscount, 2) }}
                                                            <i class="bi bi-tag"></i></td>
                                                        <td class="text-xs">₱ {{ number_format($foodLineTotal - $foodLineDiscount, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            <tr><td colspan="5" class="text-center"><strong>Services and Miscellaneous</strong></td></tr>
                                                <tr>
                                                <th class="text-xs">Title</th>
                                                <th class="text-xs">Qty</th>
                                                <th class="text-xs">Amount</th>
                                                <th class="text-xs">Less</th>
                                                <th class="text-xs">Total</th>
                                            </tr>
                                            @if($selectedEvent)
                                                @foreach($selectedEvent->eventServices as $eventService)
                                                        @php
                                                            $serviceLineTotal = ($eventService->price->amount ?? 0) * ($eventService->qty ?? 0);
                                                            $serviceLineDiscount = $this->getServiceDiscountByServiceId($eventService->id);
                                                        @endphp
                                                    <tr>
                                                        <td class="text-xs">{{ $eventService->service->service_name }}</td>
                                                        <td class="text-xs">{{ $eventService->qty }}</td>
                                                        <td class="text-xs">₱ {{ number_format($eventService->price->amount, 2) }}</td>
                                                        <td class="text-xs" style="cursor: pointer; text-decoration: underline;" wire:click="setDiscountedService({{ $eventService->id }})">₱ {{ number_format($serviceLineDiscount, 2) }} <i class="bi bi-tag"></i></td>
                                                        <td class="text-xs">₱ {{ number_format($serviceLineTotal - $serviceLineDiscount, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                             <tr><td colspan="5" class="text-center"><strong>Venues</strong></td></tr>
                                                <tr>
                                                <th class="text-xs">Title</th>
                                                <th class="text-xs">Qty</th>
                                                <th class="text-xs">Amount</th>
                                                <th class="text-xs">Less</th>
                                                <th class="text-xs">Total</th>
                                            </tr>
                                            @if($selectedEvent)
                                                @foreach($selectedEvent->eventVenues as $eventVenue)
                                                    <tr>
                                                        <td class="text-xs">{{ $eventVenue->venue->venue_name }}</td>
                                                        <td class="text-xs">{{ $eventVenue->qty }}</td>
                                                        <td class="text-xs">₱ {{ number_format($eventVenue->ratePrice->amount, 2) }}</td>
                                                        @php
                                                            $venueLineTotal = ($eventVenue->ratePrice->amount ?? 0) * ($eventVenue->qty ?? 0);
                                                            $venueLineDiscount = $this->getVenueDiscountByVenueId($eventVenue->id);
                                                        @endphp
                                                        <td class="text-xs"  style="cursor: pointer; text-decoration: underline;" wire:click="setDiscountedVenue({{ $eventVenue->id }})">₱ {{ number_format($venueLineDiscount, 2) }} <i class="bi bi-tag"></i></td>
                                                        <td class="text-xs">₱ {{ number_format($venueLineTotal - $venueLineDiscount, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                </table>
                            </div>
                            <hr>
                            <div class="card" style="border: 1px dashed #8d8b8b;">
                                <div class="card-body">
                                    <h5 class="card-title">Summary</h5>
                                    <table class="table table-bordered mt-3">
                                        <tbody>
                                            <tr>
                                            <td>Subtotal</td>
                                            <td>₱ {{ number_format($subTotal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Discount Applied</td>
                                            <td>₱ {{ number_format($payment_totalDiscountAmount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Amount Due</td>
                                            <td>₱ {{ number_format($payment_totalAmountDue, 2) }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                        {{-- right --}}
                    <div class="col-md-6 card p-2 mb-2">
                        <div class="container mb-3">
                            <label for="" class="form-label">Invoice Number</label>
                            <input type="text" class="form-control" placeholder="Enter invoice number" wire:model="invoiceNumber">
                        </div>
                        <x-select
                            label="Select Discounts"
                            placeholder="Select discounts"
                            multiselect
                            icon="tag"
                            :options="$perOrderDiscounts"
                            option-value="id"
                            option-label="title"
                            wire:model.live="selectedPerOrderDiscountIds"
                        />
                        <hr>
                        <strong class="mb-3">Payment Details</strong>
                            <x-select
                                placeholder="Select payment type"
                                :options="$paymentTypes"
                                option-value="id"
                                option-label="payment_type_name"
                                wire:model.live="selectedPaymentTypeId" 
                            />
                        @if($selectedPaymentTypeId === 'SPLIT')
                            <x-button class="mt-2" label="View Split Payment" right-icon="wallet" outline primary hover="primary" focus:solid.gray /> 
                        @endif
                        <x-input
                            icon="currency-dollar"
                            placeholder="Amount Received"
                            wire:model.live.debounce.150ms="amountReceived"
                            class="mt-3"
                        />
                        <x-input
                            label="Change"
                            wire:model="changeAmount"
                            class="mt-3"
                            readonly
                        />
                        <button class="btn btn-primary mt-3 w-100" wire:click="processPayment" wire:loading.attr="disabled" @if($amountReceived < $payment_totalAmountDue) disabled @endif>
                            <span wire:loading.remove wire:target="processPayment">Complete Payment</span>
                             <span wire:loading wire:target="processPayment" >
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                 Completing ...
                            </span>
                        </button>
                    </div>
               </div>
           </div>
        </div>

        {{-- Open Tabs --}}
        <div class="container tab-pane fade" id="PaymentsTab" role="tabpanel" aria-labelledby="payments-tab" wire:ignore.self>
            <input type="text" class="form-control" placeholder="Search.." >
            <div class="container mt-3">
                <div class="d-flex gap-3 justify-content-center mb-2">
                    @forelse ($events as $event)
                        <div class="card" style="width: 18rem; border: 1px dashed #8d8b8b; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                        <div class="card-body">
                            <div class="card-title justify-content-center text-center" style="white-space: nowrap">
                                <i class="bi bi-calendar2-check"></i> <h5>{{ $event->event_name }}</h5><i>{{$event->reference}}</i>
                            </div>
                            <div class="gap-2 alert alert-secondary">
                                 <p class="card-text"><i class="bi bi-people"></i> Guest : {{ $event->guest_count }}</p>
                                 <p class="card-text"><i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($event->start_date)->format('M d.') }} - {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}</p>
                                <p> <i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($event->arrival_time)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($event->departure_time)->format('M d, Y') }}</p>
                            </div>
                            <x-primary-button class="w-100 text-center" x-on:click="$openModal('openBillingModal')">
                                View Billing
                            </x-primary-button>
                        </div>
                    </div>
                    @empty
                          <p>No events found.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- payment summary tab --}}
        <div  class="container tab-pane fade" id="PaymentsSummaryTab" role="tabpanel" aria-labelledby="payments-summary-tab" wire:ignore.self>
            <x-slot name="header">

                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboards') }}
                </h2>
            </x-slot>
            <div class="container mb-3">
                <div class="row">
                    <div class="col-md-6">
                        @if(auth()->user()->employee->getModulePermission('BEO Payment Summary') == 1 )
                        <x-primary-button>Print <i class="bi bi-printer"></i></x-primary-button>
                        <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                        @endif
                        <x-secondary-button wire:click="fetchData">Refresh &nbsp;<i class="bi bi-arrow-clockwise"></i></x-secondary-button>
                    </div>
                    <div class="col-md-6">
                        <h4 class="text-end">BEO Payment - Summary</h4>
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
                <div class="card-body overflow-auto" style="max-height: 450px;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0 table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th style="font-size: smaller;">Status</th>
                                    <th style="font-size: smaller;">Date</th>
                                    <th style="font-size: smaller;">Reference</th>
                                    <th style="font-size: smaller;">Invoice No.</th>
                                    <th style="font-size: smaller;">Customer Name</th>
                                    <th style="font-size: smaller;">Amount</th>
                                    <th style="font-size: smaller;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoices as $invoice)
                                    <tr  @if ($invoice->status == 'CANCELLED') class="table-danger" @endif>
                                        <td style="font-size: small;">
                                            @if ($invoice->status == 'CLOSED')
                                                <span class="badge bg-success">CLOSED</span>
                                            @elseif ($invoice->status == 'PARTIAL_REFUND')
                                                <span class="badge bg-warning text-dark">PARTIAL REFUND</span>
                                            @elseif ($invoice->status == 'CANCELLED')
                                                <span class="badge bg-danger">CANCELLED</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $invoice->status }}</span>
                                            @endif
                                        </td>
                                        <td style="font-size: small;">{{ $invoice->created_at->format('m/d/Y') }}</td>
                                        <td style="font-size: small;">{{ $invoice->reference ?? '' }}</td>
                                        <td style="font-size: small;">{{ $invoice->invoice_number }}</td>
                                        <td style="font-size: small;">
                                            {{ $invoice->customer->customer_name ?? $invoice->customer_name }}
                                        </td>
                                        <td style="font-size: small;"  @if ($invoice->amount <= 0) class="text-danger" @endif>
                                            {{ number_format($invoice->amount, 2) }}
                                        </td>
                                        <td>
                                            @php
                                                $latestSrpPrice = [];
                                                foreach ($invoice->event->eventMenus as $detail) {
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
                                                    data-bs-target="#supplierViewModal" data-bs-toggle="modal"
                                                    wire:click="viewInvoiceDetails({{ $invoice->event_id }}, {{ $invoice->id }})"
                                                    title="View Invoice Details">
                                                    <i class="bi bi-info-circle"></i>
                                                </x-primary-button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center" style="font-size: small;">No payments transactions found for the selected date range.</td>
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
            <div class="modal fade modal-xl" id="supplierViewModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="supplierModalLabel">Invoice Details &nbsp;<i class="bi bi-info-circle"></i></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Form -->
                            <form>
                                @csrf
                                <div class="row mb-1">
                                    <div class="col-md-6">
                                        <label for="customer_name" class="form-label">Customer Name</label>
                                        <input type="text" class="form-control" id="customer_name" wire:model="customer_name" readonly disabled>
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
                                        <label for="table" class="form-label">Event Reference No.</label>
                                        <input type="text" class="form-control" id="table_name" value="{{ $showEvent->reference ?? '' }}" readonly disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="order_number" class="form-label">Event</label>
                                        <input type="text" class="form-control text-center" id="order_number" value="{{ $showEvent->event_name ?? '' }}" readonly disabled>
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
                                <div class="card mt-3">
                                    <div class="card-header">
                                        Order Details
                                    </div>
                                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-striped table-hover me-3">
                                            <tbody id="itemTableBody">
                                                <tr><td colspan="5" class="text-center table-dark sticky-top"><strong>FOOD</strong></td></tr>
                                                <tr>
                                                    <th class="text-xs">Title</th>
                                                    <th class="text-xs">Qty</th>
                                                    <th class="text-xs">Amount</th>
                                                    <th class="text-xs">Total</th>
                                                </tr>
                                                @foreach ($showEvent->eventMenus ?? [] as $food)
                                                    <tr>
                                                        <td class="text-xs">{{ $food->menu->menu_name }}</td>
                                                        <td class="text-xs">{{ $food->qty }}</td>
                                                        <td class="text-xs">₱ {{ number_format($food->price->amount, 2) }}</td>
                                                        <td class="text-xs">₱ {{ number_format(($food->price->amount ?? 0) * ($food->qty ?? 0), 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            
                                                <tr><td colspan="5" class="text-center table-dark sticky-top"><strong>
                                                    VENUE / ROOMS </strong></td></tr>
                                                    <tr>
                                                    <th class="text-xs">Title</th>
                                                    <th class="text-xs">Qty</th>
                                                    <th class="text-xs">Amount</th>
                                                    <th class="text-xs">Total</th>
                                                </tr>
                                                @foreach ($showEvent->eventVenues ?? [] as $venue)
                                                    <tr>
                                                        <td class="text-xs">{{ $venue->venue->venue_name }}</td>
                                                        <td class="text-xs">{{ $venue->qty }}</td>
                                                        <td class="text-xs">₱ {{ number_format($venue->ratePrice->amount, 2) }}</td>
                                                        <td class="text-xs">₱ {{ number_format(($venue->ratePrice->amount ?? 0) * ($venue->qty ?? 0), 2) }}</td>
                                                    </tr>
                                                @endforeach

                                                <tr><td colspan="5" class="text-center table-dark sticky-top"><strong>
                                                SERVICES AND MISCELLANEOUS </strong></td></tr>
                                                <tr>
                                                    <th class="text-xs">Title</th>
                                                    <th class="text-xs">Qty</th>
                                                    <th class="text-xs">Amount</th>
                                                    <th class="text-xs">Total</th>
                                                </tr>
                                                @foreach ($showEvent->eventServices ?? [] as $service)
                                                    <tr>
                                                        <td class="text-xs">{{ $service->service->service_name }}</td>
                                                        <td class="text-xs">{{ $service->qty }}</td>
                                                        <td class="text-xs">₱ {{ number_format($service->price->amount, 2) }}</td>
                                                        <td class="text-xs">₱ {{ number_format(($service->price->amount ?? 0) * ($service->qty ?? 0), 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer text-right">
                                        <div class="d-flex justify-content-between">
                                            <h6>AMOUNT DUE:</h6>
                                            <h6>₱ {{ number_format($grossAmount, 2) }}</h6>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <h6>AMOUNT PAID:</h6>
                                            <h6>₱ {{ number_format($totalAmountDue, 2) }}</h6>
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
            </div>
            <!-- End Modal view-->

            {{-- PAYMENTS MODAL --}}
            <div class="modal fade modal-sm " id="ViewPaments" tabindex="-1" aria-labelledby="ViewPaymentsLabel" aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-top modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ViewPaymentsLabel">Payment Transaction &nbsp;<i class="bi bi-info-circle"></i></h5>
                            <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#supplierViewModal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="">
                        <div class="card">
                            <table class="table table-bordered table-hover align-middle mb-0 table-sm">
                            <thead>
                                <tr>
                                    <th style="font-size: smaller;">Type</th>
                                    <th style="font-size: smaller;">Mode</th>
                                    <th style="font-size: smaller;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse ($payments ?? [] as $payment)
                                <tr>
                                    <td style="font-size: smaller;">{{ $payment->type }}</td>
                                    <td style="font-size: smaller;">{{ $payment->payment_type->payment_type_name }}</td>
                                    <td style="font-size: smaller;"@if($payment->type == 'REFUND') class="text-danger" @endif>@if($payment->type == 'REFUND') -@endif{{ number_format($payment->amount, 2) }}</td>
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
                                        <th style="font-size: smaller;">Type</th>
                                        <th style="font-size: smaller;">Amount</th>
                                        <th style="font-size: smaller;">Dish</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse ($discountDetails ?? [] as $discountInfo)
                                    <tr @if($discountInfo->status == 'CANCELLED') class="table-danger" @endif>
                                        <td style="font-size: smaller;">{{ $discountInfo->discount->title }}</td>
                                        <td style="font-size: smaller">{{ $discountInfo->discount->description }}</td>
                                        <td style="font-size: smaller;">
                                            @if($discountInfo->discount->type == 'SINGLE')
                                                <span class="badge text-bg-primary">Per Item</span>
                                            @else
                                                <span class="badge text-bg-secondary">Overall</span>
                                            @endif
                                        </td>
                                        <td style="font-size: smaller;">₱ {{ number_format($discountInfo->calculated_amount, 2) }}</td>
                                        <td style="font-size: smaller;"> {{'('. ($discountInfo->eventMenu->qty ?? 0) .'x) '. ($discountInfo->eventMenu->menu->menu_name ?? 'N/A') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center" style="font-size: smaller;">No discount details available.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- END DISCOUNTS MODAL --}}
        </div>
     </div>

     {{-- openBillingModal --}}
    <x-modal name="openBillingModal" persistent>
        <x-card>
           <div>
                <div class="text-center ">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
                        <img src="{{ asset('images/' . auth()->user()->branch->company->company_logo) }}" alt="Branch Logo" style="max-height: 50px;">
                    </div>
                    <h5 style="margin: 0;"><strong>{{ auth()->user()->branch->branch_name }}</strong></h5>
                    <p class="address">{{ auth()->user()->branch->branch_address }}</p>

                </div>
           </div>
    
            <x-slot name="footer" class="flex justify-end gap-x-4">
                <x-button flat label="Close" x-on:click="close" />
    
                <x-button primary label="Print" wire:click="agree" icon="printer" />
            </x-slot>
        </x-card>
    </x-modal>

    {{-- payment type modal  splitPaymentModal --}}
    <x-modal-card title="Edit Customer" name="splitPaymentModal" persistent>
        <div class="col-span-1 sm:col-span-2">
           <div class="flex items-end gap-1 mb-3">
            <div class="grow">
                    <x-select 
                        label="Payment Type"
                        placeholder="Select payment type"
                        :options="$paymentTypesToSplit"
                        option-value="id"
                        option-label="payment_type_name"
                        icon="credit-card"
                        wire:model.live="selectedSplitId"
                    />
                </div>
                <x-primary-button
                    wire:click="addToSplitPayments"
                >Add</x-primary-button>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Payment Type</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($splitPayments as $index => $payment)
                                <tr>
                                    <td>{{ $payment['type'] }}</td>
                                    <td><input type="number" wire:model.live="splitPayments.{{ $index }}.amount" class="form-control" placeholder="0.00"/></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" wire:click="removeFromTable({{ $index }})">Remove</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <strong>Difference: ₱ {{ number_format($difference, 2) }}</strong>
                </div>
            </div>
        
        </div>
    
        <x-slot name="footer" class="flex justify-between gap-x-4">
    
            <div class="flex gap-x-4">
                <x-button flat label="Cancel" x-on:click="close" />
    
                <x-button primary label="Save" wire:click="save" />
            </div>
        </x-slot>
    </x-modal-card>

    {{-- open food discount modal --}}
    <x-modal-card title="Add Food Discount" name="foodDiscountModal" persistent>
        <div class="col-span-1 sm:col-span-2">
           <div class="flex items-end gap-1 mb-3">
                <div class="grow">
                        <x-select 
                            label="Discount"
                            placeholder="Select discount"
                            :options="$perItemDiscounts"
                            option-value="id"
                            option-label="title"
                            icon="tag"
                            wire:model.live="selectedFoodDiscountId"
                        />
                    </div>
                    <x-primary-button
                    wire:click="addToFoodDiscounts"
                >
                    <span wire:loading.remove wire:target="addToFoodDiscounts">Add</span>
                    <span wire:loading wire:target="addToFoodDiscounts">Adding...</span>
                </x-primary-button>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Discount</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($selectedItemDiscounts[$selectedMenuItemId]))
                                @foreach($selectedItemDiscounts[$selectedMenuItemId] as $index => $discount)
                                    <tr>
                                        <td> {{ $discount['title'] }}</td>
                                        <td><input type="text" class="form-control" value="{{ $discount['value']}}" /></td>
                                        <td>
                                                <button class="btn btn-danger btn-sm" wire:click="removeFromFoodDiscounts({{ $index }})">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Select a discount and click Add to display items</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <x-slot name="footer" class="flex justify-between gap-x-4">
    
            <div class="flex gap-x-4">
                <x-button primary label="Done" x-on:click="close" />
            </div>
        </x-slot>
    </x-modal-card>

    {{-- open service discount modal --}}
    <x-modal-card title="Add Service Discount" name="serviceDiscountModal" persistent>
        <div class="col-span-1 sm:col-span-2">
           <div class="flex items-end gap-1 mb-3">
                <div class="grow">
                        <x-select 
                            label="Discount"
                            placeholder="Select discount"
                            :options="$perItemDiscounts"
                            option-value="id"
                            option-label="title"
                            icon="tag"
                            wire:model.live="selectedServiceDiscountId"
                        />
                    </div>
                    <x-primary-button
                    wire:click="addToServiceDiscounts"
                >
                    <span wire:loading.remove wire:target="addToServiceDiscounts">Add</span>
                    <span wire:loading wire:target="addToServiceDiscounts">Adding...</span>
            </x-primary-button>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Discount</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                              @if(!empty($selectedItemDiscounts[$selectedServiceId]))
                                @foreach($selectedItemDiscounts[$selectedServiceId] as $index => $discount)
                                    <tr>
                                        <td>{{ $discount['title'] }}</td>
                                        <td><input type="text" class="form-control" value="{{ $discount['value']}}" /></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm" wire:click="removeFromServiceDiscounts({{ $index }})">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                             @else
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Select a discount and click Add to display items</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
         <x-slot name="footer" class="flex justify-between gap-x-4">
    
            <div class="flex gap-x-4">
                <x-button primary label="Done" x-on:click="close" />
            </div>
        </x-slot>
    </x-modal-card>

    {{-- open venue discount modal --}}
    <x-modal-card title="Add Venue Discount" name="venueDiscountModal" persistent>
        <div class="col-span-1 sm:col-span-2">
           <div class="flex items-end gap-1 mb-3">
                <div class="grow">
                        <x-select 
                            label="Discount"
                            placeholder="Select discount"
                            :options="$perItemDiscounts"
                            option-value="id"
                            option-label="title"
                            icon="tag"
                            wire:model.live="selectedVenueDiscountId"
                        />
                    </div>
                    <x-primary-button
                    wire:click="addToVenueDiscounts"
                >
                    <span wire:loading.remove wire:target="addToVenueDiscounts">Add</span>
                    <span wire:loading wire:target="addToVenueDiscounts">Adding...</span>
                </x-primary-button>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Discount</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                             @if(!empty($selectedItemDiscounts[$selectedVenueId]))
                                @foreach($selectedItemDiscounts[$selectedVenueId] as $index => $discount)
                                    <tr>
                                        <td>{{ $discount['title'] }}</td>
                                        <td><input type="text" class="form-control" value="{{ $discount['value']}}" /></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm" wire:click="removeFromVenueDiscounts({{ $index }})">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Select a discount and click Add to display items</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
         <x-slot name="footer" class="flex justify-between gap-x-4">
    
            <div class="flex gap-x-4">
                <x-button primary label="Done" x-on:click="close" />
            </div>
        </x-slot>
    </x-modal-card>


      <x-notifications />

</div>
