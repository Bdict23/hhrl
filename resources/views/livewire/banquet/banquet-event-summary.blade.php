<div class="container my-5">
        <h3 class="mb-3 text-center">Banquet Event Bookings</h3>
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
                <div class="col-md-4 mb-4 event-item" data-date="{{ $event->event_date }}" data-bs-toggle="modal" data-bs-target="#eventDetailsModal" wire:click="viewEventDetails({{ $event->id }})">
                    <div class="card event-card shadow-sm">
                        <div class="card-body">
                           <div class="d-flex justify-content-between">
                             <h5 class="card-title">{{ $event->event_name }}</h5>
                           </div>
                            <p class="card-text">
                                {{ $event->venue->venue_name }} &nbsp;({{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }} - 
                                {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }})<br>
                                {{ $event->event_type }}<br>
                                {{ $event->guest_count }} Guests
                            </p>
                            <span class="badge bg-secondary date-badge" wire:ignore>{{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}</span>

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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventDetailsModalLabel">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                       <div class="row">
                        <div class="col-md-6 mt-3 mb-3">
                            <div class="card mb-3">
                                <div  class="card-header d-flex justify-content-between">         
                                    <h5 class="col-md-6">Services Acquired</h5>
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
                                                    <td>{{ $services->price->amount }}</td>
                                                    <td>{{ $services->qty ?? '-' }}</td>
                                                    <td>{{ $services->price->amount * ($services->qty ?? 0) }}</td>
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
                            <div class="card">
                                <div  class="card-header d-flex justify-content-between">         
                                    <h5 class="col-md-6">Menu</h5>
                                </div>
                                <div class="card-body overflow-auto" style="max-height: 210px;">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-xs">Name</th>
                                                <th class="text-xs">Category</th>
                                                <th class="text-xs">Rate</th>
                                                <th class="text-xs">Quantity</th>
                                                <th class="text-xs">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($eventDetails->eventMenus ?? [] as $menus)
                                                <tr>
                                                    <td>{{ $menus->menu->menu_name }}</td>
                                                    <td>{{ $menus->menu->category->category_name }}</td>
                                                    <td>{{ $menus->price->amount }}</td>
                                                    <td>{{ $menus->qty ?? '-' }}</td>
                                                    <td>{{ $menus->price->amount * ($menus->qty ?? 0) }}</td>
                                                </tr>

                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No menus selected</td>
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
                                    <h5 class="card-title">Event Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="referenceNumber" class="form-label text-sm">Event Name</label>
                                            <input type="text" class="form-control text-center" id="referenceNumber" disabled placeholder="<AUTO>" value="{{ $eventDetails->event_name ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="requestDate" class="form-label text-sm">Customer</label>
                                            <input type="text" class="form-control" id="documentNumber" value="{{ $eventDetails->customer->customer_fname ?? '' }} {{ $eventDetails->customer->customer_lname ?? '' }}" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="department" class="form-label text-sm">Event Date</label>
                                            <input type="text" class="form-control text-center" id="department" value="{{ $eventDetails->event_date ?? '' }}" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="eventType" class="form-label text-sm">Start Time</label>
                                            <input type="time" class="form-control" id="eventType" placeholder="Start Time" disabled value="{{ $eventDetails->start_time ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="guestCount" class="form-label text-sm">End Time</label>
                                            <input type="time" class="form-control" id="guestCount" disabled value="{{ $eventDetails->end_time ?? '' }}" placeholder="End Time">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="venue" class="form-label text-sm">Venue</label>
                                            <input type="text" class="form-control" id="venue" placeholder="Venue" disabled value="{{ $eventDetails->venue->venue_name ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="eventType" class="form-label text-sm">Guest Count</label>
                                            <input type="text" class="form-control" id="eventType" placeholder="Guest Count" disabled value="{{ $eventDetails->guest_count ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="myNote" class="form-label text-sm">Note</label>
                                            <textarea name="" id="myNote" class="form-control" disabled>{{ $eventDetails->note ?? '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            @if (auth()->user()->employee->getModulePermission('Banquet Events') == 1)
                                                {{-- <a href="" wire:click="openToEdit({{ $eventDetails->id }})">Edit</a> --}}
                                                <button class="btn btn-primary text-sm" 
                                                        {{-- wire:click="openToEdit({{ $event->id }})" 
                                                        @if($event->status == 'pending') disabled @endif --}}
                                                        >
                                                    Edit
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                   </div>
                </div>
            </div>
        </div>
    </div>

   
        <script>

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
        </script>
</div>