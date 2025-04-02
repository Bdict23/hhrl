<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}

    <x-primary-button style="text-decoration: none;">
        <a href="{{ route('po.create') }}" style="text-decoration: none; color: inherit;">+ Create Purchase Order</a>
    </x-primary-button>
    <div class="card mt-3 mb-3">
        <header class="card-header">

            <div class="col-md-7">
                <h2>Purchase Order Summary</h2>
            </div>
            <div class="col-md-6">
                <div class="row ">
                    <div class="col-md-4 d-flex align-items-center">
                        <label for="from_date" class="me-2">From:</label>
                        <input type="date" id="from_date" name="from_date" value="{{ date('Y-m-d') }}"
                            class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <label for="to_date" class="me-2">To:</label>
                        <input type="date" id="to_date" name="to_date" value="{{ date('Y-m-d') }}"
                            class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-warning btn-sm w-9 h-8 "><svg xmlns="http://www.w3.org/2000/svg"
                                width="16" height="16" fill="currentColor" class="bi bi-lightning-charge-fill"
                                viewBox="0 0 16 16">
                                <path
                                    d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z" />
                            </svg></button>
                    </div>
                </div>
            </div>



    </div>

    </header>
    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th>Order To</th>
                <th>Order Number</th>
                <th>Order Date</th>
                <th>Prepared By</th>
                <th>PO Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchaseOrderSummary as $requisition)
                <tr>
                    <td>{{ $requisition->supplier->supp_name ?? 'N/A' }}</td>
                    <td>{{ $requisition->requisition_number }}</td>
                    <td>{{ $requisition->trans_date }}</td>
                    <td>{{ $requisition->preparer->name }}</td>
                    <td>{{ $requisition->requisition_status }}</td>
                    <td>{{ $requisition->remarks }}</td>
                    <input id="company_id" name='company_id' type="hidden">
                    <td>
                        <div class="button-group">
                            <a onclick='' class="action-btn"
                                href="{{ route('po.show', ['id' => $requisition->id]) }}"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-eye " viewBox="0 0 16 16">
                                    <path
                                        d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                    <path
                                        d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                </svg></a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    </div>


</div>
