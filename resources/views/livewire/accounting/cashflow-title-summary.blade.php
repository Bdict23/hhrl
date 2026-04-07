<div class="overflow-x-auto">
    <div class="container mb-3">
            <div class="row">
                <div class="col-md-6">
                    @if (auth()->user()->employee->getModulePermission('Cash Flow Titles') == 1)
                        <x-primary-button x-on:click="$openModal('cardModal')">+ Create</x-primary-button>
                        <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4 class="text-end">Cash Flow Titles - Summary <i class="bi bi-file-text"></i></h4>
                </div>
            </div>
        </div>
        <div class="card mt-3 mb-3">  
            <div class=" card-header align-items-end mx-2">
                <div class="input-group">
                    <span class="input-group-text">Search</span>
                    <input type="text" class="form-control" placeholder="Search ...">
                </div>
            </div>


        <div class="card-body ">
                <div style="height: 500px; overflow-x: auto; display: block;">
                    <table class="table table-striped table-hover table-sm " >
                        <thead class="table-dark">
                            <tr>
                                <th>Business Unit</th>
                                <th>Branch</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cashFlowsTitles as $title)
                                <tr>
                                    <td>{{ $title->branch->company->company_name }}</td>
                                    <td>{{ $title->branch->branch_name }}</td>
                                    <td>{{ $title->title }}</td>
                                    <td>{{ $title->description }}</td>
                                    <td>{{ $title->status }}</td>
                                    <td>{{ $title->created_by }}</td>
                                    <td>{{ $title->type }}</td>
                                    <td>
                                        <div class="flex gap-2">
                                            <x-secondary-button
                                            x-on:click="$wire.edit({{ $title->id }})">
                                               <span wire:loading wire:target="edit({{ $title->id }})"><span class="spinner-border spinner-border-sm"></span> wait</span>
                                                  <span wire:loading.remove wire:target="edit({{ $title->id }})">Edit</span>
                                            </x-secondary-button>
                                            <x-toggle id="color-positive-{{ $title->id }}" 
                                                name="toggle" 
                                                :label="$title->status === 'ACTIVE' ? 'Active' : 'Inactive'" 
                                                positive xl 
                                               x-on:click="$wire.changeStatus({{ $title->id }})"
                                                :checked="$title->status === 'ACTIVE'"
                                                />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No records found</td>
                                </tr>
                            @endforelse
                    
                        </tbody>
                    </table>
                </div>
        </div>
    </div>


    {{-- create modal --}}

        
            <x-modal-card title="New Cashflow Title" name="cardModal">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-select
                    label="Search a BUSINESS UNIT"
                    placeholder="Select BU"
                    :options="$businessUnits"
                    option-label="company_name"
                    option-value="id"
                    wire:model.live="selectedBusinessUnitId"
                    min-items-for-search="0"
                />
        
                <x-select
                    label="Search a BRANCH"
                    placeholder="Select Branch"
                    :options="$branches"
                    option-label="branch_name"
                    option-value="id"
                    wire:model.live="selectedBranchId"
                    min-items-for-search="0"
                />
                <x-input label="Title" placeholder="Enter title" wire:model.live="titleName" />
                <x-select
                        label="Select Type"
                        placeholder="Select type"
                        :options="['COLLECTION', 'LESS']"
                        wire:model.live="type"
                    />

                <div class="col-span-1 sm:col-span-2">
                    <x-textarea label="Description" placeholder="Describe the cashflow title" wire:model.live="description"/>

                </div>
        
            </div>
        
            <x-slot name="footer" class="flex justify-between gap-x-4">
        
                <div class="flex gap-x-4">
                    <x-button flat label="Cancel" x-on:click="close" />
        
                   <x-primary-button wire:loading.attr="disabled" wire:click="save" wire:loading.attr="disabled">
                        <span wire:loading wire:target="save"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>&nbsp;Saving...</span>
                        <span wire:loading.remove wire:target="save">Save</span>
                 </x-primary-button>
                </div>
            </x-slot>
        </x-modal-card>

        {{-- update modal --}}
        <x-modal-card title="Edit Cashflow Title" name="updateCardModal">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-select
                    label="Search a BUSINESS UNIT"
                    placeholder="Select BU"
                    :options="$businessUnits"
                    option-label="company_name"
                    option-value="id"
                    wire:model.live="selectedBusinessUnitId"
                    min-items-for-search="0"
                />
        
                <x-select
                    label="Search a BRANCH"
                    placeholder="Select Branch"
                    :options="$branches"
                    option-label="branch_name"
                    option-value="id"
                    wire:model.live="selectedBranchId"
                    min-items-for-search="0"
                />
                <x-input label="Title" placeholder="Enter title" wire:model.live="titleName" />
                <x-select
                        label="Select Type"
                        placeholder="Select type"
                        :options="['COLLECTION', 'LESS']"
                        wire:model.live="type"
                    />

                <div class="col-span-1 sm:col-span-2">
                    <x-textarea label="Description" placeholder="Describe the cashflow title" wire:model.live="description"/>

                </div>
        
            </div>
        
            <x-slot name="footer" class="flex justify-between gap-x-4">
        
                <div class="flex gap-x-4">
                    <x-button flat label="Cancel" x-on:click="$dispatch('close')" />
        
                   <x-primary-button wire:loading.attr="disabled" wire:click="update" wire:loading.attr="disabled">
                        <span wire:loading wire:target="update"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>&nbsp;Updating...</span>
                        <span wire:loading.remove wire:target="update">Update</span>
                 </x-primary-button>
                </div>
            </x-slot>
        </x-modal-card>
        
        <script>
            window.addEventListener('showAlert', event => {
                const data = event.detail[0];
                Swal.fire({
                    icon: data.type,
                    text: data.message,
                    title: data.title,
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        </script>

        <x-notifications />


    </div>



