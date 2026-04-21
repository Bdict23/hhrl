<div class="overflow-x-auto">
    @if (session('status') == 'error')
        <div class="alert alert-danger">
        {{ session('message') ?? 'Something went wrong.' }}
        </div>
    @endif
   <div class="d-flex justify-content-between">
     <div class="w-50">
        <input type="text" placeholder="Search" id="searchKey" class="form-control form-control-sm" onkeyup="searchKey()">
     </div>
       <h4 class="text-end">For Review Summary <i class="bi bi-list-task"></i></h4>
   </div>
    <div class="card mt-3 mb-3">  
        <div class="d-flex justify-content-end mx-2">
            
            <div class="container">
                
                <script>
                    function searchKey() {
                        
                        var input, filter, table, tr, td, i, txtValue;
                        input = document.getElementById("searchKey");
                        filter = input.value.toUpperCase();
                        table = document.querySelector("#poSummaryTable").closest("tbody");
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
                                    <td>{{ $batch->batchType->name ?? 'N/A' }}</td>
                                    <td>{{ $batch->preparedBy->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($batch->issued_date)->format('M. d, Y') }}</td>
                                    <td>{{ $batch->purpose }}</td>
                                    <td>
                                       
                                        <a style="text-decoration: none" href="{{ route('assets.review.show', ['id' => $batch->id , 'action' => 'review']) }}">
                                            <x-primary-button class="button-group"><u>View</u></x-primary-button>
                                       </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No data found</td>
                                </tr>
                            @endforelse
                    
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>



