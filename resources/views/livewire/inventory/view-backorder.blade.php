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
    <div class="row justify-content-center">
        <!-- Left Dashboard -->
        <div class="card col-md-7 " style="flex:1">

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


                <div class="card-body table-responsive-sm" wire:ignore.self>
                    <table class="table table-striped table-hover table-sm table-responsive-sm">
                        <thead class="table-dark">
                            <tr style="font-size: x-small">
                                <th>Code/SKU</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Req. Qty</th>
                                <th>Received</th>
                                <th>Reg. Cost</th>
                                <th>New Cost</th>
                                <th>Sub-Total</th>
                            </tr>
                        </thead>
                        <tbody style="font-optical-sizing: auto">
                            {{-- POPULATE HERE --}}
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    @error('qtyAndPrice.*.newCost')
                        <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                    @enderror
                    @error('id')
                        <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                    @enderror
                    {{-- <strong style="float: right">Total QTY: {{ $requestInfo && $requestInfo->requisitionDetails ? $requestInfo->requisitionDetails->sum('qty') : 'N/A' }}</strong> --}}
                </div>

        </div>
        <!-- Right Dashboard -->
        <div class="card col-md-5">
            <header class="card-header">
                <h1>Purchase Order Information</h1>
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
                            <div class="col-md-12">
                                <label for="delivered_by" class="form-label" style="width: 100; font-size: 13px">Delivered By</label>
                                <input wire:model="delivered_by" type="delivered_by" class="form-control"style="width: 100; font-size: 13px" value="{{ $delivered_by ?? ''}}">
                                @error('delivered_by')
                                    <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                                @enderror
                            </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label for="waybill_no" class="form-label" style="width: 100; font-size: 13px">Waybill No</label>
                            <input wire:model="waybill_no" type="text" class="form-control"  id="waybill_no" value="{{ $waybill_no ?? ''}}"
                                style="width: 100; font-size: 13px">
                            @error('waybill_no')
                                <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="delivery_no" class="form-label" style="width: 100; font-size: 13px">Delivery No.</label>
                            <input wire:model="delivery_no" type="text" class="form-control" style="width: 100; font-size: 13px" value="{{ $delivery_no ?? 'N/A' }}">
                            @error('delivery_no')
                                <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="invoice_no" class="form-label" style="width: 100; font-size: 13px">Invoice No.</label>
                            <input wire:model="invoice_no" type="text" class="form-control" style="width: 100; font-size: 13px" value="'{{ $invoice_no ?? ''}}'">

                            @error('invoice_no')
                                <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                    <div class="col-md-12">
                        <label for="receiving_no" class="form-label" style="width: 100; font-size: 13px">Receiving No.</label>
                        <input wire:model="receiving_no" type="text" class="form-control" id="receiving_no"
                            style="width: 100; font-size: 12px">
                        @error('receiving_no')
                            <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row mb-1">
                        <div class="col-md-12">
                            <label for="remarks" class="form-label" style="font-size: 13px">Remarks</label>
                            <textarea wire:model="remarks"  id="remarks" cols="30" rows="10" class="form-control md-12 "
                                style="height: 80px; font-size: 12px">
                            </textarea>
                            @error('remarks')
                                <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12">
                            <label for="attachment" class="form-label" style="width: 100; font-size: 13px">Attachment</label>
                            <input wire:model="attachments" type="file" class="form-control" id="attachments" style="width: 100; font-size: 13px" multiple>
                            @error('attachments.*')
                                <span class="text-danger" style="font-size: 12px">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <label for="" class="form-label" style="width: 100; font-size: 13px">Total Request Amount</label>
                            <input type="text" class="form-control fw-bold" style="width: 100; font-size: 13px"
                                {{-- value="₱{{ number_format($requestInfo->requisitionDetails->sum(function($detail) { return $detail->qty * ($detail->items->costPrice->amount ?? 0); }), 2) }}" --}}
                                readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="" class="form-label" style="width: 100; font-size: 13px">Total Received Amount</label>
                            <input type="text" class="form-control fw-bold" style="width: 100; font-size: 13px"
                                {{-- value="₱{{ number_format($requestInfo->requisitionDetails->sum(function($detail) use ($totalReceived) { return ($totalReceived[$detail->item_id] ?? 0) * ($detail->items->costPrice->amount ?? 0); }), 2) }}" --}}
                                readonly>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="getPONumberModal" tabindex="-1" aria-labelledby="getPONumberModalLabel"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="getPONumberModalLabel">To RECEIVE PURCHASE ORDER</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="poNumberInput" class="form-control" placeholder="Enter PO Number">

                            <table class=" table-striped table-hover table-responsive-lg">
                                <thead class="table-dark table-sm">
                                    <tr style="font-size: x-small">
                                        <th>PO Number</th>
                                        <th>Supplier</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                        <TH>Action</TH>
                                    </tr>
                                </thead>
                                <tbody id="poNumberTableBody">
                                      @if(isset($toReceiveRequests) && $toReceiveRequests->isNotEmpty())
                                          @foreach ($toReceiveRequests as $request)
                                              <tr>
                                                  <td style="font-size: x-small">{{ $request->requisition_number }}</td>
                                                  <td style="font-size: x-small">{{ $request->supplier->supp_name }}</td>
                                                  <td style="font-size: x-small">{{ $request->trans_date }}</td>
                                                  <td style="font-size: x-small">{{ $request->requisition_status }}</td>
                                                    <td>
                                                        <button wire:click="selectPO({{ $request->id }})" type="button" class="btn btn-primary btn-sm" onclick="closeModal()">
                                                            Select
                                                        </button>
                                                    </td>
                                              </tr>
                                          @endforeach
                                      @else
                                          <tr><td colspan="5" class="text-center">No data available</td></tr>
                                      @endif
                                    </tr>
                                </tbody>
                            </table>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function closeModal() {
            var modal = document.getElementById('getPONumberModal');
            var modalInstance = bootstrap.Modal.getInstance(modal);
            modalInstance.hide();
        }

        function updateTotalPrice(input) {
            // Find the row of the input
            const row = input.closest('tr');
            // Retrieve the price from the input field in the 7th column
            const priceInput = row.querySelector('td:nth-child(8) input');
            const price = parseFloat(priceInput.value || 0);
            // Retrieve the quantity from the input field in the 5th column
            const qtyInput = row.querySelector('td:nth-child(6) input');
            const requestQty = parseInt(qtyInput.value || 0); // Default to 0 if emptyy
            // Calculate the total price
            const totalPriceCell = row.querySelector('.total-price');
            totalPriceCell.textContent = (price * requestQty).toFixed(2);
        }
    </script>
</div>
    </script>
</div>
