@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-md-8 card mb-3">
            <div class=" card-body">
                <header>
                    <h1>ORDERS</h1>
                    <div class="me-3">
                        <x-primary-button type="button"> Create Order
                        </x-primary-button>
                        <x-secondary-button onclick="history.back()" type="button"> Back
                        </x-secondary-button>
                    </div>
                </header>
                <div class="row me-3">
                    <div class="col-md-6">
                        <div class="input-group input-group-sm input-group-solid">
                            <input wire:model="search" type="text" class="form-control" id="search" name="search"
                                placeholder="Search Order Number">
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"></rect>
                                        <path
                                            d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path
                                            d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                            fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover me-3">
                    <thead class="thead-dark me-3">
                        <tr style="font-size: smaller;">
                            <th>Order No.</th>
                            <th>Status</th>
                            <th>Customer Name</th>
                            <th>Payment Status</th>
                            <th>Table</th>
                            <th>Total Orders</th>
                        </tr>
                    </thead>
                    <tbody id="itemTableBody">
                        @forelse ($orders as $order)
                            <tr onclick="selectOrder(this)" data-order-id="{{ $order->id }}"
                                data-order-number="{{ $order->order_number }}"
                                data-order-status="{{ $order->order_status }}"
                                data-customer-name="{{ $order->customer_name }}"
                                data-payment-status="{{ $order->payment_status }}"
                                data-total-order="{{ $order->total_order }}" data-total-price="{{ $order->total_price }}">
                                <td class="text-center" style="font-size: smaller;">{{ $order->order_number }}</td>
                                <td class="text-center" style="font-size: smaller;">{{ $order->order_status }}</td>
                                <td class="text-center" style="font-size: smaller;">{{ $order->customer_name ?? '' }}</td>
                                <td class="text-center" style="font-size: smaller;">{{ $order->payment_status }}</td>
                                <td class="text-center" style="font-size: smaller;">
                                    {{ $order->table->table_name ?? 'N/A' }}</td>
                                <td class="text-center" style="font-size: smaller;">{{ $order->order_details->count() }}
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No Orders Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-4">
            <form action="{{ route('allocate-order') }}" method="POST">
                @csrf
                <div class="card" wire:ignore.self>
                    <div class="card-header">
                        <h4>Allocate Order</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="customerName">Customer Name</label>
                            <input type="text" class="form-control" id="customerName2" name="customerName"
                                placeholder="(Optional)">
                        </div>
                        <div class="form-group">
                            <label for="order">Order</label>
                            <input type="text" class="form-control" id="order" name="order" readonly>
                            <input type="text" class="form-control" id="orderId" name="orderId" hidden>
                        </div>
                        <div class="form-group">
                            <label for="table">Table</label>
                            <select class="form-control" id="table" name="tableid" required>
                                <option value="">Select Table</option>
                                @foreach ($tables as $table)
                                    <option value="{{ $table->id }}">{{ $table->table_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <x-danger-button>Cancel Order</x-danger-button>
                        <x-primary-button type="submit">Allocate</x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function selectOrder(row) {
            document.getElementById('customerName2').value = row.getAttribute('data-customer-name');
            document.getElementById('order').value = row.getAttribute('data-order-number');
            document.getElementById('orderId').value = row.getAttribute('data-order-id');
            document.getElementById('customerName2').focus(); // Add this line to focus on the customer name input field
        }
    </script>
@endsection
