  @extends('layouts.master')
  @section('content')
      <!-- Branch Create Modal-->
      <div class="dashboard">
          <div>
              <div>
                  <div class="modal-header">
                      <h4 class="modal-title" id="branchCreateModalLabel">Branch Form</h4>

                  </div>
                  <div class="modal-body">
                      <!-- Form -->
                      <form action="{{ route('branch.store') }}" method="POST">
                          @csrf
                          <div class="row mb-3">
                              <div class="col-md-6">
                                  <label for="supp_name" class="form-label">Branch Name</label>
                                  <input type="text" class="form-control" id="branch_name" name="branch_name" required>
                              </div>
                              <div class="col-md-6">
                                  <label for="branch_code" class="form-label">Branch Code</label>
                                  <input type="text" class="form-control" id="branch_code" name="branch_code" required>
                              </div>
                          </div>
                          <div class="row mb-3">
                              <div class="col-md-6">
                                  <label for="contact_no_1" class="form-label">Branch Type</label>
                                  <input type="text" class="form-control" id="contact1" name="branch_type" required>
                              </div>
                              <div class="col-md-6">
                                  <label for="branch_cell" class="form-label" required>Contact Number</label>
                                  <input type="text" class="form-control" id="branch_cell" name="branch_cell">
                              </div>
                          </div>
                          <div class="row mb-3">
                              <div class="col-md-6">
                                  <label for="branch_email" class="form-label">Email Address</label>
                                  <input type="text" class="form-control" id="branch_email" name="branch_email" required>
                              </div>

                              <div class="col-md-6">
                                  <label for="branch_address" class="form-label">Branch Address</label>
                                  <input type="text" class="form-control" id="branch_address" name="branch_address"
                                      required>
                              </div>
                          </div>
                          <div class="row mb-3">
                              <div class="col-md-6">
                                  <label for="options" class="form-label">Select Company</label>
                                  <select id="options" name="company_id" class="form-control" required
                                      onchange="fetchcompany(this.value)">
                                      @foreach ($companies as $company)
                                          <option value="{{ $company->id }}">
                                              {{ $company->company_name }}
                                          </option>
                                      @endforeach
                                  </select>
                              </div>


                          </div>
                          <div class="modal-footer">
                              <a type="button" class="btn btn-secondary" href="\branch_list"> Back </a>
                              <div style="padding-left: 10px">
                                  <button type="submit" CLASS = "btn btn-outline-success"> Save </button>
                              </div>
                          </div>
                      </form>
                  </div>
              </div>

          </div>
      </div>
  @endsection


  @section('script')
      <script>
          function fetchcompany(data) {
              console.log(data);
          }
      </script>
  @endsection
