@extends('layouts.master')
@section('content')
    
    <div>
        @livewire('master-data.company-summary')
    </div>


    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    @endif
   

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

                    <form action="{{ route('company.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                         <div class="row mb-3">
                            <div class="col-md-6">
                                <img id="company_logo_preview_update" src="" alt="Logo Preview" class="img-thumbnail" style="max-height: 100px; display: none;">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="company_logo" class="form-label">Company Logo</label>
                                <input type="file" class="form-control" id="company_logo_update" name="company_logo" accept="image/*" onchange="document.getElementById('company_logo_preview_update').src = window.URL.createObjectURL(this.files[0]); document.getElementById('company_logo_preview_update').style.display = 'block';">
                            </div>
                        </div>
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
