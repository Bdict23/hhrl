<div class="container">
    <div>
        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
    <div>
        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
    <div class="row  container">
        <!-- Left Dashboard -->
        <div class="col-md-7 mt-2">
            <div class="card">

                @csrf
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>BACKORDER ITEMS</h5>
                        </div>
                    </div>
                </div>

                    <div class="m-2">
                        
                        <a href="/back-orders">  <x-secondary-button> Summary </x-secondary-button> </a>

                            <i class="float-right mr-2" wire:loading>Please wait...</i>
                            <span wire:loading class="mr-2 spinner-border text-primary float-right" role="status"></span>
                    </div>


                <div class="card-body table-responsive-sm" style="display: height: 400px; overflow-x: auto;" wire:ignore.self>
                    <table class="table table-striped table-hover table-sm table-responsive-sm">
                        <thead class="table-dark">
                            <tr style="font-size: x-small">
                                <th>Code</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Req. Qty</th>
                                <th>Received</th>
                                <th>BO Qty</th>
                                <th>Cost</th>
                                <th>Rec. Attempt</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody style="font-optical-sizing: auto">
                           @foreach ($backOrders as  $item)
                            <tr>
                                <td style="font-size: small;">{{ $item->item->item_code }}</td>
                                <td style="font-size: x-small">{{ $item->item->item_description}}</td>
                                <td style="font-size: x-small">{{$item->item->uom->unit_symbol}}</td>
                                <td style="font-size: x-small">{{$backorderItems[$item->item_id]['req_qty']}}</td>
                                <td style="font-size: x-small">{{$backorderItems[$item->item_id]['received']}}</td>
                                <td style="font-size: x-small">{{$backorderItems[$item->item_id]['lacking']}}</td>
                                <td style="font-size: x-small">{{$backorderItems[$item->item_id]['req_cost']}}</td>
                                <td style="font-size: x-small">{{$item->receiving_attempt}}</td>
                                <td style="font-size: x-small">
                                    <span class="
                                        @if($item->status == 'ACTIVE') badge bg-warning text-dark
                                        @elseif($item->status == 'FULFILLED') badge bg-success
                                        @elseif($itemitem->status == 'FOR PO') badge bg-info
                                        @elseif($item->status == 'CANCELLED') badge bg-danger 
                                        @else badge bg-secondary 
                                        @endif"> {{ $item->status }}
                                    </span>
                                </td> 

                            </tr>
                               
                           @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{-- @error('qtyAndPrice.*.newCost')
                        <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                    @enderror
                    @error('id')
                        <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                    @enderror
                    <strong style="float: right">Total QTY: {{ $requestInfo && $requestInfo->requisitionDetails ? $requestInfo->requisitionDetails->sum('qty') : 'N/A' }}</strong> --}}
                </div>

            </div>
            <div class="card mt-2 mb-2">
                <div class="card-header">
                    <h5>RECEIVING LIST</h5>
                </div>
                <div class="card-body table-responsive-sm" style="display: height: 300px; overflow-x: auto;" wire:ignore.self>
                    <table class="table table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Receiving Number</th>
                                        <th>Transaction Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($receivingList as $receiving)
                                        <tr>
                                            <td>  
                                                <button 
                                                    wire:click="openReceivingNumber('{{ $receiving->RECEIVING_NUMBER }}',{{ $receiving->requisition->id }})"
                                                    class="btn btn-link btn-sm">
                                                    <strong>{{ $receiving->RECEIVING_NUMBER }}</strong>
                                                </button>
                                            </td>
                                            <td>{{ $receiving->created_at->format('d-M-Y') }}</td>
                                            <td>{{ $receiving->RECEIVING_STATUS}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                </div>
            </div>
            {{-- @if($showModal)
                <div class="fixed flex inset-0 items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white p-6 rounded shadow-lg w-1/2xl">
                        <h2 class="text-lg font-bold mb-4">This is a Livewire Modal</h2>
                        <p class="mb-4">Your modal content goes here.</p>
                        <button wire:click="closeModal" class="px-4 py-2 bg-red-500 text-white rounded">Close</button>
                    </div>
                </div>
            @endif --}}
        </div>

        
      
        <!-- Right Dashboard -->
        <div class="col-md-5 mt-2">
            <div class="card">
                <header class="card-header">
                    <h4>Purchase Order Information</h4>
                </header>
                <div class="card-body">
    
                        <div class="row">
                            <div class="col-md-6">
                                <label for="supp_name" class="form-label" style="width: 100; font-size: 13px">Supplier Name</label>
                                <input type="text" class="form-control" id="name" name="company_name"
                                    value="{{ $requestInfo->supplier->supp_name ?? '' }}" readonly
                                    style="width: 100; font-size: 13px" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="po_number" class="form-label" style="width: 100; font-size: 13px">PO Number</label>
                                <input id="po_number" type="text" class="form-control"
                                    value="{{ $requestInfo->requisition_number ?? '' }}"
                                    style="width: 100; font-size: 13px" disabled>
                            </div>
                        </div>
                        <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="mpo-no" class="form-label" style="width: 100; font-size: 13px">Merchandise PO No.</label>
                                    <input id="mpo-no" class="form-control text-center"style="width: 100; font-size: 13px" value="{{ $requestInfo->merchandise_po_number ?? 'N/A'}}" disabled>
                                </div>
                                <div class="col-md-6 display-end">
                                    <label for="receiving_no" class="form-label" style="width: 100; font-size: 13px">Total Receiving</label>
                                    <div class="input-group">
                                        <input  type="text" class="form-control text-center" id="receiving_no"
                                        style="width: 100; font-size: 12px" value={{ $receivingCount }} disabled>
                                    </div>       
                                </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label for="po-created" class="form-label" style="width: 100; font-size: 13px">P.O Created</label>
                                <input  type="text" class="form-control"  id="po-created" value="{{ $requestInfo->created_at->format('d-M-Y') ?? 'N/A'}}"
                                    style="width: 100; font-size: 13px" disabled>
                               
                            </div>
                            <div class="col-md-4">
                                <label for="delivery_no" class="form-label" style="width: 100; font-size: 13px">BO Category</label>
                                <input type="text" class="form-control" style="width: 100; font-size: 13px" value="{{ $requestInfo->category ?? 'N/A'}}" disabled>
                              
                            </div>
                            <div class="col-md-4">
                                <label for="invoice_no" class="form-label" style="width: 100; font-size: 13px">TERMS</label>
                                <input  type="text" class="form-control" style="width: 100; font-size: 13px" value="{{ $requestInfo->term->term_name ?? 'N/A'}}" disabled>
    
                            </div>
                        </div>
                        
    
                        <div class="row mb-1">
                            <div class="col-md-12">
                                <label for="remarks" class="form-label" style="font-size: 13px">Remarks</label>
                                <textarea  id="remarks" cols="30" rows="10" class="form-control md-12 "
                                    style="height: 80px; font-size: 12px"> {{ $requestInfo->remarks ?? 'N/A'}}
                                </textarea>
                            </div>
                        </div>
                       
                        <div class="row mb-1">
                            <div class="col-md-6">
                                <label for="" class="form-label" style="width: 100; font-size: 13px">Recieved Backorder Amount</label>
                                <input type="text" class="form-control fw-bold" style="width: 100; font-size: 13px"
                                    value="₱ {{ number_format($totalRegCost, 2) }}"
                                    readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="" class="form-label" style="width: 100; font-size: 13px">To Receive Backorder Amount</label>
                                <input type="text" class="form-control fw-bold" style="width: 100; font-size: 13px"
                                    value="₱ {{ number_format($totalToReceiveCost, 2) }}"
                                    readonly>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>

    
</div>
    
