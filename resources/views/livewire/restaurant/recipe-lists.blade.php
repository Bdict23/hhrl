<div class="overflow-x-auto">
@if (session('status') == 'error')
    <div class="alert alert-danger">
        {{ session('message') ?? 'Something went wrong.' }}
    </div>
@endif
    <div class="card mt-3 mb-3">
        <div class="card-header p-2 ">
            <div class="row">
                <div class=" row col-md-6">
                    <div class="col-md-6">
                        @if(auth()->user()->employee->getModulePermission('Purchase Order') == 1 )
                            <x-primary-button style="text-decoration: none;">
                                <a href="{{ route('menus.create') }}" style="text-decoration: none; color: inherit;">+ Create New Recipe</a>
                            </x-primary-button>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <span wire:loading class="spinner-border text-primary" role="status"></span>
                    </div>
                </div>

                <div class="col-md-6">
                <div class="d-flex">
                    <div class="input-group">
                        <label for="PO-status" class="input-group-text">Status</label>
                        <select wire:model="statusPO" id="PO-status"  class="form-select form-select-sm">
                            <option value="All">All</option>
                          
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="from_date" class="input-group-text">From:</label>
                        <input wire:model="fromDate" type="date" id="from_date" name="from_date" value="{{ date('Y-m-d') }}"
                            class="form-control form-control-sm">
                    </div>
                    <div class="input-group">
                        <label for="to_date" class="input-group-text">To:</label>
                        <input wire:model="toDate" type="date" id="to_date" name="to_date" value="{{ date('Y-m-d') }}"
                            class="form-control form-control-sm">
                            <button wire:click="search" class="btn btn-primary input-group-text">search</button>
                    </div>
                    <div>
                    </div>
                </div>
            </div>
            </div>
        </div>


        <div class="card-body ">
                <div class="overflow-x-auto" style="display: height: 400px; overflow-x: auto;">
                    <table class="table min-w-full table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Recipe Name</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Code</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse ($recipes as $recipe)
                                <tr>
                                    <td>{{ $recipe->menu_name }}</td>
                                    <td>{{ $recipe->menu_type ?? 'N/A' }}</td>
                                    <td>{{ $recipe->category->category_name ?? 'N/A' }}</td>
                                    <td>{{ $recipe->menu_code }}</td>
                                    <td>{{ $recipe->status }}</td>
                                    <td>
                                        <a href="" class="btn btn-sm btn-info">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No recipes found.</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>



