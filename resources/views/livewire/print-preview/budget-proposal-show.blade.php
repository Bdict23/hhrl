<div>
    <h2>Event Order Document</h2>

    <!-- Document Information Tables -->
    <div class="flex-container">
        <table class="table-xs">
            <tr>
                <th>Document No.</th>
                <td>{{ $proposal->document_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Customer Name</th>
                <td>{{ $proposal->event->customer->customer_fname . ' ' . $proposal->event->customer->customer_lname ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Request Budget</th>
                <td>{{ $proposal->suggested_amount ?? 'N/A' }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <th>Reference No.</th>
                <td>{{ $proposal->reference_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Event Name</th>
                <td>{{ $proposal->event->event_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Event Date</th>
                <td>{{ $proposal->event->event_date ? \Carbon\Carbon::parse($proposal->event->event_date)->format('M-d-Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Time</th>
                <td>
                    {{ 
                        ($proposal->event->start_time && $proposal->event->end_time) 
                            ? \Carbon\Carbon::parse($proposal->event->start_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($proposal->event->end_time)->format('h:i A') 
                            : 'N/A' 
                    }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Acquired Services Section -->
    <div class="section-title">Acquired Services</div>
    <table>
        <thead>
            <tr style="border: 1px solid #333;">
                <th class="text-xs">Title</th>
                <th class="text-xs">Qty</th>
                <th class="text-xs">Income</th>
                <th class="text-xs">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($selectedEvent->eventServices ?? [] as $services)
                <tr>
                    <td>{{ $services->service->service_name }}</td>
                    <td>{{ $services->qty ? $services->qty : '-' }}</td>
                    
                    @if ($services->service->service_type == 'INTERNAL')
                        <td>{{ $services->price->amount }}</td>
                    @else
                        <td>{{ $services->cost->amount ?? '0' }}</td>
                    @endif
                    
                    @if ($services->service->service_type == 'INTERNAL')
                        <td>{{ $services->price->amount * ($services->qty ? $services->qty : 1) }}</td>
                    @else
                        <td>{{ ($services->cost->amount ?? 0) * ($services->qty ? $services->qty : 1) }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No services found.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end"><strong>Total Income</strong></td>
                <td>
                    @php
                        $internalTotal = isset($selectedEvent) && $selectedEvent->eventServices
                            ? $selectedEvent->eventServices->where('service.service_type', 'INTERNAL')->sum(function($service) {
                                return $service->price->amount * ($service->qty ? $service->qty : 1);
                            })
                            : 0;

                        $externalTotal = isset($selectedEvent) && $selectedEvent->eventServices
                            ? $selectedEvent->eventServices->where('service.service_type', 'EXTERNAL')->sum(function($service) {
                                return ($service->cost->amount ?? 0) * ($service->qty ? $service->qty : 1);
                            })
                            : 0;

                        $totalIncome = $internalTotal + $externalTotal;
                    @endphp
                    {{ number_format($totalIncome, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Purchase Orders Section -->
    <div class="section-title">Purchase Orders</div>
    <table>
        <thead>
            <tr style="border: 1px solid #333;">
                <th class="text-xs">P.O No.</th>
                <th class="text-xs">Total Items</th>
                <th class="text-xs">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($selectedEvent->purchaseOrders ?? [] as $orders )
             <tr>
                <td>{{ $orders->requisition_number }}</td>
                <td>{{ $orders->requisitionDetails->count() }}</td>
                <td>{{ $orders->requisitionDetails->sum(fn($detail) => $detail->totalAmount()) }}</td>
            </tr>
            @endforeach
            @if(empty($selectedEvent->purchaseOrders))
                <tr>
                    <td colspan="4" class="text-center">No purchase orders found.</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end"><strong>Total</strong></td>
                <td>
                    {{ isset($selectedEvent) && $selectedEvent->purchaseOrders
                        ? $selectedEvent->purchaseOrders->sum(function($order) {
                            return $order->requisitionDetails->sum(fn($detail) => $detail->totalAmount());
                        })
                        : 0
                    }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Summary Section -->
    <div class="section-title">Summary</div>
    <table>
         <tr>
            <td class="text-xs">Income on Service</td>
            <td class="text-xs">
                @php
                    $internalTotal = isset($selectedEvent) && $selectedEvent->eventServices
                        ? $selectedEvent->eventServices->where('service.service_type', 'INTERNAL')->sum(function($service) {
                            return $service->price->amount * ($service->qty ? $service->qty : 1);
                        })
                        : 0;

                    $externalTotal = isset($selectedEvent) && $selectedEvent->eventServices
                        ? $selectedEvent->eventServices->where('service.service_type', 'EXTERNAL')->sum(function($service) {
                            return ($service->cost->amount ?? 0) * ($service->qty ? $service->qty : 1);
                        })
                        : 0;

                    $totalIncome = $internalTotal + $externalTotal;
                @endphp
                {{ $totalIncome }}
            </td>
        </tr>
        <tr>
            <td class="text-xs"><strong>Buffet Menu</strong></td>
            <td class="text-sm"><strong>
                {{ isset($selectedEvent) && $selectedEvent->eventMenus
                    ? $selectedEvent->eventMenus->sum(function($menu) {
                        return $menu->price->amount * ($menu->qty ? $menu->qty : 1);
                    })
                    : 0
                }}</strong><br>
            </td>
        </tr>
    </table>

    <!-- Signature Section -->
    <table class="signature">
        <tr>
            <td style="padding-right: 60px;">
                <span style="font-size: x-small">Noted By:</span>
                <br><br>
                <div style="text-align: center;">
                    {{ $proposal->notedBy->name . ' ' . $proposal->notedBy->last_name ?? 'N/A' }}
                </div>
                <hr>
                <div style="text-align: center;">
                    {{ $proposal->notedBy->position->position_name ?? 'N/A' }}
                    <br><br>
                    <span>Date:&nbsp;____________________</span>
                </div>
            </td>
            <td>
                <span style="font-size: x-small">Approved By:</span>
                <br><br>
                <div style="text-align: center;">
                    {{ $proposal->approver->name . ' ' . $proposal->approver->last_name ?? 'N/A' }}
                </div>
                <hr>
                <div style="text-align: center;">
                    {{ $proposal->approver->position->position_name ?? 'N/A' }}
                    <br><br>
                    <span>Date:&nbsp;____________________</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Action Buttons -->
    <div class="no-print">
        <button onclick="history.back()" class="btn btn-secondary" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor"
                class="bi bi-reply-fill" viewBox="0 0 16 16">
                <path d="M5.921 11.9 1.353 8.62a.72.72 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z" />
            </svg>
            Back
        </button>
        <button class="btn btn-primary" onclick="printDocument()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-printer" viewBox="0 0 16 16">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
            </svg>
            Print
        </button>
    </div>

    <!-- JavaScript -->
    <script>
        function printDocument() {
            window.print();
        }
    </script>
</div>
