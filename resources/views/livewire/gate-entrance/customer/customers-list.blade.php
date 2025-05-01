<div class="card">
    <div class="d-flex justify-content-end card-header row">
        <input type="text" id="customerSearch" class="col-md-6 col-lg-5 col-xl-4 col-xxl-3 col-sm-12 me-2 border border-gray-400 rounded p-2 mx-2" 
            style="height: 35px;" placeholder="Search Customer">
    </div>

    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-striped table-hover w-full table-sm">
            <thead class="table-dark sticky-top">
                <tr>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Age</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="customerTable">
                @foreach ($customers as $customer)
                    <tr>
                        <td >
                            {{ $customer->customer_lname . ' ' . $customer->customer_fname }}
                        </td>
                        <td>{{ $customer->gender }}</td>
                        <td>{{ $customer->birthday->age ?? 'N/A'}}</td>
                        <td>
                            {{ $customer->bookingrecords->isNotEmpty() ? 'Active' : 'Not active' }}</td>
                        <td>
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
</div>

<script>
    document.getElementById('customerSearch').addEventListener('input', function () {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#customerTable tr');

        rows.forEach(row => {
            const name = row.querySelector('td:first-child').textContent.toLowerCase();
            row.style.display = name.includes(searchValue) ? '' : 'none';
        });
    });
</script>
