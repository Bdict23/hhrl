<div>
    <div class="card" {{ $showWithdrawalSummary ? 'style=display:block' : 'style=display:none' }}>
        <header class="card-header">
            <h2>For review withdrawals</h2>
        </header>
        <div class="card-body">
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
                    @forelse ($withdrawals as $withdrawal)
                        <tr>

                            <td>{{ $withdrawal->reference_number }}</td>
                            <td>{{ $withdrawal->department->department_name }}</td>
                            <td>{{ $withdrawal->preparedBy->name }} {{ $withdrawal->preparedBy->last_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($withdrawal->usage_date)->format('d/m/Y') }}</td>
                            <td>{{ $withdrawal->created_at->format('d/m/Y') }}</td>

                            <td>
                                <a href="#" class="btn btn-sm btn-primary btn-sm"
                                    wire:click="viewWithdrawal({{ $withdrawal->id }})">View</a>

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
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control" wire:model="search"
                                    placeholder="Search for items..." />
                            </div>
                            <div class="col-md-6">
                                <x-secondary-button class="btn btn-sm float-end" wire:click="fetchData">
                                    Back
                                </x-secondary-button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
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
                                        <td>{{ $cardex->priceLevel->amount }}</td>
                                        <td>{{ $cardex->qty_out * $cardex->priceLevel->amount }}</td>
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
                                <div class="col-md-4">
                                    <label for="reference_number" class="form-label">Ref. Number</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" id="reference_number"
                                        name="reference_number" readonly
                                        value="{{ $withdrawal && $withdrawal->reference_number ? $withdrawal->reference_number : 'N/A' }}">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="deptartment" class="form-label"
                                        style="width: 100; font-size: 13px">Department</label>
                                    <input type="text" class="form-control" id="department" name="department"
                                        readonly
                                        value="{{ $withdrawal && $withdrawal->department ? $withdrawal->department->department_name : 'N/A' }}">
                                </div>
                                <div class="col-md-6">

                                    <label for="prepared_date" class="form-label "
                                        style="width: 100; font-size: 13px">Prepared
                                        Date</label>
                                    <input type="date" class="form-control" id="prepared_date" name="prepared_date"
                                        value="{{ $withdrawal ? $withdrawal->created_at->format('Y-m-d') : 'N/A' }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="usage_date" class="form-label"
                                            style="width: 100; font-size: 13px">To be
                                            use on</label>
                                        <input type="text" class="form-control" id="usage_date"
                                            value="{{ $withdrawal && $withdrawal->usage_date ? \Carbon\Carbon::parse($withdrawal->usage_date)->format('M. d, Y') : 'N/A' }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-6">

                                        <label for="usage_date" class="form-label"
                                            style="width: 100; font-size: 13px">Validity</label>
                                        <input type="date" class="form-control" id="usage_date" name="usage_date"
                                            value="{{ $withdrawal && $withdrawal->useful_date ? $withdrawal->useful_date : 'N/A' }}"
                                            readonly>

                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <label for="remarks" class="form-label" style="font-size: 13px;">Remarks</label>
                                    <textarea type="text" class="form-control" id="remarks" name="remarks" style="font-size: 13px; height: 100px"
                                        readonly> {{ $withdrawal && $withdrawal->remarks ? $withdrawal->remarks : 'N/A' }}</textarea>
                                </div>

                                <div class="row mt-1">
                                    <div class="col-md-6">
                                        <label for="reviewed_to" class="form-label" style="font-size: 13px;">Prepared By
                                        </label>
                                        <input type="text" class="form-control" id="prepared_by" name="prepared_by"
                                            value="{{ $withdrawal && $withdrawal->preparedBy ? $withdrawal->preparedBy->name . ' ' . $withdrawal->preparedBy->last_name : 'N/A' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="approved_to" class="form-label" style="font-size: 13px;">Approved
                                            To</label>
                                        <input type="text" class="form-control" id="approved_to"
                                            name="approved_to"
                                            value="{{ $withdrawal && $withdrawal->approvedBy ? $withdrawal->approvedBy->name . ' ' . $withdrawal->approvedBy->last_name : 'N/A' }}">
                                    </div>

                                </div>
                                <div>
                                    @if ($withdrawal && $withdrawal->id)
                                        <x-primary-button class=" mt-3"
                                            wire:click="approveWithdrawal({{ $withdrawal->id }})">Reviewed</x-primary-button>
                                        <x-danger-button
                                            wire:click="rejectWithdrawal({{ $withdrawal->id }})">Reject</x-danger-button>
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
