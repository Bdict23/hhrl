<div class="container my-5">
        <h3 class="mb-3 text-center">Banquet Event Order &nbsp;<i class="bi bi-journals"></i></h3>
    <div class="container my-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex flex-wrap align-items-end gap-2">

                <!-- Date range filter group -->
                <div class="d-flex flex-wrap gap-2">
                    <div>
                        <label for="startDate" class="form-label mb-1 fw-semibold">From</label>
                        <input type="date" id="startDate" class="form-control" style="min-width: 160px;" wire:model='fromDate'>
                    </div>
                    <div>
                        <label for="endDate" class="form-label mb-1 fw-semibold">To</label>
                        <input type="date" id="endDate" class="form-control" style="min-width: 160px;" wire:model='toDate'>
                    </div>
                    <div class="d-flex align-items-end gap-2">
                        <button class="btn btn-primary" wire:click="filterEvents">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <button class="btn btn-outline-secondary" wire:click="resetFilters">
                        <i class="bi bi-x-circle"></i> Reset
                    </button>
                    </div>
                </div>

                <!-- Spacer -->
                @if (auth()->user()->employee->getModulePermission('Banquet Events') == 1)
                    <div class="ms-auto">
                        <a href="{{ route('banquet_events.create') }}" style="text-decoration: none;" class="btn btn-primary">+ Create New Event</a>
                    </div>
                @endif
                
            </div>
        </div>
    </div>

    <!-- Events Card View -->
    <div class="row" id="eventsContainer">
        <!-- Events -->
        @foreach ($eventLists as $event)
                <div class="col-md-4 mb-4 event-item" data-date="{{ $event->start_date }}" data-bs-toggle="modal" wire:click="viewEventDetails({{ $event->id }})">
                    <div class="card event-card shadow-sm">
                        <div class="card-body">
                           <div class="d-flex justify-content-between">
                             <h5 class="card-title">{{ $event->event_name }}</h5>
                           </div>
                           <p class="card-text"> {{ $event->event_address }}</p>
                            <p class="card-text">
                                <strong> {{ \Carbon\Carbon::parse($event->start_date)->format('M. d, Y') }} </strong> ({{ \Carbon\Carbon::parse($event->arrival_time)->format('g A') }})
                                until <strong>{{ \Carbon\Carbon::parse($event->end_date)->format('M. d, Y') }}</strong> ({{ \Carbon\Carbon::parse($event->departure_time)->format('g:i A') }})<br>
                                {{ $event->event_type }}<br>
                                {{ $event->guest_count }} Guests
                            </p>
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-secondary date-badge" wire:ignore></span>
                                <span wire:loading wire:target="viewEventDetails({{ $event->id }})"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait... </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
           
            @if ($eventLists->isEmpty())
                <div class="col-12 text-center">
                    <p class="text-muted">No events found.</p>
                </div>
            @endif
        </div>

        {{-- view details modal --}}
    <div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventDetailsModalLabel">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                   <div class="d-flex justify-content-end gap-2 p-3">
                     @if (auth()->user()->employee->getModulePermission('Banquet Events') == 1)
                         {{-- <a href="" wire:click="openToEdit({{ $eventDetails->id }})">Edit</a> --}}
                         <button class="btn btn-primary text-sm btn-sm" 
                                 wire:click="openEvent" 
                                 @if($selectedEventStatus == 'CONFIRMED') hidden @endif>
                             EDIT &nbsp;
                             <i class="bi bi-pencil-square"></i>
                         </button>
                     @if($selectedEventStatus == 'PENDING')
                             <button class="btn btn-success text-sm btn-sm" 
                                 wire:click="confirmEvent" 
                         @if($selectedEventStatus == 'CONFIRMED') disabled @endif>
                                 CONFIRM EVENT &nbsp;
                                 <i class="bi bi-check-circle"></i>
                             </button>
                         @endif
                     @endif
                         @if($selectedEventStatus != 'CANCELLED' && $selectedEventStatus != 'CONFIRMED')
                             <button class="btn btn-danger text-sm btn-sm" 
                                     wire:click="cancelEvent" 
                                     @if($selectedEventStatus == 'CANCELLED') disabled @endif>
                                 CANCEL EVENT &nbsp;
                                 <i class="bi bi-x-circle"></i>
                             </button>
                     @endif
                     <a class="btn btn-sm btn-secondary" href="/print-preview?event-id={{ $selectedEventId }}">PRINT PREVIEW&nbsp;<i class="bi bi-printer"></i></a>
                   </div>

                <div class="modal-body">
                       <div class="card">
                        <div class="card-body row">
                            {{-- LEFT --}}
                            <div class="col-md-6 mt-3 mb-3">

                                {{-- venues --}}
                                <div class="card mb-3">
                                    <div  class="card-header d-flex justify-content-between">         
                                        <h5 class="col-md-6">Locations</h5>
                                    </div>
                                    <div class="card-body overflow-auto" style="max-height: 280px;">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-xs">Location</th>
                                                    <th class="text-xs">Rate</th>
                                                    <th class="text-xs">Quantity</th>
                                                    <th class="text-xs">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($venueDetails ?? [] as $venues)
                                                    <tr>
                                                        <td>{{ $venues->venue->venue_name }}</td>
                                                        <td>{{ number_format($venues->ratePrice->amount, 2 ) }}</td>
                                                        <td>{{ $venues->qty ?? '-' }}</td>
                                                        <td>{{ number_format($venues->ratePrice->amount * ($venues->qty ?? 0), 2 ) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">No venues acquired</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                {{-- service --}}
                                <div class="card mb-3">
                                    <div  class="card-header d-flex justify-content-between">         
                                        <h5 class="col-md-6">Services / Miscellaneous</h5>
                                    </div>
                                    <div class="card-body overflow-auto" style="max-height: 280px;">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-xs">Service Name</th>
                                                    <th class="text-xs">Rate</th>
                                                    <th class="text-xs">Quantity</th>
                                                    <th class="text-xs">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($eventDetails->eventServices ?? [] as $services)
                                                    <tr>
                                                        <td>{{ $services->service->service_name }}</td>
                                                        <td>{{ number_format($services->price->amount, 2) }}</td>
                                                        <td>{{ $services->qty ?? '-' }}</td>
                                                        <td>{{ number_format($services->price->amount * ($services->qty ?? 0), 2) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">No services acquired</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                {{-- food --}}
                                <div class="card">
                                    <div  class="card-header d-flex justify-content-between">         
                                        <h5 class="col-md-6">Food</h5>
                                    </div>
                                    <div class="card-body overflow-auto" style="max-height: 210px;">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-xs">Name</th>
                                                    <th class="text-xs">Category</th>
                                                    <th class="text-xs">Rate</th>
                                                    <th class="text-xs">Quantity</th>
                                                    <th class="text-xs">Note</th>
                                                    <th class="text-xs">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($eventDetails->eventMenus ?? [] as $menus)
                                                    <tr>
                                                        <td>{{ $menus->menu->menu_name }}</td>
                                                        <td>{{ $menus->menu->category->category_name }}</td>
                                                        <td>{{ number_format($menus->price->amount, 2) }}</td>
                                                        <td>{{ $menus->qty ?? '-' }}</td>
                                                        <td title="{{ $menus->note ?? '-' }}" style="cursor: pointer; text-decoration: underline; color: blue;" data-bs-dismiss="modal" onclick="showNoteModal( '{{ $menus->note ?? '' }}' , '{{ $menus->menu->menu_name }}' )"> 
                                                           {{ Str::limit($menus->note ?? '-', 10) }}
                                                           
                                                        </td>
                                                        <td>{{ number_format($menus->price->amount * ($menus->qty ?? 0), 2) }}</td>
                                                    </tr>
                            
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">No menus selected</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                {{-- STATUS --}}
                                 <div class="mt-4 alert @if($selectedEventStatus == 'PENDING') alert-warning 
                                                            @elseif($selectedEventStatus == 'CONFIRMED') alert-success 
                                                            @elseif($selectedEventStatus == 'CANCELLED') alert-danger 
                                                            @endif" role="alert">
                                    @if($selectedEventStatus == 'PENDING')
                                        <strong>Status:</strong> Pending Confirmation
                                    @elseif($selectedEventStatus == 'CONFIRMED')
                                        <strong>Status:</strong> Confirmed
                                    @elseif($selectedEventStatus == 'CANCELLED')
                                        <strong>Status:</strong> Cancelled
                                    @endif
                                    <p class="mt-3" ></p>
                                </div>
                            </div>
                            {{-- RIGHT --}}
                            <div class="col-md-6 mt-3 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Event Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text">Reference</span>
                                                    <input type="text" class="form-control text-center" id="referenceNumber" disabled placeholder="<AUTO>" value="{{ $eventDetails->reference ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="referenceNumber" class="form-label text-sm">Event Name</label>
                                                <input type="text" class="form-control text-center" id="referenceNumber" disabled placeholder="<AUTO>" value="{{ $eventDetails->event_name ?? '' }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="requestDate" class="form-label text-sm">Customer</label>
                                               <div class="input-group">
                                                 <input type="text" class="form-control" id="documentNumber" value="{{ $eventDetails->customer->customer_fname ?? '' }} {{ $eventDetails->customer->customer_lname ?? '' }}" disabled>
                                                <button type="button" class="input-group-text" style="background-color: rgb(142, 207, 250)" data-bs-toggle="modal" data-bs-target="#customerDetailsModal">view</button>
                                                {{-- data-bs-toggle="modal" data-bs-target="#customerDetailsModal" wire:click="viewCustomer({{ $eventDetails->customer->id ?? '' }})" --}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="department" class="form-label text-sm">Start Date</label>
                                                <input type="text" class="form-control text-center" id="department" value="{{ $eventDetails->start_date ?? '' }}" disabled>
                                            </div>
                                             <div class="col-md-6 mb-3">
                                                <label for="department" class="form-label text-sm">End Date</label>
                                                <input type="text" class="form-control text-center" id="department" value="{{ $eventDetails->end_date ?? '' }}" disabled>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="eventType" class="form-label text-sm">Arrival Time</label>
                                                <input type="time" class="form-control" id="eventType" placeholder="Start Time" disabled value="{{ $eventDetails->arrival_time ?? '' }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="guestCount" class="form-label text-sm">Departure Time</label>
                                                <input type="time" class="form-control" id="guestCount" disabled value="{{ $eventDetails->departure_time ?? '' }}" placeholder="End Time">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="event_address" class="form-label text-sm">Event Address</label>
                                                <input type="text" class="form-control" id="event_address" disabled value="{{ $eventDetails->event_address ?? '' }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="eventType" class="form-label text-sm">Guest Count / Pax. Count</label>
                                                <input type="text" class="form-control" id="eventType" placeholder="Guest Count" disabled value="{{ $eventDetails->guest_count ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="myNote" class="form-label text-sm">Note</label>
                                                <textarea name="" id="myNote" class="form-control" disabled>{{ $eventDetails->notes ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group mt-2">
                                    <label for="" class="input-group-text">Reviewed By</label>
                                    <input type="text" class="form-control" value="{{ $eventDetails->reviewer->name ?? '' }} {{ $eventDetails->reviewer->last_name ?? '' }}" disabled>
                                </div>
                                <div class="input-group mt-2">
                                    <label for="" class="input-group-text">Approved By</label>
                                    <input type="text" class="form-control" value="{{ $eventDetails->approver->name ?? '' }} {{ $eventDetails->approver->last_name ?? '' }}" disabled>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- view customer modal --}}
        <div class="modal fade" id="customerDetailsModal" tabindex="-1" aria-labelledby="customerDetailsModalLabel" aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="customerDetailsModalLabel">Customer Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#eventDetailsModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Customer Information</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> {{ $customerDetails->customer_fname ?? 'N/A' }} {{ $customerDetails->customer_mname ?? '' }} {{ $customerDetails->customer_lname ?? 'N/A' }} {{ $customerDetails->suffix ?? '' }}</p>
                                        <p><strong>Gender:</strong> {{ $customerDetails->gender ?? '' }}</p>
                                        <p><strong>Email:</strong> {{ $customerDetails->email ?? '' }}</p>
                                        <p><strong>Phone Number 1 :</strong> {{ $customerDetails->contact_no_1 ?? '' }}</p>
                                        <p><strong>Phone Number 2 :</strong> {{ $customerDetails->contact_no_2 ?? '' }}</p>
                                        <p><strong>Address:</strong> {{ $customerDetails->customer_address ?? '' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Contact Person:</strong> {{ $customerDetails->contact_person ?? '' }}</p>
                                        <p><strong>Contact Relation:</strong> {{ $customerDetails->contact_person_relation ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- view note modal --}}
        <div class="modal fade" id="menuNoteModal" tabindex="-1" aria-labelledby="menuNoteModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuNoteModalLabel">Menu Note</h5>
                </div>
                <div class="modal-body">
                    <form id="menuNoteForm">
                        <div class="mb-3">
                            <label for="menu_note" class="form-label">Note</label>
                            <textarea wire:model="currentMenuNote" class="form-control" id="menu_note" name="menu_note" rows="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-bs-target="#eventDetailsModal" data-bs-toggle="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
   
    <script>

        function showNoteModal(note, menuName) {
            let menuNoteModal = new bootstrap.Modal(document.getElementById('menuNoteModal'));
            let modalTitle = document.getElementById('menuNoteModalLabel');
            modalTitle.textContent = `Menu Note - ${menuName}`;
            document.getElementById('menu_note').value = note;
            menuNoteModal.show();
        }

        function updateDaysLeft() {
            let today = new Date();
            let items = document.querySelectorAll('.event-item');

            items.forEach(item => {
                let dateStr = item.getAttribute('data-date');
                let eventDate = new Date(dateStr);
                let diffTime = eventDate - today;
                let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                let circle = document.createElement('span');
                circle.classList.add('circle');

                if (diffDays <= 3) {
                    circle.classList.add('red');
                } else if (diffDays <= 10) {
                    circle.classList.add('yellow');
                } else {
                    circle.classList.add('green');
                }

                let badge = item.querySelector('.date-badge');
                // Store original content if not already stored
                if (!badge.hasAttribute('data-original')) {
                    badge.setAttribute('data-original', badge.textContent);
                }
                // Always reset to original before updating
                badge.innerHTML = `${circle.outerHTML} ${badge.getAttribute('data-original')} <small>(${diffDays} day(s) left)</small>`;
            });
        }
        function sortEventsByDate() {
            let container = document.getElementById('eventsContainer');
            let items = Array.from(container.querySelectorAll('.event-item'));

            items.sort((a, b) => {
            let dateA = new Date(a.getAttribute('data-date'));
            let dateB = new Date(b.getAttribute('data-date'));
            return dateA - dateB;
            });

            items.forEach(item => {
            container.appendChild(item);
            });
        }

        // Sort events and update days left indicators on load
        sortEventsByDate();
        updateDaysLeft();

        // Listen for Livewire event to update badges after DOM update
        window.addEventListener('modalOpened', function () {
            updateDaysLeft();
        });
        // open showEventDetailsModal modal from livewire
        window.addEventListener('showEventDetailsModal', function () {
            console.log('Opening event details modal...');
            let eventDetailsModal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
            eventDetailsModal.show();
        });

    </script>
</div>