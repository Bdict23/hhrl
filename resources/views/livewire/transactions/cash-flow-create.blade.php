<div>
    <div class="d-flex justify-content-between mb-2">
        <div class="container">
            @if(auth()->user()->employee->getModulePermission('Cash Flow') == 1 && $status == 'NEW')
               <x-primary-button wire:click="saveCashflow" wire:loading.attr="disabled">
                <span wire:loading wire:target="saveCashflow">Saving...</span>
                <span wire:loading.remove wire:target="saveCashflow">Save &nbsp; <i class="bi bi-save"></i></span>
            </x-primary-button>
               @if($status != 'NEW') <x-secondary-button>Print &nbsp;<i class="bi bi-printer"></i></x-secondary-button> @endif
            @endif
            <a href="{{ route('cash_flow.summary') }}" style="text-decoration: none; ">
                <x-secondary-button>Summary &nbsp;<i class="bi bi-list-columns"></i></x-secondary-button>
            </a>
        </div>
       <span style="white-space: nowrap"> Cashflow Date : <strong> {{ $cashflowDate }}</strong> 
                @if($status == 'NEW')<x-badge outline warning :label="$status" />
                @elseif($status == 'OPEN')<x-badge rounded="xl" warning :label="$status" />
                @elseif( $status == 'CLOSED' )<x-badge rounded="xl" positive :label="$status" />
                @elseif( $status == 'CANCELLED' )<x-badge rounded="xl" negative :label="$status" />
                @endif
            </span>
        <div class="container">
            <h4 class="text-end">Cash Flow - Build <i class="bi bi-file-text"></i></h4>
        </div>
    </div>
     @if ($hasOpenShift)
       
        <x-alert title="Open Shift Detected!" negative padding="small">
            <x-slot name="slot">
               Active shifts exist in the system. Please ensure all shifts are closed prior to recording cashflow.
            </x-slot>
        </x-alert>
    @endif
    <x-card squared class="mb-3">
       
        <x-slot name="slot">
            <div class="d-flex gap-2 justify-content-center px-2">
                <div class="col-md-6 list-group">
                    <div class="list-group-item list-group-item-action bg-success text-white">
                        <strong>COLLECTION</strong>
                    </div>
                    <div class="container list-group-item list-group-item-action">
                        <div class="input-group mb-3">
                            <label for="" class="input-group-text">Restaurant Revenue</label>
                            <input type="text" class="form-control form-control-sm text-end" placeholder="<AUTO>">
                        </div>
                    
                        <div class="input-group mb-3">
                            <label for="" class="input-group-text">Baquet Event Revenue</label>
                            <input type="text" class="form-control form-control-sm text-end" placeholder="<AUTO>">
                        </div>
                        <div class="input-group mb-3 ">
                            <label for="" class="input-group-text">Sales Order Revenue</label>
                            <input type="text" class="form-control form-control-sm text-end" placeholder="<AUTO>">
                        </div>
                        <div class="input-group mb-3">
                            <label for="" class="input-group-text">Gate Entrance Revenue</label>
                            <input type="text" class="form-control form-control-sm text-end" placeholder="<AUTO>">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 list-group">
                    <div class="list-group-item list-group-item-action bg-danger text-white">
                        <strong>LESS</strong>
                    </div>
                    <div class="container list-group-item list-group-item-action">
                        <div class="input-group mb-3 ">
                            <label for="" class="input-group-text">AFL - Disburser</label>
                            <input type="text" class="form-control form-control-sm text-end" placeholder="<AUTO>" value="{{ $afl > 0 ? '₱ ' . number_format($afl, 2) : null }}" readonly>
                        </div>
                    
                        <div class="input-group mb-3">
                            <label for="" class="input-group-text">Online Payments</label>
                            <input type="text" class="form-control form-control-sm text-end" placeholder="<AUTO>">
                        </div>
                   
                        <div class="input-group mb-3">
                            <label for="" class="input-group-text">Discounts</label>
                            <input type="text" class="form-control form-control-sm text-end" placeholder="<AUTO>">
                        </div>
                    
                        <div class="input-group mb-3">
                            <label for="" class="input-group-text">Refund</label>
                            <input type="text" class="form-control form-control-sm text-end" placeholder="<AUTO>">
                        </div>
                   
                        <div class="input-group mb-3">
                            <label for="" class="input-group-text">Cash Return - BEO</label>
                            <input type="text" class="form-control form-control-sm text-end" placeholder="<AUTO>">
                        </div>
                    </div>
                </div>
            </div>
        </x-slot>

       
        <x-slot name="footer" class="d-flex gap-2 justify-content-center">
            
                <div class="alert alert-warning col-md-3 " role="alert">
                 {{$status == 'NEW' ? 'LESS (PARTIAL)' : 'LESS'}}<h3><strong> ₱ {{ $grandTotalLess > 0 ? number_format($grandTotalLess, 2) : '--.--' }}</strong></h3>
                </div>
                <div class="alert alert-success col-md-3" role="alert">
                 {{$status == 'NEW' ? 'COLLECTION (PARTIAL)' : 'COLLECTION'}}<h3><strong> ₱ {{ $grandTotalCollection > 0 ? number_format($grandTotalCollection, 2) : '--.--' }}</strong></h3>
                </div>
            
                <div class="alert alert-info col-md-3" role="alert">
                 {{$status == 'NEW' ? 'NET COLLECTION (PARTIAL)' : 'NET COLLECTION'}} <h3><strong> ₱ {{ $netCollection > 0 ? number_format($netCollection, 2) : '--.--' }}</strong></h3>
                </div>
                <div class="alert alert-primary col-md-3" role="alert">
                   {{$status == 'NEW' ? 'CASH ON HAND (PARTIAL)' : 'CASH ON HAND'}}: <h3><strong> ₱ {{ $cashOnHand > 0 ? number_format($cashOnHand, 2) : '--.--' }}</strong></h3>
                </div>
        </x-slot>
    </x-card>
    <div class="dashboard mb-3">
        <div class="card-body">
            <h4 class="text-center">CHECKS</h4>
            <div style="height: 200px; overflow-x: auto; display: block;">
                <table class="table table-hover table-sm" >
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Bank</th>
                            <th>Check No.</th>
                            <th>Account Name</th>
                            <th>Check Status</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($status == 'NEW')
                            <tr>
                                <td colspan="5" class="text-center">Checks will be displayed here once the cash flow is saved.</td>
                            </tr>
                        @else
                            <td colspan="5" class="fw-bold table-secondary">POST-DATED</td>
                            @forelse ($pdcChecks ?? [] as $PDC)
                                <tr>
                                    <td>{{ $PDC->bank->bank_name }}</td>
                                    <td>{{ $PDC->check_number }}</td>
                                    <td>{{ $PDC->account_name }}</td>
                                    <td>{{ $PDC->check_status }}</td>
                                    <td class="text-end">₱ {{ number_format($PDC->check_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No records found</td>
                                </tr>
                            @endforelse
                            <td colspan="5" class="fw-bold table-secondary">CURRENT</td>
                            @forelse ($curChecks ?? [] as $cur)
                                <tr>
                                    <td>{{ $cur->bank->bank_name }}</td>
                                    <td>{{ $cur->check_number }}</td>
                                    <td>{{ $cur->account_name }}</td>
                                    <td>{{ $cur->check_status }}</td>
                                    <td class="text-end">₱ {{ number_format($cur->check_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No records found</td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="dashboard mb-3">
        
    <div class="card-body">
        {{-- OTHERS --}}
         <h4 class="text-center"><strong>OTHERS</strong></h4>
            <table class="table table-striped table-hover table-sm">
                <thead class="table-dark">
                    <tr>
                        <th class="text-start" style="width: 40%;">Denomination</th>
                        <th class="text-center" style="width: 30%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-secondary"><td colspan="3" class="fw-bold"><h3>Collection <i class="bi bi-plus-square-dotted"></i></h3></td></tr>
                    @foreach ($collectionTitles as $collectionTitle)
                        <tr>
                            <td class="text-start fw-bold">{{ $collectionTitle->title }}</td>
                            <td class="text-center">
                                <input type="number" min="0" wire:model.live="collectionAmount.{{ $collectionTitle->id }}" class="form-control text-end" placeholder="0">
                            </td>
                        </tr>
                    @endforeach
                    <tr class="table-primary text-end"><td colspan="3" class="fw-bold"> <h6>Total : ₱ {{ number_format($collectionSubAmount, 2) }}</h6></td></tr>
                    <tr class="table-secondary"><td colspan="3" class="fw-bold"><h3>Less <i class="bi bi-dash-square-dotted"></i></h3></td></tr>
                    @foreach ($lessTitles as $lessTitle)
                        <tr>
                            <td class="text-start fw-bold">{{ $lessTitle->title }}</td>
                            <td class="text-center">    
                                <input type="number" min="0" wire:model.live="lessAmount.{{ $lessTitle->id }}" class="form-control text-end" placeholder="0">
                            </td>
                        </tr>
                    @endforeach
                    <tr class="table-primary text-end"><td colspan="3" class="fw-bold"> <h6>Total : ₱ {{ number_format($lessSubAmount, 2) }}</h6></td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class=" dashboard g-4">
        <div class="card-body g-4">
            {{-- DENOMINATIONS --}}
            <div class="mb-2">
                <strong>CASH BREAKDOWN</strong>
                <div class="d-flex justify-content-between align-items-center g-2">
                    <div class="container shadow-sm ">
                        <div class="card-body g-2">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm">
                                    <thead class="table-dark">
                                        <tr>
                                            <th class="text-start" style="width: 40%;">Account</th>
                                            <th class="text-center" style="width: 30%;">Amount</th>
                                            <th class="text-end" style="width: 30%;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-secondary"><td colspan="3" class="fw-bold"><h4>Bills <i class="bi bi-cash-stack"></i></h4></td></tr>
                                            @foreach ($billDenominations as $denomination)
                                                <tr>
                                                    <td class="text-start fw-bold">₱ {{ number_format($denomination->value, 2) }}</td>
                                                    <td class="text-center">
                                                        <input type="number" min="0" wire:model.live="denominationCounts.{{ $denomination->id }}" class="form-control text-center" placeholder="0">
                                                    </td>
                                                    <td class="text-end text-primary fw-bold">₱ {{ number_format((float)($denominationCounts[$denomination->id] ?? 0) * (float)$denomination->value, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        <tr class="table-primary text-end"><td colspan="3" class="fw-bold"> <h6>Total : ₱ {{ number_format($billSubTotal, 2) }}</h6></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="container shadow-sm ">
                        <div class="card-body g-2">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm">
                                    <thead class="table-dark">
                                        <tr>
                                            <th class="text-start" style="width: 40%;">Account</th>
                                            <th class="text-center" style="width: 30%;">Amount</th>
                                            <th class="text-end" style="width: 30%;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-secondary"><td colspan="3" class="fw-bold"><h4>Coins <i class="bi bi-coin"></i></h4></td></tr>
                                        @foreach ($coinDenominations as $denomination)
                                            <tr>
                                                <td class="text-start fw-bold">₱ {{ number_format($denomination->value, 2) }}</td>
                                                <td class="text-center">    
                                                    <input type="number" min="0" wire:model.live="denominationCounts.{{ $denomination->id }}" class="form-control text-center" placeholder="0" >
                                                </td>
                                                <td class="text-end text-primary fw-bold">₱ {{ number_format((float)($denominationCounts[$denomination->id] ?? 0) * (float)$denomination->value, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-primary text-end"><td colspan="3" class="fw-bold"> <h6>Total : ₱ {{ number_format($coinSubTotal, 2) }}</h6></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-2 mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                  <div class="mb-2">
                    <label for="" class="form-label text-sm">Remarks</label>
                      <input type="text" 
                        @if($remarks == 'BALANCED' ) class="form-control alert alert-success text-center"
                        @elseif($remarks == 'EXCESS') class="form-control alert alert-warning text-center"
                        @elseif($remarks == 'SHORT') class="form-control alert alert-danger text-center"
                        @else class="form-control alert alert-info text-center"
                        @endif  placeholder="<AUTO>" readonly {{ $remarks ? 'value='.$remarks : null }}>
                  </div>
                    <x-textarea label="Notes" placeholder="write your notes" wire:model="notes"/>
                </div>
                <div class="col-md-6">
                    <x-select
                            label="Approver"
                            placeholder="Select some user"
                            wire:model="approver_id"
                            :options="$approvers"
                            option-label="full_name" 
                            option-value="id"
                            class="mb-2"
                       />
                       <div>
                            <label for="" class="form-label form-label-sm">Prepared By</label>
                            <input type="text" class="form-control form-control-sm text-muted" 
                                value="{{ auth()->user()->employee->name}} {{ auth()->user()->employee->middle_name}} {{ auth()->user()->employee->last_name}}"
                                readonly>
                       </div>
                        
                </div>
            </div>
        </div>
    </div>
      <x-notifications />
</div>
