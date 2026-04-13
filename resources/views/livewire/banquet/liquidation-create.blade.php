<div>
  <div class="row">
    <div class="col-md-6">
        @if($status == 'DRAFT')
            <x-badge flat secondary :label="$status">
                <x-slot name="prepend" class="relative flex items-center w-2 h-2">
                    <span class="absolute inline-flex w-full h-full rounded-full opacity-75 bg-secondary animate-ping"></span>
                    <span class="relative inline-flex w-2 h-2 rounded-full bg-secondary"></span>
                </x-slot>
            </x-badge>
        @elseif($status == 'OPEN')
            <x-badge flat warning :label="$status">
                <x-slot name="prepend" class="relative flex items-center w-2 h-2">
                    <span class="absolute inline-flex w-full h-full rounded-full opacity-75 bg-warning animate-ping"></span>
                    <span class="relative inline-flex w-2 h-2 rounded-full bg-warning"></span>
                </x-slot>
            </x-badge>
        @elseif($status == 'CLOSED')
        <x-badge flat primary :label="$status">
            <x-slot name="prepend" class="relative flex items-center w-2 h-2">
                <span class="absolute inline-flex w-full h-full rounded-full opacity-75 bg-cyan-500 "></span>
                <span class="relative inline-flex w-2 h-2 rounded-full bg-cyan-500"></span>
            </x-slot>
        </x-badge>
        @elseif($status == 'CANCELLED')
        <x-badge flat negative :label="$status">
            <x-slot name="prepend" class="relative flex items-center w-2 h-2">
                <span class="absolute inline-flex w-full h-full rounded-full opacity-75 bg-red-500 "></span>
                <span class="relative inline-flex w-2 h-2 rounded-full bg-red-500"></span>
            </x-slot>
        </x-badge>
         @endif
    </div>
    <div class="col-md-6">
        <div class="d-flex align-items-end justify-content-end">
            @if($isApproval)
                <h5>BEO LIQUIDATION - APPROVAL</h5>
            @elseif($isValidator)
                <h5>BEO LIQUIDATION - VALIDATE</h5>
            @elseif($isLiquidationExists && !$isApproval && !$isValidator) 
                <h5>BEO LIQUIDATION - VIEW</h5>
            @elseif($isEditable && !$isLiquidationExists)
                <h5>BEO LIQUIDATION - CREATE</h5>
            @endif
             <x-icon name="calculator" class="w-8 h-8" outline />
        </div>
    </div>
  </div>
    <div class="row align-content-center g-2 ">
        {{-- Left --}}
        <div class="col-md-6">
           <x-card title="Expenses" rounded="3xl" padding="small">
               <table class="table table-sm">
                    <tbody>
                        <tr><td colspan="8" class="text-center text-xs"><strong>Petty Cash Vouchers</strong></td></tr>
                        <tr>
                            <th class="text-xs">Status</th>
                            <th class="text-xs">Date</th>
                            <th class="text-xs">Reference</th>
                            <th class="text-xs">Payee</th>
                            <th class="text-xs">PCV amount</th>
                            <th class="text-xs">Return amount</th>
                            <th class="text-xs">Total</th>
                        </tr>
                        @foreach ($pettyCashVouchers ?? [] as $voucher)
                            <tr>
                                <td class="text-xs">{{ $voucher->status }}</td>
                                <td class="text-xs">{{ $voucher->created_at->format('M. d, Y') }}</td>
                                <td class="text-xs">{{ $voucher->reference }}</td>
                                <td class="text-xs">
                                    @if($voucher->employee)
                                        {{ $voucher->employee->name }} {{ $voucher->employee->last_name }}
                                    @elseif($voucher->customer)
                                        {{ $voucher->customer->customer_fname }} {{ $voucher->customer->customer_lname }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="text-xs">₱{{ number_format($voucher->total_amount, 2) }}</td>
                                <td class="text-xs">₱{{ number_format($voucher->cashReturn->amount_returned ?? 0, 2) }}</td>
                                <td class="text-xs">₱{{ number_format($voucher->total_amount - ($voucher->cashReturn->amount_returned ?? 0), 2) }}</td>
                            </tr>
                        @endforeach
                        <tr><td colspan="8" class="text-center"><strong>Purchase Orders</strong></td></tr>
                        <tr class="table-light">
                            <td class="text-xs"><strong>Status</strong></td>
                            <td class="text-xs" colspan="2"><strong>Date</strong></td>
                            <td class="text-xs" colspan="2"><strong>Reference</strong></td>
                            <td class="text-xs"><strong>Rec. REF</strong></td>
                            <td class="text-xs"><strong>Amount</strong></td>
                        </tr>
                        @foreach ($purchaseOrders ?? [] as $purchase)
                            <tr>
                                <td class="text-xs">{{ $purchase->requisition_status }}</td>
                                <td class="text-xs" colspan="2">{{ $purchase->created_at->format('M. d, Y') }}</td>
                                <td class="text-xs" colspan="2">{{ $purchase->requisition_number }}</td>
                                <td class="text-xs">
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
                    <h6><strong> Total</strong> : ₱  {{ number_format($totalExpense,2) }}</h6>
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
                        <x-textarea rows="1" label="Purpose" placeholder="Additional Info." wire:model="liquidationNotes" :readonly="!$isEditable" />
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
                                wire:model.live="incurredAmount"
                                :readonly="!$isEditable"
                                placeholder="Enter amount"
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
                                :readonly="!$isEditable"
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
                                :readonly="!$isEditable"
                            />
                        </div>
                    </div>
                </x-card>
                <div class="d-flex mt-3">
                    <div class="container">
                        <div class="input-group">
                            @if($isApproval)
                                <select name="" id="" class="form-select" wire:model="saveAs">
                                     <option value="" >Select Action</option>
                                    <option value="APPROVED" >APPROVED</option>
                                    <option value="REVISE" >REVISE</option>
                                </select>
                                <x-primary-button wire:click="approvalAction" wire:loading.attr="disabled" class="ml-2">
                                    <span wire:loading.remove wire:target="approvalAction">Save</span>
                                    <span wire:loading wire:target="approvalAction">Saving...</span>
                                </x-primary-button>
                            @elseif($isValidator)
                                <select name="" id="" class="form-select" wire:model="saveAs">
                                    <option value="" >Select Action</option>
                                    <option value="VALIDATED" >VALIDATED</option>
                                    <option value="REVISE" >REVISE</option>
                                </select>
                                <x-primary-button wire:click="validationAction" wire:loading.attr="disabled" class="ml-2">
                                    <span wire:loading.remove wire:target="validationAction">Save</span>
                                    <span wire:loading wire:target="validationAction">Saving...</span>
                                </x-primary-button>
                            @else
                                @if ($status != 'CLOSED') 
                                    <select name="" id="" class="form-select" wire:model="saveAs" {{ $isEditable ? '' : 'disabled' }}>
                                        <option value="" >Select Action</option>
                                        <option value="DRAFT" {{ $saveAs === 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                                        <option value="OPEN" {{ $saveAs === 'OPEN' ? 'selected' : '' }}>FINAL</option>
                                    </select>
                                @endif
                                @if($isEditable && !$isLiquidationExists)
                                    <x-primary-button wire:click="saveLiquidation" wire:loading.attr="disabled" class="ml-2">
                                        <span wire:loading.remove wire:target="saveLiquidation">Save</span>
                                        <span wire:loading wire:target="saveLiquidation">Saving...</span>
                                    </x-primary-button>
                                @elseif($isEditable && $isLiquidationExists)
                                    <x-primary-button wire:click="updateLiquidation" wire:loading.attr="disabled" class="ml-2">
                                        <span wire:loading.remove wire:target="updateLiquidation">Update</span>
                                        <span wire:loading wire:target="updateLiquidation">Updating...</span>
                                    </x-primary-button>
                                @endif
                            @endif
                        </div>
                         @error('saveAs')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                    </div>
                    @if($isApproval)
                        <a href="{{ route('beo.liquidation.approval.lists') }}"><x-secondary-button>Summary</x-secondary-button></a>
                    @elseif($isValidator)
                        <a href="{{ route('beo.liquidation.validate.lists') }}"><x-secondary-button>Summary</x-secondary-button></a>
                    @else
                        <a href="{{ route('beo.liquidation.summary') }}"><x-secondary-button>Summary</x-secondary-button></a>
                    @endif
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
