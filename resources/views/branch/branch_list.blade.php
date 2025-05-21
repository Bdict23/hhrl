@extends('layouts.master')
@section('content')
        <h4>Branch Lists</h4>
    <div class="card">
        <div class="card-header row">
            <div class="col-md-6 mb-2">
                @if (auth()->user()->employee->getModulePermission('branches')== 1)
                   <a href="\branch\branch_create" style="text-decoration: none;"> <x-primary-button>+ Add Branch</x-primary-button> </a> 
                @endif
            </div>
           <div class="col-md-6">
                <input type="text" id="branchSearch" class="form-control" placeholder="Search Branches" onkeyup="searchBranches()">
           </div>
        </div>
          <script>
                function searchBranches() {
                    let input = document.getElementById('branchSearch').value.toLowerCase();
                    let rows = document.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        let branchName = row.querySelector('td:first-child').textContent.toLowerCase();
                        if (branchName.includes(input)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }
            </script>
        <div class="card-body table-responsive-sm overflow-x-auto">
            <table class="table min-w-full">
                <thead class="table-dark">
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
                                    <a onclick='viewBranch({!! $branch !!})'
                                        href="{{ route('branch.show', ['id' => $branch->id]) }}"><span
                                            class="text-sm"><x-primary-button> <u>view</u> </x-primary-button></span></a>
                                    @if (auth()->user()->employee->getModulePermission('branches')== 1)
                                        <x-secondary-button data-bs-target="#branchUpdateModal" data-bs-toggle="modal"
                                            onclick='viewBranch({!! $branch !!})'>Edit</x-secondary-button>
                                        <a href="{{ route('branch.deactivate', ['id' => $branch->id]) }}">
                                            <x-danger-button>Deactivate</x-danger-button>
                                        </a>
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
                                <li type="text" class="form-control" id="branch_id_update" ></li>
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
                        <input id="branch_id" type="hidden">
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
