<div class="overflow-x-auto">
@if (session('status') == 'error')
    <div class="alert alert-danger">
        {{ session('message') ?? 'Something went wrong.' }}
    </div>
@endif

<div class="justify-content-end d-flex">
    <h4>Recipe - Summary &nbsp;<i class="bi bi-fork-knife"></i></h4>
</div>
    <div class="card mt-3 mb-3">
        <div class="card-header p-2 ">
            <div class="row">
                <div class="col-md-3 mt-2">
                    @if(auth()->user()->employee->getModulePermission('Purchase Order') == 1 )
                        
                            <a href="\create_menu" style="text-decoration: none; color: inherit;">
                                <x-primary-button style="text-decoration: none;" class="text-nowrap">+ Create New Recipe</x-primary-button>
                            </a>
                    @endif
                </div>
                <div class="col-md-9 mt-2">
                    <div class="container">
                        <div class="d-flex justify-content-end">
                            <div class="input-group me-2">
                                <label for="PO-status" class="input-group-text">Type</label>
                                <select wire:model="type" id="PO-status"  class="form-select form-select-sm">
                                    <option value="All">All</option>
                                    <option value="Ala Carte">Ala Carte</option>
                                    <option value="Banquet">Banquet</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="PO-status" class="input-group-text">Status</label>
                                <select wire:model="statusPO" id="PO-status"  class="form-select form-select-sm">
                                    <option value="All">All</option>
                                    <option value="Available">Available</option>
                                    <option value="For Approval">For Approval</option>
                                </select>
                                    <button wire:click="filter" class="btn btn-primary input-group-text">
                                        <span wire:loading wire:target="filter">
                                            Filtering...
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        </span>
                                        <span wire:loading.remove wire:target="filter">Filter &nbsp;<i class="bi bi-funnel-fill"></i></span>
                                    </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card-body ">
                <div class="overflow-x-auto" style="height: 400px; overflow-x: auto;">
                    <table class="table min-w-full table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Image</th>
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
                                    <td>
                                        @if($recipe->menu_image)
                                            <img class="img-thumbnail" src="{{ asset('storage/' . $recipe->menu_image) }}" alt="Image" width="50" height="50">
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td>{{ $recipe->menu_name }}</td>
                                    <td>{{ $recipe->menu_type ?? 'N/A' }}</td>
                                    <td>{{ $recipe->category->category_name ?? 'N/A' }}</td>
                                    <td>{{ $recipe->menu_code }}</td>
                                    <td>
                                        <span 
                                        @if($recipe->status == 'AVAILABLE') class="badge bg-success" 
                                        @elseif($recipe->status == 'FOR APPROVAL') class="badge bg-warning" 
                                        @elseif($recipe->status == 'PENDING') class="badge bg-secondary"
                                        @else class="badge bg-danger" @endif>
                                            {{ $recipe->status }}
                                        </span>
                                    </td>
                                    <td>
                                            <a href="/recipe-edit?recipe-id={{ $recipe->id }}" style="text-decoration: none; color: inherit;">
                                                <x-primary-button style="text-decoration: none;">Edit</x-primary-button></a>
                                            <a href="" style="text-decoration: none; color: inherit;">
                                                <x-secondary-button style="text-decoration: none;">View</x-secondary-button>
                                            </a>
                                       
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No recipes found.</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>



