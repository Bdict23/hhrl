<div>
    <div class="container mt-4" style="display:block">
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
                    role="tab" aria-controls="pcv" aria-selected="false">Orders Summary</button>
            </li>
        </ul>
        <div class="tab-content" id="jobOrderTabContent">
            <div class="tab-pane fade show active" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
                <!-- Purchase Order Content -->

                <div class="dashboard">
                    <header>
                        <h2>Purchase Order Review Request</h2>

                    </header>
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Order To</th>
                                <th>Order Number</th>
                                <th>Prepared By</th>
                                <th>Prepare Date</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($review_requests as $requestInfo)
                                <tr>

                                    <td>{{ $requestInfo->supplier->supp_name }}</td>
                                    <td>{{ $requestInfo->requisition_number }}</td>
                                    <td>{{ $requestInfo->preparer->name }}</td>
                                    <td>{{ $requestInfo->trans_date }}</td>
                                    <td>{{ $requestInfo->remarks }}</td>

                                    <td>
                                        <a class="btn btn-primary btn-sm" onclick="addToTable({{ $requestInfo }})"
                                            href="{{ route('po.show_request_review', ['id' => $requestInfo->id]) }}">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


            </div>
            <div class="tab-pane fade" id="job-order" role="tabpanel" aria-labelledby="job-order-tab">
                <!-- Stock Transfer Form -->
                <h3 style="text-align: center;" class="mt-4">Not available</h3>

            </div>
            <div class="tab-pane fade" id="pcv" role="tabpanel" aria-labelledby="pcv-tab">
                <!--  Content -->
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
                            @foreach ($all_review_requests as $requestInfo)
                            <tr>
                                <td>{{ $requestInfo->supplier->supp_name }}</td>
                                <td>{{ $requestInfo->requisition_number }}</td>
                                <td>{{ $requestInfo->trans_date }}</td>
                                <td>{{ $requestInfo->remarks }}</td>
                                <td>{{ $requestInfo->requisition_status }}</td>
                                <td>{{ $requestInfo->remarks }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="addToTable({{ $requestInfo }})">
                                        View
                                    </button>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
