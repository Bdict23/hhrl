@extends('layouts.master')
@section('content')
    <div class="dashboard">
        <header>
            <h2>Company List</h2>
            <button class="add-btn" type="button" data-bs-toggle="modal" data-bs-target="#supplierModal">+ Add Company</button>
        </header>

        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Company Name</th>
                    <th>Company Code</th>
                    <th>TIN</th>
                    <th>Company Type</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($companies as $company)
                    <tr>
                        <td>{{ $company->company_name }}</td>
                        <td>{{ $company->company_code }}</td>
                        <td>{{ $company->company_tin }}</td>
                        <td>{{ $company->company_type }}</td>
                        <td>{{ $company->company_description }}</td>

                        <td>
                            <div class="button-group">
                                <a href="{{ route('company.show', ['id' => $company->id]) }}" class="action-btn"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-eye" viewBox="0 0 16 16">
                                        <path
                                            d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                        <path
                                            d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                    </svg></a>
                                <button onclick='viewCompany({!! $company !!})' class="action-btn"
                                    data-bs-target="#supplierUpdateModal" data-bs-toggle="modal"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-pen" viewBox="0 0 16 16">
                                        <path
                                            d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z" />
                                    </svg></button>
                                <button class="action-btn" data-bs-target="#supplierRemoveModal" data-bs-toggle="modal"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-trash3" viewBox="0 0 16 16">
                                        <path
                                            d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                    </svg></button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>


    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    @endif
    <!-- Modal Company Create-->
    <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="companyCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="companyCreateModalLabel">Company Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form -->
                    <form action="{{ route('company.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="supp_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="name" name="company_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="postal_address" class="form-label">Company Code</label>
                                <input type="text" class="form-control" id="postal_address" name="company_code" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_no_1" class="form-label">Company Tin</label>
                                <input type="text" class="form-control" id="contact1" name="company_tin" required>
                            </div>
                            <div class="col-md-6">
                                <label for="supp_address" class="form-label">Type</label>
                                <input type="text" class="form-control" id="address" name="company_type" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_no_2" class="form-label">Description</label>

                                <input type="text" class="form-control" id="contact2" name="company_description"
                                    required>
                            </div>

                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input type="submit" value="Save" CLASS = "btn btn-outline-success">
                </div>
            </div>
            </form>
        </div>
    </div>

    <!-- Modal Company Update-->
    <div class="modal fade" id="supplierUpdateModal" tabindex="-1" aria-labelledby="companyUpdateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="companyUpdateModalLabel">Update Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form -->

                    <form action="{{ route('company.update') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company_name_update" name="company_name"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="company_code" class="form-label">Code</label>
                                <input type="text" class="form-control" id="company_code_update" name="company_code"
                                    required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_tin" class="form-label">TIN</label>
                                <input type="text" class="form-control" id="company_tin_update" name="company_tin"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="company_type" class="form-label">Company Type</label>
                                <input type="text" class="form-control" id="company_type_update" name="company_type"
                                    required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_description" class="form-label">Description</label>
                                <input type="text" class="form-control" id="company_description_update"
                                    name="company_description" required>
                            </div>

                        </div>
                        <input id="id1" name='myid' type="hidden">
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
        function viewCompany(data) {
            console.log(data);
            document.getElementById('company_name_update').value = data.company_name;
            document.getElementById('company_code_update').value = data.company_code;
            document.getElementById('company_tin_update').value = data.company_tin;
            document.getElementById('company_type_update').value = data.company_type;
            document.getElementById('company_description_update').value = data.company_description;
            document.getElementById('id1').value = data.id;
        }
    </script>
@endsection
