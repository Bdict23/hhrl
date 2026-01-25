<div>
     @if (session()->has('success'))
        <div class="alert alert-success" id="success-message">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
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
                        <span wire:loading class="spinner-border text-primary" role="status"></span>
                    </div>
                </div>

                <div class="col-md-5">
                <div class="d-flex">
                    <div class="input-group">
                        <input  type="text" class="form-control form-control-sm" id="searchInput" onkeyup="searchRecipe()">
                            <span class=" btn-primary input-group-text">search</span>
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
                        <tbody id="branchRecipeTable">

                            @forelse ($branchMenuRecipes as $recipe)
                                <tr>
                                    <td>{{ $recipe->menu->menu_name }}</td>
                                    <td>{{ $recipe->menu->category->category_name ?? 'N/A' }}</td>
                                    <td>{{ $recipe->default_qty }}</td>
                                    <td>{{ $recipe->bal_qty ?? 0}}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editQtyModal" wire:click="$set('selectedRecipeId', {{ $recipe->id }})">Edit</button>
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



        <!-- Edit Quantity Modal -->
        <div class="modal fade" id="editQtyModal" tabindex="-1" aria-labelledby="editQtyModalLabel" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editQtyModalLabel">Edit Available Quantity</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="availableQty" class="form-label">Available Quantity</label>
                            <input type="number" class="form-control" id="availableQty" wire:model="availableQty" min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="updateQuantity">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('refresh', event => {
            // Reset all forms on the page
            var modal = bootstrap.Modal.getInstance(document.getElementById('editQtyModal'));
            modal.hide();
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 1500);
        });

        function searchRecipe() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("branchRecipeTable");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0]; // Assuming search is based on the first column (Recipe Name)
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
        </script>
    


</div>
