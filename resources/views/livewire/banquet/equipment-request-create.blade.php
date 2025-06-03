
<div class="container">
    <div class="row">
        <div class="col-md-6 mt-3 mb-3">
             <div class="card mb-3">
                 <div  class="card-header d-flex justify-content-between">         
                     <h5 class="col-md-6">Equipment Lists</h5>
                     <button class="btn btn-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#equipmentModal" >Add Equipment</button> 
                 </div>
                 <div class="card-body">
                     <table class="table table-sm">
                         <thead>
                             <tr>
                                 <th class="text-xs">Equipment Name</th>
                                 <th class="text-xs">Category</th>
                                 <th class="text-xs">Quantity</th>
                                 <th class="text-xs">Action</th>
                             </tr>
                         </thead>
                         <tbody>
                            
                         </tbody>
                     </table>
                 </div>
             </div>
             <div class="card">
                <div  class="card-header d-flex justify-content-between">         
                     <h5 class="col-md-6">Handling Team</h5>
                     <button class="btn btn-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#employeetModal" >Add Employee</button> 
                 </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th class="text-xs">Name</th>
                                <th class="text-xs">Lastname</th>
                                <th class="text-xs">Position</th>
                                <th class="text-xs">Action</th>
                            </tr>
                        </thead>
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
                    <form wire:submit.prevent="submitRequest">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="referenceNumber" class="form-label text-sm">Reference Number</label>
                                <input type="text" class="form-control text-center" id="referenceNumber" disabled placeholder="<AUTO>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="requestDate" class="form-label text-sm">Document Series No. <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="documentNumber" wire:model="referenceNumber" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="eventName" class="form-label text-sm">Event Name<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="eventName" wire:model="eventName" disabled>
                                  <button class="input-group-text" type="button"
                                        style="background-color: rgb(190, 243, 217);" data-bs-toggle="modal" data-bs-target="#getEventModal">Get</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="department" class="form-label text-sm">Department <span class="text-danger">*</span></label>
                                <select class="form-select" id="department" wire:model="department" required>
                                    <option value="">Select Department</option>
                                   
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="referenceNumber" class="form-label text-sm">Event Date</label>
                                <input type="text" class="form-control" id="referenceNumber" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                           <div class="col-md-6">
                                <label for="eventNote" class="form-label text-sm">Event Note</label>
                                <textarea name="" id="eventNote" class="form-control" disabled></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="myNote" class="form-label text-sm">Leave a note <span class="text-muted text-xs">(optional)</span></label>
                                <textarea name="" id="myNote" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="attachment" class="form-label text-sm" style="width: 100; ">Layout <span style="font-size: 13px" class="text-muted">(optional)</span></label>
                                        <input wire:model.live="attachments" type="file" class="form-control" id="attachments" style="width: 100; font-size: 13px" multiple>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="inchargedBy" class="form-label text-sm">Incharged By<span class="text-danger">*</span></label>
                                <select class="form-select" id="inchargedBy" wire:model="inchargedBy" required>
                                    <option value="">Select Incharged By</option>
                                    {{-- Add options here --}}
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="approvedBy" class="form-label text-sm">Approved By<span class="text-danger">*</span></label>
                                <select class="form-select" id="approvedBy" wire:model="approvedBy" required>
                                    <option value="">Select Approved By</option>
                                    {{-- Add options here --}}
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Submit Request</button>
                    </form>
                </div>
            </div>
    
        </div>
    </div>


    <!-- Get Event Modal -->
    <div class="modal fade" id="getEventModal" tabindex="-1" aria-labelledby="getEventModalLabel" aria-hidden="true">
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
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment Modal -->
    <div class="modal fade" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="equipmentModalLabel">Select Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <select class="form-select" style="width: 200px;" wire:model="selectedCategory">
                                <option value="">All Categories</option>
                               
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
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Modal -->
    <div class="modal fade" id="employeetModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
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
                            {{-- Loop employees here --}}
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>