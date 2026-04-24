<div>
    @error('selectedItems')
        <div class="alert alert-danger" id="success-message">
            {{ $message }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @enderror
    <div class="row">
        {{-- asset lists --}}
        <div class="col-md-7">
             <x-card title="Added Items">
                <x-slot name="action">
                    <div class="d-flex">
                        @if($isEditable)
                            <x-primary-button lable="open" x-on:click="$openModal('cardModal')">add</x-primary-button>
                        @endif
                    </div>
                </x-slot>
            <table class="table table-sm">
               <thead class="table-dark">
                    <tr class="text-sm">
                         <th> Item Code</th>
                         <th> Item Name</th>
                         <th> Serial</th>
                         <th> S.I.#/D.R.#</th>
                         <th> Cost</th>
                         <th> Qty</th>
                         <th> Span</th>
                         <th> Condition</th>
                         @if($isEditable)
                            <th>Action</th>
                        @endif
                     </tr>
               </thead>
                <tbody>
                    
                    @forelse($selectedItems as $index => $item)
                       <tr>
                            <td>{{ $item['itemCode']}}</td>
                            <td>{{ $item['itemName']}}</td>
                            <td >{{ $item['serial']}}</td>
                            <td class="text-center">{{ $item['sidr']}}</td>
                            <td>₱ {{ $item['cost']}}</td>
                            <td class="text-center"> {{ $item['qty'] }}
                            <td class="text-center">{{ $item['span']}}</td>
                            <td class="text-center">{{ $item['condition']}}</td>
                            {{-- <td>  {!! QrCode::format('svg')
                                    ->generate('Hello World') !!} </td> --}}
                             @if($isEditable)
                                <td><x-danger-button wire:click="removeItem({{ $index }})">
                                    <span wire:loading.remove wire:target="removeItem({{ $index }})"><x-heroicons::outline.trash class="w-4 h-4"/></span>
                                    <span wire:loading wire:target="removeItem({{ $index }})"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i></span>
                                    </x-danger-button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No selected items</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
                
            </x-card>
        </div>
        {{-- form --}}
        <div class="col-md-5">
            <x-card title="Information">
                <x-slot name="action">
                    <div class="d-flex gap-2">
                        @if($saveAs != 'CANCELLED' | $saveAs != 'CLOSED')
                            <div class="input-group">
                                @if($saveAs == 'DRAFT')
                                    <select name="" id="" class="form-select" wire:model="saveAs">
                                        <option value="DRAFT">DRAFT</option>
                                        <option value="OPEN">FINAL</option>
                                    </select>
                                @elseif($action=='create')
                                    <x-badge outline cyan label="New" />
                                @elseif($action=='view')
                                    <x-badge outline warning label="OPEN" />
                                @elseif($action=='review' && $existingData->reviewed_date == null )
                                    <select name="" id="" class="form-select" wire:model="saveAs">
                                        <option value="DRAFT">REVISE</option>
                                        <option value="OPEN">REVIEWED</option>
                                    </select>
                                    <x-primary-button wire:click="reviewAction">
                                        <span wire:loading.remove wire:target="reviewAction">save </span>
                                        <span wire:loading wire:target="reviewAction">saving..</span>
                                    </x-primary-button>
                                @elseif($action=='approval' && $existingData->approved_date == null)
                                    <select name="" id="" class="form-select" wire:model="saveAs">
                                        <option value="DRAFT">REVISE</option>
                                        <option value="OPEN">APPROVED</option>
                                    </select>
                                    <x-primary-button wire:click="approvalAction">
                                        <span wire:loading.remove wire:target="approvalAction">save </span>
                                        <span wire:loading wire:target="approvalAction">saving..</span>
                                    </x-primary-button>
                                @endif
                                
                                @if($isNew)
                                    <x-primary-button wire:click="save">
                                        <span wire:loading.remove wire:target="save">save </span>
                                        <span wire:loading wire:target="save">saving..</span>
                                    </x-primary-button>
                                @elseif(!$isNew && $isEditable)
                                    <x-primary-button wire:click="updateBatch">
                                        <span wire:loading.remove wire:target="updateBatch">Update </span>
                                        <span wire:loading wire:target="updateBatch">updating..</span>
                                    </x-primary-button>
                                @endif
                            </div>
                           
                                
                            
                        @elseif($saveAs == 'CLOSED')
                            <x-badge outline info label="CLOSED" />
                        @else
                            <x-badge outline negative label="CANCELLED" />
                        @endif
                        @if($saveAs == 'CLOSED')
                            <x-primary-button style="white-space: nowrap" wire:click="exportToPdf">export qr code</x-primary-button>
                        @endif
                        @if($action=='view' || $action=='create')
                            <a href="{{ route('batch.summary') }}"><x-secondary-button>Summary</x-secondary-button></a>
                        @endif
                        @if($action=='review')
                            <a href="{{ route('asset-review-list') }}"><x-secondary-button>Summary</x-secondary-button></a>
                        @endif
                        @if($action=='approval')
                            <a href="{{ route('asset-approval-list') }}"><x-secondary-button>Summary</x-secondary-button></a>
                        @endif
                    </div>
                </x-slot>
            
                 <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-input label="Referene" placeholder="<Auto>" readonly="true" wire:model="reference"/>
                    <x-select
                        label="Type"
                        placeholder="Select type (optional)"
                        wire:model="selectedTypeId"
                        :options="$types"
                        option-label="requisition_number"
                        option-value="id"
                        :readonly="!$isEditable"
                    />
                     <x-select
                        label="P.O Reference"
                        placeholder="P.O Ref. (optional)"
                        :options="$purchaseOrders"
                        option-label="requisition_number"
                        option-value="id"
                        wire:model="selectedPurchaseOrderId"
                        :readonly="!$isEditable"

                    />
                    <x-datetime-picker
                        label="Date Issued"
                        placeholder="Issued Date"
                        without-timezone
                        without-time="true"
                        requires-confirmation="true"
                        wire:model="dateIssued"
                        :readonly="!$isEditable"

                    />
                    <x-input 
                        label="Purpose" 
                        placeholder="Purpose" 
                        wire:model="purpose"
                        :readonly="!$isEditable"
                     />
            
                    <div class="col-span-1 sm:col-span-2">
                    <x-textarea 
                        label="Notes" 
                        placeholder="write your notes" 
                        rows="2" 
                        wire:model="note"
                        :readonly="!$isEditable"
                        />
                    </div>
                     <x-select
                        label="Approved By"
                        placeholder="Select"
                        :options="$approvers"
                        option-label="full_name"
                        option-value="id"
                        wire:model="approverId"
                        :readonly="!$isEditable"

                    />
            
                     <x-select
                        label="Reviewed By"
                        placeholder="Select type"
                        :options="$reviewers"
                        option-label="full_name"
                        option-value="id"
                        wire:model="reviewerId"
                        :readonly="!$isEditable"
                    />
                    
                </div>
            </x-card>
        </div>
    </div>

    {{-- ad item modal --}}
        <x-modal-card title="Edit Customer" name="cardModal">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="col-span-1 sm:col-span-2 content-end">
                    <x-checkbox id="color-primary" wire:model.live="isSerialized" label="Serialized" primary value="primary" md />
                </div>
                <x-select
                        label="Item"
                        placeholder="Select Item"
                        :options="$items"
                        option-label="item_description"
                        option-value="id"
                       option-description="item_code" 
                       wire:model.live="addedItemId"
                    />
                  <x-currency
                    label="Cost"
                    placeholder="<Auto>"
                    wire:model="addedItemCost"
                />
                @if($isSerialized)
                    <x-input label="Serial" placeholder="Add " wire:model="addedItemSerial"/>
                    <x-input label="S.I.#/D.R.#" placeholder="Add " wire:model="addedItemSiDr"/>
                @endif
               
                <x-number min="1" max="10" step="0.1" label="Useful Life" placeholder="No. Of Years" wire:model="addedItemLifeSpan"/>
                <x-select
                        label="Condition"
                        placeholder="Select"
                        :options="['NEW', 'USED']"
                        wire:model="addedItemCondition"
                    />
                @if(!$isSerialized)
                    <x-number min="1" step="1" label="Quantity" placeholder="0" wire:model="qty"/>
                @endif
            </div>
        
            <x-slot name="footer" class="flex justify-between gap-x-4">
               
        
                <div class="flex gap-x-4">
                    <x-button flat label="Close" x-on:click="close" />
                    <x-primary-button  wire:click="addItem" >
                         <span wire:loading.remove wire:target="addItem">Add</span>
                        <span wire:loading wire:target="addItem"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i> Adding</span>
                    </x-primary-button>
                </div>
            </x-slot>
        </x-modal-card>

      <x-notifications />

</div>
