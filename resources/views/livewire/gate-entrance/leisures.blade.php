<div>

    <div class="text-center">
        <h1 class="text-2xl font-bold">Services</h1>
        <p class="text-gray-600">HHRL</p>
        
        @if (session()->has('message'))
        <div class="alert alert-success" id="success-message">
            {{ session('message') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row p-4">

            {{-- left --}}
            <div
            @if(auth()->user()->employee->getModulePermission('Leisures') == 1)
                class="col-md-6 mb-3"
            @else
                class="col-md-12 mb-3"
            @endif>
                <div class="card">
                    <strong class="card-header"> Services List</strong>
                    <input type="text" id="searchInput" class="col-md-6 border border-gray-300 rounded mt-2 ml-1" placeholder="Search...">
                    <div class="card-body overflow-auto" style="height: 400px;">
                        <table class="table table-sm" id="leisuresTable">
                            <thead class="table-dark"> 
                                <tr>
                                    <th> Name</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    @if(auth()->user()->employee->getModulePermission('Leisures') == 1)
                                        <th >Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($leisures as $leisure)
                                    <tr>
                                        <td>{{ $leisure->name }}</td>
                                        <td>{{ $leisure->description }}</td>
                                        <td>{{ $leisure->amount }}</td>
                                        <td>
                                            @if ($leisure->status == 1)
                                                Active
                                            @else
                                                Inactive
                                            @endif
                                        </td>
                                        @if(auth()->user()->employee->getModulePermission('Leisures') == 1)
                                            <td>
                                                <x-secondary-button onclick="editLeisure({{$leisure}})" wire:click="editLeisure({{ $leisure->id }})" data-bs-toggle="modal" data-bs-target="#aditServiceModal">
                                                    Edit
                                                </x-secondary-button>
                                                <x-danger-button wire:click="deactivateLeisure({{ $leisure->id }})">
                                                    Inactive
                                                </x-danger-button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                        
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
           
            {{-- right --}}
           
            @if(auth()->user()->employee->getModulePermission('Leisures') == 1)
                <div class="col-md-6 ">
                    <div class="card">
                        <strong class="card-header"> New Service</strong>
                            <div class="card-body">
                                <form wire:submit.prevent="createLeisure" >
                                    @csrf
                                    
                                        <div class="">
                                            <div class="row mt-2">
                                                <label for="name" class="col-md-3">Leisure Name <span style="color: red;">*</span></label>
                                                <input type="text"  wire:model="name"
                                                    class="col-md-9 border border-gray-300 rounded" placeholder="Leisure Name">
                                            </div>
                                            @error('name')
                                                    <span class="text-red-500 text-xs italic">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="">
                                            <div class="row">
                                                <label for="description" class="col-md-3 mt-1"> Description <span style="color: red;">*</span> </label>
                                                <input type="text"  wire:model="description"
                                                    class="col-md-9 mt-1 border border-gray-300 rounded " placeholder="Description">
                                                @error('description')
                                                    <span class="text-red-500 text-xs italic">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="">
                                    
                                            <div class="row">
                                                <label for="description" class="col-md-3 mt-1">Amount <span style="color: red;">*</span></label>
                                                <input type="number"  wire:model="amount"
                                                    class="col-md-9 mt-1 border border-gray-300 rounded " placeholder="Amount">
                                                @error('description')
                                                    <span class="text-red-500 text-xs italic">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="">
                                            {{-- hidden input --}}
                                            <input type="hidden" wire:model="status" value="1">
                                            <input type="hidden"  wire:model="branch_id" value="1">
                                            <input type="submit" value="Create Leisure"
                                                class="bg-blue-500 hover:bg-blue-100 text-white font-bold py-2 px-4 rounded mx-2 mt-2">
                                        </div>
                                </form>
                            </div>
                    </div>
                </div>
            @endif
        </div>

         {{-- modal --}}
         <div class="modal fade" id="aditServiceModal" tabindex="-1" aria-labelledby="aditServiceModal" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="aditServiceModalLabel">Service Info</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Ensure the fields are populated with the selected leisure's data --}}
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="sname" class="form-label">Leisure Name <span style="color: red;">*</span> </label>
                                <input type="text" class="border border-gray-300 rounded" id="sname" wire:model="name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="sdescription" class="form-label">Description <span style="color: red;">*</span> </label>
                                <input type="text" class="border border-gray-300 rounded" id="sdescription" wire:model="description">
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label for="samount" class="form-label">Amount <span style="color: red;">*</span> </label>
                            <input type="number" class="border border-gray-300 rounded" id="samount" wire:model="amount">
                            @error('amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <x-primary-button class="mt-4" wire:click="updateLeisure" data-bs-dismiss="modal">Update</x-primary-button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('searchInput').addEventListener('input', function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#leisuresTable tbody tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const match = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(filter));
                row.style.display = match ? '' : 'none';
            });
        });

        function editLeisure($leisure) {
            document.getElementById('sname').value = $leisure.name;
            document.getElementById('sdescription').value = $leisure.description;
            document.getElementById('samount').value = $leisure.amount;
            // Set the status and branch_id if needed
        }
    </script>



</div>
