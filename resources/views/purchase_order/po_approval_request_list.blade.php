@extends('layouts.master')
@section('content')
    <div class="container mt-4">
        <ul class="nav nav-tabs" id="jobOrderTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="invoice-tab" data-bs-toggle="tab" data-bs-target="#invoice" type="button"
                    role="tab" aria-controls="invoice" aria-selected="true">Purchase Orders</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="job-order-tab" data-bs-toggle="tab" data-bs-target="#job-order" type="button"
                    role="tab" aria-controls="job-order" aria-selected="false">Stock
                    Transfer</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pcv-tab" data-bs-toggle="tab" data-bs-target="#pcv" type="button"
                    role="tab" aria-controls="pcv" aria-selected="false">Summary</button>
            </li>
        </ul>
        <div class="tab-content" id="jobOrderTabContent">
            <div class="tab-pane fade show active" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
                <!-- Purchase Order Content -->

                <div class="dashboard">
                    <header>
                        <h2>Purchase Order Approval Request</h2>

                    </header>
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Order To</th>
                                <th>Order Number</th>
                                <th>Prepared By</th>
                                <th>Reviewed By</th>
                                <th>Prepared Date</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($approval_requests as $requestInfo)
                                <tr>

                                    <td>{{ $requestInfo->supplier->supp_name }}</td>
                                    <td>{{ $requestInfo->requisition_number }}</td>
                                    <td>{{ $requestInfo->preparer->name }}</td>
                                    <td>{{ $requestInfo->reviewer->name }}</td>
                                    <td>{{ $requestInfo->trans_date }}</td>
                                    <td>{{ $requestInfo->remarks }}</td>

                                    <td>
                                        <a class="btn btn-primary btn-sm" onclick="addToTable({{ $requestInfo }})"
                                            href="{{ route('po.show_request_approval', ['id' => $requestInfo->id]) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-eye " viewBox="0 0 16 16">
                                                <path
                                                    d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                                <path
                                                    d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


            </div>
            <div class="tab-pane fade" id="job-order" role="tabpanel" aria-labelledby="job-order-tab">
                <!-- Stock Transfer  -->

                <h3 style="text-align: center;" class="mt-4">Not available</h3>

            </div>
            <div class="tab-pane fade" id="pcv" role="tabpanel" aria-labelledby="pcv-tab">
                <!-- Approval Summary Content -->
                <div class="dashboard">
                    <header>
                        <h2>Summary</h2>

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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rejected_requests as $requestInfo2)
                                <tr>
                                    <td>{{ $requestInfo2->supplier->supp_name }}</td>
                                    <td>{{ $requestInfo2->requisition_number }}</td>
                                    <td>{{ $requestInfo2->trans_date }}</td>
                                    <td>{{ $requestInfo2->preparer->name }}</td>
                                    <td>{{ $requestInfo2->requisition_status }}</td>
                                    <td>{{ $requestInfo2->remarks }}</td>
                                    <td>
                                        <a class="btn btn-primary btn-sm" onclick="addToTable({{ $requestInfo2 }})"
                                            href="{{ route('po.show_request_approval', ['id' => $requestInfo2->id]) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-eye " viewBox="0 0 16 16">
                                                <path
                                                    d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                                <path
                                                    d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@endsection
