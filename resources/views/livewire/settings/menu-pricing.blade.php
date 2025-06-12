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
           <h5>Menu Price Lists</h5>
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
                           <th>SRP</th>
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
                                <td>{{ number_format($recipe->srp, 2) }}</td>
                                <td class="d-flex">
                                    <button 
                                    data-bs-toggle="modal"
                                    data-bs-target="#trendModal"
                                    class="btn btn-primary btn-sm text-smaller">
                                    Trend
                                </button>
                                <button 
                                class=" btn btn-outline-primary btn-sm"
                                type="button"
                                style="font-size: smaller;"
                                data-bs-toggle="modal"
                                data-bs-target="#addCostModal">
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



   {{-- Update Modal --}}
<div class="modal fade" id="UpdateVenue" tabindex="-1" aria-labelledby="updateVenueModal" aria-hidden="true" wire:ignore.self>
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" >Update Venue</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form action="" wire:submit.prevent="updateVenue" id="UpdateVenueForm">
                   <div class="row">
                       <div class="col-md-12 mb-3">
                           <label for="venue_name-update" class="form-label">Venue Name</label>
                           <input type="text" class="form-control" id="venue_name-update-input" wire:model="venue_name_input">
                           @error('venue_name_input')
                               <span class="text-danger">{{ $message }}</span>
                           @enderror
                       </div>
                       <div class="row">
                           <div class="col-md-4 mb-3">
                               <label for="venue_code-update" class="form-label">Venue Code</label>
                               <input type="text" class="form-control" id="venue_code-update-input" wire:model="venue_code_input">
                               @error('venue_code_input')
                                   <span class="text-danger">{{ $message }}</span>
                               @enderror
                           </div>
                           <div class="col-md-4 mb-3">
                               <label for="capacity-update" class="form-label">Capacity</label>
                               <input type="number" class="form-control" id="capacity-update-input" wire:model="capacity_input">
                               @error('capacity_input')
                                   <span class="text-danger">{{ $message }}</span>
                               @enderror
                           </div>
                           <div class="col-md-4 mb-3">
                               <label for="venue_rate-update" class="form-label">Rate <span class="text-danger">*</span></label>
                               <input type="number" step="0.01" class="form-control" id="venue_rate-update-input" wire:model="venue_rate_input">
                               @error('venue_rate_input')
                                   <span class="text-danger">{{ $message }}</span>
                               @enderror
                           </div>
                       </div>
                       
                   </div>
                       <div class=" mb-3">
                           <label for="venue_description-update" class="form-label">Description</label>
                           <textarea class="form-control" id="venue_description-update-input" wire:model="venue_description_input" rows="3"></textarea>
                           @error('category_description_input')
                               <span class="text-danger">{{ $message }}</span>
                           @enderror
                       </div>

                       <x-primary-button type="submit">Update</x-primary-button>
                   </form>
           </div>
       </div>
   </div>
</div>

   {{-- Category Form --}}
   <div id="venue-form" class="tab-content card" style="display: none;" wire:ignore.self>
       <div class="card-header">
           <h5>Add Venue</h5>
       </div>
       <div class="card-body">
           <x-secondary-button type="button" class="mb-3 btn-sm"
               onclick="showTab('venue-lists', document.querySelector('.nav-link.active'))">Summary</x-secondary-button>
           <form wire:submit.prevent="storeVenue" id="venueForm">
               @csrf
               <div class="mb-3">
                   <label for="venue_name-input" class="form-label">Venue Name <span style="color: red;">*</span></label>
                   <input type="text" class="form-control" id="venue_name-input" wire:model="venue_name_input" >
                   @error('venue_name_input')
                       <span class="text-danger">{{ $message }}</span>
                   @enderror
               </div>
               <div class="row mb-3">
                   <div class="col-md-4 mb-3">
                       <label for="venue_code-input" class="form-label">Venue Code <span style="color: red;">*</span></label>
                       <input type="text" class="form-control" id="venue_code-input" wire:model="venue_code_input" >
                       @error('venue_code_input')
                           <span class="text-danger">{{ $message }}</span>
                       @enderror
                   </div>
                   <div class="col-md-4 mb-3">
                       <label for="capacity-input" class="form-label">Capacity <span style="color: red;">*</span></label>
                       <input type="number" class="form-control" id="capacity-input" wire:model="capacity_input" >
                       @error('capacity_input')
                           <span class="text-danger">{{ $message }}</span>
                       @enderror
                   </div>
                   <div class="col-md-4 mb-3">
                               <label for="venue_rate" class="form-label">Rate Price</label>
                               <input type="number" step="0.01" class="form-control" id="venue_rate-input" wire:model="venue_rate_input">
                               @error('venue_rate_input')
                                   <span class="text-danger">{{ $message }}</span>
                               @enderror
                           </div>
               </div>
               <div class="mb-3">
                   <label for="venue_description-input" class="form-label">Description <span style="color: red;">*</span></label>
                   <textarea class="form-control" id="venue_description-input-update" wire:model="venue_description_input" rows="3" ></textarea>
                   @error('venue_description_input')
                       <span class="text-danger">{{ $message }}</span>
                   @enderror
               </div>
               <x-primary-button type="submit">Save</x-primary-button>
           </form>
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
