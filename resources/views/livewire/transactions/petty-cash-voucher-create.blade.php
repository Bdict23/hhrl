<div>
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <select name="" id="" class="form-select form-select-sm input-group-text" wire:model="saveAsStatus">
                            <option value="DRAFT"  @if($currentPCVStatus == 'DRAFT') selected @endif>DRAFT</option>
                            <option value="OPEN" @if($currentPCVStatus == 'OPEN') selected @endif>FINAL</option>
                        </select>
                        @if($isCreate)
                            <x-primary-button wire:click="savePCV" wire:loading.attr="disabled">
                               <span wire:loading.remove wire:target="savePCV">Save</span>
                               <span wire:loading wire:target="savePCV">Saving...</span>
                            </x-primary-button>
                        @elseif($currentPCVStatus == 'DRAFT' && !$isCreate)
                            <x-primary-button wire:click="updatePCV" wire:loading.attr="disabled">
                               <span wire:loading.remove wire:target="updatePCV">UPDATE</span>
                               <span wire:loading wire:target="updatePCV">Updating...</span>
                            </x-primary-button>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                <a href="/petty-cash-voucher-summary"><x-secondary-button >Summary</x-secondary-button></a> 
                </div>
            </div>
        </div>

        <div class="col-md-6 d-flex justify-content-end">
            <h5 class="alert-heading" style="white-space: nowrap;">Petty Cash Voucher - Create &nbsp;<i class="bi bi-card-text"></i></h5>
            
        </div>
    </div>
    <div class="row">
        {{-- LEFT --}}
        <div class="mb-3 col-md-6">
            <div class="card">
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong>PARTICULARS</strong>
                    </div>
                    <table class="table table-sm table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Account Title</th>
                                <th>Debit</th>
                                <th>Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($particulars ?? [] as $particular)
                                <tr>
                                    <td>{{ $particular['account_title'] }}</td>
                                    @if($particular['type'] == 'DEBIT')
                                        <td><input type="number" class="form-control form-control-sm" wire:model="particulars.{{ $loop->index }}.amount" id="particular-debit-{{ $loop->index }}"></td>
                                        <td></td>
                                    @else
                                        <td></td>
                                        <td><input type="number" class="form-control form-control-sm" wire:model="particulars.{{ $loop->index }}.amount" id="particular-credit-{{ $loop->index }}"></td>
                                    @endif
                                </tr>
                            @empty
                                <tr class="text-center">
                                    <td colspan="4">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                   @error('saveAsStatus')
                        <span class="text-danger">{{ $message }}</span>
                   @enderror
                   @error('selectedTemplate')
                        <span class="text-danger">{{ $message }}</span>
                   @enderror
                </div>
            </div>
        </div>
        {{-- RIGHT --}}
        <div class="mb-3 col-md-6 container">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="input-group mb-2">
                        <label for="" class="input-group-text">Reference</label>
                        <input type="text" class="form-control" placeholder="<AUTO>" disabled wire:model="reference">
                    </div>
                    <div class="input-group mb-2">
                        <label for="" class="input-group-text">Transaction Type</label>
                        <select name="" id="" wire:model="selectedTransactionTypeID" class="form-select">
                            <option value="">Select</option>
                            @foreach ($transactionTypes ?? [] as $type)
                                <option value="{{ $type->id }}" @if($selectedTransactionTypeID == $type->id) selected @endif>{{ $type->type_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('selectedTransactionTypeID')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                    <div class="input-group mb-2">
                        <label for="" class="input-group-text">Transaction</label>
                        <input type="text" class="form-control" placeholder="Select Transaction ->" value="{{ $selectedTemplate->template_name ?? ''}}" disabled>
                        <button class="btn btn-secondary" wire:click="showTransactions">
                            <span wire:loading wire:target="showTransactions"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i></span>
                            <span wire:loading.remove wire:target="showTransactions"><i class="bi bi-list-check"></i></span>
                        </button>
                    </div>
                    @error('transactions')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @error('selectedTemplate')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                    <div class=" mb-2">
                        <label for="" class="form-label">Transaction Description</label>
                        <textarea name="" id="" cols="30" rows="2" class="form-control" disabled value >
                            {{ $selectedTemplate->description ?? '' }}
                        </textarea>
                    </div>
                    
                    <div class="input-group mb-2">
                        <label for="" class="input-group-text">A.R Reference Number</label>
                        <input type="text" class="form-control" placeholder="Select Acknowledgement Receipt    ->" value="{{ $selectedAR->reference ?? ''}}" disabled>
                        <span class="input-group-text" style="cursor: pointer; background-color:aquamarine" data-bs-toggle="modal" data-bs-target="#acknowledgementReceiptListModal">
                            <i class="bi bi-wallet2"></i>
                        </span>
                    </div>

                    @error('arID')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                    <div class="input-group mb-4">
                        <label for="" class="input-group-text">A.R - Check Balance</label>
                        <input type="text" class="form-control" disabled value="₱ {{ number_format($totalARBalance,2) ?? '₱ 0.00'}}">
                    </div>

                    <div class="row">
                        <strong class="col-md-3 my-auto">PCV Information</strong>
                        <hr class="col-md-9">
                    </div>
                    
                    <div class="input-group mb-2">
                        <label for="" class="input-group-text">Voucher Series Number</label>
                        <input type="text" class="form-control" placeholder="Enter Voucher Series Number" wire:model="voucherSeriesNumber">
                    </div>
                    @error('voucherSeriesNumber')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                    <div class="input-group mb-2">
                        <label for="" class="input-group-text">Payee</label>
                        <input type="text" class="form-control" placeholder="Select Payee ->" disabled value="{{ $payeeName }}">
                        <span class="input-group-text" style="cursor: pointer; background-color:aquamarine" data-bs-toggle="modal" data-bs-target="#payeeListModal"><i class="bi bi-person-check-fill"></i></span>
                    </div>
                    @error('employeeId')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror 
                    @error('customerId')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label for="" class="input-group-text">Debit Total</label>
                                <input type="text" class="form-control" disabled placeholder="₱ 0.00" id="DebitTotalTF" wire:model="debitTotal">
                            </div>
                        </div>
                        <div class="col-md-6">
                                <div class="input-group"> 
                                <label for="" class="input-group-text">Credit Total</label>
                                <input type="text" class="form-control" disabled placeholder="₱ 0.00" id="CreditTotalTF" wire:model="creditTotal">
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-2">
                        <label for="" class="input-group-text">Disburse Amount</label>
                        <input type="text" class="form-control" disabled placeholder="₱ 0.00" id="DisburseAmountTF" wire:model="totalDisburseAmount">
                        <input type="hidden" id="totalDisburseAmountHidden" wire:model.live="totalDisburseAmount">
                    </div>
                    @error('totalDisburseAmount')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    
                    <div class="mb-2">
                        <textarea name="" id="" cols="30" rows="3" class="form-control" placeholder="Remarks" wire:model="note"></textarea>
                    </div>
                    @error('note')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                </div>
            </div>
        </div>
    </div>


    <!-- Acknowledgement Receipt List Modal -->
    <div class="modal fade" id="acknowledgementReceiptListModal" tabindex="-1" aria-labelledby="acknowledgementReceiptListModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header row">
                        <div class="col-md-6">
                            <h5 class="modal-title" id="acknowledgementReceiptListModalLabel">Select Acknowledgement Receipt</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-2">
                                <span class="input-group-text">Search</span>
                                <input type="text" class="form-control" id="search-ar"
                                    onkeyup="filterCustomers()">
                            </div>
                        </div>
                  
                    <script>
                        function filterCustomers() {
                            const searchInput = document.getElementById('search-ar');
                            const filter = searchInput.value.toLowerCase();
                            const tableBody = document.getElementById('ARListTable').getElementsByTagName('tbody')[0];
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
                    <table class="table table-striped" id="ARListTable">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Check Number</th>
                                <th>Event</th>
                                <th>Note</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($acknowledgementReceipts ?? [] as $ar)
                               <tr>
                                    <td>{{ $ar->reference }}</td>
                                    <td>{{ $ar->check_number }}</td>
                                    <td>{{ $ar->event->event_name ?? '' }}</td>
                                    <td>{{ $ar->notes }}</td>
                                    <td>{{ $ar->check_amount }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary" wire:click="selectAcknowledgementReceipt({{ $ar->id }})">
                                            <span wire:loading wire:target="selectAcknowledgementReceipt({{ $ar->id }})"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait...</span>
                                            <span wire:loading.remove wire:target="selectAcknowledgementReceipt({{ $ar->id }})">Select</span>
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

    {{-- PAYEE/EMPLOYEE LIST MODAL --}}
    <div class="modal fade" id="payeeListModal" tabindex="-1" aria-labelledby="payeeListModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header row">
                        <div class="col-md-6">
                            <h5 class="modal-title" id="payeeListModalLabel">Select Payee</h5>
                        </div>
                </div>
                <div class="modal-body overflow-auto" style="max-height: 400px;">
                     <div class="container">
                            <ul class="nav nav-tabs" id="jobOrderTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="employee-tab" data-bs-toggle="tab" data-bs-target="#employee" type="button"
                                        role="tab" aria-controls="employee" aria-selected="true">EMPLOYEES</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" type="button"
                                        role="tab" aria-controls="customer" aria-selected="false">
                                        CUSTOMERS</button>
                                </li>
                            </ul>

                            <div class="tab-content alert-success">
                                <div class="tab-pane fade show active" id="employee" role="tabpanel" aria-labelledby="employee-tab">
                                    <div class="mt-2">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">Search</span>
                                            <input type="text" class="form-control" id="search-employee"
                                                onkeyup="filterBanks()" placeholder="Search Employee...">
                                        </div>
                                    </div>
                        
                                    <script>
                                        function filterBanks() {
                                            const searchInput = document.getElementById('search-employee');
                                            const filter = searchInput.value.toLowerCase();
                                            const tableBody = document.getElementById('employeeListTable').getElementsByTagName('tbody')[0];
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
                                    <table class="table table-striped table-sm" id="employeeListTable">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>Name</th>
                                                <th>Position</th>
                                                <th>Department</th>
                                                <th>Branch</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($employees ?? [] as $employee)
                                            <tr>
                                                    <td>{{ $employee->name }} {{$employee->last_name}}</td>
                                                    <td>{{ $employee->position->position_name ?? 'N/A' }}</td>
                                                    <td>{{ $employee->department->department_name ?? 'N/A' }}</td>
                                                    <td>{{ $employee->branch->branch_name ?? 'N/A' }}</td>
                                                    <td>
                                                        <button wire:click="selectEmployee({{ $employee->id }})" class="btn btn-sm btn-success select-employee-btn" data-name="{{ $employee->name }}" class="btn btn-sm">
                                                            <span wire:loading wire:target="selectEmployee({{ $employee->id }})"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>&nbsp;Wait...</span>
                                                            <span wire:loading.remove wire:target="selectEmployee({{ $employee->id }})">Select</span>
                                                        </button>
                                                    
                                                    </td>

                                            </tr>
                                            @empty

                                            <tr>
                                                <td colspan="5" class="text-center">No Data Found</td>
                                            </tr>
                                                
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="customer" role="tabpanel" aria-labelledby="customer-tab">
                                    <div class="mt-2">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">Search</span>
                                            <input type="text" class="form-control" id="search-customer"
                                                onkeyup="filterCustomers()" placeholder="Search Customer...">
                                        </div>
                                    </div>
                        
                                    <script>
                                        function filterCustomers() {
                                            const searchInput = document.getElementById('search-customer');
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

                                       <table class="table table-sm table-striped mt-2" id="customerListTable">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>Name</th>
                                                <th>Gender</th>
                                                <th>Contact Number</th>
                                                <th>Email</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($customers ?? [] as $customer)
                                            <tr>
                                                    <td>{{ $customer->customer_fname }} {{ $customer->customer_mname }} {{ $customer->customer_lname }} {{ $customer->suffix }}</td>
                                                    <td>{{ $customer->gender }}</td>
                                                    <td>{{ $customer->contact_no_1 }}</td>
                                                    <td>{{ $customer->email }}</td>
                                                    <td>
                                                        <button wire:click="selectCustomer({{ $customer->id }})" class="btn btn-sm btn-success select-customer-btn" data-name="{{ $customer->name }}" class="btn btn-sm">
                                                            <span wire:loading wire:target="selectCustomer({{ $customer->id }})"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>&nbsp;Wait...</span>
                                                            <span wire:loading.remove wire:target="selectCustomer({{ $customer->id }})">Select</span>
                                                        </button>
                                                    </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No Data Found</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table> 
                                </div>
                            </div>
                    </div>
                </div>
                 <!-- Customer List Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
    </div>

    {{-- TRANSACTION LISTS MODAL --}}
      <div class="modal fade" id="transactionListModal" tabindex="-1" aria-labelledby="transactionListModalLabel" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header row">
                        <div class="col-md-12">
                            <div class="input-group mb-2">
                                <span class="input-group-text">Search</span>
                                <input type="text" class="form-control" id="search-transaction"
                                    onkeyup="filterTransactions()">
                            </div>
                        </div>
                  
                    <script>
                        function filterTransactions() {
                            const searchInput = document.getElementById('search-transaction');
                            const filter = searchInput.value.toLowerCase();
                            const tableBody = document.getElementById('transactionListTable').getElementsByTagName('tbody')[0];
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
                    <table class="table table-striped" id="transactionListTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions ?? [] as $transaction)
                               <tr>
                                    <td>{{ $transaction->template_name }}</td>
                                    <td>
                                        <button wire:click="selectTransaction({{ $transaction->id }})" class="btn btn-sm btn-success select-transaction-btn" data-name="{{ $transaction->name }}">
                                            <span wire:loading wire:target="selectTransaction({{ $transaction->id }})"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>&nbsp;Wait...</span>
                                            <span wire:loading.remove wire:target="selectTransaction({{ $transaction->id }})"><i class="bi bi-check-circle"></i>&nbsp;Select</span>
                                        </button>
                                    </td>
                               </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center">No Data Found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Bank List Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
       
        //hides Transaction List Modal after selecting a Transaction
        window.addEventListener('hideTransactionListModal', event => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('transactionListModal'));
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
                timer: 3000,
                showConfirmButton: true,
                });
                // redirect to summary page after saving
                if(data.type === 'success' && data.title === 'Success' && data.message === 'PCV saved successfully.'){
                    setTimeout(() => {
                        window.location.href = '/petty-cash-voucher-summary';
                    }, 3000); // Redirect after 3 seconds (same as the timer duration of the alert)
                }
        });
        //hides Payee List Modal after selecting a Payee/Employee
        window.addEventListener('hidePayeeListModal', event => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('payeeListModal'));
            if (modal) {
                modal.hide();
            }
        });

        window.addEventListener('showTransactionLists', event =>{
            var modalElement = document.getElementById('transactionListModal');
            var modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.show();
        });

        window.addEventListener('closeTransactionLists', event =>{
            var modal = bootstrap.Modal.getInstance(document.getElementById('transactionListModal'));
            if (modal) {
                modal.hide();
            }
        });

        window.addEventListener('closeAcknowledgementReceiptLists', event =>{
            var modal = bootstrap.Modal.getInstance(document.getElementById('acknowledgementReceiptListModal'));
            if (modal) {
                modal.hide();
            }
        });

        window.addEventListener('closePayeeLists', event =>{
            var modal = bootstrap.Modal.getInstance(document.getElementById('payeeListModal'));
            if (modal) {
                modal.hide();
            }
        });

        function updateTransactionTotals() {
            const debitInputs = document.querySelectorAll('input[id^="particular-debit-"]');
            const creditInputs = document.querySelectorAll('input[id^="particular-credit-"]');

            let debitTotal = 0;
            let creditTotal = 0;

            debitInputs.forEach((input) => {
                const value = parseFloat(input.value);
                debitTotal += Number.isNaN(value) ? 0 : value;
            });

            creditInputs.forEach((input) => {
                const value = parseFloat(input.value);
                creditTotal += Number.isNaN(value) ? 0 : value;
            });

            const disburseAmount = creditTotal;

            const debitTotalTF = document.getElementById('DebitTotalTF');
            const creditTotalTF = document.getElementById('CreditTotalTF');
            const disburseAmountTF = document.getElementById('DisburseAmountTF');
            const hiddenDisburseAmount = document.getElementById('totalDisburseAmountHidden');

            if (debitTotalTF) {
                debitTotalTF.value = debitTotal.toFixed(2);
            }

            if (creditTotalTF) {
                creditTotalTF.value = creditTotal.toFixed(2);
            }

            if (disburseAmountTF) {
                disburseAmountTF.value = disburseAmount.toFixed(2);
            }

            if (hiddenDisburseAmount) {
                hiddenDisburseAmount.value = disburseAmount.toFixed(2);
                hiddenDisburseAmount.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
       
        document.addEventListener('input', function (event) {
            const target = event.target;
            if (!target || !target.id) {
                return;
            }

            if (target.id.startsWith('particular-debit-') || target.id.startsWith('particular-credit-')) {
                updateTransactionTotals();
            }
        });

        window.addEventListener('closeTransactionLists', () => {
            setTimeout(updateTransactionTotals, 0);
        });

        setTimeout(updateTransactionTotals, 0);
    </script>
</div>
