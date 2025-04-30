<div class="content-fluid">
    @if (session()->has('success'))
    <div class="alert alert-success" id="success-message">
        {{ session('success') }}
        <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="card mt-3 mb-3">
        <div class="card-header p-2">
            <div class="row">
                <div class=" row col-md-12">
                    <div class="col-md-6">
                        @if (auth()->user()->employee->getModulePermission('companies') == 1)
                            <x-primary-button style="text-decoration: none;" data-bs-toggle="modal" data-bs-target="#createCompanyModal">
                                + New Company
                            </x-primary-button>
                        @endif
                        <span wire:loading class="spinner-border text-primary" role="status"></span>
                    </div>
                    <div class="col-md-6">
                        <h5>Company Lists</h5>
                    </div>
                </div>

                <div class="col-md-6">
                
                </div>
            </div>
        </div>


        <div class="card-body">
            <div class="table-responsive-sm">
                <div class="d-flex justify-content-between mb-3">
                    <table class="table table-striped table-hover table-sm table-responsive">
                        <thead class="table-dark table-sm ">
                            <tr>
                                <th>Company Name</th>
                                <th>Company Code</th>
                                <th>TIN</th>
                                <th>Company Type</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($companies as $company)
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
                                            @if (auth()->user()->employee->getModulePermission('companies') == 1)
                                                <button onclick='viewCompany({{ json_encode($company) }})' class="action-btn"
                                                    data-bs-target="#supplierUpdateModal" data-bs-toggle="modal"
                                                    style="text-decoration: none">Edit</button>
                                                <a href="{{ route('company.deactivate', ['id' => $company->id]) }}"
                                                    class="action-btn btn-sm" style="text-decoration: none">
                                                    {{ _('Remove') }}</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No company found</td>
                                </tr>
                        @endforelse

                        </tbody>
                    </table>

            </div>
                
        </div>
    </div>


     <!-- Modal Company Create-->
     <div class="modal fade" id="createCompanyModal" tabindex="-1" aria-labelledby="companyCreateModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="companyCreateModalLabel">Company Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form -->
                    <form wire:submit.prevent="createCompany" id="companyCreateForm">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="supp_name" class="form-label">Company Name</label>
                                <input wire:model='comp_name' type="text" class="form-control" id="name" name="company_name" >
                                @error('comp_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="postal_address" class="form-label">Company Code</label>
                                <input wire:model='comp_code' type="text" class="form-control" id="postal_address" name="company_code" >
                                @error('comp_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_no_1" class="form-label">Company Tin</label>
                                <input wire:model='comp_tin' type="text" class="form-control" id="contact1" name="company_tin" >
                                @error('comp_tin')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="supp_address" class="form-label">Type</label>
                                <input wire:model='comp_type' type="text" class="form-control" id="address" name="company_type" >
                                @error('comp_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="contact_no_2" class="form-label">Description</label>

                                <textarea wire:model='comp_desc' type="text" class="form-control" id="contact2" name="company_description"> </textarea>
                                @error('comp_desc')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading wire:target="createCompany">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Saving
                                </span>
                                <span wire:loading.remove wire:target="createCompany">
                                    Save
                                </span>
                            </button>
                        </div>
                    </form>
                </div>       
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('dispatch-success', event => {
            
            setTimeout(function() {
            document.getElementById('success-message').style.display = 'none';
        }, 1500);
        document.getElementById('companyCreateForm').reset();
        var modal = bootstrap.Modal.getInstance(document.getElementById('createCompanyModal'));
        modal.hide();

        
        });
            window.addEventListener('dispatch-error', event => {
                setTimeout(function() {
                    document.getElementById('error-message').style.display = 'none';
                }, 1500);
            });
        });
    </script>
</div>



