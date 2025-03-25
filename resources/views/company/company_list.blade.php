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
                                <a href="{{ route('company.show', ['id' => $company->id]) }}" class="action-btn"
                                    style="text-decoration: none">View</a>
                                <button onclick='viewCompany({{ json_encode($company) }})' class="action-btn"
                                    data-bs-target="#supplierUpdateModal" data-bs-toggle="modal"
                                    style="text-decoration: none">Edit</button>
                                <a href="{{ route('company.deactivate', ['id' => $company->id]) }}"
                                    class="action-btn btn-sm" style="text-decoration: none">
                                    {{ _('Remove') }}</a>
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
                            <div class="col-md-12">
                                <label for="contact_no_2" class="form-label">Description</label>

                                <textarea type="text" class="form-control" id="contact2" name="company_description"> </textarea>
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
                        <div class="row m-1">

                            <label for="company_description" class="form-label">Description</label>
                            <textarea type="text" class="form-control" id="company_description_update" name="company_description" required> </textarea>


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
