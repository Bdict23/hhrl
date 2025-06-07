
   <div>
    {{-- upper dashboard --}}
    <div class="row">
        {{-- left dashboard --}}
        <div class="col-md-6">
            <div class="container my-2">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                            <h6 class="col-md-6">Acquired Services</h6>
                       <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-xs">Title</th>
                                    <th class="text-xs">Qty</th>
                                    <th class="text-xs">Rate</th>
                                    <th class="text-xs">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($selectedEvent->eventServices ?? [] as $services)
                                    <tr>
                                        <td>{{ $services->service->service_name }}</td>
                                        <td>{{ $services->qty ? $services->qty : '-' }}</td>
                                        <td>{{ $services->price->amount }}</td>
                                        <td>{{ $services->price->amount * ($services->qty ? $services->qty : 1) }}</td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No services found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                             <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end">Total</td>
                                    <td>
                                        {{ isset($selectedEvent) && $selectedEvent->eventServices
                                            ? $selectedEvent->eventServices->sum(function($service) {
                                                return $service->price->amount * ($service->qty ? $service->qty : 1);
                                            })
                                            : 0
                                        }}
                                    </td>
                                </tr>
                        </tfoot>
                        </table>
                       </div>
                    </div>
                </div>
                <div class="card shadow-sm border-0 mt-2">
                    <div class="card-body">
                        <div  class="card-title row">
                            <h6 class="col-md-6">Withdrawn Items</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-xs">Reference</th>
                                        <th class="text-xs">Status</th>
                                        <th class="text-xs">Department</th>
                                        <th class="text-xs">Amount</th>
                                        <th class="text-xs">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>  
                                    @forelse ($selectedEvent->withdrawals ?? [] as $withdrawal)
                                        <tr>
                                            <td>{{ $withdrawal->reference_number }}</td>
                                            <td>
                                                <span class="badge badge-pill bg-{{ $withdrawal->withdrawal_status == 'APPROVED' ? 'success' : ($withdrawal->withdrawal_status == 'PREPARING' ? 'secondary' : ($withdrawal->withdrawal_status == 'REJECTED' ? 'danger' : 'info') ) }}">
                                                    {{ ucfirst($withdrawal->withdrawal_status) }}
                                                </span>
                                            </td>
                                            <td>{{ $withdrawal->department->department_name ?? '-' }}</td>
                                            <td>{{ $withdrawal->getTotalPriceLevelAmountAttribute() }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-link" wire:click="viewWithdrawal({{ $withdrawal->id }})">View</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No withdrawn items found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                 <tfoot>
                            <tr>
                                <td colspan="4" class="text-end">Total</td>
                                <td>
                                    {{ isset($selectedEvent) && $selectedEvent->withdrawals
                                        ? $selectedEvent->withdrawals->where('withdrawal_status', 'APPROVED')->sum(function($withdrawal) {
                                            return $withdrawal->getTotalPriceLevelAmountAttribute();
                                        })
                                        : 0
                                    }}     </td>
                            </tr>
                        </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        {{-- right dashboard --}}
        <div class="col-md-6 container">
            <div class="container">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form action="" method="POST">
                            @csrf
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="cust_name" class="form-label text-sm">Customer Name</label>
                                    <input type="text" class="form-control" id="event_name" name="event_name" disabled value="{{ isset($selectedEvent->customer) ? $selectedEvent->customer->customer_fname . ' ' . $selectedEvent->customer->customer_lname : '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="event_name" class="form-label text-sm">Event Name</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="event_name" name="event_name" disabled value="{{ isset($selectedEvent) ? $selectedEvent->event_name : '' }}">
                                        <button class="input-group-text" type="button"
                                            style="background-color: rgb(190, 243, 217);" data-bs-toggle="modal" data-bs-target="#getEventModal"><strong class="text-sm">Get</strong></button>
                                    </div>  
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="event_date" class="form-label text-sm">Event Date</label>
                                    <input type="date" class="form-control" id="event_date" name="event_date" value="{{ isset($selectedEvent) ? $selectedEvent->event_date : '' }}" required>
                                </div>
                                <div class="row col-md-6">
                                    <div class="col-md-6">
                                        <label for="event_time" class="form-label text-sm">Start Time</label>
                                        <input type="time" class="form-control" id="event_time" name="event_time" value="{{ isset($selectedEvent) ? $selectedEvent->start_time : '' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="event_time" class="form-label text-sm">End Time</label>
                                        <input type="time" class="form-control" id="event_time" name="event_time" value="{{ isset($selectedEvent) ? $selectedEvent->end_time : '' }}" required>
                                    </div>
                                </div>
                            </div>
                        </form>

                         <div class="card shadow-sm border-0 mt-2">
                            <div class="card-body">
                                <div  class="card-title row">
                                    <h6 class="col-md-6">Event Requirements</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-xs">Reference</th>
                                                <th class="text-xs">Department</th>
                                                <th class="text-xs">Status</th>
                                                <th class="text-xs">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                            @forelse ($selectedEvent->equipmentRequests ?? [] as $request)
                                                <tr>
                                                    <td>{{ $request->reference_number }}</td>
                                                    <td>{{ $request->department->department_name }}</td>
                                                    <td>
                                                        <span class="badge badge-pill bg-{{ $request->status == 'PENDING' ? 'success' : ($request->status == 'PREPARING' ? 'secondary' : 'info') }}">
                                                            {{ ucfirst($request->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-link" wire:click="viewRequest({{ $request->id }})">View</button>
                                                    </td>
                                                </tr>

                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No equipment requests found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>    
            </div>
        </div>
    </div>

    {{-- lower dashboard --}}
    <div>
        <div class="container my-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Summary</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-xs">Item</th>
                                <th class="text-xs">Quantity</th>
                                <th class="text-xs">Rate</th>
                                <th class="text-xs">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Example static rows, replace with dynamic data -->
                            <tr>
                                <td>Decoration</td>
                                <td>1</td>
                                <td>1000</td>
                                <td>1000</td>
                            </tr>
                            <tr>
                                <td>Sound System</td>
                                <td>1</td>
                                <td>500</td>
                                <td>500</td>
                            </tr>
                            <!-- End static rows -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end">Total Amount</td>
                                <td>1500</td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-success" type="submit">Save Event</button>
                        <button class="btn btn-secondary ms-2" type="reset">Reset</button>
                        <button class="btn btn-danger ms-2" type="reset">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Modal -->
    <div class="modal fade" id="servicesModal" tabindex="-1" aria-labelledby="servicesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="servicesModalLabel">Select Services</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="servicesForm">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th>Rate</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Example static rows, replace with  for dynamic data -->
                                <tr>
                                    <td>Decoration</td>
                                    <td>1000</td>
                                    <td>Basic hall decoration</td>
                                    <td><button class="btn btn-sm btn-success">Add</button></td>
                                </tr>
                                <tr>
                                    <td>Sound System</td>
                                    <td>500</td>
                                    <td>Standard sound setup</td>
                                    <td><button class="btn btn-sm btn-success">Add</button></td>
                                </tr>
                                <tr>
                                    <td>Lighting</td>
                                    <td>700</td>
                                    <td>Event lighting package</td>
                                    <td><button class="btn btn-sm btn-success">Add</button></td>
                                </tr>
                                <!-- End static rows -->
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="addSelectedServices">Add Selected Services</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Menu Modal -->
    <div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuModalLabel">Select Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="menuForm">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Menu Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Example static rows, replace with  for dynamic data -->
                                <tr>
                                    <td>Buffet Lunch</td>
                                    <td>Lunch</td>
                                    <td>1200</td>
                                    <td>Includes 3 main courses, dessert, and drinks</td>
                                     <td>
                                        <button class="btn btn-sm btn-success select-customer-btn" data-name="Jane Smith">Select</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>High Tea</td>
                                    <td>Snacks</td>
                                    <td>800</td>
                                    <td>Assorted snacks and tea/coffee</td>
                                     <td>
                                        <button class="btn btn-sm btn-success select-customer-btn" data-name="Jane Smith">Select</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Dinner Deluxe</td>
                                    <td>Dinner</td>
                                    <td>1500</td>
                                    <td>Multi-course dinner with starters and dessert</td> 
                                    <td>
                                        <button class="btn btn-sm btn-success select-customer-btn" data-name="Jane Smith">Select</button>
                                    </td>
                                </tr>
                                <!-- End static rows -->
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="addSelectedMenus">Add Selected Menus</button>
                </div>
            </div>
        </div>
    </div>
     <!-- Get Event Modal -->
    <div class="modal fade" id="getEventModal" tabindex="-1" aria-labelledby="getEventModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="getEventModalLabel">Select Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Event Date</th>
                                <th>Customer Name</th>
                                <th>Guest Count</th>
                                <th>Venue</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($events as $event)
                                <tr>
                                    <td>{{ $event->event_name }}</td>
                                    <td>{{ $event->event_date }}</td>
                                    <td>{{ $event->customer->customer_fname . ' ' . $event->customer->customer_lname }}</td>
                                    <td>{{ $event->guest_count }}</td>
                                    <td>{{ $event->venue->venue_name }}</td>
                                    <td>
                                        <button wire:click="loadEventDetails({{ $event->id }})" class="btn btn-sm btn-primary">Select</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No events found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Customer List Modal -->
    <div class="modal fade" id="customerListModal" tabindex="-1" aria-labelledby="customerListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerListModalLabel">Select Customer</h5>
                    <div class="ms-auto">
                        <input type="text" class="form-control" id="customerSearchInput" placeholder="Search customer...">
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table table-striped" id="customerListTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Example static rows, replace with dynamic data -->
                            <tr>
                                <td>John Doe</td>
                                <td>john@example.com</td>
                                <td>1234567890</td>
                                <td>
                                    <button class="btn btn-sm btn-success select-customer-btn" data-name="John Doe">Select</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Jane Smith</td>
                                <td>jane@example.com</td>
                                <td>9876543210</td>
                                <td>
                                    <button class="btn btn-sm btn-success select-customer-btn" data-name="Jane Smith">Select</button>
                                </td>
                            </tr>
                            <!-- End static rows -->
                        </tbody>
                    </table>
                    <!-- Customer List Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

