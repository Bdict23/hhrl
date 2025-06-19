<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Procurement Summary</h5>
        </div>
        <div class="card-tools">
           <div class="row mt-2 pl-3 pr-3">
            <div class="col-md-6">
                <a class="btn btn-primary" href="\banquet-procurement-create">Create New</a>
            </div>
             <div class="col-md-6">
                <input type="text" class="form-control" placeholder="Search" wire:model="searchTerm">
             </div>
           </div>
        </div>
        <div class="card-body">

            <table class="table">
                <tr>
                    <th>Reference Number</th>
                    <th>Document Number</th>
                    <th>Date Created</th>
                    <th>Event Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <tbody>
                    @forelse ($procurementLists as $procurement)
                        <tr>
                            <td>{{ $procurement->reference_number }}</td>
                            <td>{{ $procurement->document_number }}</td>
                            <td>{{ $procurement->created_at->format('Y-m-d') }}</td>
                            <td>{{ $procurement->event_name }}</td>
                            <td>{{ $procurement->status }}</td>
                            <td>
                                <a href="{{ route('banquet.procurement.edit', $procurement->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                <a href="{{ route('banquet.procurement.view', $procurement->id) }}" class="btn btn-sm btn-secondary">View</a>
                                <button wire:click="delete({{ $procurement->id }})" class="btn btn-sm btn-danger">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No procurement records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
