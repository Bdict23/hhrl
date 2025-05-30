@extends('layouts.master')
@section('content')
    <!-- Modal Branch View-->
    <div class="dashboard">
        <div>
            <div>
                <div class="modal-header" style="background-color: rgba(173, 170, 170, 0.342)">
                    <h4 class="modal-title" id="branchViewModalLabel" style="color: rgb(95, 95, 94)">Branch Details</h4>

                </div>
                <div>
                    <!-- Form -->

                    <form action="{{ route('branch.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="branch_name_view" class="form-label">Branch Name</label>
                                <input type="text" class="form-control" id="branch_name_view"
                                    value="{{ $branch->branch_name }}">
                            </div>
                            <div class="col-md-6">
                                <label for="branch_code_view" class="form-label">Branch Code</label>
                                <input type="text" class="form-control" id="branch_code_view"
                                    value="{{ $branch->branch_code }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="branch_type_view" class="form-label">Branch Type</label>
                                <input type="text" class="form-control" id="branch_type_view"
                                    value="{{ $branch->branch_type }}">
                            </div>
                            <div class="col-md-6">
                                <label for="branch_cell_view" class="form-label">Contanct No.</label>
                                <input type="text" class="form-control" id="branch_cell_view"
                                    value="{{ $branch->branch_cell }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="branch_email_view" class="form-label">Email</label>
                                <input type="text" class="form-control" id="branch_email_view"
                                    value="{{ $branch->branch_email }}">
                            </div>

                            <div class="col-md-6">
                                <label for="branch_address_view" class="form-label">Branch Address</label>
                                <input type="text" class="form-control" id="branch_address_view"
                                    value="{{ $branch->branch_address }}">
                            </div>

                        </div>

                </div>

                <div class="modal-header" style="background-color: rgba(173, 170, 170, 0.342)">
                    <h4 class="modal-title" style="color: rgb(95, 95, 94)">Company</h4>
                </div>
                <div class="modal-body">
                    <!-- Form -->

                    <form>
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="branch_name_view" class="form-label">Company
                                    Name</label>
                                <input type="text" class="form-control" id="branch_name_view"
                                    value="{{ $branch->company->company_name }}">
                            </div>
                            <div class="col-md-6">
                                <label for="branch_code_view" class="form-label">Comany Code</label>
                                <input type="text" class="form-control" id="branch_code_view"
                                    value="{{ $branch->company->company_code }}">
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button onclick="history.back()" class="btn btn-secondary" type="button">Back </button>
                </div>

            </div>

            </form>


        </div>





    </div>
@endsection



<script>
    function viewBranch(data) {
        console.log(data);
        document.getElementById('branch_name_update').value = data.branch_name;
        document.getElementById('branch_name_view').value = data.branch_name;

        document.getElementById('branch_code_update').value = data.branch_code;
        document.getElementById('branch_code_view').value = data.branch_code;

        document.getElementById('branch_type_update').value = data.branch_type;
        document.getElementById('branch_type_view').value = data.branch_type;

        document.getElementById('branch_cell_update').value = data.branch_cell;
        document.getElementById('branch_cell_view').value = data.branch_cell;

        document.getElementById('branch_email_update').value = data.branch_email;
        document.getElementById('branch_email_view').value = data.branch_email;

        document.getElementById('branch_address_update').value = data.branch_address;
        document.getElementById('branch_address_view').value = data.branch_address;

        document.getElementById('branch_id').value = data.id;

    }
</script>
