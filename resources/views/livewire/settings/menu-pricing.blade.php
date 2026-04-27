<div>
    {{-- return flash message --}}
    @if (session()->has('success'))
        <div class="alert alert-success" id="success-message">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger" id="success-message">
            {{ session('error') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div id="recipe-pricing-list" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Recipe Price Lists</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3 d-flex justify-content-start align-items-center">
                        <select name="" id="" class="form-select" style="width: min-content">Category
                            <option value="">All</option>
                            @forelse ($categories ?? [] as $category)
                                <option value="{{ $category->id }}" @if ($category->id == $selectedCategory) selected @endif>
                                    {{ $category->category_name }}
                                </option>
                            @empty
                                <option value="" disabled>No categories available</option>
                            @endforelse
                        </select>
                        <x-secondary-button type="button" class="btn-sm"
                            wire:click="fetchData()">Refresh</x-secondary-button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 input-group">
                        <span class="input-group-text">Search</span>
                        <input type="text" class="form-control" id="search-recipe" onkeyup="filterRecipes()">
                    </div>
                </div>
            </div>
            <script>
                function filterRecipes() {
                    const input = document.getElementById('search-recipe');
                    const filter = input.value.toLowerCase();
                    const table = document.querySelector('#recipe-pricing-list table');
                    const trs = table.querySelectorAll('tbody tr');

                    trs.forEach(row => {
                        // Skip "No recipes found" row
                        if (row.children.length < 2) return;
                        const name = row.children[0].textContent.toLowerCase();
                        const code = row.children[1].textContent.toLowerCase();
                        if (name.includes(filter) || code.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }
            </script>
            <div class="mt-3 mb-3 table-responsive d-flex justify-content-center"
                style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-sm small">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Recipe</th>
                            <th>CODE</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>COST</th>
                            <th>SELL RATE</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- populate --}}
                        @forelse ($menus ?? [] as $index => $recipe)
                            <tr>
                                <td>{{ $recipe->menu_name }}</td>
                                <td>{{ $recipe->menu_code }}</td>
                                <td>{{ $recipe->recipe_type }}</td>
                                <td>{{ $recipe->category ? $recipe->category->category_name : 'N/A' }}</td>
                                <td>{{ number_format($recipestWithTotalCost[$index]['total_cost'] ?? 0, 2) }}</td>
                                <td>{{ $recipe->mySRP->amount ?? '0.00' }}</td>
                                <td class="d-flex">
                                    <button wire:click="viewPriceTrend({{ $recipe->id }})" data-bs-toggle="modal"
                                        data-bs-target="#priceTrendModal" class="btn btn-primary btn-sm text-smaller">
                                        Trend
                                    </button>
                                    <button wire:click="selectedMenuToUpdate({{ $recipe->id }})"
                                        class=" btn btn-outline-primary btn-sm" type="button"
                                        style="font-size: smaller;" data-bs-toggle="modal"
                                        data-bs-target="#addMenuCostModal2">
                                        Update Selling Price
                                    </button>
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
    <!-- Right Column: Cost Trend Chart with Filters -->
    <div class="modal modal-xl " id="priceTrendModal" tabindex="-1" aria-labelledby="priceTrendModalLabel"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="flex gap-2 align-items-center">
                        <h5 class="mb-0 card-title">Selling Trend</h5>
                        <div class="gap-2 d-flex">
                            <select wire:model="chartYear" class="form-select form-select-sm" style="width: 100px;">
                                <option value="">All Years</option>
                                @foreach ($availableYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            <select wire:model="chartMonth" class="form-select form-select-sm" style="width: 120px;">
                                <option value="">All Months</option>
                                @foreach ($months as $num => $name)
                                    <option value="{{ $num }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body position-relative" style="height: 400px; min-height: 400px;">
                        @if ($chartLoading)
                            <div class="chart-overlay">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        @endif
                        @if (empty($chartData))
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <div class="text-muted">
                                    @if ($selectedMenuId)
                                        Cost of {{ now()->format('F Y') }} not available
                                    @else
                                        Select a menu to view cost trend
                                    @endif
                                </div>
                            </div>
                        @endif
                        <canvas id="menuCostChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- update cost modal --}}

    <div class="modal fade" id="addMenuCostModal2" tabindex="-1" aria-labelledby="addCostModalLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCostModalLabel">Update Selling Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" wire:submit.prevent="addNewMenuCost" id="addCostForm">
                        <div class="mb-3">
                            <label for="cost_amount" class="form-label">Selling Price</label>
                            <input type="number" step="0.01" class="form-control" id="cost_amount"
                                wire:model="menu_cost_amount" placeholder="Enter selling price">
                            @error('menu_cost_amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <x-primary-button type="submit">Update</x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Listen for the DOMContentLoaded event
        document.addEventListener('DOMContentLoaded', function() {
            // Listen wire:success event
            window.addEventListener('clearForm', event => {
                // Clear the form fields
                document.getElementById('venueForm').reset();
            });

            // Show the venue lists tab by default
            // showTab('venue-lists', document.querySelector('.nav-link.active'));

            // Listen for the success event
            window.addEventListener('success', event => {
                // Show the success message
                document.getElementById('success-message').style.display = 'block';
                document.getElementById('success-message').innerHTML = event.detail.message;

                // Hide the success message after 1 second
                setTimeout(function() {
                    document.getElementById('success-message').style.display = 'none';
                }, 1500);
            });
        });

        // HIDE UPDATEcATEGORY MODAL
        window.addEventListener('hideUpdateVenueModal', event => {
            // Clear the form fields
            document.getElementById('venue_name-update-input').value = '';
            document.getElementById('venue_description-update-input').value = '';
            document.getElementById('venue_code-update-input').value = '';
            document.getElementById('capacity-update-input').value = '';
            document.getElementById('venue_rate-update-input').value = '';
            // Hide the modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('UpdateVenue'));
            modal.hide();

            // Hide the success message after 1 second
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 1500);
        });

        function updateVenue($data) {
            // Set the values of the input fields
            console.log($data);
            document.getElementById('venue_name-update-input').value = $data.venue_name;
            document.getElementById('venue_description-update-input').value = $data.description;
            document.getElementById('venue_code-update-input').value = $data.venue_code;
            document.getElementById('capacity-update-input').value = $data.capacity;
            document.getElementById('venue_rate-update-input').value = $data.rate_price ? $data.rate_price.amount : '';


        }
    </script>
</div>
