
   <div class="row">
        @if (session()->has('success'))
        <div class="alert alert-success" id="success-message">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if (session()->has('error'))
        <div class="alert alert-danger" id="success-message">
            {{ session('error') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    <div class="col-md-6">
        <div class="container my-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                   <div  class="card-title row">
                    <h6 class="col-md-6">Event Services</h6>
                    <div class="col-md-6 d-flex justify-content-end">
                        <button class="btn btn-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#servicesModal">Add Services</button>
                    </div>
                   </div>
                   <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-xs">Title</th>
                                <th class="text-xs">Qty</th>
                                <th class="text-xs">Rate</th>
                                <th class="text-xs">Sub Total</th>
                                <th class="text-xs">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($selectedServices as $index => $service )
                                <tr>
                                    <td>{{ $service->service_name }}</td>
                                    @if (isset($service->has_multiplier) && $service->has_multiplier == 1)
                                        <td>
                                            <input wire:model="servicesAdded.{{ $index }}.qty" type="number" class="form-control" style="width: 60px;" min="1" value="{{ $servicesAdded[$index]['qty'] ?? 1 }}" onchange="updateTotalServicePrice(this)">
                                        </td>
                                    @else
                                        <td> - </td>
                                    @endif
                                    <td>{{ $service->ratePrice->amount ?? 'FREE' }}</td>
                                    <td class="total-service-price">
                                        {{ isset($service->ratePrice) ? (floatval($servicesAdded[$index]['qty']) * floatval($service->ratePrice->amount)) : '-' }}
                                    </td>
                                    <td>
                                        <button wire:click="removeService({{ $index }})" class="btn btn-sm btn-danger">x</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No services selected</td>
                                </tr>
                            @endforelse
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
                                    <th class="text-xs">Total</th>
                                    <th class="text-xs">Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($selectedMenus as $index => $menu)
                                    <tr>
                                        <td>{{ $menu->menu_name }}</td>
                                        <td>{{ $menu->categories->category_name }}</td>
                                        <td>{{ $menu->mySRP->amount ?? 'FREE' }}</td>
                                        <td>
                                            <input wire:model="menusAdded.{{ $index }}.qty" type="number" class="form-control form-control-sm" min="1" value="1" onchange="updateTotalMenuPrice(this)">
                                        </td>
                                        <td class="total-price-menu">{{ isset($menu->mySRP) ? ($menusAdded[$index]['qty'] * $menu->mySRP->amount) : '-' }}</td>
                                        <td>
                                            <button wire:click="removeMenu({{ $index }})" class="btn btn-sm btn-danger">x</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No menus selected</td>
                                    </tr>
                                @endforelse

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
                    <form action="" method="POST" wire:submit.prevent="createEvent">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="event_name" class="form-label text-sm">Event Name <span class="text-danger">*</span></label>
                                <input wire:model="event_name" type="text" class="form-control" id="event_name" name="event_name" >
                                @error('event_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label text-sm">Customer Name</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" disabled value="{{ $selectedCustName }}">
                                    <button class="input-group-text" type="button"
                                        style="background-color: rgb(190, 243, 217);" data-bs-toggle="modal" data-bs-target="#customerModal">ADD</button>
                                </div>  
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-6">
                                <label for="event_date" class="form-label text-sm">Event Date</label>
                                <input wire:model="event_date" type="date" class="form-control" id="event_date" name="event_date">
                                @error('event_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="row col-md-6">
                                <div class="col-md-6">
                                    <label for="event_time" class="form-label text-sm">Start Time</label>
                                    <input wire:model="event_start_time" type="time" class="form-control" id="event_time" name="event_time">
                                </div>
                                <div class="col-md-6">
                                    <label for="event_time" class="form-label text-sm">End Time</label>
                                    <input wire:model="event_end_time" type="time" class="form-control" id="event_time" name="event_time">
                                </div>
                                @error('event_start_time')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @error('event_end_time')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row col-md-12 mb-1">
                             <div class="col-md-6">
                                    <div class="mb-1">
                                    <label for="venue" class="form-label text-sm">Venue</label>
                                    <select wire:model="venue_id" class="form-select">
                                        <option value="">Select Venue</option>
                                        @forelse ($venues as $venue)
                                            <option value="{{ $venue->id }}">{{ $venue->venue_name }} &nbsp; ({{ $venue->ratePrice && $venue->ratePrice->amount ? '₱' . $venue->ratePrice->amount : 'FREE' }})</option>
                                        @empty
                                            
                                        @endforelse
                                    </select>
                                @error('venue_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="guest_count" class="form-label text-sm">Expected Guest Count</label>
                                    <input wire:model="guest_count" type="number" class="form-control" id="guest_count" name="guest_count" min="0" placeholder="Enter expected guest count">
                                    @error('guest_count')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="event-notes" class="form-label text-sm">Notes</label>
                            <textarea wire:model="event_notes" class="form-control" id="event-notes" name="event-notes" rows="4"></textarea>
                            </div>
                        </div>
                        
                        <div class="mb-1">
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
                        </div>
                    </div>
                </div>    
            </div>
             {{-- lower dashboard --}}
            <div>
                <div class="container">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-end">
                                <div class="input-group">
                                    <select name="" class="form-select" id="" class="" wire:model='saveAs'>
                                        <option value="">Save As</option>
                                        <option value="DRAFT">Draft</option>
                                        <option value="FINAL">Final</option>
                                    </select>
                                    @if (!$is_editing && $saveAs != 'FINAL')
                                        <button type="submit" class="btn btn-success ">Save</button>
                                        <button wire:click="resetForm" class="btn btn-danger ms-2" type="reset">Reset</button>

                                    @elseif ($is_editing)
                                        <button type="submit" class="btn btn-success ">Update</button>
                                    @endif
                                    <a type="button" href="{{ route('banquet_events.summary') }}" class="btn btn-secondary input-group-text">Summary</a>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    
    
        <!-- Services Modal -->
    <div class="modal fade" id="servicesModal" tabindex="-1" aria-labelledby="servicesModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="servicesModalLabel">Select Services</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="overflow-auto" style="max-height: 400px;">
                        <div class="card-header">
                           <div class="input-group mb-2">
                                <span class="input-group-text">Search</span>
                                <input type="text" class="form-control" id="search-services"
                                    onkeyup="filterServices()">
                            </div>
                            <script>
                                function filterServices() {
                                    const searchInput = document.getElementById('search-services');
                                    const filter = searchInput.value.toLowerCase();
                                    const tableBody = document.getElementById('servicesTableBody');
                                    const rows = tableBody.getElementsByTagName('tr');
                        
                                    for (let i = 0; i < rows.length; i++) {
                                        const cells = rows[i].getElementsByTagName('td');
                                        let match = false;
                        
                                        for (let j = 0; j < cells.length; j++) {
                                            const cell = cells[j];
                                            if (cell) {
                                                const text = cell.textContent || cell.innerText;
                                                if (text.toLowerCase().indexOf(filter) > -1) {
                                                    match = true;
                                                    break;
                                                }
                                            }
                                        }
                        
                                        rows[i].style.display = match ? '' : 'none';
                                    }
                                }
                            </script>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-xs">Service Name</th>
                                    <th class="text-xs">Rate</th>
                                    <th class="text-xs">Description</th>
                                    <th class="text-xs">Action</th>
                                </tr>
                            </thead>
                            <tbody id="servicesTableBody">
                                @forelse ($services as $service)
                                    <tr>
                                        <td>{{ $service->service_name }}</td>
                                        <td>{{ $service->ratePrice && $service->ratePrice->amount ? '₱' . $service->ratePrice->amount : 'FREE' }}</td>
                                        <td class="text-wrap">{{ $service->service_description }}</td>     
                                        <td>
                                            <button wire:click="selectService({{ $service->id }})" class="btn btn-sm btn-success select-service-btn">ADD</button>
                                        </td>
                                    </tr>
                                    
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No services available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <span wire:loading wire:target="selectService" class="me-2 text-primary">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Adding...
                    </span>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>
   
   
    <!-- Menu Modal -->
    <div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuModalLabel">Select Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-header">
                           <div class="input-group mb-2">
                                <span class="input-group-text">Search</span>
                                <input type="text" class="form-control" id="search-menus"
                                    onkeyup="filterMenus()">
                            </div>
                            <script>
                                function filterMenus() {
                                    const searchInput = document.getElementById('search-menus');
                                    const filter = searchInput.value.toLowerCase();
                                    const tableBody = document.getElementById('menusTableBody');
                                    const rows = tableBody.getElementsByTagName('tr');
                        
                                    for (let i = 0; i < rows.length; i++) {
                                        const cells = rows[i].getElementsByTagName('td');
                                        let match = false;
                        
                                        for (let j = 0; j < cells.length; j++) {
                                            const cell = cells[j];
                                            if (cell) {
                                                const text = cell.textContent || cell.innerText;
                                                if (text.toLowerCase().indexOf(filter) > -1) {
                                                    match = true;
                                                    break;
                                                }
                                            }
                                        }
                        
                                        rows[i].style.display = match ? '' : 'none';
                                    }
                                }
                            </script>
                        </div>
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
                        <tbody id="menusTableBody">
                            @forelse ($menus ?? [] as $menu)
                                <tr>
                                    <td>{{ $menu->menu->menu_name }}</td>
                                    <td>{{ $menu->menu->categories ? $menu->menu->categories->category_name : 'N/A' }}</td>
                                    
                                    <td>{{ $menu->menu->mySRP && $menu->menu->mySRP->amount ? '₱' . $menu->menu->mySRP->amount : 'FREE' }}</td>
                                    <td class="text-wrap">{{ $menu->menu->menu_description }}</td>
                                    <td>
                                        <button wire:click="selectMenu({{ $menu->menu->id }})" class="btn btn-sm btn-success select-menu-btn">ADD</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No menus available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <span wire:loading wire:target="selectMenu" class="me-2 text-primary">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Adding...
                    </span>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Customer Registration Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Register New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="customerRegistrationForm" wire:submit.prevent="registerCustomer">
                        <div class="mb-3 row">
                            <div class="col-md-4">
                                <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                <input wire:model="customerFirstName" type="text" class="form-control" id="customer_name" name="customer_name">
                                @error('customerFirstName')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="customer_midname" class="form-label">Middle Name</label>
                                <input wire:model="customerMiddleName" type="text" class="form-control" id="customer_midname" name="customer_midname">
                                @error('customerMiddleName')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="customer_lastname" class="form-label">Last Name<span class="text-danger">*</span></label>
                                <input wire:model="customerLastName" type="text" class="form-control" id="customer_lastname" name="customer_lastname" >
                                @error('customerLastName')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label for="customer_suffix" class="form-label">Suffix</label>
                                <input wire:model="customerSuffix" type="text" class="form-control" id="customer_suffix" name="customer_suffix">
                            </div>
                            @error('customerSuffix')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6"> 
                                <label for="customer_gender" class="form-label">Gender</label>
                                <select wire:model="customerGender" class="form-select" id="customer_gender" name="customer_gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Neutral">Neutral</option>
                                </select>
                                @error('customerGender')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="customer_dob" class="form-label">Date of Birth</label>
                                <input wire:model="customerBirthdate" type="date" class="form-control" id="customer_dob" name="customer_dob">
                            </div>
                            @error('customerBirthdate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                       
                        <div class="mb-3">
                            <label for="customer_email" class="form-label">Email</label>
                            <input wire:model="customerEmail" type="email" class="form-control" id="customer_email" name="customer_email">
                        </div>
                        @error('customerEmail')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Phone</label>
                            <input wire:model="customerPhone" type="text" class="form-control" id="customer_phone" name="customer_phone">
                        </div>
                        @error('customerPhone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="mb-3">
                            <label for="customer_address" class="form-label">Address</label>
                            <textarea wire:model="customerAddress" class="form-control" id="customer_address" name="customer_address" rows="2"></textarea>
                        </div>
                        @error('customerAddress')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
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
                <div class="modal-header row">
                        <div class="col-md-6">
                            <h5 class="modal-title" id="customerListModalLabel">Select Customer</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-2">
                                <span class="input-group-text">Search</span>
                                <input type="text" class="form-control" id="search-customers"
                                    onkeyup="filterCustomers()">
                            </div>
                        </div>
                  
                    <script>
                        function filterCustomers() {
                            const searchInput = document.getElementById('search-customers');
                            const filter = searchInput.value.toLowerCase();
                            const tableBody = document.getElementById('customerListTable').getElementsByTagName('tbody')[0];
                            const rows = tableBody.getElementsByTagName('tr');

                            for (let i = 0; i < rows.length; i++) {
                                const cells = rows[i].getElementsByTagName('td');
                                let match = false;

                                for (let j = 0; j < cells.length; j++) {
                                    const cell = cells[j];
                                    if (cell) {
                                        const text = cell.textContent || cell.innerText;
                                        if (text.toLowerCase().indexOf(filter) > -1) {
                                            match = true;
                                            break;
                                        }
                                    }
                                }

                                rows[i].style.display = match ? '' : 'none';
                            }
                        }
                    </script>
                </div>
                <div class="modal-body overflow-auto" style="max-height: 400px;">
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
                                        <button wire:click="selectCustomer({{ $customer->id }}, '{{ $customer->customer_fname . ' ' . $customer->customer_mname . ' ' . $customer->customer_lname }}')" class="btn btn-sm btn-success select-customer-btn" data-name="{{ $customer->customer_fname . ' ' . $customer->customer_mname . ' ' . $customer->customer_lname }}">Select</button>
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

    {{-- service qty modal --}}
        <div class="modal fade" id="serviceQtyModal" tabindex="-1" aria-labelledby="serviceQtyModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="serviceQtyModalLabel">Service Quantity</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="serviceQtyForm">
                            <div class="mb-3">
                                <label for="service_qty" class="form-label">Quantity</label>
                                <input wire:model="service_qty" type="number" class="form-control" id="service_qty" name="service_qty" min="1">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" wire:click="updateServiceQty">Update</button>
                    </div>
                </div>
            </div>
        </div>

    {{-- menu qty modal --}}
        <div class="modal fade" id="menuQtyModal" tabindex="-1" aria-labelledby="menuQtyModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="menuQtyModalLabel">Menu Quantity</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="menuQtyForm">
                            <div class="mb-3">
                                <label for="menu_qty" class="form-label">Quantity</label>
                                <input wire:model="menu_qty" type="number" class="form-control" id="menu_qty" name="menu_qty" min="1">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" wire:click="updateMenuQty">Update</button>
                    </div>
                </div>
            </div>
        </div>

    <script>
        window.addEventListener('refresh', event => {
            // Reset all forms on the page
            document.querySelectorAll('form').forEach(form => form.reset());
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 1500);
        });
        window.addEventListener('hideCustomerRegistrationModal', event => {
            // reset form
                 document.getElementById('customerRegistrationForm').reset();
            // Hide the modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('customerModal'));
            modal.hide();

            // Hide the success message after 1 second
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 1500);
        });

        window.addEventListener('hideCustomerListModal', event => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('customerListModal'));
            if (modal) {
                modal.hide();
            }
        });

        function updateTotalMenuPrice(input) {
            const row = input.closest('tr');
            const priceCell = parseFloat(row.querySelector('td:nth-child(3)').textContent);
            const requestQty = parseInt(input.value) || 0;
            const totalPriceCell = row.querySelector('.total-price-menu');

            // Ensure price and quantity are parsed correctly
            // const price = parseFloat(priceCell.textContent) || 0;

            // Update the total price for the row
            totalPriceCell.textContent = (priceCell * requestQty).toFixed(2);
            console.log(totalPriceCell.textContent);

            // Update the total amount at the footer
            updateTotalAmount();
        }

        function updateTotalServicePrice(input) {
            const row = input.closest('tr');
            const priceCell = row.querySelector('td:nth-child(3)');
            const totalPriceCell = row.querySelector('.total-service-price');

            // Ensure price and quantity are parsed correctly
            const price = parseFloat(priceCell.textContent) || 0;
            const requestQty = parseInt(input.value) || 0;

            // Update the total price for the row
            totalPriceCell.textContent = (price * requestQty).toFixed(2);

            // Update the total amount at the footer
            updateTotalAmount();
        }

        function updateTotalAmount() {
            const tableBody = document.getElementById('itemTableBody');
            const totalAmountElement = document.getElementById('totalAmount');

            // Calculate the total amount by summing all row totals
            const totalAmount = Array.from(tableBody.querySelectorAll('.total-price'))
                .reduce((sum, cell) => sum + parseFloat(cell.textContent || 0), 0);

            // Update the total amount in the footer
            totalAmountElement.textContent = totalAmount.toFixed(2);
        }

    </script>
</div>

