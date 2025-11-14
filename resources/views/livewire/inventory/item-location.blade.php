<div>
        <div>
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        <div>
            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
     <div class="row  container">
        <!-- Left Dashboard -->
        <div 
          @if (auth()->user()->employee->getModulePermission('Item Location') == 1 )
            class="col-md-7"
            @else
            class="col-md-12"
            @endif>
                <div class="col-md-6">
                    <h5>ITEM LISTS</h5>
                </div>
            <div class="card mt-2">

                @csrf
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                             <input type="text" class="form-control" style="width: 100; font-size: 10px" placeholder="Search" id="searchInput" onkeyup="filterTable()">
                        </div>
                    </div>
                    <script>
                        function filterTable() {
                            var input, filter, table, tr, td, i, txtValue;
                            input = document.getElementById("searchInput");
                            filter = input.value.toUpperCase();
                            table = document.querySelector("#itemTable");
                            tr = table.getElementsByTagName("tr");
                            for (i = 0; i < tr.length; i++) {
                                tr[i].style.display = "none";
                                td = tr[i].getElementsByTagName("td");
                                for (var j = 0; j < td.length; j++) {
                                    if (td[j]) {
                                        txtValue = td[j].textContent || td[j].innerText;
                                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                            tr[i].style.display = "";
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    </script>
                </div>
                <div class="card-body table-responsive-sm" wire:ignore.self style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-striped table-hover table-sm table-responsive-sm">
                        <thead class="table-dark">
                            <tr style="font-size: x-small">
                                <th>Code</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Location</th>
                                <th>Group Location</th>
                                @if (auth()->user()->employee->getModulePermission('Item Location') == 1 )
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody style="font-optical-sizing: auto" id="itemTable">
                            @forelse ($items as $item)
                                <tr>
                                    <td style="font-size: x-small">{{ $item->item_code ?? '' }}</td>
                                    <td style="font-size: x-small">{{ $item->item_description ?? '' }}</td>
                                    <td style="font-size: x-small">{{ $item->uom->unit_symbol ?? 'N/A' }}</td>
                                    <td style="font-size: x-small">{{ $location[$item->id]['location_name'] ?? '' }}</td>
                                    <td style="font-size: x-small">{{ $location[$item->id]['group_location'] ?? '' }}</td>
                                    @if (auth()->user()->employee->getModulePermission('Item Location') == 1 )
                                        <td style="font-size: x-small">
                                            <button class="btn btn-primary btn-sm" wire:click="editItemLocation({{ $item->id }})">Edit</button>
                                        </td>
                                        
                                    @endif
                                </tr>
                            @empty
                                
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Right Dashboard -->
        @if (auth()->user()->employee->getModulePermission('Item Location') == 1 )
            <div class="col-md-5">
                <div class="card mt-2">
                    <header class="card-header">
                        <h5>Information</h5>
                    </header>
                    <div class="card-body">
                        <form wire:submit.prevent="saveLocation" id="locationForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="Item_code" class="form-label" style="width: 100; font-size: 13px">Item Code/ SKU</label>
                                    <input value="{{ $itemSKU }}" type="text" class="form-control" id="name" readonly
                                        style="width: 100; font-size: 13px" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="po_number" class="form-label" style="width: 100; font-size: 13px">Item Name</label>
                                    <input value="{{ $itemName }}" id="po_number" type="text" class="form-control"
                                        style="width: 100; font-size: 13px" disabled>
                                </div>
                            </div>
                            <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="delivered_by" class="form-label" style="width: 100; font-size: 13px">Current Location</label>
                                        <input value="{{ $itemLocationName }}" type="delivered_by" class="form-control" style="width: 100; font-size: 13px" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="delivered_by" class="form-label" style="width: 100; font-size: 13px">Current Group Location</label>
                                        <input value="{{ $itemGroupLoc }}" type="delivered_by" class="form-control" style="width: 100; font-size: 13px" disabled>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <label for="delivered_by" class="form-label" style="width: 100; font-size: 13px">New Location</label>
                                    <input wire:model="newItemLocation" type="text" class="form-control text-uppercase" style="width: 100; font-size: 13px" oninput="this.value = this.value.toUpperCase();">
                                    @error('newItemLocation')
                                        <span class="text-danger" style="font-size: 13px">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label for="delivered_by" class="form-label" style="width: 100; font-size: 13px">New Group Location</label>
                                    <input wire:model='newItemLocationGroup' type="delivered_by" class="form-control" style="width: 100; font-size: 13px" oninput="this.value = this.value.toUpperCase();">
                                    @error('newItemGroupLocation')
                                        <span class="text-danger" style="font-size: 13px">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
        
        
                            <div>
                                <button  type="submit" wire:loading.attr="disabled" wire:target="saveLocation" class="btn btn-primary btn-sm float-right mt-2">
                                    <span wire:loading.remove wire:target="saveLocation">Save Changes</span>
                                    <span wire:loading wire:target="saveLocation">Saving...</span>
                                </button>
                            </div>
                        </form>
                        <div class="float-left">
                                <i class="float-left mr-2" style="font-size: 8px;" wire:loading>Please wait...</i>
                            <span wire:loading class="mr-2 spinner-border  text-primary float-left" role="status"></span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
             window.addEventListener('clearForm', event => {
            // Listen for the wire:success event
                document.getElementById('locationForm').reset();
                 // Get the form element by its ID
                var form = document.getElementById('locationForm');
                // Call the reset() method to clear all fields
                form.reset();
                 // Hide the success message after 1 second
                        setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
                            }, 1500);
            });
        });
    </script>
    
</div>
