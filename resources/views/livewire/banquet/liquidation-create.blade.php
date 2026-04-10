<div>
  <div class="d-flex justify-content-end">
     <h5>BEO LIQUIDATION - CREATE</h5><x-icon name="calculator" class="w-8 h-8" outline />
  </div>
    <div class="row align-content-center g-2 ">
        {{-- Left --}}
        <div class="col-md-6">
           <x-card title="Expenses" rounded="3xl" padding="small">
               <table class="table table-sm">
                    <thead>
                        <tr class="table-dark">
                            <th>Status</th>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Rec. REF</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchaseOrders ?? [] as $purchase)
                            <tr>
                                <td>{{ $purchase->requisition_status }}</td>
                                <td>{{ $purchase->created_at->format('M. d, Y') }}</td>
                                <td>{{ $purchase->requisition_number }}</td>
                                <td>
                                    @if($purchase->receivings->isEmpty())
                                        <span class="text-muted italic small">No records</span>
                                    {{-- @elseif($purchase->receivings->count() === 1)
                                        Single record: Show as a simple badge for zero-click visibility
                                        <span class="badge rounded-pill bg-light text-dark border">
                                            {{ $purchase->receivings->first()->RECEIVING_NUMBER }}
                                        </span> --}}
                                    @else
                                        {{-- Multiple records: Use a Bootstrap Dropdown --}}
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" 
                                                    id="dropdownMenu{{ $purchase->id }}" 
                                                    data-bs-toggle="dropdown" 
                                                    aria-expanded="false">
                                                <i class="bi bi-list-check"></i> {{ $purchase->receivings->count() }} Recs
                                            </button>
                                            <ul class="dropdown-menu shadow-sm" aria-labelledby="dropdownMenu{{ $purchase->id }}">
                                                <li class="dropdown-header font-weight-bold">Receiving Numbers</li>
                                                @foreach($purchase->receivings as $receiving)
                                                    <li>
                                                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                                            {{ $receiving->RECEIVING_NUMBER }}
                                                            <small class="text-muted ms-2">{{ $receiving->created_at->format('m/d/Y') }}</small>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    ₱{{ number_format($purchase->total_received_amount ?? $purchase->receivings->sum('receive_amount') ?? 0, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <x-slot name="footer" class="flex items-end" >
                    <h6><strong> Total</strong> : ₱  {{ number_format($incurredAmount,2) }}</h6>
                </x-slot>
           </x-card>
        </div>
        {{-- end of left --}}

        {{-- right tab --}}
        <div class="col-md-6">
            <div class="row mb-2">
                <div class="col-md-6">
                    <x-input
                    label="Reference"
                    placeholder="<Auto>"
                    :readonly="true"
                    wire:model="referenceNumber"
                />
                </div>
                <div class="col-md-6">
                    <x-datetime-picker
                        wire:model.live="createDate"
                        label="Liquidation Date"
                        placeholder="<Auto>"
                        parse-format="DD-MM-YYYY HH:mm"
                        :readonly="true"

                    />
                </div>
            </div>

            <div class="m2 card p-2">
                <div class="row">
                    <div class="col-md-6">
                        <x-select
                            label="Event" 
                            placeholder="Select BEO ..."
                            :options="$events"
                            option-value="id"
                            :min-items-for-search="0"
                            option-label="event_name"
                            wire:model.live="selectedEventId"
                            class="mb-3"
                            :readonly="$isLiquidationExists" 
                        />
                    </div>
                    <div class="col-md-6">
                         <x-input
                            label="Check Number"
                            placeholder="<Auto>"
                            :readonly="true"
                            wire:model="checkNumber"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-textarea rows="1" label="Purpose" placeholder="Additional Info." wire:model="liquidationNotes" :readonly="$isOpenStatus" />
                    </div>
                    <div class="col-md-6">
                        <x-input label="REF. / CRS No." placeholder="N/A" wire:model="crsReference" :readonly="$hasCRS">
                            <x-slot name="append">
                                <x-button
                                    class="h-full"
                                    icon="plus"
                                    rounded="rounded-r-md"
                                    primary
                                    flat
                                    wire:click="createCRS"
                                />
                            </x-slot>
                        </x-input>
                    </div>
                </div>
                <x-card title="Reconciliation"  padding="none" shadow="none">
                   <div class="row mt-2 mb-2">
                        <div class="col-md-6">
                            <x-currency
                                label="Cash-out amount"
                                prefix="₱"
                                thousands=","
                                wire:model="checkAmount"
                                :readonly="true"
                                placeholder="<Auto>"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-currency
                                label="Total incurred amount"
                                prefix="₱"
                                thousands=","
                                wire:model="incurredAmount"
                                :readonly="true"
                                placeholder="<Auto>"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <x-currency
                                label="Net : Excess / Short"
                                prefix="₱"
                                thousands=","
                                wire:model="remarks"
                                :readonly="true"
                                placeholder="<Auto>"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-currency
                                label="Returned Amount"
                                prefix="₱"
                                thousands=","
                                wire:model="amountReturned"
                                :readonly="true"
                                placeholder="<Auto>"
                            />
                        </div>
                    </div>
                </x-card>
                <x-card title="Validation"  padding="none" shadow="none">
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <x-select
                                label="Approved By" 
                                placeholder="Select approver"
                                :options="$approvers"
                                option-value="id"
                                :min-items-for-search="0"
                                option-label="full_name"
                                wire:model.live="selectedApproverId"
                                class="mb-3"
                                :readonly="$isOpenStatus"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-select
                                label="Reviewed & Validated by" 
                                placeholder="Select reviewer"
                                :options="$reviewers"
                                option-value="id"
                                :min-items-for-search="0"
                                option-label="full_name"
                                wire:model.live="selectedReviewerId"
                                class="mb-3"
                                :readonly="$isOpenStatus"
                            />
                        </div>
                    </div>
                </x-card>
                <div class="d-flex mt-3">
                    <div class="container">
                        <div class="input-group">
                            <select name="" id="" class="form-select" wire:model="saveAs" {{ $isOpenStatus ? 'disabled' : '' }}>
                                <option value="DRAFT" {{ $saveAs === 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                                <option value="OPEN" {{ $saveAs === 'OPEN' ? 'selected' : '' }}>FINAL</option>
                            </select>
                            @if(!$isOpenStatus)
                                <x-primary-button wire:click="saveLiquidation" wire:loading.attr="disabled" class="ml-2">
                                    <span wire:loading.remove wire:target="saveLiquidation">Save</span>
                                    <span wire:loading wire:target="saveLiquidation">Saving...</span>
                                </x-primary-button>
                            @endif
                            
                        </div>
                    </div>
                    <a href="{{ route('beo.liquidation.summary') }}"><x-secondary-button>Summary</x-secondary-button></a>
                </div>
            </div>
        </div>
        {{-- end of right tab --}}
    </div>
 
        <x-modal-card title="Create CRS" name="cardModal">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-input label="Event" placeholder="No selected BEO"  wire:model="eventName" :readonly="true"/>
        
                <x-input label="CRS Reference No." placeholder="<Auto>" :readonly="true"/>
        
                <div class="col-span-1 sm:col-span-2">
                    <x-currency
                                label="Return Amount"
                                prefix="₱"
                                thousands=","
                                wire:model="returnAmount"
                                placeholder="No Selected Event"
                            />
                </div>
                 <x-textarea rows="1" label="Note" placeholder="Note" wire:model="crsNote"/>
                 <x-datetime-picker
                        wire:model.live="createDate"
                        label="CRS Date"
                        parse-format="DD-MM-YYYY HH:mm"
                        :readonly="true"

                    />
                 <x-select
                        label="Received By" 
                        placeholder="Select"
                        :options="$crsApprover"
                        option-value="id"
                        :min-items-for-search="0"
                        option-label="full_name"
                        wire:model.live="selectedCrsApproverId"
                        class="mb-3"
                    />
            </div>
        
            <x-slot name="footer" class="flex justify-between gap-x-4">
                <x-button flat negative label="Cancel" x-on:click="close" />
        
                <div class="flex gap-x-4">
                    <x-button primary label="Save" wire:click="saveCrs" />
                </div>
            </x-slot>
        </x-modal-card>

      <x-notifications />

</div>
