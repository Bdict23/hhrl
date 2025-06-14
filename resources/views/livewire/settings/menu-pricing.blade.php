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
        <div class="d-flex justify-content-between align-items-center mb-2">
            <select name="" id="" class="form-select" style="width: min-content">Category
                <option value="">All</option>
                @forelse ($categories as $category)
                    <option value="{{ $category->id }}" @if ($category->id == $selectedCategory) selected @endif>
                        {{ $category->category_name }}
                    </option>
                @empty      
                    <option value="" disabled>No categories available</option>
                @endforelse
           </select>
           <x-secondary-button type="button" class="mb-2 btn-sm"
           wire:click="fetchData()">Refresh</x-secondary-button>
        </div>
           <div class="table-responsive mt-3 mb-3 d-flex justify-content-center"
               style="max-height: 400px; overflow-y: auto;">
               <table class="table table-striped table-sm small">
                   <thead class="table-dark sticky-top">
                       <tr>
                           <th>Recipe</th>
                           <th>CODE</th>
                           <th>Type</th>
                           <th>Category</th>
                           <th>COST</th>
                           <th>SELLING RATE</th>
                           <th>Action</th> 
                       </tr>
                   </thead>
                   <tbody>
                      {{-- populate --}}
                      @forelse ($menus as $index => $recipe)
                            <tr>
                                <td>{{ $recipe->menu_name }}</td>
                                <td>{{ $recipe->menu_code }}</td>
                                <td>{{ $recipe->recipe_type }}</td>
                                <td>{{ $recipe->category ? $recipe->category->category_name : 'N/A' }}</td>
                                <td>{{ number_format($recipestWithTotalCost[$index]['total_cost'] ?? 0, 2) }}</td>
                                <td>{{ number_format($recipe->mySRP->amount ?? 0, 2) }}</td>
                                <td class="d-flex">
                                <button 
                                    data-bs-toggle="modal"
                                    data-bs-target="#trendModal"
                                    class="btn btn-primary btn-sm text-smaller">
                                    Trend
                                </button>
                                <button wire:click="selectedMenuToUpdate({{ $recipe->id }})"
                                    class=" btn btn-outline-primary btn-sm"
                                    type="button"
                                    style="font-size: smaller;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#addMenuCostModal2">
                                    Update Cost
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




   {{-- update cost modal --}}

   <div class="modal fade" id="addMenuCostModal2" tabindex="-1" aria-labelledby="addCostModalLabel" aria-hidden="true" wire:ignore.self>
       <div class="modal-dialog">
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title" id="addCostModalLabel">Update Cost</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                   <form action="" wire:submit.prevent="addNewMenuCost" id="addCostForm">
                       <div class="mb-3">
                           <label for="cost_amount" class="form-label">Cost Amount</label>
                           <input type="number" step="0.01" class="form-control" id="cost_amount" wire:model="menu_cost_amount" placeholder="Enter cost amount">
                           @error('menu_cost_amount')
                               <span class="text-danger">{{ $message }}</span>
                           @enderror
                       </div>
                       <x-primary-button type="submit">Update Cost</x-primary-button>
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
