<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Equipment Request Summary</h5>
        </div>
        <div class="card-tools">
           <div class="row mt-2 pl-3 pr-3">
            <div class="col-md-6">
                <a class="btn btn-primary" href="{{ route('banquet.equipment-request.create') }}">New Request</a>
            </div>
             <div class="col-md-6">
                <input type="text" class="form-control" placeholder="Search Equipment" wire:model="searchTerm">
             </div>
           </div>
        </div>
        <div class="card-body">

          <div class="table-responsive overflow-x-auto">
              <table class="table">
                  <tr>
                      <th>Reference Number</th>
                      <th>Department</th>
                      <th>Date Requested</th>
                      <th>Event Name</th>
                      <th>Event Date</th>
                      <th>Status</th>
                      <th>Action</th>
                  </tr>
                  <tbody>
                      @forelse ($equipmentRequests as $request)
                          <tr>
                              <td>{{ $request->reference_number }}</td>
                              <td>{{ $request->department->department_name ?? '' }}</td>
                              <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M-d-Y') }}</td>
                              <td>{{ $request->event->event_name ?? '' }}</td>
                              <td>{{ \Carbon\Carbon::parse($request->event?->event_date)->format('M-d-Y') ?? '' }}</td>
                              <td>{{ $request->status }}</td>
                              <td>
                                  <button wire:click="viewRequest('{{ $request->reference_number }}')" type="button"  class="btn btn-info" >View</button>
                              </td>
                          </tr>
                      @empty
                          <tr>
                              <td colspan="7" class="text-center">No equipment requests found.</td>
                          </tr>
                      @endforelse
                  </tbody>
              </table>
          </div>
        </div>
    </div>
</div>
