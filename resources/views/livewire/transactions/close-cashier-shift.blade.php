<div>
    <div class=" card g-4">
        <div class="card-header justify-content-between d-flex"><h4><i class="bi bi-clock-history">  </i> Close Cashier Shift</h4>
        <h5><i class="bi bi-person"> </i>Cashier: {{ $cashierShift->employee->name . ' ' . $cashierShift->employee->last_name ?? '' }}</h5>
        </div>
        <div class="card-body row g-4">
            {{-- DENOMINATIONS --}}
            <div class="col-md-6 mb-2">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-start" style="width: 40%;">Denomination</th>
                                        <th class="text-center" style="width: 30%;">Count</th>
                                        <th class="text-end" style="width: 30%;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-secondary"><td colspan="3" class="fw-bold"><h3>Bills <i class="bi bi-cash-stack"></i></h3></td></tr>
                                    @foreach ($billDenominations as $denomination)
                                        <tr>
                                            <td class="text-start fw-bold">₱ {{ number_format($denomination->value, 2) }}</td>
                                            <td class="text-center">
                                                <input type="number" min="0" wire:model.live="denominationCounts.{{ $denomination->id }}" class="form-control text-center" placeholder="0">
                                            </td>
                                            <td class="text-end text-primary fw-bold">₱ {{ number_format((float)($denominationCounts[$denomination->id] ?? 0) * (float)$denomination->value, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-secondary"><td colspan="3" class="fw-bold"><h3>Coins <i class="bi bi-coin"></i></h3></td></tr>
                                    @foreach ($coinDenominations as $denomination)
                                        <tr>
                                            <td class="text-start fw-bold">₱ {{ number_format($denomination->value, 2) }}</td>
                                            <td class="text-center">    
                                                <input type="number" min="0" wire:model.live="denominationCounts.{{ $denomination->id }}" class="form-control text-center" placeholder="0" style="max-width: 150px; margin: 0 auto;">
                                            </td>
                                            <td class="text-end text-primary fw-bold">₱ {{ number_format((float)($denominationCounts[$denomination->id] ?? 0) * (float)$denomination->value, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- SHIFT SUMMARY AND DETAILS --}}
            <div class="col-md-6">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center bg-light">
                                <h6 class="text-muted mb-2">Beginning Balance</h6>
                                <h4 class="text-primary fw-bold mb-0">₱ {{ number_format($cashierShift->starting_cash, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center bg-light">
                                <h6 class="text-muted mb-2">Current Shift Sales</h6>
                                <h4 class="text-success fw-bold mb-0">₱ {{ number_format($cashierShift->payments->sum('amount'), 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center bg-light">
                                <h6 class="text-muted mb-2">Difference</h6>
                                <h4 class="text-warning fw-bold mb-0">₱ 0.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-4 shadow-sm">
                    <div class="card-header">
                        <strong>Status</strong> 
                        <span class="badge @if($cashierShift->shift_status == 'OPEN') bg-success @elseif($cashierShift->shift_status == 'CLOSED') bg-danger @endif">{{ $cashierShift->shift_status ?? '' }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="" class="form-label">Cash Drawer</label>
                                <input type="text" class="form-control" value="{{ $cashierShift->cashDrawer->drawer_name ?? '' }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="" class="form-label">Cashier Shift Reference</label>
                                <input type="text" class="form-control" value="{{ $cashierShift->reference ?? '' }}" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="" class="form-label">Shift Started</label>
                                <input type="text" class="form-control" value="{{ $cashierShift->shift_started ? \Carbon\Carbon::parse($cashierShift->shift_started)->format('M. d, Y @ g:iA') : '' }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="" class="form-label">Shift Ended</label>
                                <input type="text" class="form-control" value="{{ $cashierShift->shift_ended ?? 'N/A' }}" disabled>
                            </div>
                        </div>
                        <div>
                            <label for="" class="form-label mt-3 mb-3">Notes</label>
                            <textarea class="form-control" rows="3" @if($cashierShift->shift_status == 'CLOSED') disabled @endif wire:model="notes">{{ $cashierShift->notes ?? '' }}</textarea>
                        </div>
                        <div class="mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="verified" id="verifiedCheck" @if($cashierShift->shift_status == 'CLOSED') disabled @endif>
                                <label class="form-check-label" for="verifiedCheck">
                                    I have verified that all the information indicated are correct
                                </label>
                            </div>
                            <button class="btn btn-primary mt-3" wire:click="closeShift" @if($cashierShift->shift_status == 'CLOSED') disabled @endif>Close Shift</button>
                            <button class="btn btn-secondary mt-3" wire:click="printShiftReport" @if($cashierShift->shift_status == 'OPEN') hidden @endif>Print Review</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

       <script>
  

        // Listen for Livewire alert event
        window.addEventListener('alert', event => {
            const data = event.detail[0];
            Swal.fire({
                icon: data.type,
                title: data.type === 'success' ? 'Success!' : 'Error!',
                text: data.message,
                timer: 5000,
                showConfirmButton: false
            });
        });
    </script>

</div>
