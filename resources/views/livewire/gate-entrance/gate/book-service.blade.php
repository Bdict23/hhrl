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
            @if (session()->has('record_message'))
                <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded mb-4">
                    {{ session('record_message') }}
                </div>
            @endif
            @if (session()->has('customer_message'))
            <div class="alert alert-danger mt-2">
                {{ session('customer_message') }}
            </div>
        @endif

        </div>
        {{-- header --}}
        <div class="w-4/6 px-2 rounded border border-gray-300 p-4 bg-white">
            <form wire:submit.prevent="submit" action="" onsubmit="resetInput()">
                @csrf
                <p class="text-center text-bold font-200"><b>Customer</b></p>
                <a href="{{route('customers.page')}}" class="btn btn-primary float-right">Customer</a>
                <div class="row">
                    <div class="col-8">
                        @if ($type == 1)
                            <div class="row">
                                @csrf
                                <div class="row p-2">
                                    {{-- LAST NAME --}}
                                    <label class="p-2 col-2 text-center" for="">Last Name <span
                                            class="text-danger">*</span>:</label>
                                    <div class="col-4">
                                        <input type="text"class="form-control" wire:model="lname"
                                            placeholder="Last Name " required>
                                    </div>
                                    {{-- END LAST NAME --}}
                                    {{-- FIRST NAME --}}
                                    <label class="p-2 col-2 text-center" for="">First Name <span
                                            class="text-danger">*</span>:</label>
                                    <div class="col-4">
                                        <input type="text"class="form-control" wire:model="fname"
                                            placeholder="First Name " required>
                                    </div>
                                    {{-- END FIRST NAME --}}
                                </div>
                                <div class="row p-2">

                                </div>
                                <div class="row p-2">
                                    {{-- MIDDLE NAME --}}
                                    <label class="p-2 col-2 text-center" for="">Middle Name:</label>
                                    <div class="col-4">
                                        <input type="text"class="form-control" wire:model="mname"
                                            placeholder="Middle Name">
                                    </div>
                                    {{-- END MIDDLE NAME --}}
                                    {{-- SUFFIX --}}
                                    <label class="p-2 col-2 text-center" for="">Suffix:</label>
                                    <div class="col-4">
                                        <select name="suffix" id="" class="form-control">
                                            <option value="" selected >Optional</option>
                                            <option value="Sr">Sr</option>
                                            <option value="Sr">Sr</option>
                                            <option value="Jr">Jr</option>
                                            <option value="I">I</option>
                                            <option value="II">II</option>
                                            <option value="III">III</option>
                                            <option value="IV">IV</option>
                                            <option value="V">V</option>
                                            <option value="VI">VI</option>
                                            <option value="VII">VII</option>
                                            <option value="VIII">VIII</option>
                                            <option value="IX">IX</option>
                                            <option value="X">X</option>
                                            <option value="XI">XI</option>
                                            <option value="XII">XII</option>
                                            <option value="XIII">XIII</option>
                                            <option value="XIV">XIV</option>
                                            <option value="XV">XV</option>
                                        </select>
                                    </div>
                                    {{-- END SUFFIX --}}
                                </div>

                                <div class="row p-2">
                                    {{-- GENDER  --}}
                                    <label class="p-2 col-2 text-center" for="">Gender <span
                                            class="text-danger">*</span>:</label>
                                    <div class="col-4">
                                        <select class="form-control" wire:model="gender" id="" required>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    {{-- END GENDER --}}

                                    {{-- BIRTH DATE --}}
                                    <label class="p-2 col-2 text-center" for="">Birth Date <span
                                            class="text-danger">*</span>:</label>
                                    <div class="col-4">
                                        <input type="Date" class="form-control" wire:model="bday" required>
                                        @if (session()->has('date_error'))
                                            <div class="alert alert-danger mt-2">
                                                {{ session('date_error') }}
                                            </div>
                                        @endif
                                    </div>
                                    {{-- END BIRTH DATE --}}
                                </div>


                            </div>
                        @endif
                        <select class="form-control" name="" id="" value="{{ $type }}"
                            wire:model.live="type">
                            <option value="1" wire:model="customer_type">Member</option>
                            <option value="0" wire:model="customer_type">Walk-in</option>
                        </select>
                    </div>

                    <div class="col-4 row float-right">
                        <div class="row">
                            <div class="col-4"> <b>Booking Number:</b></div>
                            <div class="text-danger col-6">
                                {{ $booking_number }}
                            </div>
                        </div>
                        <div class="row">
                            <b class="col-2 justify-center">OR No:</b>
                            <span class="col-10">
                            {{ $or_number}}

                                @error('or_number')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </span>

                        </div>



                    </div>



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
                                        type="Number"
                                        value='{{ (int) ($booking_details[$index]['male_count'] ?? 0) }}'
                                        min='0' class="border border-gray-400 text-center male_count">
                                </td>
                                </td>
                                <td class="border  border-gray-300">
                                    <input wire:model.live='booking_details.{{ $index }}.female_count'
                                        {{-- wire:keydown='setAmount({{ $index }},`female_count`,$event.target.value )' --}} size="1" value="0" min="0"
                                        type="Number"
                                        value="{{ (int) ($booking_details[$index]['female_count'] ?? 0) }}"
                                        min='0' class="border border-gray-400 text-center female_count">
                                </td>
                                <td class="border  border-gray-300">
                                    {{ (int) ($booking_details[$index]['female_count'] ?? 0) + (int) ($booking_details[$index]['male_count'] ?? 0) }}
                                </td>
                                <td class="border  border-gray-300">
                                    ₱
                                    {{ ((int) ($booking_details[$index]['female_count'] ?? 0) + (int) ($booking_details[$index]['male_count'] ?? 0)) * $detail['entrance_fee'] }}
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

                {{-- <h2 class="font-bold">Services</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Add Service
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

                </table> --}}
                {{-- Payment Section --}}
                <table class="table table-bordered table-striped mt-4">
                    <thead>
                        <tr>
                            <th class="row">

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
                            <td> <input wire:model.live='total_payment' type="number" size="1"
                                    class="form-control" id="ttl_payment" min="0" placeholder="₱ 00.00"
                                    required>
                                </b>

                                @if (session()->has('error'))
                                    <div class="bg-red-100 text-red-800 px-4 rounded">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                @error('total_payment')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><b>Balance:</b></td>
                            <td><b>₱ {{ $balance }} </b></td>
                        </tr>
                    </tbody>
                </table>

                <button class="btn btn-success" type="submit">Save</button>
            </form>
        </div>
        <div class="w-1/6 px-2 p-4">
        </div>

    </div>





</div>
<script>
    function resetInput() {
        if (confirm('Are you sure you want to proceed?')) {
            document.getElementById('ttl_payment').value = 0;
            document.querySelectorAll('.female_count').forEach(el => el.value = 0);
            document.querySelectorAll('.male_count').forEach(el => el.value = 0)
        }
    }
</script>
