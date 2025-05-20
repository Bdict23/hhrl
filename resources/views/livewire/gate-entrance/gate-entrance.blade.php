<div>
    <div class="text-center">
        <h1 class="text-2xl font-bold">Gate Entrance</h1>
        <p class="text-gray-600">HHRL</p>
        @if (session()->has('message'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        <div class="flex flex-wrap -mx-2 p-2">

            <div class="w-1/6 px-2 p-4">
            </div>

            <div class="w-4/6 px-2 rounded border border-gray-300 p-4 text-center">
                <table class="table table-striped table-bordered text-center">
                    <thead>
                        <tr>
                            <th class="text-left">
                                Customer
                            </th>
                            <th class="text-left">Date Checked in</th>
                            <th class="text-left">Customer Code</th>
                            <th class="text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookingrecords as $bookingrecord)
                            <tr>
                                <td>{{ $bookingrecord->customer_id ==0 ? 'Walk in' :$bookingrecord->customer->customer_fname . ' ' . $bookingrecord->customer->customer_lname }}
                                </td>
                                <td>{{ $bookingrecord->created_at->format('M d,Y') }}</td>
                                <td>{{ $bookingrecord->booking_number }}</td>
                                <td>
                                    <a class="btn btn-success"
                                        href="{{ route('booking.view.page', ['booking_number' => $bookingrecord->booking_number]) }}">View</a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <div class="w-1/6 px-2 p-4">
            </div>

        </div>


    </div>




</div>
