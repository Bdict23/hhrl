<div>
     @if (session()->has('error'))
        <div class="alert alert-danger" id="error-message">
            {{ session('error') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Proposal Lists</h5>
        </div>
        <div class="card-tools">
           <div class="row mt-2 pl-3 pr-3">
             <div class="col-md-6">
                <input type="text" class="form-control" placeholder="Search" wire:model="searchTerm">
             </div>
           </div>
        </div>
        <div class="card-body">

            <table class="table table-bordered table-striped">
                <tr>
                    @if ($referenceNumber)
                        <th>REF. No.</th>
                        
                    @endif
                    @if ($documentNumber)
                        <th>Doc. No.</th>
                    @endif
                    @if ($dateCreated)
                        <th>Date Created</th>
                    @endif
                    @if ($eventName)
                        <th>Event</th>
                    @endif
                    @if ($status)
                        <th>Status</th>
                    @endif
                    @if ($approvedAmount)
                        <th>Approved Amount</th>
                    @endif
                    @if ($suggestedAmount)
                        <th>Suggested Amount</th>
                    @endif
                    @if ($approvedBy)
                        <th>Approved By</th>
                    @endif
                    @if ($notedBy)
                        <th>Noted By</th>
                    @endif
                    @if ($createdBy)
                        <th>Created By</th>
                    @endif
                    @if ($notes)
                        <th>Notes</th>
                    @endif
                    @if ($customerName)
                        <th>Customer Name</th>
                    @endif
                    <th>
                        Action
                           
                        <button type="button"
                            class="btn btn-sm float-end"
                            style="background: transparent; border: none; font-size: 1.25rem; padding: 0; line-height: 1;"
                            data-bs-toggle="modal"
                            data-bs-target="#customCol"
                            title="Add or remove column">
                            +
                        </button>
                    </th>
                </tr>
                <tbody>
                    @forelse ($procurementLists as $procurement)
                        <tr>
                            @if ($referenceNumber)
                                <td>{{ $procurement->reference_number }}</td>
                            @endif
                            @if ($documentNumber)
                                <td>{{ $procurement->document_number }}</td>
                            @endif
                            @if ($dateCreated)
                                <td>{{ $procurement->created_at->format('M-d-Y') }}</td>
                            @endif
                            @if ($eventName)
                                <td>{{ $procurement->event->event_name ?? '' }}</td>
                            @endif
                            @if ($status)
                                <td>
                                    @if ($procurement->status == 'PREPARING')
                                        <span class="badge bg-secondary">DRAFT</span>
                                    @elseif ($procurement->status == 'APPROVED')
                                        <span class="badge bg-success">APPROVED</span>
                                    @elseif ($procurement->status == 'REJECTED')
                                        <span class="badge bg-danger">REJECTED</span>
                                    @else       
                                        <span class="badge bg-info">FOR APPROVAL</span>
                                    @endif
                                </td>
                            @endif
                            @if ($approvedAmount)
                                <td>{{ number_format($procurement->approved_amount, 2) }}</td>
                            @endif
                            @if ($suggestedAmount)
                                <td>{{ number_format($procurement->suggested_amount, 2) }}</td>
                            @endif
                            @if ($approvedBy)
                                <td>{{ $procurement->approver ? $procurement->approver->name : 'N/A' }}</td>
                            @endif
                            @if ($notedBy)
                                <td>{{ $procurement->notedBy ? $procurement->notedBy->name : 'N/A' }}</td>
                            @endif
                            @if ($createdBy)
                                <td>{{ $procurement->createdBy ? $procurement->createdBy->name : 'N/A' }}</td>
                            @endif
                            @if ($notes)
                                <td>{{ $procurement->notes }}</td>
                            @endif
                            @if ($customerName)
                                <td>{{ $procurement->event->customer->customer_fname . ' ' . $procurement->event->customer->customer_lname }}</td>
                            @endif
                            <td>
                                <a wire:click="view({{ $procurement->id }})" class="btn btn-sm btn-link">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No procurement records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


      <div class="modal fade" id="customCol" tabindex="-1" aria-labelledby="CustomModalLabel" aria-hidden="true"  wire:ignore.self>
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="CustomModalLabel">Custom Columns</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-check">
                            <input wire:model.live="referenceNumber" class="form-check-input" type="checkbox" value="" id="checkReferenceNumber">
                            <label class="form-check-label" for="checkReferenceNumber">
                                Reference Number (REF. No.)
                            </label>
                        </div>
                        <div class="form-check">
                            <input wire:model.live="documentNumber" class="form-check-input" type="checkbox" value="" id="checkDocumentNumber">
                            <label class="form-check-label" for="checkDocumentNumber">
                                Document Number (DOC. No.)
                            </label>
                        </div>
                        <div class="form-check">
                            <input wire:model.live="dateCreated" class="form-check-input" type="checkbox" value="" id="checkDateCreated">
                            <label class="form-check-label" for="checkDateCreated">
                                Date Created (DATE CREATED)
                            </label>
                        </div>
                        <div class="form-check">
                            <input wire:model.live="eventName" class="form-check-input" type="checkbox" value="" id="checkEventName">
                            <label class="form-check-label" for="checkEventName">
                                Event Name (EVENT)
                            </label>
                        </div>
                        <div class="form-check">
                            <input wire:model.live="status" class="form-check-input" type="checkbox" value="" id="checkStatus">
                            <label class="form-check-label" for="checkStatus">
                                Status (STATUS)
                            </label>
                        </div>
                        <div class="form-check">
                            <input wire:model.live="approvedAmount" class="form-check-input" type="checkbox" value="" id="checkApprovedAmount">
                            <label class="form-check-label" for="checkApprovedAmount">
                                Approved Amount (APPROVED AMT.)
                            </label>
                        </div>
                        <div class="form-check">
                            <input wire:model.live="suggestedAmount" class="form-check-input" type="checkbox" value="" id="checkSuggestedAmount">
                            <label class="form-check-label" for="checkSuggestedAmount">
                                Suggested Amount (SUGGESTED AMT.)
                            </label>
                        </div>
                        <div class="form-check">
                            <input wire:model.live="approvedBy" class="form-check-input" type="checkbox" value="" id="checkApprovedBy">
                            <label class="form-check-label" for="checkApprovedBy">
                                Approved By (APPROVED BY)
                            </label>
                        </div>
                        <div class="form-check">
                            <input wire:model.live="notedBy" class="form-check-input" type="checkbox" value="" id="checkNotedBy">
                            <label class="form-check-label" for="checkNotedBy">
                                Book Keeper (NOTED BY)
                            </label>
                        </div>
                        <div class="form-check">
                            <input wire:model.live="createdBy" class="form-check-input" type="checkbox" value="" id="checkCreatedBy">
                            <label class="form-check-label" for="checkCreatedBy">
                                Created By (CREATED BY)
                            </label>
                        </div>
                        <div class="form-check">
                            <input wire:model.live="notes" class="form-check-input" type="checkbox" value="" id="checkNotes">
                            <label class="form-check-label" for="checkNotes">
                                Notes (NOTES)
                            </label>
                        </div>
                        <div class="form-check">
                            <input wire:model.live="customerName" class="form-check-input" type="checkbox" value="" id="checkCustomerName">
                            <label class="form-check-label" for="checkCustomerName">
                                Customer Name (CUSTOMER)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
