@extends('layouts.master')
@section('content')
    {{-- <div class="steps-container">
        <div class="step completed">
            <span class="circle">1</span>
            <span class="label">Order</span>
        </div>
        <div class="line completed"></div>
        <div class="step completed">
            <span class="circle">2</span>
            <span class="label">Review</span>
        </div>
        <div class="line completed"></div>
        <div class="step in-progress">
            <span class="circle">3</span>
            <span class="label">Approval</span>
        </div>
        <div class="line"></div>
        <div class="step">
            <span class="circle">4</span>
            <span class="label">To Received</span>
        </div>
        <div class="line"></div>
        <div class="step">
            <span class="circle">5</span>
            <span class="label">Delivered</span>
        </div>

    </div> --}}
    <div class="d-flex " style="position: absolute; right: 1%; left: 1%;">
        <!-- Left Dashboard -->
        <div class="dashboard col-md-6 me-3" style="width: 950px;">
            <header>
                <div>

                    <button class="btn {{ $requestInfo->requisition_status == 'PENDING' ? 'btn-success' : 'btn-secondary' }}"
                        type="button"
                        onclick="window.location.href='{{ route('po.po_receive', ['poNumber' => $requestInfo->requisition_number]) }}'"
                        {{ $requestInfo->requisition_status != 'PENDING' ? 'disabled' : '' }}>
                        Receive
                    </button>

                    <button onclick="window.location.href='{{ route('po.edit', ['id' => $requestInfo->id]) }}'"
                        class="btn {{ $requestInfo->requisition_status == 'PREPARING' ? 'btn-primary' : 'btn-secondary' }}"
                        {{ $requestInfo->requisition_status != 'PREPARING' ? 'disabled' : '' }}>
                        Update PO
                    </button>

                    <a href="{{ route('po.print', ['id' => $requestInfo->id]) }}" class="btn btn-primary">
                        Print Preview
                    </a>
                    <a href="/po_create" class="btn btn-primary">
                        + New PO
                    </a>
                    <button onclick="history.back()" class="btn btn-primary"> Back </button>
                    <span style="margin-left: 250PX"></span>
                    <span class="btn btn-outline-info">{{ $requestInfo->requisition_status }}</span>
                    <h1 style="underlined">&nbsp;

                    </h1>
                    <h1>Purchase Order Details</h1>
                </div>
            </header>
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ITEM CODE</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>REQUEST QTY.</th>
                        <th>RECEIVE QTY.</th>
                        <th>COST</th>
                        <th>TOTAL</th>
                        <th>REFERENCE COST</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requestInfo->requisitionDetails as $reqdetail)
                        <tr data-id="{{ $reqdetail->requisition_number }}">
                            <td>{{ $reqdetail->items->item_code }}</td>
                            <td>{{ $reqdetail->items->item_description }} </td>
                            <td> &nbsp;&nbsp;{{ $reqdetail->qty }}</td>
                            <td>0{{ $reqdetail->receive_qty }}</td>
                            <td>{{ $reqdetail->items->priceLevel()->latest()->where('price_type', 'cost')->first()->amount }}
                            </td>
                            <td>{{ $reqdetail->total }}</td>
                            <td>{{ $reqdetail->reference_cost }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No data available</td>

                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Right Dashboard -->
        <div class="dashboard">
            <header>
                <h1>Purchase Order Information</h1>
            </header>
            <form>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label for="supp_name" class="form-label" style="width: 100; font-size: 13px">Supplier Name</label>
                        <input type="text" class="form-control" id="name" name="company_name"
                            value="{{ $requestInfo->supplier->supp_name ?? 'N/A' }}" readonly
                            style="width: 100; font-size: 13px">
                    </div>
                    <div class="col-md-6">
                        <label for="postal_address" class="form-label" style="width: 100; font-size: 13px">PO Number</label>
                        <input type="text" class="form-control" id="postal_address" name="company_code"
                            value="{{ $requestInfo->requisition_number }}" readonly style="width: 100; font-size: 13px">
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label for="contact_no_1" class="form-label" style="width: 100; font-size: 13px">M. PO
                            NUMBER</label>
                        <input type="text" class="form-control" id="contact1" name="company_tin"
                            value="{{ $requestInfo->merchandise_po_number }}" readonly style="width: 100; font-size: 13px">
                    </div>
                    <div class="col-md-6">
                        <label for="supp_address" class="form-label" style="width: 100; font-size: 13px">Type</label>
                        <input type="text" class="form-control" value="{{ $requestInfo->requisitionTypes->type_name }}"
                            readonly style="width: 100; font-size: 13px">
                    </div>
                </div>


                <div class="col-md-10 mt-4">
                    <label for="contact_no_1" class="form-label" style="width: 100; font-size: 13px">Prepared
                        By</label>
                    <input type="text" class="form-control" id="contact1" name="company_tin"
                        value="{{ $requestInfo->preparer->name }} {{ $requestInfo->preparer->last_name }}" readonly
                        style="width: 100; font-size: 12px">
                </div>
                <div class="col-md-10">
                    <label for="contact_no_1" class="form-label" style="width: 100; font-size: 13px">Reviewed
                        By</label>
                    <input type="text" class="form-control" id="contact1" name="company_tin"
                        value="{{ $requestInfo->reviewer->name }} {{ $requestInfo->reviewer->last_name }}" readonly
                        style="width: 100; font-size: 12px">
                </div>
                <div class="col-md-10">
                    <label for="contact_no_1" class="form-label" style="width: 100; font-size: 13px">Approved
                        By</label>
                    <input type="text" class="form-control" id="contact1" name="company_tin"
                        value="{{ $requestInfo->approver->name }} {{ $requestInfo->approver->middle_name }} {{ $requestInfo->approver->last_name }}"
                        readonly style="width: 100; font-size: 12px">
                </div>


                <div class="row mb-2">
                    <div class="col-md-12">
                        <label for="contact_no_2" class="form-label" style="font-size: 13px">Remarks</label>
                        <textarea name="" id="" cols="30" rows="10" readonly class="form-control md-12 "
                            style="height: 100px; font-size: 12px"> {{ $requestInfo->remarks }} </textarea>
                    </div>



                </div>


            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.querySelectorAll('.editable').forEach(cell => {
            cell.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent newline
                    this.blur(); // Trigger the blur event
                }
            });

            cell.addEventListener('blur', function() {
                const row = this.closest('tr');
                const id = row.getAttribute('data-id');
                const column = this.getAttribute('data-column');
                const value = this.innerText;

                fetch('/update-receive-qty', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id,
                            column,
                            value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Updated successfully!');
                        } else {
                            alert('Failed to update.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });



        function updateStatus(currentStep) {
            const steps = document.querySelectorAll('.status-flow .step');
            steps.forEach((step, index) => {
                if (index < currentStep) {
                    step.classList.add('completed');
                    step.classList.remove('active');
                } else if (index === currentStep) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('completed', 'active');
                }
            });
        }
    </script>
@endsection
