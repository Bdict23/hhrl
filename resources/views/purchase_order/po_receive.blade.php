@extends('layouts.master')
@section('content')
<<<<<<< HEAD
    <style>
        .cost-cell {
            position: relative;
            padding-right: 25px;
        }

        .edit-cost-icon {
            transition: all 0.3s ease;
            opacity: 0.6;
            font-size: 0.8em;
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
        }

        .edit-cost-icon:hover {
            color: #0056b3 !important;
            opacity: 1;
        }

        .cost-display {
            display: inline-block;
            min-width: 80px;
        }

        .cost-edit-input {
            display: inline-block !important;
            width: 80px !important;
            height: 30px !important;
            padding: 5px !important;
        }
    </style>
    <div class="container mx-auto px-4 py-8">
        <!-- PO Number at the Top -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Receiving Form for PO: {{ $requestInfos->requisition_number ?? 'N/A' }}
            </h1>
        </div>
=======
    {{-- <div>
        <header>
            <h1>Receiving Form</h1>
>>>>>>> 21e0930d73201dc604e6f582bb099db141df5abf

        <form id="poReceivingForm" method="POST" action="{{ route('receiving.store') }}">
            @csrf
<<<<<<< HEAD
            <!-- Main Content -->
            <div class="flex flex-wrap -mx-4">
                <div class="w-full px-4">
                    <!-- Table -->
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200 bg-white shadow-md rounded-lg">
                            <thead class="bg-gray-50">
=======
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
>>>>>>> 21e0930d73201dc604e6f582bb099db141df5abf
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ITEM CODE</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ITEM DESCRIPTION</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">REQUEST QTY.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To Rec. QTY.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RECEIVED QTY.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody id="itemTableBody" class="divide-y divide-gray-200">
                                @forelse ($requestInfos->requisitionDetails ?? [] as $reqdetail)
                                    <tr class="even:bg-gray-50 hover:bg-gray-100" data-id="{{ $reqdetail->item->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $reqdetail->item->item_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $reqdetail->item->item_description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-gray-700">{{ $reqdetail->qty }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                            {{ $reqdetail->qty - ($cardexSum[$reqdetail->item->id]->total_received ?? 0) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" class="block w-20 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" name="received_qty[]" oninput="updateTotalPrice(this)" value="0" min="0" max="{{ $reqdetail->qty - ($cardexSum[$reqdetail->item->id]->total_received ?? 0) }}" step="1" />
                                            <input type="hidden" name="requisition_id[]" value="{{ $reqdetail->requisition_info_id }}">
                                            <input type="hidden" name="item_id[]" value="{{ $reqdetail->item->id }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap cost-cell">
                                            <span class="cost-display">₱{{ number_format($reqdetail->item->priceLevels()->latest()->where('price_type', 'cost')->first()->amount, 2) }}</span>
                                            <i class="fas fa-edit edit-cost-icon ml-2 text-gray-500 cursor-pointer"></i>
                                            <input type="hidden" name="cost[]" class="cost-input" value="{{ $reqdetail->item->priceLevels()->latest()->where('price_type', 'cost')->first()->amount }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap total-price text-gray-700">0.00</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-wrap gap-4 mb-6">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">
                            Save
                        </button>
                        <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" type="button" data-bs-toggle="modal" data-bs-target="#getPONumberModal">
                            Get PO Number
                        </button>
                        <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded" type="button" onclick="history.back()">
                            Back
                        </button>
                        <button class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded" type="button" onclick="window.location.href='#'">
                            Order Summary
                        </button>
                    </div>

                    <!-- Form Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-6 rounded-lg shadow-md">
                        <div>
                            <label for="receive_from" class="block text-sm font-medium text-gray-700">Receive From</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="receive_from" name="receive_from" value="{{ $requestInfos->supplier->supp_name ?? '' }}" readonly>
                        </div>
                        <div>
                            <label for="po_number" class="block text-sm font-medium text-gray-700">PO Number</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="po_number" name="po_number" value="{{ $requestInfos->requisition_number ?? '' }}" readonly>
                            <input type="hidden" name="po_id" value="{{ $requestInfos->id ?? '' }}">
                        </div>
                        <div>
                            <label for="delivered_by" class="block text-sm font-medium text-gray-700">Delivered By</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="delivered_by" name="delivered_by" required>
                        </div>
                        <div>
                            <label for="packing_list_date" class="block text-sm font-medium text-gray-700">Packing List Print Date</label>
                            <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="packing_list_date" name="packing_list_date" required>
                        </div>
                        <div>
                            <label for="way_bill_no" class="block text-sm font-medium text-gray-700">Way Bill No.</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" name="way_bill_no">
                        </div>
                        <div>
                            <label for="delivery_no" class="block text-sm font-medium text-gray-700">Delivery No.</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" name="delivery_no">
                        </div>
                        <div>
                            <label for="invoice_no" class="block text-sm font-medium text-gray-700">Invoice No.</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" name="invoice_no">
                        </div>
                        <div>
                            <label for="receiving_packing_no" class="block text-sm font-medium text-gray-700">Receiving Packing No.</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" name="receiving_packing_no" required>
                        </div>
                        <div>
                            <label for="receiving_date" class="block text-sm font-medium text-gray-700">Receiving Date</label>
                            <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" name="receiving_date" required>
                        </div>
                        <div>
                            <label for="total_price" class="block text-sm font-medium text-gray-700">Total Price</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="total_price" name="total_price" readonly>
                        </div>
                        <div>
                            <label for="total_qty" class="block text-sm font-medium text-gray-700">Total QTY.</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="total_qty" name="total_qty" readonly>
                        </div>
                        <div class="col-span-2">
                            <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                            <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="remarks" name="remarks"></textarea>
                        </div>
                        <div>
                            <label for="checked_by" class="block text-sm font-medium text-gray-700">Checked By</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" name="checked_by">
                                @forelse ($checkers as $checker)
                                    <option value="{{ $checker->employees->id }}">{{ $checker->employees->name }} {{ $checker->employees->middle_name }} {{ $checker->employees->last_name }}</option>
                                @empty
                                    <option value="">No data</option>
                                @endforelse
                            </select>
                        </div>
                        <div>
                            <label for="allocated_by" class="block text-sm font-medium text-gray-700">To Allocate By</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" name="allocated_by">
                                @forelse ($allocators as $allocator)
                                    <option value="{{ $allocator->employees->id }}">{{ $allocator->employees->name }} {{ $allocator->employees->middle_name }} {{ $allocator->employees->last_name }}</option>
                                @empty
                                    <option value="">No data</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
<<<<<<< HEAD
=======

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


>>>>>>> 21e0930d73201dc604e6f582bb099db141df5abf
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
                    <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded" data-bs-dismiss="modal">Close</button>
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
                    <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Get PO Number Modal -->
    <div class="modal fade" id="getPONumberModal" tabindex="-1" aria-labelledby="getPONumberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="getPONumberModalLabel">Enter PO Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="poNumberInput" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter PO Number">
                </div>
                <div class="modal-footer">
                    <button type="button" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="fetchPODetails()">Get Details</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
<<<<<<< HEAD
        const defaultCost = 0.00;
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.edit-cost-icon').forEach(icon => {
                icon.addEventListener('click', function(e) {
                    Swal.fire({
                        title: 'Edit Cost',
                        text: 'Are you sure you want to edit the cost?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, edit it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const cell = e.target.closest('.cost-cell');
                            const displaySpan = cell.querySelector('.cost-display');
                            const hiddenInput = cell.querySelector('.cost-input');
                            const currentValue = parseFloat(hiddenInput.value);

                            // Create input element
                            const input = document.createElement('input');
                            input.type = 'number';
                            input.className = 'block w-20 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500';
                            input.value = currentValue;
                            input.style.display = 'inline-block';

                            // Replace display with input
                            displaySpan.parentNode.replaceChild(input, displaySpan);
                            input.focus();

                            // Handle input completion
                            input.addEventListener('blur', saveCost);
                            input.addEventListener('keypress', (e) => {
                                if (e.key === 'Enter') saveCost(e);
                            });

                            function saveCost(e) {
                                const newValue = parseFloat(input.value);
                                if (newValue !== currentValue) {
                                    hiddenInput.value = newValue;
                                    displaySpan.textContent = `₱${newValue.toFixed(2)}`;
                                    input.parentNode.replaceChild(displaySpan, input);
                                    // Update total price
                                    const row = cell.closest('tr');
                                    const qtyInput = row.querySelector('input[name="received_qty[]"]');
                                    updateTotalPrice(qtyInput);
                                } else {
                                    input.parentNode.replaceChild(displaySpan, input);
                                }
                            }
                        }
                    });
                });
            });

            // Initial totals update
            updateTotals();
        });

        function updateTotalPrice(input) {
            const row = input.closest('tr');
            const price = parseFloat(row.querySelector('.cost-input').value);
            const qty = parseInt(input.value) || 0;
            const totalPriceCell = row.querySelector('.total-price');
            totalPriceCell.textContent = (price * qty).toFixed(2);
            updateTotals();
        }
=======
        // function updateTotalPrice(input) {
        //     // Find the row of the input
        //     const row = input.closest('tr');
        //     // Correct the column index for price (5th column)
        //     const price = parseFloat(row.querySelector('td:nth-child(6)').textContent);
        //     const requestQty = parseInt(input.value);
        //     const totalPriceCell = row.querySelector('.total-price');
        //     totalPriceCell.textContent = (price * requestQty).toFixed(2);
        // }
>>>>>>> 21e0930d73201dc604e6f582bb099db141df5abf

        function updateTotals() {
            let totalPrice = 0;
            let totalQty = 0;
            document.querySelectorAll('#itemTableBody tr').forEach(row => {
                const qty = parseInt(row.querySelector('input[name="received_qty[]"]').value) || 0;
                const price = parseFloat(row.querySelector('.cost-input').value);
                totalPrice += price * qty;
                totalQty += qty;
            });
            document.getElementById('total_price').value = totalPrice.toFixed(2);
            document.getElementById('total_qty').value = totalQty;
        }

        function fetchPODetails() {
            const poNumber = document.getElementById('poNumberInput').value;
            if (poNumber) {
                window.location.href = `/get-po-details/${poNumber}`;
            }
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
    </script>
@endsection