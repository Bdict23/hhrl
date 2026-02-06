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


    <div class="container mb-3">
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    <span wire:loading class="spinner-border text-primary" role="status"></span>
                </div>
            </div>
            <div class="col-md-6">
                <h4 class="text-end">Recipe Count &nbsp;<i class="bi bi-receipt-cutoff"></i></h4>
            </div>
        </div>
    </div>
    <div class="card  mt-3 mb-3">
        <div class=" card-header d-flex justify-content-between mx-2">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <div class="input-group">
                            <label for="search" class="input-group-text">Search &nbsp;<i class="bi bi-search"></i></label>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by recipe name..." onkeyup="searchRecipe()">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body overflow-x-auto" style="display: height: 400px; overflow-x: auto;">
            <table class="table min-w-full table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Image</th>
                            <th>Recipe Name</th>
                            <th>Category</th>
                            <th >Available QTY</i></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="branchRecipeTable">

                        @forelse ($branchMenuRecipes as $recipe)
                            <tr>
                                <td>
                                    @if($recipe->menu->menu_image)
                                        <img src="{{ asset('images/' . $recipe->menu->menu_image) }}" alt="{{ $recipe->menu->menu_image }}" width="50" height="50">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>{{ $recipe->menu->menu_name }}</td>
                                <td>{{ $recipe->menu->category->category_name ?? 'N/A' }}</td>
                                <td style=" text-decoration-line: underline; color: rgb(0, 106, 255); cursor: pointer;" 
                                wire:click="editRecipe({{ $recipe->id }})" data-bs-toggle="modal" data-bs-target="#editQtyModal">
                                    {{ $recipe->bal_qty ?? 0}}  <i class="bi bi-pencil-square"></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#recipeCardexModal" wire:click="viewCardex({{ $recipe->menu_id }})">View Cardex</button>
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


        {{--cardex modal --}}
        <div class="modal fade" id="recipeCardexModal" tabindex="-1" aria-labelledby="recipeCardexModalLabel" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="recipeCardexModalLabel">Recipe Cardex</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div style="height: 400px; overflow-x: auto;">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Transaction</th>
                                        <th>In</th>
                                        <th>Out</th>
                                        <th>Balance</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($cardexDetails ?? [] as $detail)
                                        <tr>
                                            <td>{{ $detail->created_at->format('M d, Y') }}</td>
                                            <td>{{ $detail->transaction_type }}</td>
                                            <td>{{ intval($detail->qty_in) }}</td>
                                            <td>{{ intval($detail->qty_out) }}</td>
                                            <td>{{ intval($detail->balance) }}</td>
                                            <td>@if($detail->qty_in != 0){{ $detail->adjustment->reference }}@else 
                                                {{ $detail->order->invoice->first()->reference ?? 'N/A' }} 
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No transactions found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- update qty modal --}}
        <div class="modal fade" id="editQtyModal" tabindex="-1" aria-labelledby="editQtyModalLabel" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editQtyModalLabel">Add Quantity</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="availableQty" class="form-label">Enter Additional Quantity</label>
                            <input type="number" class="form-control" id="availableQty" wire:model="additionalQTY" min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" wire:click="updateQuantity">Save changes</button>
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
                td = tr[i].getElementsByTagName("td")[1]; // Assuming search is based on the second column (Recipe Name)
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

        window.addEventListener('success', event=>{
            Swal.fire({
                        icon: 'success',
                        title: 'Quantity Successfully Added!',
                        showConfirmButton: false,
                        timer: 1500
                    });
        });
        </script>
    


</div>
