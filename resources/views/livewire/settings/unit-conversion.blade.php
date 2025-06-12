<div>
    {{-- return flash message --}}
    @if (session()->has('success'))
    <div class="alert alert-success" id="success-message">
        {{ session('success') }}
        <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif


   <div id="unit-conversion-lists" class="tab-content card" style="display: none" wire:ignore.self>
       <div class="card-header">
           <h5>Unit Conversion Lists</h5>
       </div>
       <div class="card-body">
           {{-- @if (auth()->user()->employee->getModulePermission('Item Unit Conversions') == 1 ) --}}
               <x-primary-button type="button" class="mb-3 btn-sm"
                   onclick="showTab('unit-conversion-form', document.querySelector('.nav-link.active'))">+ Add Unit Conversion</x-primary-button>
           {{-- @endif --}}
           <x-secondary-button type="button" class="mb-3 btn-sm"
               wire:click="fetchData()">Refresh</x-secondary-button>

           <div class="table-responsive mt-3 mb-3 d-flex justify-content-center"
               style="max-height: 400px; overflow-y: auto;">
               <table class="table table-striped table-sm small">
                   <thead class="table-dark sticky-top">
                       <tr>
                           <th>Main Unit</th>
                           <th>Sub Unit</th>
                           <th class="text-end">Conversion Factor</th>
                            <th class="text-end">ACTIONS</th>
                       </tr>
                   </thead>
                   <tbody>
                    @forelse ($unitOfMeasures as $uom)
                        
                        <tr>
                            <td>{{ $uom->From->unit_name }} ( {{ $uom->From->unit_symbol }} )</td>
                            <td>{{ $uom->To->unit_name }} ( {{ $uom->To->unit_symbol }} )</td>
                            <td class="text-end">{{ $uom->conversion_factor }}</td>
                            <td class="text-end">
                                <x-secondary-button type="button" wire:click="edit({{ $uom->id }})">Edit</x-secondary-button>
                                <x-danger-button type="button" wire:click="delete({{ $uom->id }})">Delete</x-danger-button>
                            </td>
                        </tr>
                    @empty
                        
                        <tr>
                            <td colspan="4" class="text-center">No unit conversions found.</td>
                        </tr>
                    @endforelse

                       
                   </tbody>
               </table>
           </div>
       </div>
   </div>

   {{-- unit-conversion Form --}}
   <div id="unit-conversion-form" class="tab-content card" style="display: none" wire:ignore.self>
       <div class="card-header">
           <h5>Add Unit Conversion</h5>
       </div>
       <div class="card-body">

           <x-secondary-button type="button" class="mb-3"
               onclick="showTab('unit-conversion-lists', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
           <form wire:submit.prevent="storeUnitConversion" id="unit_conversion-form">
               @csrf
               <div class="row">
                   <div class="col-md-6">
                       <div class="mb-3">
                           <label for="unit-conversion_name-input" class="form-label">From this Unit <span
                                   style="color: red;">*</span></label>
                           <select class="form-control" id="sub-classification_id" wire:model="fromUOM">
                               <option value="">Select Main Unit</option>
                                 @forelse ($UOM as $unit)
                                      <option value="{{ $unit->id }}">{{ $unit->unit_name }} ({{ $unit->unit_symbol }})</option>
                              
                                 @empty
                                      <option value="">No units available</option>
                                 @endforelse
                           </select>
                           @error('fromUOM')
                               <span class="text-danger">{{ $message }}</span>
                           @enderror
                       </div>
                   </div>
                   <div class="col-md-6">
                       <div class="mb-3">
                           <label for="unit-conversion_name-input" class="form-label">To This Unit<span
                                   style="color: red;">*</span></label>
                                   <select class="form-control" id="sub-unit-id" wire:model="toUOM">
                                    <option value="">Select Sub Unit</option>
                                    @forelse ($UOM as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }} ({{ $unit->unit_symbol }})</option>
                                        
                                    @empty
                                        
                                        <option value="">No units available</option>
                                    @endforelse
                                   
                                </select>
                            @error('toUOM')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                       </div>
                   </div>

               </div>
               <div class="mb-3">
                   <label for="sub-classification_description-input" class="form-label">Conversion Factor <span style="color: red;">*</span></label>
                   <input type="number" class="form-control" id="sub-classification_description-input" wire:model="conversionFactor" step="any" min="0.001" placeholder="Enter conversion factor">
                   @error('conversionFactor')
                       <span class="text-danger">{{ $message }}</span>
                   @enderror
               </div>
               <x-primary-button type="submit">Save</x-primary-button>
           </form>
       </div>
   </div>



       {{-- Update Category Modal --}}
       <div class="modal fade" id="UpdateSubClass" tabindex="-1" aria-labelledby="updateCategoryModal" aria-hidden="true" wire:ignore.self>
           <div class="modal-dialog">
               <div class="modal-content">
                   <div class="modal-header">
                       <h5 class="modal-title" >Update Sub-Classification</h5>
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                   </div>
                   <div class="modal-body">
                       <div>
                           <label for="name" class="form-label">Parent Classification <span
                                   style="color: red;">*</span></label>
                           <select class="form-control" id="classification_id-update" wire:model="classification_id">
                            
                           </select>
                           @error('classification_id')
                               <span class="text-danger">{{ $message }}</span>
                           @enderror
                       </div>
                           <div class="row">
                               <div class="col-md-12 mb-3">
                                   <label for="subclass_name-update" class="form-label">Sub-Classification Name</label>
                                   <input type="text" class="form-control" id="subclass_name-update-input" wire:model="classification_name">
                                   @error('classification_name')

                                       <span class="text-danger">{{ $message }}</span>
                                   @enderror
                               </div>
                           </div>
                               <div class=" mb-3">
                                   <label for="category_description-update" class="form-label">Description</label>
                                   <textarea class="form-control" id="subclass_description-update-input" wire:model="classification_description" rows="3"></textarea>
                                   @error('classification_description')
                                       <span class="text-danger">{{ $message }}</span>
                                   @enderror
                               </div>

                           <x-primary-button type="button" wire:click="updateSubClassification">Update</x-primary-button>
                   </div>
               </div>
           </div>
       </div>


   <script>
       document.addEventListener('DOMContentLoaded', function () {
           window.addEventListener('clearSubclassForm', function (event) {
               console.log('Form cleared subclassification');
               document.getElementById('sub-classification_id').value = '';
               document.getElementById('sub-classification_name-input').value = '';
               document.getElementById('sub-classification_description-input').value = '';
               // back to subclass lists tab
               document.getElementById('sub-classification-table').style.display = 'block';
               document.getElementById('sub-classification-form').style.display = 'none';
               setTimeout(function () {
                   var successMessage = document.getElementById('success-message');
                   if (successMessage) {
                       successMessage.style.display = 'none';
                   }
               }, 1500);
           });

           window.addEventListener('unitConversionAdded', function (event) {
            
            //   reset form fields
               document.getElementById('unit_conversion-form').reset(); // Reset the form fields
               setTimeout(function () {
               var successMessage = document.getElementById('success-message');
               if (successMessage) {
                   successMessage.style.display = 'none';
               }
           }, 1500);

           //close the modal
           let modal = bootstrap.Modal.getInstance(document.getElementById('UpdateSubClass'));
           modal.hide();
           });

       });
           function UpdateSubClassField($data){
               // Set the values of the input fields in the modal
               console.log($data);
               document.getElementById('classification_id-update').value = $data.class_parent;
               document.getElementById('subclass_name-update-input').value =$data.classification_name;
               document.getElementById('subclass_description-update-input').value = $data.classification_description;

           }
   </script>

</div>
