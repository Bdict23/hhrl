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
        <div class="card-body" style="max-height: 600px; overflow-y: auto;">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 table-sm border-radius-15" >
                    <thead class="table-dark card-header">
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
                                    <button class="btn btn-info btn-sm" wire:click="viewShiftDetails({{ $shift->id }})" wire:loading.attr="disabled">
                                        View Details <i class="bi bi-eye" wire:loading.remove wire:target="viewShiftDetails({{ $shift->id }})"></i>
                                        <i class="spinner-border spinner-border-sm" role="status" wire:loading wire:target="viewShiftDetails({{ $shift->id }})"></i>
                                    </button>
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





    {{-- MODAL --}}
    <div class="modal fade modal-lg " id="ViewShiftDetails" tabindex="-1" aria-labelledby="ViewShiftDetailsLabel" aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ViewShiftDetailsLabel">Shift Details &nbsp;<i class="bi bi-info-circle"></i></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body card">
                   <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="nav nav-tabs mb-3" id="denominationTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="opening-denominations-tab" data-bs-toggle="tab" data-bs-target="#opening-denominations" type="button" role="tab" aria-controls="opening-denominations" aria-selected="true">Opening</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="closing-denominations-tab" data-bs-toggle="tab" data-bs-target="#closing-denominations" type="button" role="tab" aria-controls="closing-denominations" aria-selected="false">Closing</button>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                 <div class="tab-pane fade show active" id="opening-denominations" role="tabpanel" aria-labelledby="opening-denominations-tab">
                                   <table class="table table-sm">
                                       <tbody>
                                           <th colspan="3" class="text-center bg-dark text-white">Bills</th>
                                           </tr>
                                               <th style="font-size: smaller;">Denomination</th>
                                               <th style="font-size: smaller;">Quantity</th>
                                               <th style="font-size: smaller;">Total</th>
                                           </tr>
                                           @foreach ($billDenominations as $denomination)
                                               <tr>
                                                   <td style="font-size: smaller;">₱ {{ number_format($denomination->value, 2) }}</td>
                                                   <td style="font-size: smaller;">@if($curShift){{ ($curShift->openingShiftDenominations->where('denomination_id', $denomination->id)->first()->quantity) ?? 0 }}@endif</td>
                                                   <td style="font-size: smaller;">₱ @if($curShift){{ number_format((float)($curShift->openingShiftDenominations->where('denomination_id', $denomination->id)->first()->quantity ?? 0) * (float)$denomination->value, 2) }}@else 0.00 @endif</td>
                                               </tr>
                                           @endforeach
                                           <th colspan="3" class="text-center bg-dark text-white">Coins</th>
                                           </tr>
                                               <th style="font-size: smaller;">Denomination</th>
                                               <th style="font-size: smaller;">Quantity</th>
                                               <th style="font-size: smaller;">Total</th>
                                           </tr>
                                           @foreach ($coinDenominations as $denomination)
                                               <tr>
                                                   <td style="font-size: smaller;">₱ {{ number_format($denomination->value, 2) }}</td>
                                                   <td style="font-size: smaller;">@if($curShift){{ $curShift->openingShiftDenominations->where('denomination_id', $denomination->id)->first()->quantity ?? 0 }}@endif</td>
                                                   <td style="font-size: smaller;">₱ @if($curShift){{ number_format((float)($curShift->openingShiftDenominations->where('denomination_id', $denomination->id)->first()->quantity ?? 0) * (float)$denomination->value, 2) }}@else 0.00 @endif</td>
                                               </tr>
                                           @endforeach
                                           <tfoot>
                                               <tr class="bg-light">
                                                   <td colspan="2" class="text-end fw-bold" style="font-size: smaller;">Total:</td>
                                                   <td style="font-size: smaller;" class="fw-bold">₱ @if($curShift){{ number_format($curShift->openingShiftDenominations->sum(function($item) {
                                                       return $item->quantity * $item->denomination->value;
                                                   }), 2) }}@else 0.00 @endif</td>
                                               </tr>
                                           </tfoot>
                                       </tbody>
                                   </table>
                                 </div>
                                 <div class="tab-pane fade" id="closing-denominations" role="tabpanel" aria-labelledby="closing-denominations-tab">
                                   <table class="table table-sm">
                                       <tbody>
                                           <th colspan="3" class="text-center bg-dark text-white">Bills</th>
                                           </tr>
                                               <th style="font-size: smaller;">Denomination</th>
                                               <th style="font-size: smaller;">Quantity</th>
                                               <th style="font-size: smaller;">Total</th>
                                           </tr>
                                           @foreach ($billDenominations as $denomination)
                                               <tr>
                                                   <td style="font-size: smaller;">₱ {{ number_format($denomination->value, 2) }}</td>
                                                   <td style="font-size: smaller;">@if($curShift){{ $curShift->closingShiftDenominations->where('denomination_id', $denomination->id)->first()->quantity ?? 0 }}@else 0.00 @endif</td>
                                                   <td style="font-size: smaller;">₱ @if($curShift){{ number_format((float)($curShift->closingShiftDenominations->where('denomination_id', $denomination->id)->first()->quantity ?? 0) * (float)$denomination->value, 2) }}@else 0.00 @endif</td>
                                               </tr>
                                           @endforeach
                                           <th colspan="3" class="text-center bg-dark text-white">Coins</th>
                                           </tr>
                                               <th style="font-size: smaller;">Denomination</th>
                                               <th style="font-size: smaller;">Quantity</th>
                                               <th style="font-size: smaller;">Total</th>
                                           </tr>
                                           @foreach ($coinDenominations as $denomination)
                                               <tr>
                                                   <td style="font-size: smaller;">₱ {{ number_format($denomination->value, 2) }}</td>
                                                   <td style="font-size: smaller;">@if($curShift){{ $curShift->closingShiftDenominations->where('denomination_id', $denomination->id)->first()->quantity ?? 0 }}@else 0.00 @endif</td>
                                                   <td style="font-size: smaller;">₱ @if($curShift){{ number_format((float)($curShift->closingShiftDenominations->where('denomination_id', $denomination->id)->first()->quantity ?? 0) * (float)$denomination->value, 2) }}@else 0.00 @endif</td>
                                               </tr>
                                           @endforeach
                                           <tfoot>
                                               <tr class="bg-light">
                                                   <td colspan="2" class="text-end fw-bold" style="font-size: smaller;">Total:</td>
                                                   <td style="font-size: smaller;" class="fw-bold">₱ @if($curShift){{ number_format($curShift->closingShiftDenominations->sum(function($item) {
                                                       return $item->quantity * $item->denomination->value;
                                                   }), 2) }}@else 0.00 @endif</td>
                                               </tr>
                                           </tfoot>
                                       </tbody>
                                   </table>
                                 </div>
                              </div>
                            </div>
                            <div class="col-md-6 card">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th style="font-size: smaller;">Beginning Balance:</th>
                                            <td style="font-size: smaller;">₱ @if($curShift){{ number_format($curShift->starting_cash, 2) }}@else 0.00 @endif</td>
                                        </tr>
                                        <tr>
                                            <th style="font-size: smaller;">Ending Balance:</th>
                                            <td style="font-size: smaller;">₱ @if($curShift) {{ number_format($curShift->ending_cash, 2) ?? 'N/A' }}@else 0.00 @endif</td>
                                        </tr>
                                        <tr>
                                            <th style="font-size: smaller;">Remarks:</th>
                                            <td style="font-size: smaller;"> @if($curShift) {{ $curShift->notes ?? 'N/A' }}@endif</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="">
                                    <div class="card bg-light text-center mb-3">
                                        <h6 class="mb-2 mt-3">Total Refund</h6>
                                        <h5 class="mb-3">₱ 0.00</h5>
                                </div>
                                <div class="card bg-light text-center mb-3">
                                        <h6 class="mb-2 mt-3">Total Collections</h6>
                                        <h5 class="mb-3">₱ @if($shift){{ number_format($shift->shiftDenominations->sum(function($item) {
                                            return $item->quantity * $item->denomination->value;
                                        }), 2) }}@else 0.00 @endif</h5>
                                </div>
                                <div class="card bg-light text-center mb-3">
                                        <h6 class="mb-2 mt-3">Current Shift Sales</h6>
                                        <h5 class="mb-3">₱ @if($curShift){{ number_format($curShift->payments->sum('amount'), 2) }}@else 0.00 @endif</h5>
                                </div>
                            </div>
                        </div>
                   </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END PAYMENTS MODAL --}}

    <script>
        window.addEventListener('showShiftDetails', event => {
            var myModal = new bootstrap.Modal(document.getElementById('ViewShiftDetails'), {
                keyboard: false
            });
            myModal.show();
        });
    </script>
</div>