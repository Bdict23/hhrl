<div>
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <select name="" id="" class="form-select form-select-sm input-group-text" wire:model="saveAsStatus">
                            @if($currentARStatus == 'DRAFT')
                                <option value="DRAFT">DRAFT</option>
                            @endif
                            <option value="OPEN">FINAL</option>
                        </select>
                        @if($isCreate && $currentARStatus == 'DRAFT')
                            <x-primary-button wire:click="saveAcknowledgementReceipt">Save</x-primary-button>
                        @elseif($currentARStatus == 'DRAFT' && !$isCreate)
                            <x-primary-button wire:click="updateAcknowledgementReceipt">UPDATE</x-primary-button>
                        @endif
                    </div>
                    @error('saveAsStatus')
                            <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-6">
                <a href="/acknowledgement-receipt-summary"><x-secondary-button >Summary</x-secondary-button></a> 
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <h5 class="alert-heading" style="white-space: nowrap;">Acknowledgement Receipt - Create &nbsp;<i class="bi bi-card-text"></i></h5>
            
        </div>
    </div>
    <div class="row">
        @if(!$isCreate && $currentARStatus == 'OPEN')
        {{-- LEFT --}}
        <div class="card mb-3 mr-3 col-md-6 container">
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <div class="card-header">
                    <h6>Expenses</h6>
                </div>
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Date</th>
                            <th>Particulars</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses ?? [] as $expense)
                            <tr>
                                <td>{{ $expense->reference }}</td>
                                <td>{{ $expense->date }}</td>
                                <td>{{ $expense->particulars }}</td>
                                <td>{{ $expense->amount }}</td>
                            </tr>
                            
                        @empty
                            <tr class="text-center">
                                <td colspan="4">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        {{-- RIGHT --}}
            <div @if($isCreate || $currentARStatus == 'DRAFT') class="col-md-12 container" @else class="col-md-6 container" @endif>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                 <div class="input-group mb-2">
                                    <label for="" class="input-group-text">Reference</label>
                                    <input type="text" class="form-control" placeholder="<AUTO>" disabled wire:model="reference">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-2">
                                    <label for="" class="input-group-text">Event</label>
                                    <input type="text" class="form-control" placeholder="Select Event ->" value="{{ $selectedEvent->event_name ?? '' }}" disabled>
                                    <span class="input-group-text" style="cursor: pointer; background-color:aquamarine" data-bs-toggle="modal" data-bs-target="#banquetEventModal"><i class="bi bi-calendar-event"></i></span>
                                </div>
                            </div>
                        </div>
                       
                        <div class="input-group mb-2">
                            <label for="" class="input-group-text">Source</label>
                            <input type="text" class="form-control" placeholder="Select Customer" value="{{ $selectedCustomer->customer_fname ?? ''}} {{ $selectedCustomer->customer_mname ?? ''}} {{ $selectedCustomer->customer_lname ?? ''}}" disabled>
                            <span class="input-group-text" style="cursor: pointer; background-color:aquamarine" data-bs-toggle="modal" data-bs-target="#customerModal">
                                <i class="bi bi-person"></i>
                            </span>
                        </div>
                        <div class="input-group mb-4">
                            <label for="" class="input-group-text">Address</label>
                            <input type="text" class="form-control" disabled value="{{ $selectedCustomer->customer_address ?? ''}}">
                        </div>

                        @error('customerId')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <div class="row">
                            <strong class="col-md-3 my-auto">Check Details</strong>
                            <hr class="col-md-9">
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                <div class="input-group mb-2">
                                    <label for="" class="input-group-text">Account Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Account Name" wire:model="accountName">
                                </div>
                                @error('accountName')
                                    <span class="text-danger">{{ $message }}</span>
                                 @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-2">
                                    <label for="" class="input-group-text">Bank</label>
                                    <input type="text" class="form-control" placeholder="Select Bank ->" value="{{ $selectedBank->bank_name ?? '' }}" disabled>
                                    <span class="input-group-text" style="cursor: pointer; background-color:aquamarine" data-bs-toggle="modal" data-bs-target="#bankListModal"><i class="bi bi-bank"></i></span>
                                </div>
                                 @error('bankId')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                <div class="input-group mb-2">
                                    <label for="" class="input-group-text">Check Number</label>
                                    <input type="text" class="form-control" placeholder="Enter Check Number" wire:model="checkNumber">
                                </div>
                                @error('checkNumber')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-2">
                                    <label for="" class="input-group-text">Check Date</label>
                                    <input type="date" class="form-control" wire:model="checkDate">
                                </div>
                                @error('checkDate')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12 row">
                            <div class="col-md-6">
                                <div class="input-group mb-2">
                                    <label for="" class="input-group-text">Check Status</label>
                                    <select name="" id="" class="form-select" wire:model="checkStatus">
                                        <option value="">Select Check Status</option>
                                        <option value="CURRENT">Current</option>
                                        <option value="POST-DATED">Post-Dated</option>
                                    </select>
                                </div>
                                @error('checkStatus')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                    
                                <div class="input-group mb-2">
                                    <label for="" class="input-group-text">Check Amount</label>
                                    <input type="number" class="form-control" placeholder="Enter Check Amount" wire:model.live="checkAmount">
                                </div>
                                @error('checkAmount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-2">
                                    <label for="" class="input-group-text">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" wire:model="notes"></textarea>
                                </div>
                                @error('notes')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="" class="input-group-text">Amount in Words</label>
                            <textarea class="form-control" placeholder="<AUTO>" wire:model="amountInWords" disabled></textarea>
                        </div>
                        @error('amountInWords')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
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
                        <button type="submit" class="btn btn-primary"><i class="bi bi-node-plus"></i>&nbsp;Save|Add</button>
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
                                        <button wire:click="selectCustomer({{ $customer->id }})" class="btn btn-sm btn-success select-customer-btn" data-name="{{ $customer->customer_fname . ' ' . $customer->customer_mname . ' ' . $customer->customer_lname }}">
                                            <span wire:loading wire:target="selectCustomer({{ $customer->id }})"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>&nbsp;Wait...</span>
                                            <span wire:loading.remove wire:target="selectCustomer({{ $customer->id }})"><i class="bi bi-check-circle"></i>&nbsp;Select</span>
                                        </button>
                                    
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
    {{-- BANK LIST MODAL --}}
    <div class="modal fade" id="bankListModal" tabindex="-1" aria-labelledby="bankModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header row">
                        <div class="col-md-6">
                            <h5 class="modal-title" id="bankModalLabel">Select Bank</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-2">
                                <span class="input-group-text">Search</span>
                                <input type="text" class="form-control" id="search-banks"
                                    onkeyup="filterBanks()">
                            </div>
                        </div>
                  
                    <script>
                        function filterBanks() {
                            const searchInput = document.getElementById('search-banks');
                            const filter = searchInput.value.toLowerCase();
                            const tableBody = document.getElementById('bankListTable').getElementsByTagName('tbody')[0];
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
                    <table class="table table-striped" id="bankListTable">
                        <thead>
                            <tr>
                                <th>Bank Name</th>
                                <th>Account Name</th>
                                <th>Account Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($banks as $bank)
                               <tr>
                                    <td>{{ $bank->bank_name }}</td>
                                    <td>{{ $bank->account_name }}</td>
                                    <td>{{ $bank->account_number }}</td>
                                    <td>
                                        <button wire:click="selectBank({{ $bank->id }})" class="btn btn-sm btn-success select-bank-btn" data-name="{{ $bank->bank_name }}">
                                            <span wire:loading wire:target="selectBank({{ $bank->id }})"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>&nbsp;Wait...</span>
                                            <span wire:loading.remove wire:target="selectBank({{ $bank->id }})"><i class="bi bi-check-circle"></i>&nbsp;Select</span>
                                        </button>
                                    
                                    </td>

                               </tr>
                            @empty

                            <tr>
                                <td colspan="4" class="text-center">No Data Found</td>
                            </tr>
                                
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Bank List Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#bankRegistrationModal">Create New</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- BANK REGISTRATION MODAL --}}
    <div class="modal fade" id="bankRegistrationModal" tabindex="-1" aria-labelledby="bankRegistrationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bankRegistrationModalLabel">Register Bank Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bankRegistrationForm" wire:submit.prevent="registerBank">
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Bank Name <span class="text-danger">*</span></label>
                            <input wire:model="bankName" type="text" class="form-control" id="bank_name" name="bank_name">
                            @error('bankName')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="bank_code" class="form-label">Bank Code</label>
                            <input wire:model="bankCode" type="text" class="form-control" id="bank_code" name="bank_code" placeholder="(Optional)">
                            @error('bankCode')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="bank_address" class="form-label">Address</label>
                            <input wire:model="bankAddress" type="text" class="form-control" id="bank_address" name="bank_address">
                            @error('bankAddress')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="bank_contact_number" class="form-label">Contact Number</label>
                            <input wire:model="bankContactNumber" type="text" class="form-control" id="bank_contact_number" name="bank_contact_number">
                            @error('bankContactNumber')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="bank_email" class="form-label">Email</label>
                            <input wire:model="bankEmail" type="text" class="form-control" id="bank_email" name="bank_email">
                            @error('bankEmail')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" form="bankRegistrationForm"><i class="bi bi-node-plus"></i>&nbsp;Save|Add</button>
                    <button type="button" class="btn btn-link ms-2" data-bs-toggle="modal" data-bs-target="#bankListModal">Already Exist?</button>
                </div>
            </div>
        </div>
    </div>
    {{-- BANQUET EVENT MODAL --}}
    <div class="modal fade" id="banquetEventModal" tabindex="-1" aria-labelledby="banquetEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header row">
                        <div class="col-md-6">
                            <h5 class="modal-title" id="banquetEventModalLabel">Select Event</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-2">
                                <span class="input-group-text">Search</span>
                                <input type="text" class="form-control" id="search-events"
                                    onkeyup="filterEvents()">
                            </div>
                        </div>
                  
                    <script>
                        function filterEvents() {
                            const searchInput = document.getElementById('search-events');
                            const filter = searchInput.value.toLowerCase();
                            const tableBody = document.getElementById('eventListTable').getElementsByTagName('tbody')[0];
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
                    <table class="table table-striped" id="eventListTable">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Event Name</th>
                                <th>Event Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($events as $event)
                               <tr>
                                    <td>{{ $event->reference }}</td>
                                    <td>{{ $event->event_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($event->start_date)->format('M. d, Y') }}</td>
                                    <td>
                                        <button wire:click="selectEvent({{ $event->id }})" class="btn btn-sm btn-success select-event-btn" data-name="{{ $event->name }}">
                                            <span wire:loading wire:target="selectEvent({{ $event->id }})"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>&nbsp;Wait...</span>
                                            <span wire:loading.remove wire:target="selectEvent({{ $event->id }})"><i class="bi bi-check-circle"></i>&nbsp;Select</span>
                                        </button>
                                    
                                    </td>

                               </tr>
                            @empty

                            <tr>
                                <td colspan="4" class="text-center">No Data Found</td>
                            </tr>
                                
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Event List Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        window.addEventListener('hideEventListModal', event => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('banquetEventModal'));
            if (modal) {
                modal.hide();
            }
        });
        window.addEventListener('hideCustomerListModal', event => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('customerListModal'));
            if (modal) {
                modal.hide();
            }
        });

        window.addEventListener('showAlert', event => {
           const data = event.detail[0];
              Swal.fire({
                icon: data.type,
                title: data.title,
                text: data.message,
                timer: 2000,
                showConfirmButton: false,
                });
        });

        window.addEventListener('resetBankRegistrationForm', event => {
                document.getElementById('bankRegistrationForm').reset();
                var modal = bootstrap.Modal.getInstance(document.getElementById('bankRegistrationModal'));
                if (modal) {
                    modal.hide();
                }
        });

        window.addEventListener('resetCustomerRegistrationForm', event => {
            document.getElementById('customerRegistrationForm').reset();
            // hide modal after registration
            var modal = bootstrap.Modal.getInstance(document.getElementById('customerModal'));
            if (modal) {
                modal.hide();
            }
        });

        window.addEventListener('hideBankListModal', event => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('bankListModal'));
            if (modal) {
                modal.hide();
            }
        });
    </script>
</div>
