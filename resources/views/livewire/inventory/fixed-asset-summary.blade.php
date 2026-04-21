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
                     <a href="{{ route('batch.summary') }}" style="text-decoration: none; color: white;"><x-secondary-button ><i class="bi bi-arrow-90deg-left"></i> Back</x-secondary-button></a>
                     <x-primary-button>export <i class="bi bi-box-arrow-up"></i></x-primary-button>
                @endif
                <div class="d-flex justify-content-end">
                    <span wire:loading class="spinner-border text-primary" role="status"></span>
                </div>
            </div>
            <div class="col-md-6">
                <h4 class="text-end">Fixed Asset Summary <i class="bi bi-list-task"></i></h4>
            </div>
        </div>
    </div>
    <div class="card mt-3 mb-3">  
        <div class=" card-header d-flex justify-content-between mx-2">
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
                    <table class="table table-striped table-hover table-sm table-bordered " >
                        <thead class="table-dark">
                            <tr>
                                <th>Batch REF.</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Serial</th>
                                <th>S.I/D.R</th>
                                <th>Cost</th>
                                <th>Life Span</th>
                                <th>Span Ended</th>
                                <th>Condition</th>
                                <th>Issued Date</th>
                            </tr>
                        </thead>
                        <tbody id="poSummaryTable">
                            @forelse ($assets ?? [] as $asset)
                                <tr class="mt-1">
                                    <td class="align-content-center">{{ $asset->batch->reference }}</td>
                                    <td class="align-content-center">{{$asset->itemDetail->item_code}}</td>
                                    <td class="align-content-center">{{$asset->itemDetail->item_description}}</td>
                                    <td class="align-content-center">{{$asset->serial}}</td>
                                    <td class="align-content-center">{{$asset->sidr_no}}</td>
                                    <td class="align-content-center">{{$asset->cost}}</td>
                                    <td class="align-content-center">{{$asset->lifespan}}</td>
                                    <td class="align-content-center">{{$asset->span_ended}}</td>
                                    <td class="align-content-center">{{$asset->condition}}</td>
                                    <td class="align-content-center">{{$asset->span_ended}}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">No Batch properties found</td>
                                </tr>
                            @endforelse
                    
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>



