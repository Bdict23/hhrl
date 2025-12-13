<div>
   {{-- return flash message --}}
   @if (session()->has('success'))
   <div class="alert alert-success" id="success-message">
       {{ session('success') }}
       <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
   </div>
   @endif

    <div id="discount-table" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Discount List</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    @if (auth()->user()->employee->getModulePermission('discounts'))
                        <x-primary-button type="button" class="mb-3 btn-sm"
                        onclick="showTab('discount-form', document.querySelector('.nav-link.active'))">+ ADD DISCOUNT</x-primary-button>
                    @endif
                    <x-secondary-button type="button" class="mb-3 btn-sm"
                        wire:click="fetchDiscounts()">Refresh</x-secondary-button>
                </div>
                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Search</span>
                        <input type="text" class="form-control" id="search-discount"
                            onkeyup="filterDiscounts()">
                    </div>
                </div>
            </div>
            <script>
                function filterDiscounts() {
                    const input = document.getElementById('search-discount');
                    const filter = input.value.toLowerCase();
                    const table = document.querySelector('#discount-table table');
                    const trs = table.querySelectorAll('tbody tr');

                    trs.forEach(row => {
                        // Skip "No brand found" row
                        if (row.children.length < 2) return;
                        const name = row.children[0].textContent.toLowerCase();
                        const desc = row.children[1].textContent.toLowerCase();
                        if (name.includes(filter) || desc.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }
            </script>
            <div class="table-responsive mt-3 mb-3 d-flex justify-content-center"
                style="max-height: 600px; overflow-y: auto;">

                <table class="table table-striped table-sm small">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>TITLE</th>
                            <th>DESCRIPTION</th>
                            <th class="text-end">RATE/VALUE</th>
                            <th class="text-end">TYPE</th>
                            <th class="text-end">STATUS</th>
                            @if (auth()->user()->employee->getModulePermission('Discounts'))
                                <th class="text-end">ACTIONS</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($discounts ?? [] as $discount)
                            <tr>
                                <td>{{ $discount->title ?? 'Not Registered' }}</td>
                                <td> {{ $discount->description }}</td>
                                <td class="text-end">{{ $discount->amount == '0.00' ? $discount->percentage . '%' : '₱' . $discount->amount  }}</td>
                                <td class="text-end">{{ $discount->type }}</td>
                                <td class="text-end">{{ $discount->status }}</td>
                                @if (auth()->user()->employee->getModulePermission('Discounts'))
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-primary btn-sm" onclick="editDiscount({{ json_encode($discount) }})" data-bs-toggle="modal" data-bs-target="#updateDiscountModal" wire:click="editDiscount({{ $discount->id }})">Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger btn-sm" wire:click="deactivateDiscount({{ $discount->id }})">Disable</a>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No discount found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- discount form -->
    <div id="discount-form" class="tab-content card" style="display: none;" wire:ignore.self>
        <div class="card-header">
            <h5>Add Discount</h5>
        </div>
        <div class="card-body">

            <x-secondary-button type="button" class="mb-3"
                onclick="showTab('discount-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
            <form wire:submit.prevent="storeDiscount" id="discountForm">
                <div class="mb-3 row">
                    <div class="mb-3 col-md-6">
                        <label for="discount_title-input_add" class="form-label">Discount Title <span
                                style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="discount_title-input_add" wire:model="discount_title">
                        @error('discount_title')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                     <div class="mb-3 col-md-6">
                        <label for="add_code_checkbox" class="form-label"><span
                                style="color: rgb(110, 110, 110);">(Optional)</span></label>
                            <div class="input-group mb-3">         
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="checkbox" id="add_code_checkbox" wire:model.live="add_code">
                                    <label class="form-check-label ms-2" for="add_code_checkbox">Add Code</label>
                                </div>
                                <input type="text" class="form-control" id="discount_code-input" wire:model="discount_code" @if(!$add_code) disabled @endif>
                                @error('discount_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label for="discount_description-input" class="form-label">Description</label>
                            <textarea class="form-control" id="discount_description-input" wire:model="discount_description" rows="3"></textarea>
                            @error('discount_description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row col-md-12">
                            <div class="mb-3 col-md-4">
                                <label for="discount_title-input_add" class="form-label">Rate / Value<span style="color: red;">*</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="discount_title-input_add" wire:model="discount_ratevalue">
                                    <select name="disc_type" class="form-select form-select-sm" wire:model.live="discount_ratevalue_type" >
                                        <option value="Percentage">%</option>
                                        <option value="Amount">₱</option>
                                    </select>
                                    
                                </div>
                                @error('discount_ratevalue')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="discount_type-input" class="form-label">Type <span style="color: red;">*</span></label>
                                <select name="disc_type" class="form-select form-select-sm" wire:model.live="discount_type" @if($autoapply_discount) disabled @endif>
                                    @if($autoapply_discount)
                                        <option value="order" selected>Per Order</option>
                                    @else
                                        <option value="item">Per Item</option>
                                        <option value="order">Per Order</option>
                                    @endif
                                </select>
                                @error('discount_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 ">
                               <label for="autoapply_checkbox" class="form-label"><span
                                style="color: rgb(110, 110, 110);">(Optional)</span></label>
                               <div class="input-group mb-3">
                                 <label class="form-check-label ms-2 input-group-text" for="autoapply_checkbox">Auto Apply</label>
                                <div class="input-group-text">
                                  <input class="form-check-input" type="checkbox" id="autoapply_checkbox" wire:model.live="autoapply_discount">
                                </div> 
                               </div>
                          </div>
                        </div>
                       
                    <div class="row p-2 rounded" style="background-color: #f8f9fa;">
                          <div class="col-md-2">
                            <input class="form-check-input mt-0" type="checkbox" id="add_period_checkbox" wire:model.live="add_period">
                            <label class="form-check-label ms-2" for="add_period_checkbox">Add Period</label>
                          </div>
                        <div class="col-md-5">
                            <label for="discount_amount-input" class="form-label">Period Start</label>
                            <input type="date" class="form-control" id="discount_startDate-input" wire:model="discount_startDate" @if(!$add_period) disabled @endif>
                            @error('discount_startDate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-5">
                            <label for="discount_amount-input" class="form-label">Period End</label>
                            <input type="date" class="form-control" id="discount_endDate-input" wire:model="discount_endDate" @if(!$add_period) disabled @endif>
                            @error('discount_endDate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <x-primary-button type="submit">Save</x-primary-button>
            </form>
        </div>
    </div>


    {{-- Update discount Modal --}}
    <div class="modal fade" id="updateDiscountModal" tabindex="-1" aria-labelledby="updateDiscountModalLabel" wire:ignore.self
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateDiscountModalLabel">Update Discount</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                        <div class="mb-3">
                            <label for="discount_title-input_update" class="form-label">Discount Title <span
                                    style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="discount_title-input_update"
                                wire:model="discount_title">
                            @error('discount_title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="discount_description-input_update" class="form-label">Description <span
                                    style="color: red;">*</span></label>
                            <textarea class="form-control" id="discount_description-input_update"
                                wire:model="discount_description" rows="3"></textarea>
                            @error('discount_description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <x-primary-button type="button" wire:click="updateDiscount">Update</x-primary-button>
                </div>
            </div>
        </div>
    </div>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('resetCreateDiscountForm', event => {
            
                 setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
                            }, 2000);
                            document.getElementById('discount-table').style.display = 'block';
                            document.getElementById('discount-form').style.display = 'none';
                            document.getElementById('discountForm').reset();
            });

                window.addEventListener('clearDiscountUpdateModal', event => {
                document.getElementById('discount_title-input_update').value = '';
                document.getElementById('discount_description-input_update').value = '';

                // Hide the success message after 1 second
                setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
                            }, 1500);
                // Hide the modal
                let myModal = bootstrap.Modal.getInstance(document.getElementById('updateDiscountModal'));
                myModal.hide();

            });

        });

        function editDiscount($data){
            // Set the form fields with the data
            document.getElementById('discount_title-input_update').value = $data.discount_title;
            document.getElementById('discount_description-input_update').value = $data.discount_description;

        }
    </script>
</div>
