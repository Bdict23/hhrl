
   <div>
    {{-- upper dashboard --}}
       <div class="row  mb-2">
         <div class="col-md-6 row">
                <div class="col-md-6">
                    <div class="input-group"   @if($banquetEventBudget && $banquetEventBudget->status == 'APPROVED' ) hidden @endif>
                        <select name="" class="form-select" id="" class="" wire:model='isFinal'>
                            <option value="">Save As</option>
                            <option value="PREPARING" @if ($isFinal == 'PREPARING') selected @endif>Draft</option>
                            <option value="PENDING" @if ($isFinal == 'PENDING') selected @endif>Final</option>
                        </select>
                        @if ($isNewRequest)
                            <x-primary-button  wire:click="storeRequestBudget" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="storeRequestBudget">Save</span>
                                <span wire:loading wire:target="storeRequestBudget">Saving...</span>
                            </x-primary-button>
                        @elseif ($banquetEventBudget->status == 'PREPARING')
                            <x-primary-button  wire:click="updateRequest" class="text-sm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="updateRequest">Update</span>
                                <span wire:loading wire:target="updateRequest">Updating...</span>
                            </x-primary-button>
                        @endif
                    </div>
                </div>
                     <div class="col-md-6 justify-content-center d-flex">
                       <div class="container">
                         <a type="button" href="{{ route('banquet.procurement.summary') }}" class="btn btn-secondary input-group-text">Summary &nbsp;<i class="bi bi-list-columns"></i></a> 
                       </div>
                        <div class="container">
                            @if($bebId && !$isNewRequest && $isFinal == 'APPROVED')
                             <a href="/banquet-event-budget-print?beb-id={{ $bebId }}" class="btn btn-info input-group-text">Print Preview &nbsp;<i class="bi bi-printer"></i></a>
                            @endif
                        </div>
                    </div>
         </div>
         <div class="col-md-6 d-flex justify-content-end">
           <h4>Banquet Event Budget - Create &nbsp;<i class="bi bi-calculator-fill"></i></h4>
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
                                            <input class="form-check-input" type="checkbox" id="addServiceToGross" wire:model.live="hasServices">
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
                                @if($services->service->service_type == 'EXTERNAL' )
                                    <tr>
                                        <td>{{ $services->service->service_name }}</td>
                                        <td>{{ $services->qty ? $services->qty : '-' }}</td>
                                        @if ($services->service->service_type == 'EXTERNAL')
                                            <td>{{ $services->price->amount }}</td>
                                        @else
                                            <td>{{ $services->cost->amount ?? '0' }}</td>

                                        @endif
                                        @if ($services->service->service_type == 'EXTERNAL')
                                            <td>
                                                {{ $services->price->amount * ($services->qty ? $services->qty : 1) }}
                                            </td>
                                        @else
                                            <td>
                                                {{ ($services->cost->amount ?? 0) * ($services->qty ? $services->qty : 1) }}
                                            </td>
                                            
                                        @endif

                                    </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No services found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                             <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                                    <td>
                                        @php
                                            $totalIncome = isset($selectedEvent) && $selectedEvent->eventServices
                                                ? $selectedEvent->eventServices->where('service.service_type', 'EXTERNAL')->sum(function($service) {
                                                    return $service->price->amount * ($service->qty ? $service->qty : 1);
                                                })
                                                : 0;

                                            // $externalTotal = isset($selectedEvent) && $selectedEvent->eventServices
                                            //     ? $selectedEvent->eventServices->where('service.service_type', 'EXTERNAL')->sum(function($service) {
                                            //         return ($service->cost->amount ?? 0) * ($service->qty ? $service->qty : 1);
                                            //     })
                                            //     : 0;

                                            // $totalIncome = $internalTotal + $externalTotal;
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
                                            {{ isset($selectedEvent) && $selectedEvent->eventMenus
                                                            ? $selectedEvent->eventMenus->sum(function($menu) {
                                                                return $menu->price->amount * ($menu->qty ? $menu->qty : 1);
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
                                    <label for="document_number" class="form-label form-control-sm">Document Number</label>
                                    <input type="text" class="form-control form-control-sm" id="document_number" name="document_number" wire:model="documentNumber">
                                    @error('documentNumber')
                                        <div class="text-danger text-xs">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="reference_number" class="form-label form-control-sm">Reference Number</label>
                                    <input type="text" class="form-control form-control-sm" id="reference_number" name="reference_number" placeholder="<AUTO>" value="{{ $requestReferenceNumber }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="cust_name" class="form-label form-control-sm">Customer Name</label>
                                    <input type="text" class="form-control form-control-sm" id="event_name" name="event_name" disabled value="{{ isset($selectedEvent->customer) ? $selectedEvent->customer->customer_fname . ' ' . $selectedEvent->customer->customer_lname : '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="event_name" class="form-label form-control-sm">Event Name</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm" id="event_name" name="event_name" disabled value="{{ isset($selectedEvent) ? $selectedEvent->event_name : '' }}">
                                       @if($isNewRequest) <button class="input-group-text" type="button"
                                            style="background-color: rgb(190, 243, 217);" data-bs-toggle="modal" data-bs-target="#getEventModal"><strong class="text-sm">Get</strong></button>
                                        @endif
                                    </div>
                                    @error('selectedEventId')
                                        <div class="text-danger text-xs">{{ $message }}</div>
                                    @enderror  
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                         <label for="event_date" class="form-label form-control-sm">Start Date</label>
                                        <input disabled type="date" class="form-control form-control-sm" id="event_date" name="event_date" value="{{ isset($selectedEvent) ? $selectedEvent->start_date : '' }}" required>
                                 </div>
                                 <div class="col-md-6">
                                    <label for="event_date" class="form-label form-control-sm">End Date</label>
                                    <input disabled type="date" class="form-control form-control-sm" id="event_date" name="event_date" value="{{ isset($selectedEvent) ? $selectedEvent->end_date : '' }}" required>
                                 </div>
                                <div class="col-md-6">
                                    <label for="event_time" class="form-label form-control-sm">Arrival Time</label>
                                    <input disabled type="time" class="form-control form-control-sm" id="event_time" name="event_time" value="{{ isset($selectedEvent) ? $selectedEvent->arrival_time : '' }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="event_time" class="form-label form-control-sm">Departure Time</label>
                                    <input disabled type="time" class="form-control form-control-sm" id="event_time" name="event_time" value="{{ isset($selectedEvent) ? $selectedEvent->departure_time : '' }}" required>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-12 row mb-2 mt-2">
                                        <div class="col-md-8">
                                                <div class="input-group">
                                                    <span class="input-group-text">₱</span>
                                                    <input type="text" class="form-control form-control-sm" wire:model.live='requestedBudget' placeholder="Requested Budget Amount">
                                            </div>
                                            @error('requestedBudget')
                                                <div class="text-danger text-xs">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input type="number" class="form-control form-control-sm" placeholder="Percentage" wire:model.live='totalPercentage'>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="event_description" class="form-label">Reviewed By</label>
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
                                        <textarea class="form-control" name="notes" id="" cols="30" rows="3" wire:model='notes' placeholder="Remarks"></textarea>
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
                                                       <input type="text" id="allocatedBudget" class="form-control form-control-sm" disabled value="₱  {{ $requestedBudget }} ">
                                                    </td>
                                                </td>
                                                </tr>
                                            </tbody>    
                                        </table>
                                    </div>
                                </div>
                            </div>

                            
                            </div>
                     </form>
                </div>    
            </div>
        </div>
    </div>

    

     <!-- Get Event Modal -->
    <div class="modal fade" id="getEventModal" tabindex="-1" aria-labelledby="getEventModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="getEventModalLabel">Select Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Event Name</th>
                                <th>Event Start</th>
                                <th>Event End</th>
                                <th>Customer Name</th>
                                <th>Guest Count</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($events as $event)
                                <tr>
                                    <td>{{ $event->reference }}</td>
                                    <td>{{ $event->event_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($event->start_date)->format('M. d, Y') }} ({{ \Carbon\Carbon::parse($event->arrival_time)->format('h:i A') }})</td>
                                    <td>{{ \Carbon\Carbon::parse($event->end_date)->format('M. d, Y') }} ({{ \Carbon\Carbon::parse($event->departure_time)->format('h:i A') }})</td>
                                    <td>{{ $event->customer->customer_fname . ' ' . $event->customer->customer_lname }}</td>
                                    <td>{{ $event->guest_count }}</td>
                                    <td>
                                        <button wire:click="loadEventDetails({{ $event->id }})" class="btn btn-sm btn-primary">
                                            <span wire:loading wire:target="loadEventDetails({{ $event->id }})"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait..</span>
                                            <span wire:loading.remove wire:target="loadEventDetails({{ $event->id }})">Select</span>
                                        </button>
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
                 window.scrollTo({ top: 0, behavior: 'smooth' });
            })
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
                        window.location.href = '/banquet-procurement-lists';
                    }, 100); // Redirect after 3 seconds (same as the timer duration of the alert)
                }
        });
    </script>
</div>

