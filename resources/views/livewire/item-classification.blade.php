<div>
    {{-- return flash message --}}
    @if (session()->has('success'))
    <div class="alert alert-success" id="success-message">
        {{ session('success') }}
        <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div id="classification-table" class="tab-content card" style="display: none" wire:ignore.self>
        <div class="card-header">
            <h5>Classification</h5>
        </div>
        <div class="card-body">
            @if (auth()->user()->employee->getModulePermission('Item Classifications') == 1 )
                <x-primary-button type="button" class="mb-3"
                    onclick="showTab('classification-form-create', document.querySelector('.nav-link.active'))">+ Add
                    Classification</x-primary-button>
            @endif
            
            <x-secondary-button type="button" class="mb-3"
                wire:click="fetchData()">Refresh</x-secondary-button>
            <div class="mb-3">
            <div class="table-responsive mt-3 mb-3 d-flex justify-content-center"
                style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-sm small">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>Name</th>
                            <th>DESCRIPTION</th>
                            <th class="text-end">STATUS</th>
                            <th class="text-end">Sub Classes</th>
                            <th class="text-end">REG. COMPANY</th>
                            @if (auth()->user()->employee->getModulePermission('Item Classifications') == 1 )
                                <th class="text-end">ACTIONS</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($classifications as $classification)
                            <tr>
                                <td>{{ $classification->classification_name }}</td>
                                <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $classification->classification_description }}</td>
                                <td class="text-end">{{ $classification->status }}</td>
                                <td class="text-end">
                                    {{ optional($classification->sub_classifications)->count() ?? 0 }}
                                </td>
                                <td class="text-end">
                                    {{ $classification->company->company_name ?? 'Not Registered' }}</td>
                                @if (auth()->user()->employee->getModulePermission('Item Classifications') == 1 )
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#classificationUpdate" onclick="updateClassification({{ json_encode($classification) }})" wire:click="editClassification({{ $classification->id }})">Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger btn-sm" wire:click="deactivate({{ $classification->id }})">Delete</a>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No classification found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    {{-- Classification Form --}}
    <div id="classification-form-create" class="tab-content card" style="display: none" wire:ignore.self>
        <div class="card-header">
            <h5>Add Classification</h5>
        </div>
        <div class="card-body">
            <x-secondary-button type="button" class="mb-3"
                onclick="showTab('classification-table', document.querySelector('.nav-link.active'))">Summary</x-secondary-button>
            <form wire:submit.prevent="store">
                @csrf
                <div class="mb-3">
                    <label for="classification_name-input" class="form-label">Classification Name <span
                            style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="classification_name-input" wire:model="classification_name">
                    @error('classification_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="classification_description-input" class="form-label">Description <span style="color: red;">*</span></label>
                    <textarea class="form-control" id="classification_description-input" wire:model="classification_description" rows="3"></textarea>
                    @error('classification_description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <x-primary-button type="submit">Save</x-primary-button>
            </form>
        </div>
    </div>

    {{-- update classification modal --}}
    <div id="classificationUpdate" class="modal fade" tabindex="-1" aria-labelledby="updateClassificationModal" aria-hidden="true" wire:ignore.self>
       <div class="modal-dialog">
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title" id="updateClassificationModal">Update Classification</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">

                       <div class="mb-3">
                           <label for="classification_name-update_input" class="form-label">Classification Name <span
                                   style="color: red;">*</span></label>
                           <input type="text" class="form-control" id="classification_name-update_input" wire:model="classification_name">
                           @error('classification_name')
                               <span class="text-danger">{{ $message }}</span>
                           @enderror
                       </div>
                       <div class="mb-3">
                           <label for="classification_description-update_input" class="form-label">Description <span style="color: red;">*</span></label>
                           <textarea class="form-control" id="classification_description-update_input" wire:model="classification_description" rows="3"></textarea>
                           @error('classification_description')
                               <span class="text-danger">{{ $message }}</span>
                           @enderror
                       </div>

                       <x-primary-button type="button" wire:click="updateClassification">Update</x-primary-button>

               </div>
           </div>
       </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('clearForm', event => {
                // Clear the form fields
                document.getElementById('classification_name-input').value = '';
                document.getElementById('classification_description-input').value = '';

                 // Hide the success message after 1 second
                 setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
                            }, 1500);
            });

            window.addEventListener('clearClassificationModalUpdateForm', event => {
                // clear modal form fields
                document.getElementById('classification_name-update_input').value = '';
                document.getElementById('classification_description-update_input').value = '';
                // Hide modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('classificationUpdate'));
                modal.hide();

                // Hide the success message after 1 second
                setTimeout(function() {
                    document.getElementById('success-message').style.display = 'none';
                }, 1500);
            });
        });

        function updateClassification($data) {
            // Set the values of the input fields on modal
            document.getElementById('classification_name-update_input').value = $data.classification_name;
            document.getElementById('classification_description-update_input').value = $data.classification_description;
        }
    </script>

</div>
