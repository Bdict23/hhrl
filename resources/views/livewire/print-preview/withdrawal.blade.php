<div>
    <h2>{{ $branchName ?? '' }}</h2>

    <!-- Document Information Tables -->
    <div class="flex-container">
        <table class="table-xs table-sm">
            <tr>
                <th>Reference No.</th>
                <td>{{ $reference ?? 'N/A' }}</td>
            </tr>
            @if($eventName)
                <tr>
                    <th>Event Name</th>
                    <td>{{ $eventName ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Event Date</th>
                    <td>{{ \Carbon\Carbon::parse($withdrawalData->event->event_date)->format('M-d-Y') ?? 'N/A' }}</td>
                </tr>
            @endif
            <tr>
                <th>Department</th>
                <td>{{ $withdrawalData->department->department_name ?? 'N/A' }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <th></th>
            
            </tr>
            <tr>
                <th></th>
            </tr>
            <tr>
                <th></th>
             
            </tr>
        </table>
    </div>

    <!-- Acquired Services Section -->
    <div class="section-title">ITEMS</div>
    <table>
        <thead>
            <tr style="border: 1px solid #333;">
                <th class="text-xs">CODE</th>
                <th class="text-xs">NAME</th>
                <th class="text-xs">UNIT</th>
                <th class="text-xs text-end">QTY.</th>
                <th class="text-xs text-end">COST</th>
                <th class="text-xs text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($this->selectedItems ?? [] as $item)
                <tr>
                   
                    <td class="text-xs">{{ $item['code'] ?? 'N/A' }}</td>
                    <td class="text-xs">{{ $item['name'] ?? 'N/A' }}</td>
                    <td class="text-xs">{{ $item['unit'] ?? 'N/A' }}</td>
                    <td class="text-xs text-end">{{ number_format($item['requested_qty'], 2) }}</td>
                    <td class="text-xs text-end">{{ number_format($item['cost'], 2) }}</td>
                    <td class="text-xs text-end">{{ number_format($item['requested_qty'] * $item['cost'], 2) }}</td>

                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No items found.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-end"><strong>Total </strong></td>
                <td>
                    <strong>{{ number_format($overallTotal, 2) }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Signature Section -->
    <table class="signature" style="page-break-before: never;">
        <tr>
          
                <td style="padding-right: 60px;">
                     
                    <span style="font-size: x-small">Prepared By:</span>
                    <br><br>
                    <div style="text-align: center;">
                        {{ $withdrawalData->preparedBy->name . ' ' .  $withdrawalData->preparedBy->last_name ?? 'N/A' }}
                    </div>
                    <hr>
                    <div style="text-align: center;">
                        {{ $withdrawalData->preparedBy->position->position_name ?? 'N/A' }}
                        <br><br>
                        <span>Date:&nbsp;____________________</span>
                    </div>
                    
                </td>
           
            <td style="padding-left: 60px;">
                <span style="font-size: x-small">Approved By:</span>
                <br><br>
                <div style="text-align: center;">
                    {{ $withdrawalData->approvedBy->name . ' ' . ($withdrawalData->approvedBy->middle_name[0] ?? '') . $withdrawalData->approvedBy->last_name ?? 'N/A' }}
                </div>
                <hr>
                <div style="text-align: center;">
                    {{ $withdrawalData->approvedBy->position->position_name ?? 'N/A' }}
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
