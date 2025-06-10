
   <div>
    {{-- upper dashboard --}}
    <div class="row">
        {{-- left dashboard --}}
        <div class="col-md-6">
            <div class="card mb-2 border-0">
                <div class="card">
                    <div class="card p-1">
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
                                    <td colspan="3" class="text-end"><strong>Total</strong></td>
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
                <div class="card mt-2">
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
                                                <button class="btn btn-sm btn-link" wire:click="viewWithdrawal({{ $withdrawal->id }})" data-bs-toggle="modal" data-bs-target="#viewWithdrawalModal">View</button>
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
        <div class="col-md-6">
            <div>
                <div class="card ">
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
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="event_location" class="form-label text-sm">Requested Budget</label>
                                        <input type="text" class="form-control" id="event_location" name="event_location" value="{{ isset($selectedEvent) ? $selectedEvent->event_location : '' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="event_capacity" class="form-label text-sm">Approved Budget</label>
                                        <input type="number" class="form-control" id="event_capacity" name="event_capacity" value="{{ isset($selectedEvent) ? $selectedEvent->event_capacity : '' }}" required>
                                    </div>
                                </div>
                            </div>
                        </form>

                         <div class="card mt-2">
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
                                                        <button class="btn btn-sm btn-link" wire:click="viewEquipmentInfo('{{ $request->reference_number }}')" data-bs-toggle="modal" data-bs-target="#viewEquipmentInfoModal">View</button>
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
    <div class="card">
        <div class="col-md-12">
            <div class="card mt-3 border-0">
                <div class="card-body">
                    <h6>Overview</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Services</td>
                                <td>
                                    {{ isset($selectedEvent) && $selectedEvent->eventServices
                                        ? $selectedEvent->eventServices->sum(function($service) {
                                            return $service->price->amount * ($service->qty ? $service->qty : 1);
                                        })
                                        : 0
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <td>Total Withdrawals</td>
                                <td>
                                    {{ isset($selectedEvent) && $selectedEvent->withdrawals
                                        ? $selectedEvent->withdrawals->where('withdrawal_status', 'APPROVED')->sum(function($withdrawal) {
                                            return $withdrawal->getTotalPriceLevelAmountAttribute();
                                        })
                                        : 0
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Profit</strong></td>
                                <td>
                                    <strong>
                                        {{
                                            (isset($selectedEvent) && $selectedEvent->eventServices
                                                ? $selectedEvent->eventServices->sum(function($service) {
                                                    return $service->price->amount * ($service->qty ? $service->qty : 1);
                                                })
                                                : 0
                                            )
                                            -
                                            (isset($selectedEvent) && $selectedEvent->withdrawals
                                                ? $selectedEvent->withdrawals->where('withdrawal_status', 'APPROVED')->sum(function($withdrawal) {
                                                    return $withdrawal->getTotalPriceLevelAmountAttribute();
                                                })
                                                : 0
                                            )
                                        }}
                                    </strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
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

    <!-- View Withdrawn Item Modal -->
    <div class="modal fade" id="viewWithdrawalModal" tabindex="-1" aria-labelledby="viewWithdrawalModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewWithdrawalModalLabel">Withdrawn Item Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if(isset($withdrawnItems))
                        <div class="mb-3">
                            <strong>Reference Number:</strong> {{ $withdrawalInfo->reference_number ?? '-' }}
                        </div>
                        <div class="mb-3">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ ($withdrawalInfo->withdrawal_status ?? '') == 'APPROVED' ? 'success' : (($withdrawalInfo->withdrawal_status ?? '') == 'PREPARING' ? 'secondary' : (($withdrawalInfo->withdrawal_status ?? '') == 'REJECTED' ? 'danger' : 'info') ) }}">
                                {{ ucfirst($withdrawalInfo->withdrawal_status ?? '') }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>Department:</strong> {{ $withdrawalInfo->department->department_name ?? '-' }}
                        </div>
                        <div class="mb-3">
                            <strong>Items:</strong>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Qty</th>
                                        <th>Unit Price</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($withdrawnItems as $cardex)
                                        <tr>
                                            <td>{{ $cardex->item->item_description ?? '-' }}</td>
                                            <td>{{ $cardex->qty_out ?? '-' }}</td>
                                            <td>{{ $cardex->priceLevel->amount ?? '-' }}</td>
                                            <td>{{ (isset($cardex->qty_out) && isset($cardex->priceLevel->amount)) ? $cardex->qty_out * $cardex->priceLevel->amount  : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted">No withdrawal selected.</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


        {{-- view  requirements details modal--}}

    <div class="modal fade" id="viewEquipmentInfoModal" tabindex="-1" aria-labelledby="viewRequestModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewRequestModalLabel">Equipment Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
        <div class="col-md-6 mt-3 mb-3">
             <div class="card mb-3">
                 <div  class="card-header d-flex justify-content-between">         
                     <h5 class="col-md-6">Equipment Lists</h5>
                 </div>
                 <div class="card-body overflow-auto" style="max-height: 280px;">
                     <table class="table table-sm table-striped">
                         <thead>
                             <tr>
                                 <th class="text-xs">Equipment Name</th>
                                 <th class="text-xs">Category</th>
                                 <th class="text-xs">Quantity</th>
                             </tr>
                         </thead>
                         <tbody>
                            @forelse ($selectedEquipments as $index => $item)
                                <tr>
                                    <td>{{ $item->item_description }}</td>
                                    <td>{{ $item->category->category_name ?? 'N/A' }}</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" wire:model="equipmentQty.{{ $index }}.qty" min="1">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No equipment selected</td>
                                </tr>
                            @endforelse
                         </tbody>
                     </table>
                 </div>
             </div>
             <div class="card">
                <div  class="card-header d-flex justify-content-between">         
                     <h5 class="col-md-6">Handling Team</h5>
                 </div>
                <div class="card-body overflow-auto" style="max-height: 210px;">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th class="text-xs">Name</th>
                                <th class="text-xs">Lastname</th>
                                <th class="text-xs">Position</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($handlingTeam as $member)
                                <tr>
                                    <td>{{ $member['first_name'] }}</td>
                                    <td>{{ $member['last_name'] }}</td>
                                    <td>{{ $member['position'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No team members selected</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
             </div>
        </div>
        <div class="col-md-6 mt-3 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Equipment Request Form</h5>
                </div>
                <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="referenceNumber" class="form-label text-sm">Reference Number</label>
                                <input type="text" class="form-control text-center" id="referenceNumber" disabled placeholder="<AUTO>" value="{{ $requestReferenceNumber }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="requestDate" class="form-label text-sm">Document Series No.</label>
                                <input type="text" class="form-control" id="documentNumber" value="{{ $requestDocumentNumber ?? '' }}" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="department" class="form-label text-sm">Department</label>
                                <input type="text" class="form-control text-center" id="department" value="{{ $departmentName }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="myNote" class="form-label text-sm">Note</label>
                                <textarea name="" id="myNote" class="form-control" disabled>{{ $myNote }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="inchargedBy" class="form-label text-sm">Incharged By</label>
                                <input type="text" class="form-control" id="inchargedBy" placeholder="Incharged By" disabled value="{{ $inchargedBy }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="approvedBy" class="form-label text-sm">Approved By</label>
                                <input type="text" class="form-control" id="approvedBy" placeholder="Approved By" disabled value="{{ $approver }}">
                            </div>
                        </div>
                        @if ($attachments)
                        <strong for="" class="form-label">Attachments</strong>
                           <div class="list-group list-group-horizontal table-responsive-sm">
                                @foreach ($attachments as $attachment)
                                    @php
                                        $isUploadedFile = is_object($attachment) && method_exists($attachment, 'temporaryUrl');
                                    @endphp
                                    @if ($isUploadedFile)
                                        <img class="img-thumbnail" src="{{ $attachment->temporaryUrl() }}" alt="Attachment" style="width: 100px; height: 100px; margin: 5px;">
                                    @else
                                        <div>
                                            <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="text-decoration-none">
                                                <img class="img-thumbnail" src="{{ asset('storage/' . $attachment) }}" alt="Attachment" style="width: 100px; height: 100px; margin: 5px;">
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                           </div>
                        @endif
                </div>
            </div>
        </div>
    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('closeSelectEventModal', function () {
             var modal = bootstrap.Modal.getInstance(document.getElementById('getEventModal'));
                modal.hide();
            });
        });
    </script>
</div>

