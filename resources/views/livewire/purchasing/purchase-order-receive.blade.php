<div class="container-fluid" style="width: 100%; height: 100%;">
    <div class="row justify-content-center" style="display: flex;">
        <!-- Left Dashboard -->
        <div class="card col-md-7 " style="flex:1">

                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>RECEIVE PURCHASE ORDER</h5>
                        </div>
                        {{-- <div class="col-md-6 text-end">
                            <span class="btn btn-outline-info">{{ $requestInfo->requisition_status ?? 'INVALID' }}</span>
                        </div> --}}
                    </div>
                </div>
                    <div class="m-2">
                        <x-primary-button data-bs-toggle="modal" data-bs-target="#getPONumberModal">
                            Get PO
                        </x-primary-button>
                        <x-primary-button style="background-color: rgb(84, 161, 248)"> Save </x-primary-button>
                        <x-secondary-button> Summary </x-secondary-button>
                    </div>

                <div class="card-body">
                    <table class="table table-striped table-hover table-sm table-responsive">
                        <thead class="table-dark">
                            <tr style="font-size: x-small">
                                <th>Code</th>
                                <th>Name</th>
                                <th>Request</th>
                                <th>To Receive</th>
                                <th>Received</th>
                                <th>Cost</th>
                                <th>Sub-Total</th>
                            </tr>
                        </thead>
                        <tbody style="font-optical-sizing: auto">
                            @forelse (($requestInfo->requisitionDetails ?? []) as $reqdetail)
                                <tr data-id="{{ $reqdetail['requisition_number'] }}">
                                    <td>{{ $reqdetail['items']['item_code'] }}</td>
                                    <td>{{ $reqdetail['items']['item_description'] }} </td>
                                    <td> &nbsp;&nbsp;{{ $reqdetail['qty'] }}</td>
                                    <td>0{{ $reqdetail['receive_qty'] }}</td>
                                    <td>0{{ $reqdetail['receive_qty'] }}</td>
                                    <td>{{ $reqdetail['items']['costPrice']->amount ?? '0.00' }}
                                    </td>
                                    <td>{{ $reqdetail['total'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <strong style="float: right">Total QTY: {{ $requestInfo && $requestInfo->requisitionDetails ? $requestInfo->requisitionDetails->sum('qty') : 'N/A' }}</strong>
                </div>

        </div>
        <!-- Right Dashboard -->
        <div class="card col-md-5">
            <header class="card-header">
                <h1>Purchase Order Information</h1>
            </header>
            <div class="card-body">
                <form>
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
                            <label for="receive_date" class="form-label" style="width: 100; font-size: 13px">Receive Date</label>
                            <input wire:model="receive_date" type="date" class="form-control"  id="receive_date">
                        </div>
                        <div class="col-md-6">
                            <label for="packiing_list_date" class="form-label" style="width: 100; font-size: 13px">Packing List Print Date</label>
                            <input wire:model="paking_list_date" type="date" class="form-control" style="width: 100; font-size: 13px">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label for="waybill_no" class="form-label" style="width: 100; font-size: 13px">Waybill_no</label>
                            <input wire:model="waybill_no" type="text" class="form-control"  id="waybill_no">
                        </div>
                        <div class="col-md-4">
                            <label for="delivery_no" class="form-label" style="width: 100; font-size: 13px">Delivery No.</label>
                            <input wire:model="delivery_no" type="text" class="form-control" style="width: 100; font-size: 13px">
                        </div>
                        <div class="col-md-4">
                            <label for="invoice_no" class="form-label" style="width: 100; font-size: 13px">Invoice No.</label>
                            <input wire:model="invoice_no" type="text" class="form-control" style="width: 100; font-size: 13px">
                        </div>
                    </div>


                    <div class="col-md-12">
                        <label for="receiving_no" class="form-label" style="width: 100; font-size: 13px">Receiving No.</label>
                        <input wire:model="receiving_no" type="text" class="form-control" id="receiving_no"
                            style="width: 100; font-size: 12px">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="delivered_by" class="form-label" style="width: 100; font-size: 13px">Delivered By</label>
                            <input wire:model="delivered_by" type="delivered_by" class="form-control"style="width: 100; font-size: 13px" >
                        </div>
                        <div class="col-md-6">
                            <label for="user" class="form-label" style="width: 100; font-size: 13px">Checked and Allocated By</label>
                            <select wire:model="user" id="user"  class="form-select"
                                style="width: 100; font-size: 13px">
                                <option value="">Select</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->employees->name }} {{  $user->employees->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-md-12">
                            <label for="remarks" class="form-label" style="font-size: 13px">Remarks</label>
                            <textarea wire:model="remarks"  id="remarks" cols="30" rows="10" readonly class="form-control md-12 "
                                style="height: 80px; font-size: 12px">
                            </textarea>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-12">
                            <label for="attachment" class="form-label" style="width: 100; font-size: 13px">Attachment</label>
                            <input wire:model="attachment" type="file" class="form-control" id="attachment" style="width: 100; font-size: 13px">
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


                </form>
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
                                          <tr><td colspan="3" class="text-center">No data available</td></tr>
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
    </script>
</div>
