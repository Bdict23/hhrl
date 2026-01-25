<div>
    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="card shadow-sm">
        <div class="card-header bg-light text-dark">
            <div class="mb-2 row">
                <div class="col-md-6 d-flex align-items-center">
                    <i class="bi bi-inboxes me-2"></i><select name="drawer" id="" class="form-control" wire:model.live="drawerId">
                        <option value="">Select Cash Drawer</option>
                        @foreach($drawers as $drawer)
                            <option value="{{ $drawer->id }}">{{ $drawer->drawer_name }}</option>
                        @endforeach
                    </select>
                </div>
                 <div class="text-end col-md-6 d-flex align-items-center justify-content-end">
                    <h3> <i class="bi bi-cash-coin"> </i> Beginning Balance</h3>
                </div>
            </div>
           
        </div>
        <div class="card-body" wire:ignore.self>
            <div class="row g-4">
                <!-- Bills Section -->
                <div class="col-md-6">
                    <div class="card border-0 bg-light">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Bills</h5>
                        </div>
                        <div class="card-body">
                            @foreach ($billDenominations as $denomination)
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4">
                                        <span class="fw-bold">₱ {{ number_format($denomination->value, 2) }}</span>
                                    </div>
                                    <div class="col-4">
                                        <input type="number" min="0" wire:model.live="denominationCounts.{{ $denomination->id }}" class="form-control text-end" placeholder="0">
                                    </div>
                                    <div class="col-4">
                                        <span class="text-primary fw-bold">₱ {{ number_format((float)($denominationCounts[$denomination->id] ?? 0) * (float)$denomination->value, 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Coins Section -->
                <div class="col-md-6">
                    <div class="card border-0 bg-light">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Coins</h5>
                        </div>
                        <div class="card-body">
                            @foreach ($coinDenominations as $denomination)
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4">
                                        <span class="fw-bold">₱ {{ number_format($denomination->value, 2) }}</span>
                                    </div>
                                    <div class="col-4">
                                        <input type="number" min="0" wire:model.live="denominationCounts.{{ $denomination->id }}" class="form-control text-end" placeholder="0">
                                    </div>
                                    <div class="col-4">
                                        <span class="text-primary fw-bold">₱ {{ number_format((float)($denominationCounts[$denomination->id] ?? 0) * (float)$denomination->value, 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Section -->
            <div class="card mt-4 border-primary">
                <div class="card-body bg-light text-center">
                    <h4 class="mb-2">Total Beginning Balance</h4>
                    <h2 class="text-primary fw-bold">₱ {{ number_format($totalBeginningBalance, 2) }}</h2>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4 text-center">
                
                <button wire:click="submitShift" class="btn btn-primary btn-lg px-5">
                    <span wire:loading.remove wire:target="submitShift">Start Shift</span>
                    <span wire:loading wire:target="submitShift">Processing...</span>
                </button>
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
