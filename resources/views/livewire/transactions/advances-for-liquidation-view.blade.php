<div class="">
    <div class="d-flex justify-content-between row">
        @if(auth()->user()->employee->getModulePermission('Advances For Liquidation') == 1 )
        <div class="d-flex justify-content-between col-md-6">
                <div class="input-group">
                    <select name="" id="" class="input-group-text form-select" wire:model="saveAsStatus">
                        @if($currentAFLStatus == 'DRAFT')
                        <option value="DRAFT"  @if($currentAFLStatus == 'DRAFT') selected @endif>DRAFT</option>
                        @endif
                        <option value="OPEN" @if($currentAFLStatus == 'OPEN') selected @endif>FINAL</option>
                    </select>
                    @if($isCreate)
                        <x-primary-button wire:click="saveAFL" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveAFL">Save</span>
                            <span wire:loading wire:target="saveAFL">Saving...</span>
                        </x-primary-button>
                    @elseif($currentAFLStatus == 'DRAFT' && !$isCreate)
                        <x-primary-button wire:click="updateAFL" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="updateAFL">UPDATE</span>
                            <span wire:loading wire:target="updateAFL">Updating...</span>
                        </x-primary-button>
                    @endif
                </div>
                @if($currentAFLStatus == 'OPEN' && !$this->hasReturnedAmount)
                    <x-primary-button class="ml-2 h-10" style="white-space: nowrap" data-bs-toggle="modal" data-bs-target="#amountReturnModal">Return Excess</x-primary-button>
                @endif
            <a href="/advances-for-liquidation-summary">
                <x-secondary-button class="ml-2 h-10">Summary</x-secondary-button>
            </a>
        </div>
        @endif
        <div class="d-flex col-md-6 justify-content-end">
            <h4 class="m-2">Advances for Liquidation - Create <i class="bi bi-file-text"></i></h4>
        </div>
    </div>

    <div class="dashboard">
        <div class="dashboard-content">
            <div class="row">
                <div class="input-group col-md-12 mb-2">
                    <label for="" class="input-group-text">Reference</label>
                    <input type="text" class="form-control form-control-sm" placeholder="<AUTO>" wire:model="reference" disabled>
                </div>
                <div class="input-group col-md-12 mb-2">
                    <label for="" class="input-group-text">Disburser</label>
                    <select name="" id="" class="form-select form-select-sm" wire:model="disburserId">
                        <option value="">Select Disburser</option>
                        @foreach($disbursers as $disburser)
                            <option value="{{ $disburser->employee->id }}" @if($disburserId == $disburser->employee->id) selected @endif>{{ $disburser->employee->name }} {{ $disburser->employee->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                    @error('disburserId')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                    <div class="row">
                        <div class="col-md-6">
                             <div class="input-group col-md-12 mb-3">
                                <label for="" class="input-group-text">Amount Received</label>
                                <input type="number" class="form-control form-control-sm" placeholder="Enter Amount Received" wire:model.live="amountReceive" @if($currentAFLStatus != 'DRAFT') disabled @endif>
                            </div> 
                        @error('amountReceive')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        </div>
                         <div class="col-md-6">
                             <div class="input-group col-md-12 mb-2">
                                <label for="" class="input-group-text">Amount Returned</label>
                                <input type="number" class="form-control form-control-sm" placeholder="Enter Amount Returned" wire:model="amountReturn" disabled>
                             </div>
                            @error('amountReturn')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="input-group mb-4">
                            <label for="" class="input-group-text">Total AFL Amount</label>
                            <input type="number" @if($totalAFLAmount<0) style="color:brown;" @endif class="form-control form-control-sm" placeholder="Total AFL Amount" value="{{ $totalAFLAmount }}" disabled>
                    </div>
                    @error('totalAFLAmount')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

               

                <div class="mb-3">
                    <label for="" class="form-label">Note</label>
                    <textarea class="form-control form-control-sm" placeholder="Enter Note" wire:model="notes"></textarea>
                </div>
                @error('notes')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <label for="" class="input-group-text">Prepared By</label>
                            <input type="text" class="form-control form-control-sm" placeholder="{{ auth()->user()->employee->name }} {{ auth()->user()->employee->last_name }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                             <label for="" class="input-group-text">Approved By</label>
                             <select name="" id="" class="form-select" wire:model="approverId">
                                <option value="">Select Approver</option>
                                @foreach($approvers as $approver)
                                    <option value="{{ $approver->employees->id }}" @if($approverId == $approver->employees->id) selected @endif>{{ $approver->employees->name }} {{ $approver->employees->last_name }}</option>
                                @endforeach
                             </select>
                        </div>
                        @error('approverId')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- amount return modal --}}
        <div class="modal fade" id="amountReturnModal" tabindex="-1" aria-labelledby="amountReturnModalLabel" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="amountReturnModalLabel">Return Excess Amount</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="input-group mb-3">
                            <label for="" class="input-group-text">Amount to Return</label>
                            <input type="number" class="form-control form-control-sm" placeholder="Enter Amount to Return" wire:model.live="amountReturn">
                        </div>
                        @error('amountReturn')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" wire:click="returnAmount">
                            
                           <span wire:loading.remove wire:target="returnAmount">Return Amount</span>
                            <span wire:loading wire:target="returnAmount">Wait...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    <script>
        window.addEventListener('showAlert', event => {
           const data = event.detail[0];
              Swal.fire({
                icon: data.type,
                title: data.title,
                text: data.message,
                timer: 5000,
                showConfirmButton: true,
                });
                // redirect to summary page after saving
                if(data.type === 'success' && data.title === 'Success'){
                    setTimeout(() => {
                        window.location.href = '/advances-for-liquidation-summary';
                    }, 100); // Redirect after 1 second (same as the timer duration of the alert)
                }
        });

        window.addEventListener('showConfirmOption', event =>{
            const data = event.detail[0];
            Swal.fire({
                title: 'Are you sure ?',
                text: 'This will save automatically upon confirmation.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonColor: '#F2385A',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.returnAmountConfirm();
                }
            });
        });
    </script>    
</div>
