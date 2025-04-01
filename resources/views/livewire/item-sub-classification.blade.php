<div>
     {{-- return flash message --}}
     @if (session()->has('success'))
     <div class="alert alert-success" id="success-message">
         {{ session('success') }}
         <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>
     @endif


    <div id="sub-classification-table" class="tab-content card" style="display: none" wire:ignore.self>
        <div class="card-header">
            <h5>Sub Classification Lists</h5>
        </div>
        <div class="card-body">
            <x-primary-button type="button" class="mb-3 btn-sm"
                onclick="showTab('sub-classification-form', document.querySelector('.nav-link.active'))"
                wire:click="showAddSubClassification">+ Add Sub
                Classification</x-primary-button>
            <x-secondary-button type="button" class="mb-3 btn-sm"
                wire:click="fetchData()">Refresh</x-secondary-button>

            <div class="table-responsive mt-3 mb-3 d-flex justify-content-center"
                style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-sm small">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>DESCRIPTION</th>
                            <th class="text-end">STATUS</th>
                            <th class="text-end">Parent Class</th>
                            <th class="text-end">REG. COMPANY</th>
                            <th class="text-end">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($sub_classifications as $sub_classification)
                            <tr>
                                <td>{{ $sub_classification->classification_name ?? 'Not Registered' }}</td>
                                <td>{{ $sub_classification->classification_description }}</td>
                                <td class="text-end">{{ $sub_classification->status }}</td>
                                <td class="text-end">
                                    {{ $sub_classification->classification->classification_name ?? 'Not Registered' }}
                                </td>
                                <td class="text-end">
                                    {{ $sub_classification->classification->company->company_name ?? 'Not Registered' }}
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-primary btn-sm" onclick="UpdateSubClassField({{json_encode($sub_classification)}})" data-bs-toggle="modal" data-bs-target="#UpdateSubClass" wire:click="editSubClassification({{ $sub_classification->id }})" >Edit</button>
                                    <a href="#" class="btn btn-sm btn-danger btn-sm" wire:click="deactivate({{ $sub_classification->id }})">Delete</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No sub classification found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- sub-classification Form --}}
    <div id="sub-classification-form" class="tab-content card" style="display: none" wire:ignore.self>
        <div class="card-header">
            <h5>Add Sub Classification</h5>
        </div>
        <div class="card-body">

            <x-secondary-button type="button" class="mb-3"
                onclick="showTab('sub-classification-table', document.querySelector('.nav-link.active'))">Back</x-secondary-button>
            <form wire:submit.prevent="store">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Parent Classification <span
                                    style="color: red;">*</span></label>
                            <select class="form-control" id="classification_id" wire:model="classification_id">
                                <option value="">Select Parent Classification</option>
                                @forelse ($classifications as $classification)
                                    <option value="{{ $classification->id }}">
                                        {{ $classification->classification_name }}</option>
                                @empty
                                    <option value="">No Parent Classification Found</option>
                                @endforelse
                            </select>
                            @error('classification_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="classification_name-input" class="form-label">Sub Classification Name <span
                                    style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="classification_name-input"
                                wire:model="classification_name">
                            @error('classification_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                </div>
                <div class="mb-3">
                    <label for="classification_description-input" class="form-label">Description <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="classification_description-input" wire:model="classification_description" rows="3"></textarea>
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
                                @forelse ($classifications as $classification)
                                    <option value="{{ $classification->id }}">
                                        {{ $classification->classification_name }}</option>
                                @empty
                                    <option value="">No Parent Classification Found</option>
                                @endforelse
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
            window.addEventListener('clearForm', function (event) {
                console.log('clearForm event triggered');
                document.getElementById('classification_id').value = '';
                document.getElementById('classification_name-input').value = '';
                document.getElementById('classification_description').value = '';
                setTimeout(function () {
                    var successMessage = document.getElementById('success-message');
                    if (successMessage) {
                        successMessage.style.display = 'none';
                    }
                }, 1500);
            });

            window.addEventListener('clearUpdateForm', function (event) {
                document.getElementById('classification_id-update').value = '';
                document.getElementById('subclass_name-update-input').value ='';
                document.getElementById('subclass_description-update-input').value = '';
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
