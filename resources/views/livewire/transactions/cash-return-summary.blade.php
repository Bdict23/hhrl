<div class="overflow-x-auto">
    <div class="container mb-3">
            <div class="row">
                <div class="col-md-6">
                    @if(auth()->user()->employee->getModulePermission('Acknowledgement Receipt') == 1 )
                        <x-primary-button x-on:click="$openModal('cardModal')" >+ PCV CRS</x-primary-button>
                        <a href="" style="text-decoration: none; color: white;"><x-primary-button >+ Event CRS</x-primary-button></a>
                        <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4 class="text-end">Cash Return - Summary <i class="bi bi-file-text"></i></h4>
                </div>
            </div>
        </div>
        <div class="card mt-3 mb-3">  
            <div class=" card-header d-flex justify-content-between mx-2">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="input-group">
                                <label for="CHECK-status" class="input-group-text">Status</label>
                                <select wire:model="statusCheckValue" id="CHECK-status"  class="form-select form-select-sm">
                                    <option value="ALL">ALL</option>
                                    <option value="OPEN">DRAFT</option>
                                    <option value="CLOSED">CLOSED</option>
                                    <option value="CANCELLED">CANCELLED</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="input-group">
                                <label for="from_date" class="input-group-text">From:</label>
                                <input wire:model="fromDate" type="date" id="from_date" name="from_date" value="{{ date('Y-m-d') }}"
                                    class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="input-group">
                                <label for="to_date" class="input-group-text">To:</label>
                                <input wire:model="toDate" type="date" id="to_date" name="to_date" value="{{ date('Y-m-d') }}"
                                    class="form-control form-control-sm">
                                <button wire:click="search" class="btn btn-primary input-group-text">
                                    <span wire:loading.remove>Search <i class="bi bi-search"></i></span>
                                    <span wire:loading>Searching&nbsp;<span class="spinner-border spinner-border-sm" role="status"></span></span>
                                </button>  
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        <div class="card-body ">
                <div style="height: 500px; overflow-x: auto; display: block;">
                    <table class="table table-striped table-hover table-sm " >
                        <thead class="table-dark">
                            <tr>
                                <th>Ref.</th>
                                <th>Status</th>
                                <th>PCV Ref.</th>
                                <th>Event Ref.</th>
                                <th>Prepared By</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cashReturns as $crs)
                                <tr>
                                    <td>{{ $crs->reference }}</td>
                                    <td> <span 
                                        @if( $crs->status =='DRAFT' ) class = "badge bg-secondary" 
                                        @elseif($crs->status =='CANCELLED') class= "badge bg-danger" 
                                        @elseif($crs->status =='FINAL') class="badge bg-success" 
                                        @endif>{{ $crs->status }}</span> </td>
                                    <td>{{ $crs->pettyCashVoucher->reference ?? '' }}</td>
                                    <td>{{ $crs->event->reference ?? '-' }}</td>
                                    <td>{{ $crs->preparedBy->name ?? '' }}</td>
                                    <td>₱ {{ number_format($crs->amount_returned, 2) }}</td>
                                    <td>{{ $crs->created_at->format('M. d, Y') }}</td>
                                    <td>
                                        @if($crs->pcv_id)
                                             <x-primary-button class="btn-sm" wire:click="viewCashReturnPCV({{ $crs->pcv_id }})">View</x-primary-button>
                                        @elseif($crs->event_id)
                                             <x-primary-button class="btn-sm" wire:click="viewCashReturnEvent({{ $crs->event_id }})">View</x-primary-button>
                                        @else
                                             <span class="text-muted">No Action</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No records found</td>
                                </tr>
                            @endforelse
                    
                        </tbody>
                    </table>
                </div>
        </div>
    </div>

    {{-- cash return for PCV --}}

    <x-modal-card title="Cash Return Slip - PCV" name="cardModal" wire:ignore.self>
        

         <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-3">
            <div class="input-group">
                <label for="" class="input-group-text">Reference</label>
                <input type="text" class="form-control form-control-sm" placeholder="<AUTO>" disabled wire:model="cvReferenceNumber">
            </div>
            <div class="input-group">
                <label for="" class="input-group-text">Return Date</label>
                <input type="text" class="form-control form-control-sm" value="{{ $pcrDate }}" disabled>
            </div>
         </div>
        <x-select
            label="Petty Cash Voucher" 
            placeholder="Select PCV ..."
            :options="$pettyCashVouchersWithoutCashReturn"
            option-value="id"
            :min-items-for-search="0"
            option-label="reference"
            wire:model.live="selectedPCVId"
        />

        <input type="number" class="form-control mt-2" placeholder="Enter amount to return" wire:model.live="returnAmountPCV">

        <div class="card mt-3">
            <table class="table table-sm mt-3 mb-3">
                <thead class="table-dark">
                    <th>
                        <td class="text-start"></td>
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4">PCV Details</td>
                    </tr>
                    @forelse ($selectedPCV as $pcv)
                            <tr>
                                <td><strong>PCV Date :</strong></td>
                                <td>{{($pcv->created_at->format('M. d, Y'))}}</td>
                            </tr>
                            <tr>
                                <td><strong>Transaction :</strong></td>
                                <td>{{ $pcv->transaction_title}}</td>
                            </tr>
                            <tr>
                                <td><strong>Amount :</strong></td>
                                <td>₱ {{number_format( $pcv->total_amount ,2 ) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Expense :</strong></td>
                                <td><strong @if($returnAmountPCV > $pcv->total_amount) class="text-danger" @endif>₱ {{ $returnAmountPCV ? number_format($pcv->total_amount - $returnAmountPCV, 2) :  number_format($pcv->total_amount, 2) }}</strong></td>
                            </tr>
                    @empty
                       
                    @endforelse
                </tbody>
            </table>
        </div>

       <x-textarea label="Notes" placeholder="write your notes" wire:model="pcvNote"/>
    
        <x-slot name="footer" class="flex justify-content-between gap-x-4">
    
            <x-button flat label="Cancel" x-on:click="close" />

            <div class="container">
                <div class="flex gap-x-4">
                    <div class="input-group">
                        <select name="" id="" class="form-select form-select-sm" wire:model="saveAsPcvCrs">
                            <option value="DRAFT">DRAFT</option>
                            <option value="FINAL">FINAL</option>
                        </select>
                        <x-primary-button wire:loading.attr="disabled" wire:click="savePcvCrs" wire:loading.attr="disabled">
                            <span wire:loading wire:target="savePcvCrs"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>&nbsp;Saving...</span>
                            <span wire:loading.remove wire:target="savePcvCrs">Save As</span>
                         </x-primary-button>
                    </div>
                </div>
            </div>
        </x-slot>
    </x-modal-card>

{{-- event CRS Modal --}}
     <x-modal-card title="Cash Return Slip - PCV" name="eventCRSModal" wire:ignore.self>
        

         <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-3">
            <div class="input-group">
                <label for="" class="input-group-text">Reference</label>
                <input type="text" class="form-control form-control-sm" placeholder="<AUTO>" disabled>
            </div>
            <div class="input-group">
                <label for="" class="input-group-text">Return Date</label>
                <input type="text" class="form-control form-control-sm" value="{{ today('Asia/Manila')->format('M. d, Y') }}" disabled>
            </div>
         </div>
        <x-select
            label="Petty Cash Voucher" 
            placeholder="Select PCV ..."
            :options="$pettyCashVouchers"
            option-value="id"
            :min-items-for-search="0"
            option-label="reference"
            wire:model.live="selectedPCVId"
        />

        <input type="number" class="form-control mt-2" placeholder="Enter amount to return" wire:model.live="returnAmountPCV">

        <div class="card mt-3">
            <table class="table table-sm mt-3 mb-3">
                <thead class="table-dark">
                    <th>
                        <td class="text-start"></td>
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4">PCV Details</td>
                    </tr>
                    @forelse ($selectedPCV as $pcv)
                            <tr>
                                <td><strong>PCV Date :</strong></td>
                                <td>{{($pcv->created_at->format('M. d, Y'))}}</td>
                            </tr>
                            <tr>
                                <td><strong>Transaction :</strong></td>
                                <td>{{ $pcv->transaction_title}}</td>
                            </tr>
                            <tr>
                                <td><strong>Amount :</strong></td>
                                <td>₱ {{number_format( $pcv->total_amount ,2 ) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Expense :</strong></td>
                                <td><strong @if($returnAmountPCV > $pcv->total_amount) class="text-danger" @endif>₱ {{ $returnAmountPCV ? number_format($pcv->total_amount - $returnAmountPCV, 2) :  number_format($pcv->total_amount, 2) }}</strong></td>
                            </tr>
                    @empty
                       
                    @endforelse
                </tbody>
            </table>
        </div>

       <x-textarea label="Notes" placeholder="write your notes" wire:model="pcvNote"/>
    
        <x-slot name="footer" class="flex justify-content-between">
    
            <x-button flat label="Cancel" x-on:click="close" />

            <div class="container">
                <div class="flex gap-x-4">
                    <div class="input-group">
                        <select name="" id="" class="form-select form-select-sm" wire:model="saveAsPcvCrs">
                            <option value="DRAFT">DRAFT</option>
                            <option value="FINAL">FINAL</option>
                        </select>
                        <x-primary-button wire:loading.attr="disabled" wire:click="savePcvCrs" wire:loading.attr="disabled">
                            <span wire:loading wire:target="savePcvCrs"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>&nbsp;Saving...</span>
                            <span wire:loading.remove wire:target="savePcvCrs">Save As</span>
                         </x-primary-button>
                    </div>
                </div>
            </div>
        </x-slot>
    </x-modal-card>

    {{--  --}}



      <x-notifications />


</div>



