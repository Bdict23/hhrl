@extends('layouts.master')
@section('content')
    {{-- <div class="d-flex " style="position: absolute; right: 1%; left: 1%;">
        <!-- Left Dashboard -->
        <div class="dashboard col-md-6 me-3" style="width: 950px;">
            <header>
                <div>
                    <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#approveModal">
                        Approve
                    </button>
                    <button class="btn btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        Reject
                    </button>
                    &nbsp;&nbsp;&nbsp;


                    <button onclick="history.back()" class="btn btn-primary"> Back </button>
                    <span style="margin-left: 380PX"></span>
                    <span class="btn btn-outline-info">{{ $requestInfo->requisition_status ?? 'INVALID' }}</span>

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
                    @if (is_null($requestInfo))
                        <tr>
                            <td colspan="7" class="text-center">No data available</td>
                        </tr>
                    @else
                        @forelse ($requestInfo->requisitionDetails as $reqdetail)
                            <tr data-id="{{ $reqdetail->requisition_number }}">
                                <td>{{ $reqdetail->items->item_code }}</td>
                                <td>{{ $reqdetail->items->item_description }}</td>
                                <td>&nbsp;&nbsp;{{ $reqdetail->qty }}</td>
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
                    @endif

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
                            value="{{ $requestInfo->supplier->supp_name }}" readonly style="width: 100; font-size: 13px">
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
                        <label for="supp_address" class="form-label" style="width: 100; font-size: 13px">Term</label>
                        <input type="text" class="form-control" value="{{ $requestInfo->term->term_name }}"
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

    <!-- Reject Confirmation Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to reject this request?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="confirmReview"
                        onclick="updateStatusToReject()">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Confirmation Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to approve this request?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="confirmReview"
                        onclick="updateStatusApproved()">Yes</button>
                </div>
            </div>
        </div>
    </div> --}}
    <div>
        @livewire('purchasing.purchase-order-approval-show', ['id' => $requestInfo->id])
    </div>
@endsection

@section('script')
    <script>
        function updateStatusApproved() {
            fetch('{{ url('/update-requisition-status/' . $requestInfo->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status: 'TO RECEIVE'
                    })
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = '/approval_request_list';
                    } else {
                        alert('Failed to update requisition status.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
        }


        function updateStatusToReject() {
            fetch('{{ url('/update-requisition-status/' . $requestInfo->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status: 'REJECTED'
                    })
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = '/approval_request_list';
                    } else {
                        alert('Failed to update requisition status.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
        }
    </script>
@endsection
