<div class="container-fluid" style="width: 100%; height: 100%;">
    <div>
        @if (session()->has('success'))
        <div class="alert alert-success" id="success-message-reviewedPO">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    </div>
    <div class="row">
        <!-- Left Dashboard -->
        <div class="col-md-7 ">
            <div class="card ">
            
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Purchase Order Information</h5>
                            </div>
                            <div class="col-md-6 text-end">
                                <span class="btn btn-outline-info">{{ $requestInfo->requisition_status  }}</span>
                            </div>
                        </div>
                    </div>
                        <div class="m-2">
                           <x-primary-button data-bs-toggle="modal" data-bs-target="#reviewModalConfirm">
                                APPROVE
                            </x-primary-button>
                            <x-danger-button data-bs-toggle="modal" data-bs-target="#reviewModal"> REJECT</x-danger-button>
                            <x-secondary-button ><a href="/review_request_list" class="no-underline">Summary</a> </x-secondary-button>
                        </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover table-sm table-responsive">
                            <thead class="table-dark">
                                <tr style="font-size: x-small">
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Rec. Qty.</th>
                                    <th>Cost</th>
                                    <th>Sub-Total</th>
                                    <th>Cost. Ref.</th>
                                </tr>
                            </thead>
                            <tbody style="font-optical-sizing: auto">
                                @forelse ($requestInfo->requisitionDetails as $reqdetail)
                                    <tr data-id="{{ $reqdetail->requisition_number }}">
                                        <td style="font-size: small">{{ $reqdetail->items->item_code }}</td>
                                        <td style="font-size: small">{{ $reqdetail->items->item_description }} </td>
                                        <td style="font-size: small"> {{ $reqdetail->qty }}</td>
                                        <td style="font-size: small">{{ $reqdetail->items->costPrice->amount }}</td>
                                        <td style="font-size: small">{{ ($reqdetail->qty ?? 0) * ($reqdetail->items->costPrice->amount ?? 0) }}</td>
                                        <td style="font-size: small">{{ $reqdetail->items->costPrice->supplier->supplier_code ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No data available</td>
            
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div wire:loading>
                            Updating Please Wait...
                        </div>
                        <strong style="float: right">Total QTY: {{ $requestInfo->requisitionDetails->sum('qty') }}</strong>
                    </div>
            
            </div>
        </div>
        <!-- Right Dashboard -->
        <div class="col-md-5">
            <div class="card">
                <header class="card-header">
                    <h1>Purchase Order Information</h1>
                </header>
                <div class="card-body">
                    <form>
                        <div class="row">
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
                                <label for="supp_address" class="form-label" style="width: 100; font-size: 13px">Term</label>
                                <input type="text" class="form-control" value="{{ $requestInfo->term->term_name }}"
                                    readonly style="width: 100; font-size: 13px">
                            </div>
                        </div>
            
            
                        <div class="col-md-10">
                            <label for="contact_no_1" class="form-label" style="width: 100; font-size: 13px">Prepared
                                By</label>
                            <input type="text" class="form-control" id="contact1" name="company_tin"
                                value="{{ $requestInfo->preparer->name }} {{ $requestInfo->preparer->last_name }}" readonly
                                style="width: 100; font-size: 12px">
                        </div>
                        @if ($requestInfo->reviewer)
                            <div class="col-md-10">
                                <label for="contact_no_1" class="form-label" style="width: 100; font-size: 13px">Reviewed
                                    By</label>
                                <input type="text" class="form-control" id="contact1" name="company_tin"
                                    value="{{ $requestInfo->reviewer->name }} {{ $requestInfo->reviewer->last_name }}" readonly
                                    style="width: 100; font-size: 12px">
                            </div>
                        @endif
                       
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
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label for="" class="form-label" style="width: 100; font-size: 13px">Total Request Amount</label>
                                <input type="text" class="form-control fw-bold" style="width: 100; font-size: 13px"
                                    value="₱{{ number_format($requestInfo->requisitionDetails->sum(function($detail) { return $detail->qty * ($detail->items->costPrice->amount ?? 0); }), 2) }}"
                                    readonly>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- modal --}}
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    This request will be no longer valid and cannot be reverted, Procceed Action?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button wire:click="rejectPO({{$requestInfo->id}})" type="button" class="btn btn-primary" id="confirmRevise"
                        >Yes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reviewModalConfirm" tabindex="-1" aria-labelledby="reviewModalLabel2" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel2">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to approve this request?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button wire:click="approvePO({{$requestInfo->id}})" type="button" class="btn btn-primary" id="confirmReview"
                        >Yes</button>
                </div>
            </div>
        </div>
    </div>


    <script>
         // Listen for the DOMContentLoaded event
         document.addEventListener('DOMContentLoaded', function() {
            // Listen wire:success event
            window.addEventListener('refresh', event => {


        // Hide the success message after 1 second
                setTimeout(function() {
        document.getElementById('success-message-reviewedPO').style.display = 'none';
                            }, 1500);
                            var modal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
            modal.hide();
        });
        });
    </script>
</div>
