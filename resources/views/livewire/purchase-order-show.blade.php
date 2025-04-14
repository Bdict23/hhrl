<div class="container-fluid" style="width: 100%; height: 100%;">
    <div class="row justify-content-center" style="display: flex;">
        <!-- Left Dashboard -->
        <div class="card col-md-7 " style="flex:1">

                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Purchase Order Information</h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <span  class="
                            @if($requestInfo->requisition_status == 'PREPARING') badge bg-secondary
                            @elseif($requestInfo->requisition_status == 'FOR REVIEW') badge bg-dark
                            @elseif($requestInfo->requisition_status == 'FOR APPROVAL') badge bg-dark
                            @elseif($requestInfo->requisition_status == 'TO RECIEVE') badge bg-primary 
                            @elseif($requestInfo->requisition_status == 'PARTIALLY FULFILLED') badge bg-info 
                            @elseif($requestInfo->requisition_status == 'COMPLETED') badge bg-success 
                            @elseif($requestInfo->requisition_status == 'REJECTED') badge bg-danger
                            @elseif($requestInfo->requisition_status == 'CANCELLED') badge bg-danger 
                            @else badge bg-secondary 
                            @endif">{{ $requestInfo->requisition_status }}</span>
                        </div>
                    </div>
                </div>
                    <div class="m-2">
                        {{-- <button class="btn {{ $requestInfo->requisition_status == 'TO RECEIVE' ? 'btn-success' : 'btn-secondary' }}"
                            type="button"
                            onclick="window.location.href='{{ route('po.po_receive', ['poNumber' => $requestInfo->requisition_number]) }}'"
                            {{ $requestInfo->requisition_status != 'TO RECEIVE' ? 'disabled' : '' }}>
                            Receive
                        </button> --}}

                        <button onclick="window.location.href='{{ route('po.edit', ['id' => $requestInfo->id]) }}'"
                            class="btn {{ $requestInfo->requisition_status == 'PREPARING' ? 'btn-primary' : 'btn-secondary' }}"
                            {{ $requestInfo->requisition_status != 'PREPARING' ? 'disabled' : '' }}>
                            Update PO
                        </button>

                        <a href="{{ route('po.print', ['id' => $requestInfo->id]) }}" class="btn btn-primary">
                            Print Preview
                        </a>
                        <a href="/po_create" class="btn btn-primary">
                            + New PO
                        </a>
                        <x-secondary-button onclick="history.back()"> Back </x-secondary-button>
                    </div>

                <div class="card-body">
                    <table class="table table-striped table-hover table-sm table-responsive">
                        <thead class="table-dark">
                            <tr style="font-size: x-small">
                                <th>Code</th>
                                <th>Name</th>
                                <th>Req. Qty.</th>
                                <th>Rec. Qty.</th>
                                <th>Cost</th>
                                <th>Sub-Total</th>
                                <th>Cost. Ref.</th>
                            </tr>
                        </thead>
                        <tbody style="font-optical-sizing: auto">
                            @forelse ($requestInfo->requisitionDetails as $reqdetail)
                                <tr data-id="{{ $reqdetail->requisition_number }}">
                                    <td style="font-size: small">{{ $reqdetail->items->item_code }}</td>
                                    <td style="font-size: small">{{ $reqdetail->items->item_description }} </td>
                                    <td style="font-size: small"> {{ $reqdetail->qty }}</td>
                                    <td style="font-size: small">{{ $totalReceived[$reqdetail->item_id] ?? 0 }}</td>
                                    <td style="font-size: small">{{ $reqdetail->items->costPrice->amount }}</td>
                                    <td style="font-size: small">{{ ($totalReceived[$reqdetail->item_id] ?? 0) * ($reqdetail->items->costPrice->amount ?? 0) }}</td>
                                    <td style="font-size: small">{{ $reqdetail->items->costPrice->supplier->supplier_code ?? 'N/A' }}</td>
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
                    
                    <strong style="float: right">Total QTY: {{ $requestInfo->requisitionDetails->sum('qty') }}</strong>
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
                                value="{{ $requestInfo->supplier->supp_name ?? 'N/A' }}" readonly
                                style="width: 100; font-size: 13px">
                        </div>
                        <div class="col-md-6">
                            <label for="postal_address" class="form-label" style="width: 100; font-size: 13px">PO Number</label>
                            <input type="text" class="form-control" id="postal_address" name="company_code"
                                value="{{ $requestInfo->requisition_number }}" readonly style="width: 100; font-size: 13px">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="contact_no_1" class="form-label" style="width: 100; font-size: 13px">M. PO
                                NUMBER</label>
                            <input type="text" class="form-control" id="contact1" name="company_tin"
                                value="{{ $requestInfo->merchandise_po_number }}" readonly style="width: 100; font-size: 13px">
                        </div>
                        <div class="col-md-6">
                            <label for="supp_address" class="form-label" style="width: 100; font-size: 13px">Term</label>
                            <input type="text" class="form-control" value="{{ $requestInfo->term->term_name }}"
                                readonly style="width: 100; font-size: 13px">
                        </div>
                    </div>


                    <div class="col-md-10">
                        <label for="contact_no_1" class="form-label" style="width: 100; font-size: 13px">Prepared
                            By</label>
                        <input type="text" class="form-control" id="contact1" name="company_tin"
                            value="{{ $requestInfo->preparer->name }} {{ $requestInfo->preparer->last_name }}" readonly
                            style="width: 100; font-size: 12px">
                    </div>
                    <div class="col-md-10">
                        <label for="contact_no_1" class="form-label" style="width: 100; font-size: 13px">Reviewed
                            By</label>
                        <input type="text" class="form-control" id="contact1" name="company_tin"
                            value="{{ $requestInfo->reviewer->name }} {{ $requestInfo->reviewer->last_name }}" readonly
                            style="width: 100; font-size: 12px">
                    </div>
                    <div class="col-md-10">
                        <label for="contact_no_1" class="form-label" style="width: 100; font-size: 13px">Approved
                            By</label>
                        <input type="text" class="form-control" id="contact1" name="company_tin"
                            value="{{ $requestInfo->approver->name }} {{ $requestInfo->approver->middle_name }} {{ $requestInfo->approver->last_name }}"
                            readonly style="width: 100; font-size: 12px">
                    </div>


                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label for="contact_no_2" class="form-label" style="font-size: 13px">Remarks</label>
                            <textarea name="" id="" cols="30" rows="10" readonly class="form-control md-12 "
                                style="height: 100px; font-size: 12px"> {{ $requestInfo->remarks }} </textarea>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="" class="form-label" style="width: 100; font-size: 13px">Total Request Amount</label>
                            <input type="text" class="form-control fw-bold" style="width: 100; font-size: 13px"
                                value="₱{{ number_format($requestInfo->requisitionDetails->sum(function($detail) { return $detail->qty * ($detail->items->costPrice->amount ?? 0); }), 2) }}"
                                readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="" class="form-label" style="width: 100; font-size: 13px">Total Received Amount</label>
                            <input type="text" class="form-control fw-bold" style="width: 100; font-size: 13px"
                                value="₱{{ number_format($requestInfo->requisitionDetails->sum(function($detail) use ($totalReceived) { return ($totalReceived[$detail->item_id] ?? 0) * ($detail->items->costPrice->amount ?? 0); }), 2) }}"
                                readonly>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
