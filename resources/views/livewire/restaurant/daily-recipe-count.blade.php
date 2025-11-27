<div>
    @if (session('status') == 'error')
        <div class="alert alert-danger">
            {{ session('message') ?? 'Something went wrong.' }}
        </div>
    @endif

    <div class="card">

        <div class="card-header p-2 ">
            <div class="row">
                <div class=" row col-md-6">
                    <div class="col-md-7">
                        <h5>Daily Recipe Count As of {{ date('Y-m-d') }}</h5>
                    </div>
                    <div class="col-md-6">
                        <span wire:loading class="spinner-border text-primary" role="status"></span>
                    </div>
                </div>

                <div class="col-md-5">
                <div class="d-flex">
                    <div class="input-group">
                        <input  type="text" class="form-control form-control-sm">
                            <span wire:click="search" class=" btn-primary input-group-text">search</span>
                    </div>
                    <div>
                    </div>
                </div>
            </div>
            </div>
        </div>

    </div>
    <div class="overflow-x-auto" style="display: height: 400px; overflow-x: auto;">
        <table class="table min-w-full table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Recipe Name</th>
                                <th>Category</th>
                                <th>Default QTY</th>
                                <th>Available QTY</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse ($branchMenuRecipes as $recipe)
                                <tr>
                                    <td>{{ $recipe->menu->menu_name }}</td>
                                    <td>{{ $recipe->menu->category->category_name ?? 'N/A' }}</td>
                                    <td>{{ $recipe->default_qty }}</td>
                                    <td>{{ $recipe->bal_qty ?? 0}}</td>
                                    <td>
                                        <a href="" class="btn btn-sm btn-link">Edit</a>
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
