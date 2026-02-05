<div>
    <div class="card" {{ $showWithdrawalSummary ? 'style=display:block' : 'style=display:none' }}>
        <header class="card-header">
            <h2>For Approval Withdrawals</h2>
        </header>
        <div class="card-body overflow-x-auto">
            <table class="table table-striped table-hover table-responsive">
                <thead class="thead-light">
                    <tr>
                        <th>Reference</th>
                        <th>Department</th>
                        <th>Prepared By</th>
                        <th>Use Date</th>
                        <th>Prepared Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($withdrawals as $withdrawals)
                        <tr>

                            <td>{{ $withdrawals->reference_number }}</td>
                            <td>{{ $withdrawals->department->department_name }}</td>
                            <td>{{ $withdrawals->preparedBy->name }} {{ $withdrawals->preparedBy->last_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($withdrawals->usage_date)->format('d/m/Y') }}</td>
                            <td>{{ $withdrawals->created_at->format('d/m/Y') }}</td>

                            <td>
                                <a href="#" class="btn btn-sm btn-primary btn-sm"
                                    wire:click="viewWithdrawalDetails({{ $withdrawals->id }})">View</a>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No withdrawals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div {{ $showViewWithdrawal ? 'style=display:block' : 'style=display:none' }}>
        <div class="row">
            <div class="col-md-7 mb-4">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" wire:model="search"
                                    placeholder="Search for items..." />
                            </div>

                        </div>
                    </div>
                    <div class="card-body overflow-x-auto">
                        <table class="table table-striped table-hover table-responsive">
                            <thead class="thead-dark me-3">
                                <tr>
                                    <th>Item Code</th>
                                    <th>Item Description</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Cost</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($withdrawalDetails as $cardex)
                                    <tr>
                                        <td>{{ $cardex->item->item_code }}</td>
                                        <td>{{ $cardex->item->item_description }}</td>
                                        <td>{{ $cardex->item->category->category_name }}</td>
                                        <td>{{ $cardex->qty_out }}</td>
                                        <td> ₱ {{ $cardex->priceLevel->amount }}</td>
                                        <td> ₱ {{ $cardex->qty_out * $cardex->priceLevel->amount }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        Total: <span>{{ $overAllCost }}<span>
                    </div>

                </div>
            </div>
            <div class="col-md-5">
                <div class="card">


                    <div class="card-body">
                        <h5 class="card-title">Information</h5>
                        <div class="alert" style="background-color: #f2f4f7;" role="alert">

                            <div class="row">                
                                <div class="col-md-7">
                                    <label for="reference_number" class="form-label">Ref. Number</label>
                                    <input type="text" class="form-control" id="reference_number" readonly
                                        value="{{ $reference }}">
                                </div>
                                <div class="col-md-5">
                                    <label for="withdrawal_type" class="form-label">Withdrawal Type</label>
                                    <input type="text" class="form-control" id="withdrawal_type" readonly
                                        value="{{ $withdrawalInfo->withdrawalType->setting_value ?? 'N/A' }}">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="deptartment" class="form-label"
                                        style="width: 100; font-size: 13px">Department</label>
                                    <input type="text" class="form-control" id="department" name="department"
                                        readonly value="{{ $department }}">
                                </div>
                                <div class="col-md-6">

                                    <label for="prepared_date" class="form-label "
                                        style="width: 100; font-size: 13px">Prepared
                                        Date</label>
                                    <input type="text" class="form-control" id="prepared_date" name="prepared_date"
                                        value="{{ $preparedDate }}" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="usage_date" class="form-label"
                                            style="width: 100; font-size: 13px">To be
                                            use on</label>
                                        <input type="text" class="form-control" id="usage_date"
                                            value="{{ $useDate }}" readonly>
                                    </div>
                                    <div class="col-md-6">

                                        <label for="usage_date" class="form-label"
                                            style="width: 100; font-size: 13px">Validity</label>
                                        <input type="text" class="form-control" id="usage_date" name="usage_date"
                                            value="{{ $validityDate }}" readonly>

                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <label for="remarks" class="form-label" style="font-size: 13px;">Remarks</label>
                                    <textarea type="text" class="form-control" id="remarks" name="remarks" style="font-size: 13px; height: 100px"
                                        readonly> {{ $withdrawalRemarks }}</textarea>
                                </div>

                                <div class="row mt-1">
                                    <div class="col-md-6">
                                        <label for="reviewed_to" class="form-label" style="font-size: 13px;">Prepared By
                                        </label>
                                        <input type="text" class="form-control" id="prepared_by" name="prepared_by"
                                            value="{{ $preparedBy }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="approved_to" class="form-label" style="font-size: 13px;">Approved
                                            To</label>
                                        <input type="text" class="form-control" id="approved_to"
                                            name="approved_to" value="{{ $approvedBy }}">
                                    </div>
                                </div>
                                <div>
                                    <x-primary-button class="mt-3"
                                        wire:click="approveWithdrawal(this.event.target.value)"
                                        value="{{ $withdrawalId }}">Approve</x-primary-button>
                                    <x-danger-button wire:click="rejectWithdrawal(this.event.target.value)"
                                        value="{{ $withdrawalId }}">Reject</x-danger-button>
                                    <x-secondary-button wire:click="fetchData">Back</x-secondary-button>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
