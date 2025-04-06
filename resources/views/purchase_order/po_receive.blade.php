@extends('layouts.master')
@section('content')
    {{-- <div>
        <header>
            <h1>Receiving Form</h1>

        </header>
        <form id="poReceivingForm" method="POST" action="{{ route('receiving.store') }}">
            @csrf
            <div class="row">

                <div class="col-md-8">

                    <header>
                        <div>
                            <button class="btn btn-secondary" type="submit">
                                Save
                            </button>
                            <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                data-bs-target="#getPONumberModal">
                                Get PO Number
                            </button>
                            <button onclick="history.back()" class="btn btn-primary" type="button"> Back </button>
                        </div>
                    </header>
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ITEM CODE</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>REQUEST QTY.</th>
                                <th>To Rec. QTY.</th>
                                <th>RECEIVED QTY.</th>
                                <th>COST</th>
                                <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody id="itemTableBody">

                            @forelse ($requestInfos->requisitionDetails ?? [] as $reqdetail)
                                <tr data-id="{{ $reqdetail->items->id }}">
                                    <td>{{ $reqdetail->items->item_code }}</td>
                                    <td>{{ $reqdetail->items->item_description }} </td>
                                    <td class="text-center"> {{ $reqdetail->qty }}</td>
                                    <td>
                                        {{ $reqdetail->qty - ($cardexSum[$reqdetail->items->id]->total_received ?? 0) }}
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="received_qty[]"
                                            oninput="updateTotalPrice(this)" value="0" min="0"
                                            max="{{ $reqdetail->qty - ($cardexSum[$reqdetail->items->id]->total_received ?? 0) }}"
                                            step="1" />
                                        <input type="hidden" name="requisition_id[]"
                                            value="{{ $reqdetail->requisition_info_id }}">
                                        <input type="hidden" name="item_id[]" value="{{ $reqdetail->items->id }}">
                                    </td>
                                    <td>{{ $reqdetail->items->priceLevel()->latest()->where('price_type', 'cost')->first()->amount }}
                                    </td>
                                    <td class="total-price">0.00</td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No data available</td>

                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-6">

                            <label for="options" class="form-label">Receive From</label>
                            <input type="text" class="form-control" id="receive_from" name="receive_from"
                                value = "{{ $requestInfos->supplier->supp_name ?? '' }}" readonly>

                        </div>
                        <div class="col-md-6">
                            <label for="po_number" class="form-label">PO Number</label>
                            <input type="text" class="form-control" id="po_number" name="po_number"
                                value = "{{ $requestInfos->requisition_number ?? '' }}" readonly>
                            <input type="hidden" name="po_id" value="{{ $requestInfos->id ?? '' }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="contact_no_1" class="form-label">Delivered By</label>
                            <input type="text" class="form-control" id="merchandise_po_number" name="receive_from"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="options" class="form-label">Packing List Print Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="">Way Bill No.</label>
                            <input type="text" class="form-control" name="way_bill_no">
                        </div>
                        <div class="col-md-6">
                            <label for="">Delivery No.</label>
                            <input type="text" class="form-control" name="delivery_no">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="">Invoice No.</label>
                            <input type="text" class="form-control" name="invoice_no">
                        </div>
                        <div class="col-md-6">
                            <label for="">Receiving Packing No.</label>
                            <input type="text" class="form-control" name="receiving_packing_no" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label for="">Receiving Date</label>
                            <input type="date" class="form-control" name="receiving_date" required>
                        </div>
                        <div class="col-md-5">
                            <label for="">Total Price</label>
                            <input type="text" class="form-control" name="receiving_time" readonly>
                        </div>

                        <div class="col-md-3">
                            <label for="">Total QTY.</label>
                            <input type="text" class="form-control" name="receiving_by" disabled>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <label for="contact_no_2" class="form-label">Remarks</label>

                            <textarea class="form-control" id="contact2" name="remarks"></textarea>
                        </div>
                        <div class="col-md-12">
                            <div class="row mv-20">


                                <div class="col-md-6 mt-3">
                                    <label for="options" class="form-label" style="font-size: 13px"
                                        style="width: 100; font-size: 13px">Checked
                                        By</label>
                                    <select name="checked_by">
                                        @forelse ($checkers as $checker)
                                            <option value="{{ $checker->employees->id }} ">
                                                {{ $checker->employees->name }}
                                                {{ $checker->employees->middle_name }}
                                                {{ $checker->employees->last_name }}
                                            </option>
                                        @empty
                                            <option value=""> No data</option>
                                        @endforelse

                                    </select>
                                </div>


                                <div class="col-md-6 mt-3">
                                    <label for="options" class="form-label" style="font-size: 13px"
                                        style="width: 100; font-size: 13px">To Allocate
                                        By</label>

                                    <select name="allocated_by">
                                        @forelse ($allocators as $allocator)
                                            <option value="{{ $allocator->employees->id }} ">
                                                {{ $allocator->employees->name }}
                                                {{ $allocator->employees->middle_name }}
                                                {{ $allocator->employees->last_name }}
                                            </option>
                                        @empty
                                            <option value=""> No data</option>
                                        @endforelse

                                    </select>
                                </div>


                            </div>
                        </div>

                    </div>


                </div>
        </form>
    </div> --}}

    <div>
        @livewire('purchasing.purchase-order-receive')
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Purchase order saved successfully!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    An error occurred while saving the purchase order. Please try again.
                    <div id="errorMessage"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Get PO Number Modal -->
    <div class="modal fade" id="getPONumberModal" tabindex="-1" aria-labelledby="getPONumberModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="getPONumberModalLabel">Enter PO NumberS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="poNumberInput" class="form-control" placeholder="Enter PO Number">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="fetchPODetails()">Get Details</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        // function updateTotalPrice(input) {
        //     // Find the row of the input
        //     const row = input.closest('tr');
        //     // Correct the column index for price (5th column)
        //     const price = parseFloat(row.querySelector('td:nth-child(6)').textContent);
        //     const requestQty = parseInt(input.value);
        //     const totalPriceCell = row.querySelector('.total-price');
        //     totalPriceCell.textContent = (price * requestQty).toFixed(2);
        // }

        function removeRow(button) {
            // Find the row to remove
            const row = button.closest('tr');
            // Remove the row from the table
            row.remove();
        }

        // Show success or error modal based on the session status
        @if (session('status') === 'success')
            console.log(session('status'));
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        @elseif (session('status') === 'error')
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            document.getElementById('errorMessage').textContent = "{{ session('message') }}";
            errorModal.show();
        @endif

        function fetchPODetails() {
            const poNumber = document.getElementById('poNumberInput').value;
            if (poNumber) {
                window.location.href = `/get-po-details/${poNumber}`;
            }
        }
    </script>
@endsection
