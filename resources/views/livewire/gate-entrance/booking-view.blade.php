{{-- Stop trying to control. --}}
<div class="container mt-5">
    <h2 class="text-center mb-4"><b>View Booking Details</b></h2>
    <div class="table-responsive">
        <div class="mb-4 p-4 border border-gray-300 rounded">
            <h2>Customer Details</h2>
            <p><strong>Name:</strong>
                {{ $customer_booking->customer->customer_fname . ' ' . $customer_booking->customer->customer_lname }}
            </p>
            <p>
                <strong>Booking No:</strong> {{ $customer_booking->booking_number }}
                <button wire:click="CheckOut" class="btn btn-warning">Check Out</button>
            </p>
        </div>

        <div class="mb-4 p-4 border border-gray-300 rounded">
            <h2 class="p-2">Booking Details</h2>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Customer Category</th>
                        <th>Male Count</th>
                        <th>Female Count</th>
                        <th>Total Count</th>
                        <th>Entrance Fee</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customer_booking->bookingDetails as $detail)
                        <tr>
                            <td>{{ $detail->customer_category }}</td>
                            <td>{{ $detail->male_count }}</td>
                            <td>{{ $detail->female_count }}</td>
                            <td>{{ $detail->total_count }}</td>
                            <td>{{ $detail->entrance_fee }}</td>
                            <td>{{ $detail->total_amount }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mb-4 p-4 border border-gray-300 rounded">
            <h2 class="p-2">Availed Services</h2>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customer_booking->bookingService as $availed)
                        <tr>
                            <td>{{ $availed->leisure->name }}</td>
                            <td>{{ $availed->amount }}</td>
                            <td>{{ $availed->quantity }}</td>
                            <td>{{ $availed->total_amount }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mb-4 p-4 border border-gray-300 rounded">
            <h2 class="p-2">Payment Details</h2>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Type</th>
                        <th>OR No</th>
                        <th>Amount Due</th>
                        <th>Amount Payed</th>
                        <th>Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customer_booking->bookingPayments as $payment)
                        <tr>
                            <td>{{ $payment->payment_type }}</td>
                            <td>{{ $payment->OR_number }}</td>
                            <td>{{ $payment->amount_due }}</td>
                            <td>{{ $payment->amount_payed }}</td>
                            <td>{{ $payment->balance }}</td>
                            <td>{{ $payment->payment_status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($customer_booking->booking_status == 'Active')

            <div class="mb-4 p-4 border border-gray-300 rounded">
                <span>
                    <h1>Add New Service</h1>
                </span>
                <span class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ServiceModal">Add Services</span>
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($availed_services as $index => $availed_service)
                            <tr>
                                <td>{{ $availed_service['name'] }}</td>
                                <td>{{ $availed_service['amount'] }}</td>
                                <td>
                                    <input wire:model.live='availed_services.{{ $index }}.quantity'
                                        size="1" value="0" min="0" type="Number"
                                        class="border border-gray-400 text-center">
                                </td>
                                <td>
                                    ₱
                                    {{ $availed_service['amount'] * $availed_service['quantity'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                            <td>
                                ₱ {{ $total_service_payment }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Payment:</strong></td>
                            <td>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">₱</span>
                                    <input type="text" wire:model.live='total_payment' class="form-control"
                                        placeholder="Amount">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Balance:</strong></td>
                            <td>
                                ₱ {{ $balance }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                @if (session()->has('message'))
                                    <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded mb-4">
                                        {{ session('message') }}
                                    </div>
                                @endif

                            </td>
                            <td class="d-flex justify-content-center align-items-center" colspan="3">
                                <button class="btn btn-success" wire:click='Submit'>Submit</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>


            </div>

            <!-- Services Modal -->
            <div class="modal fade" id="ServiceModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">List of Services</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Services Name</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    @foreach ($services as $service)
                                        <tr>
                                            <td>{{ $service->name }}</td>
                                            <td>{{ $service->amount }}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                                                    wire:click='addService({{ $service->id }})'>Add</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>

                    </div>
                </div>
            </div>
        @endif


    </div>
</div>
