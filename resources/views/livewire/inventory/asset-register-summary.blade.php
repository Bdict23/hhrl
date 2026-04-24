<div class="overflow-x-auto">
    @if (session('status') == 'error')
        <div class="alert alert-danger">
        {{ session('message') ?? 'Something went wrong.' }}
        </div>
    @endif
    <div class="container mb-3">
        <div class="row">
            <div class="col-md-6">
                @if(auth()->user()->employee->getModulePermission('Fixed Asset') == 1 )
                    <a href="{{ route('batch.create') }}" style="text-decoration: none; color: white;"><x-primary-button >+ New Batch</x-primary-button></a>
                     <a href="{{ route('assets.summary') }}" style="text-decoration: none; color: white;"><x-primary-button >Fixed Asset Summary</x-primary-button></a>
                @endif
                <div class="d-flex justify-content-end">
                    <span wire:loading class="spinner-border text-primary" role="status"></span>
                </div>
            </div>
            <div class="col-md-6">
                <h4 class="text-end">Batch Summary <i class="bi bi-list-task"></i></h4>
            </div>
        </div>
    </div>
    <div class="card mt-3 mb-3">  
        <div class=" card-header d-flex justify-content-between mx-2">
                    <div class="col-md-3 mb-2 container">
                        <div class="input-group">
                            <label class="input-group-text">Status</label>
                            <select wire:model="statusPO" id="PO-status"  class="form-select form-select-sm">
                                <option value="All">All</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2 container">
                        <div class="input-group">
                            <label for="from_date" class="input-group-text">From:</label>
                            <input wire:model="fromDate" type="date" id="from_date" name="from_date" value="{{ date('Y-m-d') }}"
                                class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-3 mb-2 container">
                        <div class="input-group">
                            <label for="to_date" class="input-group-text">To:</label>
                            <input wire:model="toDate" type="date" id="to_date" name="to_date" value="{{ date('Y-m-d') }}"
                                class="form-control form-control-sm">
                            <button wire:click="search" class="btn btn-primary input-group-text">Filter &nbsp;<i class="bi bi-funnel"></i></button>  
                        </div>
                    </div> 

            <div class="container">
                <input type="text" placeholder="Search" id="searchKey" class="form-control form-control-sm" onkeyup="searchKey()">
                <script>
                    function searchKey() {
                        
                        var input, filter, table, tr, td, i, txtValue;
                        input = document.getElementById("searchKey");
                        filter = input.value.toUpperCase();
                        table = document.querySelector("#poSummaryTable").closest("table");
                        tr = table.getElementsByTagName("tr");

                        for (i = 0; i < tr.length; i++) {
                            td = tr[i].getElementsByTagName("td");
                            let rowContainsFilter = false;

                            for (let j = 0; j < td.length; j++) {
                                if (td[j]) {
                                    txtValue = td[j].textContent || td[j].innerText;
                                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                        rowContainsFilter = true;
                                        break;
                                    }
                                }
                            }

                            tr[i].style.display = rowContainsFilter ? "" : "none";
                        }
                    }
                </script>
            </div>
        </div>


        <div class="card-body ">
                <div style="height: 500px; overflow-x: auto; display: block;">
                    <table class="table table-striped table-hover table-sm " >
                        <thead class="table-dark">
                            <tr>
                                <th>REF.</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th>Prepared By</th>
                                <th>Date Issued</th>
                                <th>Purpose</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="poSummaryTable">
                            @forelse ($batches ?? [] as $batch)
                                <tr>
                                    <td>{{ $batch->reference }}</td>
                                    <td>
                                        <span class="
                                            @if($batch->status == 'DRAFT') badge bg-secondary
                                            @elseif($batch->status == 'CLOSED') badge bg-primary 
                                            @elseif($batch->status == 'OPEN') badge bg-warning text-dark 
                                            @elseif($batch->status == 'CANCELLED') badge bg-danger 
                                            @else badge bg-secondary 
                                            @endif"> {{ $batch->status }}
                                        </span>
                                    </td>
                                    <td>{{ $batch->batchType->name ?? 'N/A' }}</td>
                                    <td>{{ $batch->preparedBy->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($batch->issued_date)->format('M. d, Y') }}</td>
                                    
                                    <td>{{ $batch->purpose }}</td>
                                    <input id="company_id" name='company_id' type="hidden">
                                    <td>
                                        <a style="text-decoration: none" href="{{ route('assets.view', ['id' => $batch->id, 'action' => 'view']) }}">
                                            <x-primary-button class="button-group"><u>View</u></x-primary-button>
                                        <a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No Batch properties found</td>
                                </tr>
                            @endforelse
                    
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>



