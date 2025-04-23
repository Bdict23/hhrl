<div>
    {{-- The Master doesn't talk, he acts. --}}

    <div class="flex flex-wrap -mx-2 p-2">

        <div class="w-1/6 px-2 p-4">
            @if (session()->has('message'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                    {{ session('message') }}
                </div>
            @endif
            @if (session()->has('payment_message'))
                <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded mb-4">
                    {{ session('payment_message') }}
                </div>
            @endif

        </div>
        <div class="w-4/6 px-2 rounded border border-gray-300 p-4">
            <form wire:submit.prevent="submit" action="">
                @csrf
                <h1 class="text-center text-bold">Customer</h1>
                <div class="row">
                    <span class="col-2"><b>Name:
                            {{ $customer->customer_fname . ' ' . $customer->customer_lname }}</b>
                    </span>
                    <span class="col-2"><b>Gender:
                            {{ $customer->gender }}</b>
                    </span>
                    <span class="col-2"><b>Gender:
                            {{ $customer->birthday->age }}</b>
                    </span>
                </div>

                <hr>
                <table class="table-auto w-full border border-gray-300 mt-4 table-bordered text-center">
                    <thead>
                        <tr>
                            <th class="border  border-gray-300" rowspan="2">Name</th>
                            <th class="border  border-gray-300" rowspan="2">Entrance Fee</th>
                            <th class="border  border-gray-300" colspan="2">Gender</th>
                            <th class="border  border-gray-300" rowspan="2">Quantity</th>
                            <th class="border  border-gray-300" rowspan="2">Sub Total</th>

                        </tr>
                        <tr>

                            <th class="border  border-gray-300">Male</th>
                            <th class="border  border-gray-300">Female</th>


                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($booking_details as $index => $detail)
                            <tr>
                                <td class="border  border-gray-300">{{ $detail['customer_category'] }}</td>
                                <td class="border  border-gray-300">₱ {{ $detail['entrance_fee'] }}</td>
                                <td class="border  border-gray-300">
                                    <input wire:model.live='booking_details.{{ $index }}.male_count'
                                        {{-- wire:keydown='setAmount({{ $index }},`male_count`,$event.target.value )' --}} size="1" value="0" min="0"
                                        type="Number" class="border border-gray-400 text-center">
                                </td>
                                </td>
                                <td class="border  border-gray-300">
                                    <input wire:model.live='booking_details.{{ $index }}.female_count'
                                        {{-- wire:keydown='setAmount({{ $index }},`female_count`,$event.target.value )' --}} size="1" value="0" min="0"
                                        type="Number" class="border border-gray-400 text-center">
                                </td>
                                <td class="border  border-gray-300">
                                    {{ $booking_details[$index]['female_count'] + $booking_details[$index]['male_count'] }}
                                </td>
                                <td class="border  border-gray-300">
                                    ₱
                                    {{ ($booking_details[$index]['female_count'] + $booking_details[$index]['male_count']) * $detail['entrance_fee'] }}
                                </td>


                            </tr>
                        @endforeach




                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="border  border-gray-300"></td>
                            <td class="border  border-gray-300"></td>
                            <td class="border  border-gray-300"></td>

                            <td class="border  border-gray-300">Total</td>
                            <td class="border  border-gray-300">{{ $total_customers }}</td>
                            <td class="border  border-gray-300">₱ {{ $total_entrance_payment }}</td>
                        </tr>
                    </tfoot>

                </table>

                <h2 class="font-bold">Services</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Open Modal
                </button>

                <table class="table-auto w-full border border-gray-300 mt-4 table-bordered">
                    <thead>
                        <tr class="">
                            <th class="border  border-gray-300">Service</th>
                            <th class="border  border-gray-300">Amount</th>
                            <th class="border  border-gray-300">QTY</th>
                            <th class="border  border-gray-300">Sub Total</th>
                            <th class="border  border-gray-300">Action</th>

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
                            <td class="border  border-gray-300"></td>
                            <td class="border  border-gray-300"></td>
                            <td class="border  border-gray-300">Total</td>
                            <td class="border  border-gray-300">₱ {{ $total_service_payment }}</td>
                            <td class="border  border-gray-300"></td>
                        </tr>
                    </tfoot>

                </table>
                {{-- Payment Section --}}
                <table class="table table-bordered table-striped mt-4">
                    <thead>
                        <tr>
                            <th class="row">
                                <b class="col-2 justify-center">OR No:</b>
                                <span class="col-5">
                                    <input wire:model.live="or_number" type="text" size="1"
                                        class="form-control" required>
                                </span>
                            </th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr>
                            <th>Total Entrance Fee</th>
                            <th>Total Service Fee</th>
                            <th>Total Payables</th>
                        </tr>

                    </thead>
                    <tbody>
                        <tr>
                            <td><b>₱ {{ $total_entrance_payment }}</b></td>
                            <td><b>₱ {{ $total_service_payment }}</b></td>
                            <td class="text-danger"> <b>₱ {{ $total_payable }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><b>Payed Amount:</b></td>
                            <td> <input wire:model.live='total_payment' type="text" size="1"
                                    class="form-control" placeholder="₱ 00.00" required> </b>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><b>Balance:</b></td>
                            <td><b>₱ {{ $balance }} </b></td>
                        </tr>
                    </tbody>
                </table>

                <button class="btn btn-success " type="submit">Save</button>
            </form>
        </div>
        <div class="w-1/6 px-2 p-4">
        </div>

    </div>


    <!-- Services Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">List of Services</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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



</div>
<script></script>
