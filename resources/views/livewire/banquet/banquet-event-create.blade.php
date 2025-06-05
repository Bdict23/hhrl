
   <div class="row">
    <div class="col-md-6">
        <div class="container my-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                   <div  class="card-title row">
                    <h6 class="col-md-6">Event Services</h6>
                    <div class="col-md-6 d-flex justify-content-end">
                        <button class="btn btn-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#servicesModal" >Add Services</button>
                    </div>
                   </div>
                   <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-xs">Title</th>
                                <th class="text-xs">Qty</th>
                                <th class="text-xs">Rate</th>
                                <th class="text-xs">Amount</th>
                                <th class="text-xs">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                   </div>
                </div>
            </div>
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body">
                    <div  class="card-title row">
                        <h6 class="col-md-6">Event Menus</h6>
                        <div class="col-md-6 d-flex justify-content-end">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuModal">Add Menu</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-xs">Menu Name</th>
                                    <th class="text-xs">Category</th>
                                    <th class="text-xs">Price</th>
                                    <th class="text-xs">Qty</th>
                                    <th class="text-xs">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 container">
        <h5 class="mb-2 text-center">Create Banquet Event</h5>
        <div class="container my-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="event_name" class="form-label text-sm">Event Name</label>
                                <input type="text" class="form-control" id="event_name" name="event_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="event_name" class="form-label text-sm">Customer Name</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" disabled>
                                    <button class="input-group-text" type="button"
                                        style="background-color: rgb(190, 243, 217);" data-bs-toggle="modal" data-bs-target="#customerModal">+</button>
                                </div>  
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="event_date" class="form-label text-sm">Event Date</label>
                                <input type="date" class="form-control" id="event_date" name="event_date" required>
                            </div>
                            <div class="row col-md-6">
                                <div class="col-md-6">
                                    <label for="event_time" class="form-label text-sm">Start Time</label>
                                    <input type="time" class="form-control" id="event_time" name="event_time" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="event_time" class="form-label text-sm">End Time</label>
                                    <input type="time" class="form-control" id="event_time" name="event_time" required>
                                </div>

                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="venue" class="form-label text-sm">Venue</label>
                            <select name="" id="" class="form-select" required>
                                <option value="">Select Venue</option>
                                @forelse ($venues as $venue)
                                    <option value="{{ $venue->id }}">{{ $venue->venue_name }} &nbsp; ({{ $venue->ratePrice && $venue->ratePrice->amount ? '₱' . $venue->ratePrice->amount : 'FREE' }})</option>
                                @empty
                                    
                                @endforelse
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="guest_count" class="form-label text-sm">Expected Guest Count</label>
                            <input type="number" class="form-control" id="guest_count" name="guest_count" required>
                        </div>
                        <div class="mb-3">
                            <label for="event-notes" class="form-label text-sm">Notes</label>
                            <textarea class="form-control" id="event-notes" name="event-notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>    
            </div>
             {{-- lower dashboard --}}
            <div>
                <div class="container">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title">Summary</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-xs text-start">Particular</th>
                                        <th class="text-xs text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Example static rows, replace with dynamic data -->
                                    <tr>
                                        <td class="text-start">Venue</td>
                                        <td class="text-end">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Services</td>
                                        <td class="text-end"><i>1000</i></td>
                                    </tr>
                                    <tr >
                                        <td class="text-start">Menus</td>
                                        <td class="text-end"><i>500</i></td>
                                    </tr>
                                    <!-- End static rows -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-end">Total Amount : <strong>1500</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-success" type="submit">Save Event</button>
                                <button class="btn btn-secondary ms-2" type="reset">Reset</button>
                                </form>
                            </div>
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
                                    <th class="text-xs">Service Name</th>
                                    <th class="text-xs">Rate</th>
                                    <th class="text-xs">Description</th>
                                    <th class="text-xs">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($services as $service)
                                    <tr>
                                        <td>{{ $service->service_name }}</td>
                                        <td>{{ $service->ratePrice && $service->ratePrice->amount ? '₱' . $service->ratePrice->amount : 'FREE' }}</td>
                                        <td class="text-wrap">{{ $service->service_description }}</td>     
                                        <td>
                                            <button class="btn btn-sm btn-success select-service-btn" data-name="{{ $service->service_name }}">ADD</button>
                                        </td>
                                    </tr>
                                    
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No services available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                                    <th class="text-xs">Menu Name</th>
                                    <th class="text-xs">Category</th>
                                    <th class="text-xs">Price</th>
                                    <th class="text-xs">Description</th>
                                    <th class="text-xs">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($menus as $menu)
                                    <tr>
                                        <td>{{ $menu->menu_name }}</td>
                                        <td>{{ $menu->category ? $menu->category->name : 'N/A' }}</td>
                                        <td>{{ $menu->ratePrice && $menu->ratePrice->amount ? '₱' . $menu->ratePrice->amount : 'FREE' }}</td>
                                        <td class="text-wrap">{{ $menu->description }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-success select-menu-btn" data-name="{{ $menu->menu_name }}">ADD</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No menus available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Customer Registration Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Register New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="customerRegistrationForm">
                        <div class="mb-3 row">
                            <div class="col-md-4">
                                <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="customer_midname" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="customer_midname" name="customer_midname">
                            </div>
                            <div class="col-md-4">
                                <label for="customer_lastname" class="form-label">Last Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customer_lastname" name="customer_lastname" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6"> 
                                <label for="customer_gender" class="form-label">Gender</label>
                                <select class="form-select" id="customer_gender" name="customer_gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div >
                            <div class="col-md-6">
                                <label for="customer_dob" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="customer_dob" name="customer_dob">
                            </div>
                        </div>
                       
                        <div class="mb-3">
                            <label for="customer_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email">
                        </div>
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="customer_phone" name="customer_phone">
                        </div>
                        <div class="mb-3">
                            <label for="customer_address" class="form-label">Address</label>
                            <textarea class="form-control" id="customer_address" name="customer_address" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add</button>
                        <button type="button" class="btn btn-link ms-2" data-bs-toggle="modal" data-bs-target="#customerListModal">Already Exist?</button>
                    </form>
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
                                <th>Gender</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customers as $customer)
                               <tr>
                                    <td>{{ $customer->customer_fname . ' ' . $customer->customer_mname . ' ' . $customer->customer_lname }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->contact_no_1 }}</td>
                                    <td>{{ $customer->gender }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-success select-customer-btn" data-name="{{ $customer->customer_fname . ' ' . $customer->customer_mname . ' ' . $customer->customer_lname }}">Select</button>
                                    </td>

                               </tr>
                            @empty

                            <tr>
                                <td colspan="6" class="text-center">No Data Found</td>
                            </tr>
                                
                            @endforelse
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

