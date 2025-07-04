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
                     <h5 class="col-md-6">Equipments</h5>
                 <div class="input-group">
                     <span class="input-group-text">Search</span>
                     <input type="text" class="form-control" id="search-equipment"
                         onkeyup="filterEquipment()">
                 </div>
                 <script>
                        function filterEquipment() {
                            const input = document.getElementById('search-equipment');
                            const filter = input.value.toLowerCase();
                            const table = document.querySelector('#equipment-list');
                            const trs = table.querySelectorAll('tr');
    
                            trs.forEach(row => {
                                // Skip "No equipment selected" row
                                if (row.children.length < 4) return;
                                const name = row.children[0].textContent.toLowerCase();
                                const category = row.children[1].textContent.toLowerCase();
                                if (name.includes(filter) || category.includes(filter)) {
                                    row.style.display = '';
                                } else {
                                    row.style.display = 'none';
                                }
                            });
                        }
                 </script>
            </div>
                 <div class="card-body">
                     <table class="table table-sm table-striped">
                         <thead>
                             <tr>
                                 <th class="text-xs">Equipment Name</th>
                                 <th class="text-xs">Category</th>
                                 <th class="text-xs">Quantity</th>
                             </tr>
                         </thead>
                         <tbody id="equipment-list">
                            @forelse ($selectedEquipments as $index => $item)
                                <tr>
                                    <td>{{ $item->item_description }}</td>
                                    <td>{{ $item->category->category_name ?? 'N/A' }}</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-center" wire:model="equipmentQty.{{ $index }}.qty" min="1" disabled>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No equipment selected</td>
                                </tr>
                            @endforelse
                         </tbody>
                     </table>
                 </div>
             </div>
             <div class="card">
                <div  class="card-header d-flex justify-content-between">         
                     <h5 class="col-md-6">Handling Team</h5>
                    <div class="input-group">
                        <span class="input-group-text">Search</span>
                        <input type="text" class="form-control" id="search-handling-team"
                            onkeyup="filterHandlingTeam()">
                    </div>
                 </div>
                    <script>
                            function filterHandlingTeam() {
                                const input = document.getElementById('search-handling-team');
                                const filter = input.value.toLowerCase();
                                const table = document.querySelector('#handling-team');
                                const trs = table.querySelectorAll('tr');
        
                                trs.forEach(row => {
                                    // Skip "No team members selected" row
                                    if (row.children.length < 4) return;
                                    const name = row.children[0].textContent.toLowerCase();
                                    const lastname = row.children[1].textContent.toLowerCase();
                                    if (name.includes(filter) || lastname.includes(filter)) {
                                        row.style.display = '';
                                    } else {
                                        row.style.display = 'none';
                                    }
                                });
                            }
                    </script>
                <div class="card-body">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th class="text-xs">Name</th>
                                <th class="text-xs">Lastname</th>
                                <th class="text-xs">Position</th>
                            </tr>
                        </thead>
                        <tbody id="handling-team">
                            @forelse ($handlingTeam as $member)
                                <tr>
                                    <td>{{ $member['first_name'] }}</td>
                                    <td>{{ $member['last_name'] }}</td>
                                    <td>{{ $member['position'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No team members selected</td>
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
                    <form 
                        @if ($isNewRequest)
                        wire:submit.prevent="createRequest"
                        @else
                        wire:submit.prevent="updateRequest"
                        @endif>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="referenceNumber" class="form-label text-xs">Reference Number</label>
                                <input type="text" class="form-control text-center" style="font-size: small" id="referenceNumber" disabled placeholder="<AUTO>" value="{{ $requestReferenceNumber }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="requestDate" class="form-label text-xs">Document Series No.</label>
                                <input type="text" class="form-control" style="font-size: small" id="documentNumber" wire:model="requestDocumentNumber" value="{{ $requestDocumentNumber }}" disabled>
                                @error('requestDocumentNumber')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="eventName" class="form-label text-xs">Event Name</label>
                            <div class="input-group">
                                <input type="text" class="form-control text-center" style="font-size: small" id="eventName" value="{{ $eventName }}" disabled>
                            </div>
                            @error('eventName')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="department" class="form-label text-xs">Department</label>
                                <select class="form-select" id="department" wire:model="departmentId" wire:change="loadDepartmentEmployees(event.target.value)" disabled>
                                    <option value="">Select Department</option>
                                   @forelse ($departments as $department)
                                       <option value="{{ $department->id }}" style="font-size: small">{{ $department->department_name }}</option>
                                   @empty
                                       <option value="" disabled>No Departments Found</option>
                                   @endforelse
                                </select>
                                @error('departmentId')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="referenceNumber" class="form-label text-xs">Event Date</label>
                                <input type="date" class="form-control text-center" style="font-size: small" id="referenceNumber" disabled value="{{ $eventDate }}" placeholder="<AUTO>">
                            </div>
                            @error('eventDate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row mb-2">
                           <div class="col-md-6">
                                <label for="eventNote" class="form-label text-xs">Event Note</label>
                                <textarea name="" id="eventNote" class="form-control" style="font-size: small" disabled>{{ $eventNote }}</textarea>
                                @error('eventNote')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="myNote" class="form-label text-xs">Leave a note <span class="text-muted text-xs">(optional)</span></label>
                                <textarea name="" id="myNote" class="form-control" style="font-size: small" wire:model="myNote"></textarea>
                                @error('myNote')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="inchargedBy" class="form-label text-xs">Incharged By</label>
                                <select class="form-select" id="inchargedBy" wire:model="inchargedBy" disabled>
                                    <option value="" >Select Incharged</option>
                                    @forelse ($departmentEmployees as $employee)
                                        <option value="{{ $employee->id }}" style="font-size: small">{{ $employee->name }}</option>
                                    @empty
                                        <option value="" disabled>No Employees Found</option>
                                    @endforelse
                                </select>
                                @error('inchargedBy')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="approvedBy" class="form-label text-xs">Approved By</label>
                                <select class="form-select" id="approvedBy" wire:model="approver" disabled>
                                    <option value="">Select Approved By</option>
                                    @foreach ($approvers as $approver)
                                            <option value="{{ $approver->employees->id }}" style="font-size: small">
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
                                        <option value="" disabled>Save As</option>
                                        <option value="PENDING">Draft</option>
                                        <option value="RELEASED">Approve</option>
                                        <option value="PREPARING">Revise</option>
                                    </select>
                                    @if ($isNewRequest)
                                        <button type="submit" class="btn btn-success ">Save</button>
                                        <button type="button" class="btn btn-danger input-group-text" wire:click="resetForm">Reset</button>
                                    @elseif ($saveAs === 'DRAFT')
                                        <button type="submit" class="btn btn-success ">Update</button>
                                    @endif
                                    <a type="button" href="{{ route('banquet_equipment_requests') }}" class="btn btn-secondary input-group-text" wire:click="openEventModal">Summary</a>
                                </div>
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
                           @error('attachments.*')
                                <span class="text-danger">{{ $message }}</span>
                           @enderror
                        @endif
                    </form>
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