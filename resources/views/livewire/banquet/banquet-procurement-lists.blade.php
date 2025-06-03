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

                </tbody>
            </table>
        </div>
    </div>
</div>
