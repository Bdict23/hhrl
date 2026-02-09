@extends('layouts.master')
@section('content')
    <div>

        @csrf
        <input type="hidden" name="menu_id" value="{{ $menus->id }}">
        <div class="row me-3 w-100">
            <div class=" col-md-8 card">
                <div class=" card-body">
                    <header>
                        <h2>FOR APPROVAL - RECIPE</h2>
                        <div class="me-3">
                            <button class="btn btn-success" type="button" data-bs-toggle="modal"
                                data-bs-target="#approveModal">
                                Approve
                            </button>
                            <button class="btn btn-danger" type="button" data-bs-toggle="modal"
                                data-bs-target="#rejectModal">
                                Reject
                            </button>
                            <button onclick="history.back()" class="btn btn-primary" type="button"> Back </button>
                        </div>
                    </header>
                    <div class="row me-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Search">
                        </div>
                    </div>
                    <div style="max-height: 400px; overflow-y: auto;" class="mt-4">
                        <table class="table table-striped table-hover me-3">
                            <thead class="thead-dark me-3">
                                <TR style="font-size: smaller;">
                                    <th>CODE</th>
                                    <th>DESCRIPTION</th>
                                    <th>QTY</th>
                                    <th>MEASURE</th>
                                    <th>COST</th>
                                    <th>TOTAL COST</th>
                                </tr>
                            </thead>
                            <tbody id="itemTableBody">

                                @foreach ($menus->recipes as $ingredient)
                                    <tr>
                                        <td style="font-size: 13PX;">{{ $ingredient->item->item_code }}</td>
                                        <td style="font-size: 13PX;">{{ $ingredient->item->item_description }}</td>
                                        <td style="font-size: 13PX; text-align: center">
                                            @php
                                                $factor = $ingredient->item->units->fromUnits
                                                    ->where('to_uom_id', $ingredient->uom_id)
                                                    ->first();
                                                $price = $ingredient->price_level->amount;
                                                $qty = $ingredient->qty;
                                                if ($factor && $factor->conversion_factor != 0) {
                                                    $cost = number_format($price / $factor->conversion_factor, 3);
                                                    $total_cost = number_format(
                                                        ($price / $factor->conversion_factor) * $qty,
                                                        3,
                                                        '.',
                                                        ',',
                                                    );
                                                } else {
                                                    $cost = 'N/A';
                                                    $total_cost = 'N/A';
                                                }
                                            @endphp
                                            {{ number_format((float) $qty, 2, '.', ',') }}
                                        </td>
                                        <td style="font-size: 13PX; text-align: center">
                                            {{ $ingredient->uom->unit_symbol ?? '' }}
                                        </td>
                                        <td style="font-size: 13PX; text-align: center">
                                            {{ $cost }}
                                        </td>
                                        <td class="total-price">{{ $total_cost }}</td>

                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <h4 class="alert-heading mr-12">Overall Cost</h4>
                                <h4 class="alert-heading" id="totalAmount">₱ 0.00</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">

                <div class="container">

                    <form>
                        @csrf
                        <div class="form-group">
                            <img id="imagePreview" src="{{ asset('storage/' . $menus->menu_image) }}" alt="Image Preview"
                                style="width: 90%; height: 150px; object-fit: cover;" name="image">
                        </div>
                        <div class="form-group mt-1">
                            <label for="menu_name" style="font-size: 13px;">Menu Name:</label>
                            <input type="text" class="form-control" id="menu_name" name="menu_name"
                                value="{{ $menus->menu_name ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label for="menu_description" style="font-size: 13px;">Description:</label>
                            <textarea class="form-control" id="menu_description" name="menu_description" rows="3" required
                                style="height: 70%; width:100%">{{ $menus->menu_description ?? '' }}</textarea>
                        </div>
                        <div class="form-group mt-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="menu_price" style="font-size: 13px;">CODE:</label>
                                    <input type="text" class="form-control" id="menu_code" name="menu_code" required
                                        placeholder="ex. CY23" value="{{ $menus->menu_code ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label style="font-size : 13px;" for="category">Category:</label>
                                    <input type="text" class="form-control" id="category" name="category_id"
                                        value="{{ $menus->categories->category_name ?? '' }}" disabled>
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="pareparer" style="font-size : 13px;">Prepared By:</label>
                            <input type="text" class="form-control" id="reviewer_select" name="preparer"
                                value="{{ $menus->preparer->name ?? '' }} {{ $menus->preparer->middle_name ?? '' }} {{ $menus->preparer->last_name ?? '' }} "
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="reviewer_select" style="font-size : 13px;">Reviewed By:</label>
                            <input type="text" class="form-control" id="reviewer_select" name="reviewer_id"
                                value="{{ $menus->reviewer->name ?? '' }} {{ $menus->reviewer->middle_name ?? '' }} {{ $menus->reviewer->last_name ?? '' }}"
                                disabled>

                            <label for="approver_select" style="font-size: 13px">Approve By:</label>
                            <input type="text" class="form-control" id="approver_select" name="approver_id"
                                value="{{ $menus->approver->name ?? '' }} {{ $menus->approver->middle_name ?? '' }} {{ $menus->approver->last_name ?? '' }}"
                                disabled>
                        </div>
                        {{-- <div class="form-group">
                                <label for="menu_image" style="font-size: 13px">Upload Image:</label>
                                <input type="file" class="form-control-file" id="menu_image" name="menu_image"
                                    required onchange="previewImage(event)">
                            </div> --}}

                        {{-- <button type="submit" class="btn btn-primary mt-3">Create Menu</button> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    </div>

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
    </div>
@endsection


@section('script')
    <script>
        function updateOverallCost() {
            const totalPrices = document.querySelectorAll('.total-price');
            let overallCost = 0;
            totalPrices.forEach(priceCell => {
                overallCost += parseFloat(priceCell.textContent.replace(/,/g, ''));
            });
            document.getElementById('totalAmount').textContent = `₱ ${overallCost.toFixed(2)}`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateOverallCost();
        });


        function updateStatusApproved() {
            fetch('{{ url('/menu_approved/' . $menus->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status: 'APPROVED'
                    })
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = '/menu_approval_lists';
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
            fetch('{{ url('/menu_approved/' . $menus->id) }}', {
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
                        window.location.href = '/menu_approval_lists';
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
