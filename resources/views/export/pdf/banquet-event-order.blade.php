<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banquet Event Order - {{ $eventDetails->reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            font-size: 15px;
            background: white;
        }

        @page {
            size: A4;
            margin: 15mm 10mm;
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-after: always;
            page-break-inside: avoid;
        }

        .avoid-break {
            page-break-inside: avoid;
        }

        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
        }

        /* Header Section */
        .head {
            text-align: center;
            color: #ffffff;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 3px;
            page-break-inside: avoid;
        }

        .head img {
            max-height: 45px;
            margin-bottom: 8px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .head h3 {
            margin: 8px 0 3px 0;
            font-size: 25px;
            font-weight: 600;
        }

       

        /* Title Section */
        .section-title {
            font-weight: 700;
            font-size: 19px;
            margin: 15px 5px 10px 5px;
            padding: 8px 10px;
            background-color: #ecf0f1;
            border-left: 4px solid #2c3e50;
            page-break-inside: avoid;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        table.bordered {
            border: 1px solid #bdc3c7;
        }

        table.bordered td,
        table.bordered th {
            border: 1px solid #bdc3c7;
            padding: 9px;
        }

        table td,
        table th {
            padding: 7px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #b4faae;
            font-weight: 600;
            text-align: center;
            font-size: 18px;
            color: #1a1a1a;
        }

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #f0f0f0;
        }

        .info-table {
            background: white;
        }

        .info-table td {
            padding: 8px;
            border: 1px solid #e0e0e0;
            font-size: 18px;
        }

        .info-table strong {
            display: inline-block;
            min-width: 110px;
            font-weight: 600;
            font-size: 18px;
        }

        /* Notes Box */
        .note-box {
            border: 1px solid #e0e0e0;
            padding: 10px;
            margin: 12px 0;
            background-color: #f9f9f9;
            font-size: 18px;
            white-space: pre-wrap;
            border-left: 3px solid #f39c12;
            page-break-inside: avoid;
            margin-left: 10px;
        }

        .note-box strong {
            display: block;
            margin-bottom: 5px;
            color: #2c3e50;
            font-size: 18px;
        }

        /* Totals Section */
        .totals-section {
            margin: 15px 0;
            padding: 12px;
            background-color: #ecf0f1;
            border: 1px solid #bdc3c7;
            page-break-inside: avoid;
            border-radius: 3px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #bdc3c7;
            font-size: 12px;
        }

        .totals-row:last-child {
            border-bottom: none;
        }

        .totals-row.grand-total {
            background-color: #fff;
            padding: 8px 0;
            margin-top: 8px;
            font-weight: 700;
            font-size: 12px;
            border-top: 2px solid #2c3e50;
        }

        .totals-label {
            font-weight: 600;
        }

        .totals-amount {
            text-align: right;
            font-weight: 500;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #2c3e50;
            page-break-inside: avoid;
        }

        .signature-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .signature-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 10px;
            font-size: 11px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 35px;
            padding-top: 5px;
            min-height: 15px;
        }

        .signature-name {
            font-weight: 600;
            margin-top: 1px;
        }

        .signature-title {
            font-size: 10px;
            font-style: italic;
            color: #555;
        }

        .signature-label {
            font-size: 11{
            font-size: 10px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2c3e50;
        }

        /* Utility Classes */
        .text-xs {11px;
        }

        .text-sm {
            font-size: 12
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-muted {
            color: #7f8c8d;
        }

        .break-all {
            word-break: break-word;
        }

        .m-0 { margin: 0; }
        .m-1 { margin: 1px; }
        .m-2 { margin: 4px; }

        .mb-1 { margin-bottom: 3px; }
        .mb-2 { margin-bottom: 6px; }
        .mb-3 { margin-bottom: 10px; }
        .mb-4 { margin-bottom: 15px; }

        .mt-1 { margin-top: 3px; }
        .mt-2 { margin-top: 6px; }
        .mt-3 { margin-top: 10px; }
        .mt-4 { margin-top: 15px; }

        .p-1 { padding: 3px; }
        .p-2 { padding: 6px; }

        hr {
            border: none;
            border-top: 1px solid #bdc3c7;
            margin: 8px 0;
        }

        .currency {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: 500;
        }

        /* Empty State */
        .empty-message {
            text-align: center;
            color: #95a5a6;
            padding: 10px;
            font-style: 1talic;
            font-size: 10px;
        }

        /* Table Sections */
        .table-section {
            page-break-inside: avoid;
            margin-bottom: 12px;
            margin-left: 10px;
            margin-right: 15px;
        }

        /* Row grouping */
        .row-group {
            page-break-inside: avoid;
        }
   
        .signaroty-name {
            font-weight: bold;
            text-decoration: underline;
        }
         
        .head {
            text-align: center;
            color: #ffffff;
            background-color: #272727;
            padding: 10px;
        }
            .thead {
                background-color: #b4faae;
                font-weight: bold;
                text-align: center;
            }
        .t-sm {
            font-size: 0.90rem;
        }
        .address {
            display: block;
            margin-top: 5px;
            text-align: center;
        }
         @media print {
            .no-print {
                display: none !important;
                visibility: hidden !important;
            }

            * {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                margin: 0;
                size: auto;
            }

            html, body {
                margin-left: 8pt;
                margin-right: 8pt;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="head avoid-break">
            <div style="text-align: center;">
                @if(auth()->user()->branch->company->company_logo)
                    <img src="{{ public_path('images/' . auth()->user()->branch->company->company_logo) }}" alt="Branch Logo">
                @endif
                <h3>{{ auth()->user()->branch->branch_name }}</h3>
            </div>
            <div class="address">{{ auth()->user()->branch->branch_address }}</div>
            <div class="address">{{ auth()->user()->branch->branch_type }}</div>
        </div>

        <!-- Title -->
        <div class="section-title">Banquet Event Order &nbsp;&nbsp;<strong>#{{ $eventDetails->reference }}</strong></div>

        <!-- Event Details Table -->
        <div class="table-section">
            <table class="bordered info-table avoid-break">
                <tbody>
                    <tr class="row-group">
                        <td style="width: 50%;"><strong>Event Name:</strong> <br>{{ $eventDetails->event_name }}</td>
                        <td style="width: 50%;"><strong>Customer Name:</strong> <br>{{ $eventDetails->customer->customer_fname ?? '' }} {{ $eventDetails->customer->customer_lname ?? '' }} {{ $eventDetails->customer->suffix ?? '' }}</td>
                    </tr>
                    <tr class="row-group">
                        <td><strong>Event Date:</strong> <br>{{ \Carbon\Carbon::parse($eventDetails->start_date)->format('M. d') }} - {{ \Carbon\Carbon::parse($eventDetails->end_date)->format('d, Y') }}</td>
                        <td><strong>Address:</strong> <br>{{ $eventDetails->customer->customer_address ?? '' }}</td>
                    </tr>
                    <tr class="row-group">
                        <td><strong>Location:</strong> <br>{{ $eventDetails->event_address ?? '' }}</td>
                        <td><strong>Contact #:</strong> <br>{{ $eventDetails->customer->contact_no_1 ?? '' }}{{ ($eventDetails->customer->contact_no_2 ?? false) ? ' / ' . $eventDetails->customer->contact_no_2 : '' }}</td>
                    </tr>
                    <tr class="row-group">
                        <td><strong>Guest Count:</strong> <br>{{ $eventDetails->guest_count ?? '0' }} Guests</td>
                        <td><strong>Email:</strong> <br>{{ $eventDetails->customer->email ?? '' }}</td>
                    </tr>
                    <tr class="row-group">
                        <td><strong>Start Time:</strong> <br>{{ \Carbon\Carbon::parse($eventDetails->arrival_time)->format('h:i A') }}<br>({{ \Carbon\Carbon::parse($eventDetails->start_date)->format('M. d, Y') }})</td>
                        <td><strong>End Time:</strong> <br>{{ \Carbon\Carbon::parse($eventDetails->departure_time)->format('h:i A') }}<br>({{ \Carbon\Carbon::parse($eventDetails->end_date)->format('M. d, Y') }})</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Notes Section -->
        @if($eventDetails->notes)
            <div class="note-box avoid-break">
                <strong> Note:</strong>
                {{ $eventDetails->notes }}
            </div>
        @endif

        <!-- Food Menu Table -->
        <div class="section-title">FOOD / MENU ITEMS</div>
        <div class="table-section">
            <table class="bordered">
                <thead>
                    <tr>
                        <th style="width: 25%;">Menu Item</th>
                        <th style="width: 35%;">Description</th>
                        <th style="width: 15%;">Qty</th>
                        <th style="width: 25%;">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventDetails->eventMenus ?? [] as $eventMenu)
                        <tr class="row-group">
                            <td class="text-sm">
                                @if($eventMenu->menu)
                                    <strong>{{ $eventMenu->menu->menu_name }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-xs break-all">
                                {{ $eventMenu->menu->menu_description ?? '-' }}
                                @if($eventMenu->note)
                                    <br><em style="color: #e74c3c;">Note: {{ $eventMenu->note }}</em>
                                @endif
                            </td>
                            <td class="text-center">{{ $eventMenu->qty ?? '0' }}</td>
                            <td class="text-right currency">&#8369;{{ number_format($eventMenu->price->amount * ($eventMenu->qty ?? 0), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-message">No menu items selected</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Locations/Venues Table -->
        <div class="section-title">VENUES / LOCATIONS</div>
        <div class="table-section">
            <table class="bordered">
                <thead>
                    <tr>
                        <th style="width: 40%;">Location Name</th>
                        <th style="width: 20%;">Capacity</th>
                        <th style="width: 10%;">Qty</th>
                        <th style="width: 30%;">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventDetails->eventVenues ?? [] as $eventVenue)
                        <tr class="row-group">
                            <td class="text-sm">
                                @if($eventVenue->venue)
                                    <strong>{{ $eventVenue->venue->venue_name }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center text-sm">{{ $eventVenue->venue->capacity ?? '-' }} Guests</td>
                            <td class="text-center">{{ $eventVenue->qty ?? '0' }}</td>
                            <td class="text-right currency">&#8369;{{ number_format($eventVenue->ratePrice->amount * ($eventVenue->qty ?? 0), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-message">No venues acquired</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Services/Others Table -->
        <div class="section-title">SERVICES & MISCELLANEOUS</div>
        <div class="table-section">
            <table class="bordered">
                <thead>
                    <tr>
                        <th style="width: 50%;">Service/Item Name</th>
                        <th style="width: 15%;">Qty</th>
                        <th style="width: 35%;">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventDetails->eventServices ?? [] as $eventService)
                        <tr class="row-group">
                            <td class="text-sm">
                                @if($eventService->service)
                                    <strong>{{ $eventService->service->service_name }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $eventService->qty ?? '0' }}</td>
                            <td class="text-right currency">&#8369;{{ number_format($eventService->price->amount * ($eventService->qty ?? 0), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty-message">No services acquired</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Totals Section -->
        <div class="totals-section avoid-break">
            <div class="totals-row">
                <span class="totals-label">Food & Menu Items:</span>
                <span class="totals-amount currency">&#8369;{{ number_format($totalAmountMenu, 2) }}</span>
            </div>
            <div class="totals-row">
                <span class="totals-label">Venues & Locations:</span>
                <span class="totals-amount currency">&#8369;{{ number_format($totalAmountLocation, 2) }}</span>
            </div>
            <div class="totals-row">
                <span class="totals-label">Services & Miscellaneous:</span>
                <span class="totals-amount currency">&#8369;{{ number_format($totalAmountService, 2) }}</span>
            </div>
            <div class="totals-row grand-total">
                <span class="totals-label">TOTAL AMOUNT:</span>
                <span class="totals-amount currency" style="font-size: 12px;">&#8369;{{ number_format($eventDetails->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section avoid-break">
            <div style="text-align: center; margin-bottom: 15px; font-weight: 600; font-size: 11px; color: #2c3e50;">Authorizations & Signatures</div>
            <div class="signature-row">
                <div class="signature-col">
                    <div class="signature-label">Prepared By:</div>
                    <div class="signature-line"></div>
                    <p class="signature-name">{{ $eventDetails->createdBy->name ?? '' }} {{ $eventDetails->createdBy->last_name ?? '' }}</p>
                    <p class="signature-title">{{ $eventDetails->createdBy->position->position_name ?? '' }}</p>
                    <p class="text-xs text-muted">{{ \Carbon\Carbon::parse($eventDetails->created_at)->format('M. d, Y') }}</p>
                </div>
                <div class="signature-col">
                    <div class="signature-label">Reviewed By:</div>
                    <div class="signature-line"></div>
                    <p class="signature-name">{{ $eventDetails->reviewer->name ?? '' }} {{ $eventDetails->reviewer->last_name ?? '' }}</p>
                    <p class="signature-title">{{ $eventDetails->reviewer->position->position_name ?? '' }}</p>
                </div>
                <div class="signature-col">
                    <div class="signature-label">Approved By:</div>
                    <div class="signature-line"></div>
                    <p class="signature-name">{{ $eventDetails->approver->name ?? '' }} {{ $eventDetails->approver->last_name ?? '' }}</p>
                    <p class="signature-title">{{ $eventDetails->approver->position->position_name ?? '' }}</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 20px; padding-top: 10px; border-top: 1px solid #bdc3c7; text-align: center; font-size: 11px; color: #95a5a6;">
            <p>This is an official Banquet Event Order. For inquiries, please contact the banquet department.</p>
            <p style="margin-top: 3px;">Generated on {{ \Carbon\Carbon::now()->format('M. d, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>
