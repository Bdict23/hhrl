<div class="container">
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
    <div class="row">
        <div class="col-md-6 mt-3 mb-3">
             <div class="card mb-3">
                 <div  class="card-header d-flex justify-content-between">         
                     <h5 class="col-md-6">Equipment Lists</h5>
                     <button class="btn btn-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#equipmentModal" >Add Equipment</button> 
                 </div>
                 <div class="card-body">
                     <table class="table table-sm table-striped">
                         <thead>
                             <tr>
                                 <th class="text-xs">Equipment Name</th>
                                 <th class="text-xs">Category</th>
                                 <th class="text-xs">Quantity</th>
                                 <th class="text-xs">Action</th>
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
                                    <td>
                                        <button class="btn btn-danger btn-sm" wire:click="removeEquipment({{ $index }})">x</button>
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
                     <button class="btn btn-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#employeeModal" >Add Employee</button> 
                 </div>
                <div class="card-body">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th class="text-xs">Name</th>
                                <th class="text-xs">Lastname</th>
                                <th class="text-xs">Position</th>
                                <th class="text-xs">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($handlingTeam as $member)
                                <tr>
                                    <td>{{ $member['first_name'] }}</td>
                                    <td>{{ $member['last_name'] }}</td>
                                    <td>{{ $member['position'] }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" wire:click="removeHandlingTeamMember({{ $member['id'] }})">x</button>
                                    </td>
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
                    <form wire:submit.prevent="createRequest">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="referenceNumber" class="form-label text-sm">Reference Number</label>
                                <input type="text" class="form-control text-center" id="referenceNumber" disabled placeholder="<AUTO>" value="{{ $requestReferenceNumber }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="requestDate" class="form-label text-sm">Document Series No. <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="documentNumber" wire:model="requestDocumentNumber" value="{{ $requestDocumentNumber }}" >
                                @error('requestDocumentNumber')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="eventName" class="form-label text-sm">Event Name<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control text-center" id="eventName" value="{{ $eventName }}" disabled>
                                  <button class="input-group-text" type="button"
                                        style="background-color: rgb(190, 243, 217);" data-bs-toggle="modal" data-bs-target="#getEventModal">Get</button>
                            </div>
                            @error('eventName')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="department" class="form-label text-sm">Department <span class="text-danger">*</span></label>
                                <select class="form-select" id="department" wire:model="departmentId" wire:change="loadDepartmentEmployees(event.target.value)">
                                    <option value="">Select Department</option>
                                   @forelse ($departments as $department)
                                       <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                                   @empty
                                       <option value="" disabled>No Departments Found</option>
                                   @endforelse
                                </select>
                                @error('departmentId')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="referenceNumber" class="form-label text-sm">Event Date</label>
                                <input type="date" class="form-control" id="referenceNumber" disabled value="{{ $eventDate }}" placeholder="<AUTO>">
                            </div>
                            @error('eventDate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-3">
                           <div class="col-md-6">
                                <label for="eventNote" class="form-label text-sm">Event Note</label>
                                <textarea name="" id="eventNote" class="form-control" disabled>{{ $eventNote }}</textarea>
                                @error('eventNote')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="myNote" class="form-label text-sm">Leave a note <span class="text-muted text-xs">(optional)</span></label>
                                <textarea name="" id="myNote" class="form-control" wire:model="myNote"></textarea>
                                @error('myNote')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="attachment" class="form-label text-sm" style="width: 100; ">Layout <span style="font-size: 13px" class="text-muted">(optional)</span></label>
                            <input wire:model.live="attachments" type="file" class="form-control" id="attachments" style="width: 100; font-size: 13px" multiple>
                            @error('attachments')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="inchargedBy" class="form-label text-sm">Incharged To<span class="text-danger">*</span></label>
                                <select class="form-select" id="inchargedBy" wire:model="inchargedBy">
                                    <option value="" >Select Incharged</option>
                                    @forelse ($departmentEmployees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @empty
                                        <option value="" disabled>No Employees Found</option>
                                    @endforelse
                                </select>
                                @error('inchargedBy')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="approvedBy" class="form-label text-sm">Approved By<span class="text-danger">*</span></label>
                                <select class="form-select" id="approvedBy" wire:model="approver">
                                    <option value="">Select Approved By</option>
                                    @foreach ($approvers as $approver)
                                            <option value="{{ $approver->employees->id }}">
                                                {{ $approver->employees->name }} {{ $approver->employees->last_name }}
                                            </option>
                                        @endforeach
                                </select>
                                @error('approver')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="input-group">
                                    <select name="" class="form-select" id="" class="" wire:model='saveAs'>
                                        <option value="">Save As</option>
                                        <option value="DRAFT">Draft</option>
                                        <option value="FINAL">Final</option>
                                    </select>
                                    <button type="submit" class="btn btn-success ">Save</button>
                                    <button type="button" class="btn btn-danger input-group-text" wire:click="resetForm">Reset</button>
                                    <a type="button" href="{{ route('banquet_equipment_requests') }}" class="btn btn-secondary input-group-text" wire:click="openEventModal">Summary</a>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="getEventModalLabel">Select Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover">
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
                            @foreach ($events as $event)
                                <tr>
                                    <td>{{ $event->event_name }}</td>
                                    <td>{{ $event->event_date }}</td>
                                    <td>{{ $event->customer->customer_fname . ' ' . $event->customer->customer_lname }}</td>
                                    <td>{{ $event->guest_count }}</td>
                                    <td>{{ $event->venue->venue_name }}</td>
                                    <td>
                                        <button wire:click="loadEvent({{ $event->id }})" class="btn btn-sm btn-primary">Select</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment Modal -->
    <div class="modal fade" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="equipmentModalLabel">Select Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <select class="form-select" style="width: 200px;" wire:change='loadItemByCategory(event.target.value)' wire:model="selectedCategory">
                                <option value="">All Categories</option>
                                @forelse ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @empty
                                    <option value="" disabled>No Categories Found</option>
                                @endforelse
                            </select>
                        </div>
                        <div>
                            <input type="text" class="form-control" style="width: 250px;" placeholder="Search equipment..." wire:model.debounce.300ms="equipmentSearch">
                        </div>
                    </div>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Equipment Name</th>
                                <th>Category</th>
                                <th>Available Qty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($equipments as $equipment)
                                <tr>
                                    <td>{{ $equipment->item_description }}</td>
                                    <td>{{ $equipment->category->category_name }}</td>
                                    <td>{{ $equipment->available_qty }}</td>
                                    <td>
                                        <button wire:click="addEquipment({{ $equipment->id }})" class="btn btn-sm btn-primary">Select</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No equipment found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Modal -->
    <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeeModalLabel">Select Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 d-flex justify-content-end">
                        <input type="text" class="form-control" style="width: 300px;" placeholder="Search employee..." wire:model.debounce.300ms="employeeSearch">
                    </div>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Lastname</th>
                                <th>Position</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                          @forelse ($employees as $employee)
                              <tr>
                                  <td>{{ $employee->name }}</td>
                                  <td>{{ $employee->last_name }}</td>
                                  <td>{{ $employee->position->position_name }}</td>
                                  <td>
                                      <button wire:click="addHandlingTeamMember({{ $employee->id }})" class="btn btn-sm btn-primary">Select</button>
                                  </td>
                              </tr>
                          @empty
                              <tr>
                                  <td colspan="4" class="text-center">No employees found.</td>
                              </tr>
                          @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('closeEventModal', event => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('getEventModal'));
            if (modal) {
                modal.hide();
            }
        });
    </script>

</div>