<div>
    <input type="text" class="border border-gray-300 rounded p-2 mx-2" wire:model="search"
        placeholder="Search Customer Name">
    <button class="btn btn-primary" wire:click="searchCustomer">Search</button>


    <table class="table table-bordered table-striped table-hover w-full border border-gray-300 mt-4">
        <thead>
            <tr>
                <th>Name</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

        </thead>
        <tbody>
            @foreach ($customers as $customer)
                <tr>
                    <td class="border  border-gray-300 ">
                        {{ $customer->customer_lname . ' ' . $customer->customer_fname }}
                    </td>
                    <td class="border  border-gray-300">{{ $customer->gender }}</td>
                    <td class="border  border-gray-300">{{ $customer->birthday->age }}</td>

                    <td class="border  border-gray-300">
                        {{ $customer->bookingrecords->isNotEmpty() ? 'Active' : 'Not active' }}</td>
                    <td class="border  border-gray-300">

                        @if (optional($customer->bookingrecords->firstWhere('booking_status', 'Active'))->booking_number)
                            <a class="btn btn-success"
                                href="{{ route('booking.view.page', ['booking_number' => $customer->bookingrecords->firstWhere('booking_status', 'Active')->booking_number]) }}">View</a>
                        @else
                            <a class="btn btn-primary"
                                href="{{ route('book.service.page', ['id' => $customer->id]) }}">select</a>
                        @endif

                        <a class="btn btn-primary" href="{{ route('customers.records', ['id' => $customer->id]) }}">records</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
