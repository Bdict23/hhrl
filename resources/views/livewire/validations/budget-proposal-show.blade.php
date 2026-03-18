

   <div>
      @if (session()->has('success'))
        <div class="alert alert-success" id="success-message">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    {{-- upper dashboard --}}
       <div class="row  mb-2">
         <div class="col-md-6 row">
                <div class="col-md-6">
                    <div class="input-group">
                        <select name="" class="form-select" id="" class="" wire:model="saveAs">
                            <option value="">Save Option</option>
                            <option value="APPROVED" @if ($saveAs == 'APPROVED') selected @endif>APPROVE</option>
                            <option value="PREPARING" @if ($saveAs == 'PREPARING') selected @endif>REVISE</option>
                            <option value="REJECTED" @if ($saveAs == 'REJECTED') selected @endif>REJECT</option>
                        </select>
                        <x-primary-button  wire:click="updateRequest" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="updateRequest">Save</span>
                            <span wire:loading wire:target="updateRequest">Saving...</span>
                        </x-primary-button>
                    </div>
                    @error('saveAs')
                        <div class="text-danger text-xs">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 justify-content-center d-flex">
                <div class="container">
                    <a type="button" href="/budget-proposal-approval-lists" class="btn btn-secondary input-group-text">Summary</a> 
                </div>
            </div>
         </div>
         <div class="col-md-6 d-flex justify-content-end">
           <h4>Banquet Event Budget - Validation &nbsp;<i class="bi bi-calculator-fill"></i></h4>
         </div>
       </div>
    <div class="row">
        {{-- left dashboard --}}
        <div class="col-md-6">
            <div class="card mb-2 border-0">
                <div class="card">
                    <div class="card p-1">
                           <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong class="col-md-6">Services And Miscellaneous</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <label class="form-check-label" for="addServiceToGross">Add to Gross Amount</label>
                                            <input class="form-check-input" type="checkbox" id="addServiceToGross" wire:model.live="hasServices" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                    <td colspan="3" class="text-end"><strong>Total </strong></td>
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
                                        <h6> <strong>{{ number_format($totalIncome, 2) }}</strong></h6>
                                        
                                        
                                    
                                    </td>
                                </tr>
                        </tfoot>
                        </table>
                       </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div  class="card-header">
                         <strong class="col-md-6">Food</strong>
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-xs">Menu name</th>
                                        <th class="text-xs">Category</th>
                                        <th class="text-xs">Qty</th>
                                        <th class="text-xs">Amount</th>
                                        <th class="text-xs">Total</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @forelse ($selectedEvent->eventMenus ?? [] as $orders )
                                        <tr>
                                            <td>{{ $orders->menu->menu_name }}</td>
                                            
                                            <td>{{ $orders->menu->category->category_name }}</td>
                                            <td>{{ $orders->qty }}</td>
                                            <td>{{ $orders->price->amount ?? 0 }}</td>
                                            <td>{{ ($orders->price->amount ?? 0) * ($orders->qty ?? 0) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No food orders found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total</strong></td>
                                        <td>
                                            <h6><strong>
                                                @php
                                                    $foodTotal = isset($selectedEvent) && $selectedEvent->eventMenus
                                                            ? $selectedEvent->eventMenus->sum(function($menu) {
                                                                return $menu->price->amount * ($menu->qty ? $menu->qty : 1);
                                                            })
                                                            : 0;
                                                @endphp
                                                {{ number_format($foodTotal, 2) }}</strong></h6>
                                            
                                        </td>
                                    </tr>
                                </tfoot>
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
                        <div class="row">
                            <div class="col-md-6">
                                <label for="document_number" class="form-label text-xs">Document Number</label>
                                <input type="text" class="form-control text-xs form-control-sm" id="document_number" name="document_number" wire:model="documentNumber" disabled>
                                @error('documentNumber')
                                    <div class="text-danger text-xs">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="reference_number" class="form-label text-xs">Reference Number</label>
                                <input type="text" class="form-control text-xs form-control-sm" id="reference_number" name="reference_number" placeholder="<AUTO>" value="{{ $requestReferenceNumber }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="cust_name" class="form-label text-xs">Customer Name</label>
                                <input type="text" class="form-control text-xs form-control-sm" id="event_name" name="event_name" disabled value="{{ isset($selectedEvent->customer) ? $selectedEvent->customer->customer_fname . ' ' . $selectedEvent->customer->customer_lname : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label for="event_name" class="form-label text-xs">Event Name</label>
                                    <input type="text" class="form-control text-xs form-control-sm" id="event_name" name="event_name" disabled value="{{ isset($selectedEvent) ? $selectedEvent->event_name : '' }}">
                                @error('selectedEventId')
                                    <div class="text-danger text-xs">{{ $message }}</div>
                                @enderror  
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                         <label for="event_date" class="form-label text-xs">Start Date</label>
                                        <input disabled type="date" class="form-control text-xs form-control-sm" id="event_date" name="event_date" value="{{ isset($selectedEvent) ? $selectedEvent->start_date : '' }}" required>
                            </div>
                            <div class="col-md-6">
                                    <label for="event_date" class="form-label text-xs">End Date</label>
                                    <input disabled type="date" class="form-control text-xs form-control-sm" id="event_date" name="event_date" value="{{ isset($selectedEvent) ? $selectedEvent->end_date : '' }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="event_time" class="form-label text-xs">Arrival Time</label>
                                <input disabled type="time" class="form-control text-xs form-control-sm" id="event_time" name="event_time" value="{{ isset($selectedEvent) ? $selectedEvent->arrival_time : '' }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="event_time" class="form-label text-xs">Departure Time</label>
                                <input disabled type="time" class="form-control text-xs form-control-sm" id="event_time" name="event_time" value="{{ isset($selectedEvent) ? $selectedEvent->departure_time : '' }}" required>
                            </div>
                           
                            <div class="row mb-2">
                                <div class="col-md-12 row mb-2 mt-2">
                                    <div class="col-md-8">
                                            <div class="input-group">
                                                <span class="input-group-text">₱</span>
                                                <input disabled type="text" class="form-control form-control-sm"  value="{{ $requestedBudget ? number_format($requestedBudget, 2) : '' }}" placeholder="Requested Budget">
                                        </div>
                                        @error('requestedBudget')
                                            <div class="text-danger text-xs">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <input disabled type="number" class="form-control form-control-sm" placeholder="Percentage" value="{{ ($requestedBudget && $totalGrossOrder > 0) ? number_format(($requestedBudget / $totalGrossOrder) * 100, 2) : null }}">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="event_description" class="form-label text-xs">Reviewed By</label>
                                    <input type="text" class="form-control text-xs" id="reviewed_by" name="reviewed_by" disabled value="{{ $selectedReviewer }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="guest_count" class="form-label text-xs">Approved By</label>
                                    <input type="text" class="form-control text-xs" id="approved_by" name="approved_by" disabled value="{{ $selectedApprover }}">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <label for="notes" class="form-label text-xs">Notes <span style="font-size: x-small">(optional)</span></label>
                                    <textarea class="form-control" name="notes" id="notes" cols="30" rows="3" wire:model='notes'></textarea>
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
                                                    <td class="text-xs"><strong>Total Gross Order Amount </strong></td>
                                                    <td class="text-sm">
                                                        <input id="grossOrder" type="text" class="form-control form-control-sm" value="₱ {{number_format($totalGrossOrder,2)}}" disabled>
                                                        <br>
                                                    </td>
                                                </tr>
                                                <tr><td>
                                                    <strong>
                                                    Allocated Budget {{ $totalPercentage ? number_format($totalPercentage, 2) : 0 }}%
                                                    </strong>
                                                    <td>
                                                       <input type="text" id="allocatedBudget" class="form-control form-control-sm" disabled value="₱  {{ number_format($requestedBudget, 2) }} ">
                                                    </td>
                                                </td>
                                                </tr>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('closeSelectEventModal', function () {
             var modal = bootstrap.Modal.getInstance(document.getElementById('getEventModal'));
                modal.hide();
            });
            window.addEventListener('clearFields',function(){
                document.getElementById('banquetProcurementForm').reset();
            })
            window.addEventListener('viewTop', function () {
                 window.scrollTo({ top: 0, behavior: 'smooth' });
                  document.getElementById('banquetProcurementForm').reset();
            });
        });

        //swal alert
        window.addEventListener('showAlert', event => {
           const data = event.detail[0];
              Swal.fire({
                icon: data.type,
                title: data.title,
                text: data.message,
                timer: 5000,
                showConfirmButton: true,
                });
                // redirect to summary page after saving
                if(data.type === 'success' && data.title === 'Success'){
                    setTimeout(() => {
                        window.location.href = '/budget-proposal-approval-lists';
                    }, 100); // Redirect after 3 seconds (same as the timer duration of the alert)
                }
        });
    </script>
</div>

