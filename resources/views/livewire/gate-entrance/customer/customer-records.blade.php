{{-- Stop trying to control. --}}
<div class="container mt-5">
    <h2 class="text-center mb-4"><b>View Booking Details</b></h2>
    <div class="table-responsive">
        <div class="mb-4 p-4 border border-gray-300 rounded">
            <h2>Customer Details</h2>
            <p><strong>Name:</strong>
                {{ $customer->customer_fname . ' ' . $customer->customer_lname }}
            </p>
            <p>

            </p>
        </div>

        <div class="mb-4 p-4 border border-gray-300 rounded">
            <h2 class="p-2">Booking Details</h2>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>

                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Booking Code</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customer->bookingrecords as $detail)
                        <tr>
                            <td>{{ $detail->created_at->format('M d,Y') }}</td>
                            <td>{{ $detail->updated_at->format('m d,Y') }}</td>
                            <td>{{ $detail->booking_number }}</td>
                            <td>{{ $detail->booking_status }}</td>
                            <td>
                                <a class="btn btn-success"
                                    href="{{ route('booking.view.page', ['booking_number' => $detail->booking_number]) }}">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>





    </div>
</div>
