@extends('layouts.master')
@section('content')
    <div class="dashboard">
        <header>
            <h2>Branch List</h2>
            @if (auth()->user()->employee->getModulePermission('branches')== 1)
                <a class="add-btn" type="button" href="\branch\branch_create" style="text-decoration: none;">+ Add Branch</a> 
            @endif
        </header>
        <div class="table-responsive-sm">
            <table class="table min-w-full">
                <thead class="table-dark sticky-top">
                    <tr>
                        <th>Branch Name</th>
                        <th>Branch Code</th>
                        <th>Address</th>
                        <th>Type</th>
                        <th>Contact Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($branches as $branch)
                        <tr>
                            <td>{{ $branch->branch_name }}</td>
                            <td>{{ $branch->branch_code }}</td>
                            <td>{{ $branch->branch_address }}</td>
                            <td>{{ $branch->branch_type }}</td>
                            <td>{{ $branch->branch_cell }}</td>
                            <input id="company_id" name='company_id' type="hidden">
                            <td>
                                <div class="button-group">
                                    <a onclick='viewBranch({!! $branch !!})' class="action-btn"
                                        href="{{ route('branch.show', ['id' => $branch->id]) }}"><span
                                            class="text-sm">View</span></a>
                                    @if (auth()->user()->employee->getModulePermission('branches')== 1)
                                        <button class="action-btn" data-bs-target="#branchUpdateModal" data-bs-toggle="modal"
                                            onclick='viewBranch({!! $branch !!})'><svg xmlns="http://www.w3.org/2000/svg"
                                                width="16" height="16" fill="currentColor" class="bi bi-pen"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z" />
                                            </svg></button>
                                        <a class="action-btn" href="{{ route('branch.deactivate', ['id' => $branch->id]) }}"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                class="bi bi-trash3" viewBox="0 0 16 16">
                                                <path
                                                    d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                            </svg></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>


    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    @endif

    <!-- Modal Branch Update-->
    <div class="modal fade" id="branchUpdateModal" tabindex="-1" aria-labelledby="branchUpdateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="branchUpdateModalLabel">Update Branch</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form -->

                    <form action="{{ route('branch.update') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="branch_name_update" class="form-label">Branch Name</label>
                                <input type="text" class="form-control" id="branch_name_update" name="branch_name">
                                <li type="hidden" class="form-control" id="branch_id_update" name="branch_id"></li>
                            </div>
                            <div class="col-md-6">
                                <label for="branch_code_update" class="form-label">Branch Code</label>
                                <input type="text" class="form-control" id="branch_code_update" name="branch_code">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="branch_type_update" class="form-label">Branch Type</label>
                                <input type="text" class="form-control" id="branch_type_update" name="branch_type">
                            </div>
                            <div class="col-md-6">
                                <label for="branch_cell_update" class="form-label">Contanct No.</label>
                                <input type="text" class="form-control" id="branch_cell_update" name="branch_cell">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="branch_email_update" class="form-label">Email</label>
                                <input type="text" class="form-control" id="branch_email_update" name="branch_email">
                            </div>

                            <div class="col-md-6">
                                <label for="branch_address_update" class="form-label">Branch Address</label>
                                <input type="text" class="form-control" id="branch_address_update" name="branch_address">
                            </div>

                        </div>
                        <input id="branch_id" name='branch_id' type="hidden">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input type="submit" value="Update" CLASS = "btn btn-outline-success">
                </div>
            </div>

            </form>

        </div>
    </div>



@endsection


@section('script')
    <script>
        function viewBranch(data) {
            console.log(data);
            document.getElementById('branch_id').value = data.id;
            document.getElementById('branch_name_update').value = data.branch_name;
            document.getElementById('branch_code_update').value = data.branch_code;
            document.getElementById('branch_type_update').value = data.branch_type;
            document.getElementById('branch_cell_update').value = data.branch_cell;
            document.getElementById('branch_email_update').value = data.branch_email;
            document.getElementById('branch_address_update').value = data.branch_address;
            document.getElementById('company_id').value = data.id;

        }
    </script>
@endsection
