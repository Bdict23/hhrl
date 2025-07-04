
   <div>
      @if (session()->has('success'))
        <div class="alert alert-success" id="success-message">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    {{-- upper dashboard --}}
    <div class="row">
        {{-- left dashboard --}}
        <div class="col-md-6">
            <div class="card mb-2 border-0">
                <div class="card">
                    <div class="card p-1">
                            <h6 class="card-header">Acquired Services</h6>
                       <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-xs">Title</th>
                                    <th class="text-xs">Qty</th>
                                    <th class="text-xs">Income</th>
                                    <th class="text-xs">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($selectedEvent->eventServices ?? [] as $services)
                                    <tr>
                                        <td>{{ $services->service->service_name }}</td>
                                        <td>{{ $services->qty ? $services->qty : '-' }}</td>
                                        @if ($services->service->service_type == 'INTERNAL')
                                            <td>{{ $services->price->amount }}</td>
                                        @else
                                            <td>{{ $services->cost->amount ?? '0' }}</td>

                                        @endif
                                        @if ($services->service->service_type == 'INTERNAL')
                                            <td>
                                                {{ $services->price->amount * ($services->qty ? $services->qty : 1) }}
                                            </td>
                                        @else
                                            <td>
                                                {{ ($services->cost->amount ?? 0) * ($services->qty ? $services->qty : 1) }}
                                            </td>
                                            
                                        @endif

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No services found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                             <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total Income</strong></td>
                                    <td>
                                        @php
                                            $internalTotal = isset($selectedEvent) && $selectedEvent->eventServices
                                                ? $selectedEvent->eventServices->where('service.service_type', 'INTERNAL')->sum(function($service) {
                                                    return $service->price->amount * ($service->qty ? $service->qty : 1);
                                                })
                                                : 0;

                                            $externalTotal = isset($selectedEvent) && $selectedEvent->eventServices
                                                ? $selectedEvent->eventServices->where('service.service_type', 'EXTERNAL')->sum(function($service) {
                                                    return ($service->cost->amount ?? 0) * ($service->qty ? $service->qty : 1);
                                                })
                                                : 0;

                                            $totalIncome = $internalTotal + $externalTotal;
                                        @endphp
                                        {{ $totalIncome }}
                                        
                                    
                                    </td>
                                </tr>
                        </tfoot>
                        </table>
                       </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div  class="card-header">
                        <h6 class="col-md-6">Purchase Orders</h6>
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-xs">P.O No.</th>
                                        <th class="text-xs">Status</th>
                                        <th class="text-xs">Total Items</th>
                                        <th class="text-xs">Amount</th>
                                        <th class="text-xs">Actions</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                        @foreach ($selectedEvent->purchaseOrders ?? [] as $orders )
                                            <tr>
                                                <td>{{ $orders->requisition_number }}</td>
                                                <td>
                                                    <span class="badge badge-pill bg-{{ $orders->status == 'PARTIALLY FULFILLED' ? 'warning' : ($orders->status == 'COMPLETED' ? 'success' : ($orders->status == 'REJECTED' ? 'danger' : ($orders->status == 'PREPARING' ? 'info' : 'secondary'))) }}">
                                                        {{ ucfirst($orders->requisition_status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $orders->requisitionDetails->count() }}</td>
                                                <td>{{ $orders->requisitionDetails->sum(fn($detail) => $detail->totalAmount()) }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-link" wire:click="viewPurchaseOrder({{ $orders->id }})" data-bs-toggle="modal" data-bs-target="#viewPurchaseOrderModal">View</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if(empty($selectedEvent->purchaseOrders))
                                            <tr>
                                                <td colspan="5" class="text-center">No purchase orders found.</td>
                                            </tr>
                                        @endif
                                
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                                        <td>
                                            {{ isset($selectedEvent) && $selectedEvent->purchaseOrders
                                                ? $selectedEvent->purchaseOrders->sum(function($order) {
                                                    return $order->requisitionDetails->sum(fn($detail) => $detail->totalAmount());
                                                })
                                                : 0
                                            }}
                                        </td>
                                    </tr>
                                </tfoot>
                                    {{-- @forelse ($selectedEvent->withdrawals ?? [] as $withdrawal)
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
                        </tfoot> --}}
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        {{-- right dashboard --}}
        <div class="col-md-6 mb-3">
            <div>
                <div class="card ">
                    <div class="card-body">
                        <form id="banquetProcurementForm" action="" wire:submit.prevent='storeRequestBudget'>
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="document_number" class="form-label text-xs">Document Number</label>
                                    <input type="text" class="form-control text-xs" id="document_number" name="document_number" wire:model="documentNumber">
                                    @error('documentNumber')
                                        <div class="text-danger text-xs">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="reference_number" class="form-label text-xs">Reference Number</label>
                                    <input type="text" class="form-control text-xs" id="reference_number" name="reference_number" placeholder="<AUTO>" value="{{ $requestReferenceNumber }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="cust_name" class="form-label text-xs">Customer Name</label>
                                    <input type="text" class="form-control text-xs" id="event_name" name="event_name" disabled value="{{ isset($selectedEvent->customer) ? $selectedEvent->customer->customer_fname . ' ' . $selectedEvent->customer->customer_lname : '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="event_name" class="form-label text-xs">Event Name</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control text-xs" id="event_name" name="event_name" disabled value="{{ isset($selectedEvent) ? $selectedEvent->event_name : '' }}">
                                        <button class="input-group-text" type="button"
                                            style="background-color: rgb(190, 243, 217);" data-bs-toggle="modal" data-bs-target="#getEventModal"><strong class="text-sm">Get</strong></button>
                                    </div>
                                    @error('selectedEventId')
                                        <div class="text-danger text-xs">{{ $message }}</div>
                                    @enderror  
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                         <label for="event_date" class="form-label text-xs">Event Date</label>
                                        <input disabled type="date" class="form-control text-xs" id="event_date" name="event_date" value="{{ isset($selectedEvent) ? $selectedEvent->event_date : '' }}" required>
                                 </div>
                                <div class="row col-md-6">
                                    <div class="col-md-6">
                                        <label for="event_time" class="form-label text-xs">Start Time</label>
                                        <input disabled type="time" class="form-control" style="font-size: small" id="event_time" name="event_time" value="{{ isset($selectedEvent) ? $selectedEvent->start_time : '' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="event_time" class="form-label text-xs">End Time</label>
                                        <input disabled type="time" class="form-control" style="font-size: small" id="event_time" name="event_time" value="{{ isset($selectedEvent) ? $selectedEvent->end_time : '' }}" required>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="event_location" class="form-label text-xs">Request Budget <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" wire:model='requestedBudget'>
                                        @error('requestedBudget')
                                            <div class="text-danger text-xs">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="approved_budget" class="form-label text-xs">Approved Budget</label>
                                        <input disabled type="number" class="form-control" id="approved_budget" name="approved_budget" value="{{ $approvedBudget ?? '' }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="event_description" class="form-label text-xs">Reviewed By</label>
                                        <select name="reviewed_by" id="reviewed_by" class="form-select form-select-sm" wire:model='selectedReviewer'>
                                            <option class="text-xs" value="">Select Reviewer</option>
                                            @foreach ($reviewers as $reviewer)
                                                <option class="text-xs" value="{{ $reviewer->employees->id }}" {{ (isset($selectedEvent) && $selectedEvent->reviewed_by == $reviewer->id) ? 'selected' : '' }}>
                                                    {{ $reviewer->employees->name . ' ' . $reviewer->employees->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('selectedReviewer')
                                            <div class="text-danger text-xs">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="guest_count" class="form-label text-xs">Approved By</label>
                                        <select name="approved_by" id="approved_by" class="form-select form-select-sm" wire:model='selectedApprover'>
                                            <option class="text-xs" value="">Select Approver</option>
                                            @foreach ($approvers as $approver)
                                                <option class="text-xs" value="{{ $approver->employees->id }}" {{ (isset($selectedEvent) && $selectedEvent->approved_by == $approver->id) ? 'selected' : '' }}>
                                                    {{ $approver->employees->name . ' ' . $approver->employees->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('selectedApprover')
                                            <div class="text-danger text-xs">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <textarea class="form-control" name="notes" id="" cols="30" rows="3" wire:model='notes'></textarea>
                                         @error('notes')
                                            <div class="text-danger text-xs">{{ $message }}</div>
                                        @enderror
                                    </div>    
                                </div>
                            </div>
                    
                            <div class="card mt-2">
                                <div class="card-body">
                                    <div  class="card-title row">
                                        <h6 class="col-md-6">Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped">
                                            <tbody> 
                                                
                                                <tr>
                                                    <td class="text-xs">Income on Service</td>
                                                    <td class="text-xs">
                                                        @php
                                                            $internalTotal = isset($selectedEvent) && $selectedEvent->eventServices
                                                                ? $selectedEvent->eventServices->where('service.service_type', 'INTERNAL')->sum(function($service) {
                                                                    return $service->price->amount * ($service->qty ? $service->qty : 1);
                                                                })
                                                                : 0;

                                                            $externalTotal = isset($selectedEvent) && $selectedEvent->eventServices
                                                                ? $selectedEvent->eventServices->where('service.service_type', 'EXTERNAL')->sum(function($service) {
                                                                    return ($service->cost->amount ?? 0) * ($service->qty ? $service->qty : 1);
                                                                })
                                                                : 0;

                                                            $totalIncome = $internalTotal + $externalTotal;
                                                        @endphp
                                                        {{ $totalIncome }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-xs"><strong>Buffet Menu</strong></td>
                                                    <td class="text-sm"><strong>
                                                        {{ isset($selectedEvent) && $selectedEvent->eventMenus
                                                            ? $selectedEvent->eventMenus->sum(function($menu) {
                                                                return $menu->price->amount * ($menu->qty ? $menu->qty : 1);
                                                            })
                                                            : 0
                                                        }}</strong><br>
                                                    </td>
                                                </tr>
                                            </tbody>    
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="card mt-2">
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
                            </div> --}}
                            </div>
                            <div class="card-footer">
                                <div class="input-group">
                                    <select name="" class="form-select" id="" class="" wire:model='isFinal'>
                                        <option value="">Save As</option>
                                        <option value="PREPARING" @if ($isFinal == 'PREPARING') selected @endif>Draft</option>
                                        <option value="PENDING" @if ($isFinal == 'PENDING') selected @endif>Final</option>
                                    </select>
                                    @if ($isNewRequest)
                                        <button type="submit" class="btn btn-success ">Save</button>
                                        <button type="button" class="btn btn-danger input-group-text" wire:click="resetForm">Reset</button>
                                    @elseif ($isFinal == 'PREPARING')
                                        <button type="button" wire:click="updateRequest" class="btn btn-success ">Update</button>
                                    @endif
                                    <a type="button" href="{{ route('banquet.procurement.summary') }}" class="btn btn-secondary input-group-text">Summary</a>
                                    <button type="button" class="btn btn-info input-group-text" wire:click="printPreview">Print Preview</button>
                                </div>
                            </div>
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
            window.addEventListener('clearFields',function(){
                document.getElementById('banquetProcurementForm').reset();
                 window.scrollTo({ top: 0, behavior: 'smooth' });
            })
        });
    </script>
</div>

