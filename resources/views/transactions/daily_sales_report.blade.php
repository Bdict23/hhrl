@extends('layouts.master')
@section('content')

    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboards') }}
        </h2>
    </x-slot>
    <div class="container mb-3">
        <div class="row">
            <div class="col-md-6">
                <x-primary-button>Print</x-primary-button>
                <x-primary-button>Export</x-primary-button>
            </div>
            <div class="col-md-6">

            </div>
        </div>
    </div>
    <div class="card">

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <h4 class="col-md-6">Invoice List</h4>
                    <div class="col-md-6">
                        <div class="row g-2"> <!-- Add 'g-2' for spacing -->
                            <div class="col-md-4 d-flex align-items-center">
                                <label for="from_date" class="me-2">From:</label>
                                <input type="date" id="from_date" name="from_date" value="{{ date('Y-m-d') }}"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4 d-flex align-items-center">
                                <label for="to_date" class="me-2">To:</label>
                                <input type="date" id="to_date" name="to_date" value="{{ date('Y-m-d') }}"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-warning btn-sm w-9 h-8 "><svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" fill="currentColor"
                                        class="bi bi-lightning-charge-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z" />
                                    </svg></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                <table>
                    <thead>
                        <tr>
                            <th style="position: sticky; top: 0;">Date</th>
                            <th style="position: sticky; top: 0;">Invoice No</th>
                            <th style="position: sticky; top: 0;">Customer Name</th>
                            <th style="position: sticky; top: 0;">Amount</th>
                            <th style="position: sticky; top: 0;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td style="font-size: smaller">{{ $invoice->created_at->format('m/d/Y') }}</td>
                                <td style="font-size: smaller">{{ $invoice->invoice_number }}</td>
                                <td style="font-size: smaller">
                                    {{ $invoice->customer->customer_name ?? $invoice->customer_name }}</td>
                                <td style="font-size: smaller">{{ $invoice->amount }}</td>
                                <td>

                                    @php
                                        $latestSrpPrice = [];
                                        foreach ($invoice->order->order_details as $detail) {
                                            $latestSrpPrice[] =
                                                $detail->menu
                                                    ->price_levels()
                                                    ->latest()
                                                    ->where('price_type', 'SRP')
                                                    ->first()->amount ?? '0.00';
                                        }
                                    @endphp

                                    <div class="button-group">
                                        <x-primary-button
                                            onclick="selectOrder({{ json_encode($invoice) }},{{ json_encode($latestSrpPrice) }})"
                                            data-bs-target="#supplierViewModal" data-bs-toggle="modal">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                <path
                                                    d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                                <path
                                                    d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                            </svg>
                                        </x-primary-button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    @endif



    <!-- Modal view -->
    <div class="modal fade" id="supplierViewModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierModalLabel">Invoice Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form -->
                    <form>
                        @csrf
                        <div class="row mb-1">
                            <div class="col-md-12">
                                <label for="customer_name" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="customer_name">
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-6">
                                <label for="table" class="form-label">Table No.</label>
                                <input type="text" class="form-control" id="table_name">
                            </div>
                            <div class="col-md-6">
                                <label for="order_number" class="form-label">Order No</label>
                                <input type="text" class="form-control text-center" id="order_number">
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                Order Details
                            </div>
                            <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                                <table class="table table-striped table-hover me-3">
                                    <thead class="thead-dark me-3">
                                        <tr style="font-size: smaller;">
                                            <th>Menu Name</th>
                                            <th>QTY</th>
                                            <th>PRICE</th>
                                            <th>SUB TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemTableBody">

                                        {{--           POPULATE TABLE     --}}
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer text-right">
                                <h6>Total : 0.00</h6>
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                </div>
            </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function selectOrder(data, prices) {
            console.log(data);

            // Ensure order_details is defined and is an array
            if (!data.order.order_details || !Array.isArray(data.order.order_details)) {
                console.error('Invalid order details:', data.order_details);
                return;
            }
            //mag add ug value sa customer name
            document.getElementById('customer_name').value = data.customer_name || 'N/A';
            //mag add ug data sa table field
            document.getElementById('table_name').value = data.order.tables.table_name || 'N/A';
            //mag add ug data sa order number
            document.getElementById('order_number').value = data.order.order_number || 'N/A';

            // Access the table body
            const tableBody = document.getElementById('itemTableBody');
            tableBody.innerHTML = ''; // Clear existing rows

            let sumTotal = 0;

            // Populate the table with order items
            data.order.order_details.forEach(dtls => {
                const price = prices.shift();
                const subTotal = price * dtls.qty;
                sumTotal += subTotal;

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td style="font-size: smaller; ">${dtls.menu.menu_name}</td>
                    <td style="font-size: smaller; ">${dtls.qty}</td>
                    <td style="font-size: smaller; ">${price}</td>
                    <td class="total-price" style="font-size: smaller; ">${subTotal.toFixed(2)}</td>
                `;
                tableBody.appendChild(newRow);
            });

            // Update total in the modal footer
            document.querySelector('.card-footer h6').textContent = `Total : ${sumTotal.toFixed(2)}`;
        }
    </script>
@endsection
