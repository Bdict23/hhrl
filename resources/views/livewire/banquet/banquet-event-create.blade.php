
   <div class="row">
    <div class="col-md-6">
        <div class="container my-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                   <div  class="card-title row">
                    <h5 class="col-md-6">Event Services</h5>
                    <div class="col-md-6 d-flex justify-content-end">
                        <button class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#servicesModal" >Add Services</button>
                    </div>
                   </div>
                   <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Action</th>
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
                        <h5 class="col-md-6">Event Menus</h5>
                        <div class="col-md-6 d-flex justify-content-end">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#menuModal" >Add Menu</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Menu Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Description</th>
                                    <th>Actions</th>
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
        <h4 class="mb-4 text-center">Create Banquet Event</h4>
        <div class="container my-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="event_name" class="form-label">Event Name</label>
                                <input type="text" class="form-control" id="event_name" name="event_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="event_name" class="form-label">Customer Name</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" disabled>
                                    <button class="input-group-text" type="button"
                                        style="background-color: rgb(190, 243, 217);" data-bs-toggle="modal" data-bs-target="#customerModal">+</button>
                                </div>  
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="event_date" class="form-label">Event Date</label>
                                <input type="date" class="form-control" id="event_date" name="event_date" required>
                            </div>
                            <div class="row col-md-6">
                                <div class="col-md-6">
                                    <label for="event_time" class="form-label">Start Time</label>
                                    <input type="time" class="form-control" id="event_time" name="event_time" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="event_time" class="form-label">End Time</label>
                                    <input type="time" class="form-control" id="event_time" name="event_time" required>
                                </div>

                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="venue" class="form-label">Venue</label>
                            <select name="" id="" class="form-select" required>
                                <option value="">Select Venue</option>
                                <option value="Venue 1">Venue 1</option>
                                <option value="Venue 2">Venue 2</option>
                                <option value="Venue 3">Venue 3</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="guest_count" class="form-label">Expected Guest Count</label>
                            <input type="number" class="form-control" id="guest_count" name="guest_count" required>
                        </div>
                        <div class="mb-3">
                            <label for="event-notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="event-notes" name="event-notes" rows="3"></textarea>
                        </div>


                        <button type="submit" class="btn btn-primary">Create Event</button>
                        <a href="{{ route('banquet_events.summary') }}" class="btn btn-secondary">Back to Events</a>
                    </form>
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

