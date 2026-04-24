

<div class="overflow-x-auto" wire:ignore.self>
    <ul class="nav nav-tabs" id="accountingTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="template-tab" data-bs-toggle="tab" data-bs-target="#template-summary" type="button"
                role="tab" aria-controls="template-summary" aria-selected="true">Account Template</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="account-titles-tab" data-bs-toggle="tab" data-bs-target="#account-titles" type="button"
                role="tab" aria-controls="account-titles" aria-selected="false">
                Account Titles</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="job-order-tab" data-bs-toggle="tab" data-bs-target="#job-order" type="button"
                role="tab" aria-controls="job-order" aria-selected="false">
                Account Types</button>
        </li>
    </ul>
    <div class="tab-content" id="accountingTabContent">
    <div  class=" container tab-pane fade show active" id="template-summary" role="tabpanel" aria-labelledby="template-tab" wire:ignore.self>
            <div class="row">
                <div class="col-md-6">
                    @if(auth()->user()->employee->getModulePermission('Accounting - Template Management') == 1 )
                        <a href="/coa-management?action=new" style="text-decoration: none; color: white;"><x-primary-button >+ New Template</x-primary-button></a>
                        <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4 class="text-end">Transaction Templates - Summary <i class="bi bi-file-text"></i></h4>
                </div>
            </div>
            <div class="card mt-3 mb-3">  
            <div class="card-body ">
                    <div style="height: 500px; overflow-x: auto; display: block;">
                        <table class="table table-striped table-hover table-sm " >
                            <thead class="table-dark">
                                <tr>
                                    <th>Status</th>
                                    <th>Business Unit</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Created By</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($templates as $template)
                                    <tr>
                                        <td><span @if($template->is_active == 1) class= "badge bg-success" @else class= "badge bg-danger" @endif>{{ $template->is_active == 1 ? 'ACTIVE':'INACTIVE'}}</span></td>
                                        <td>{{ $template->company->company_name}}</td>
                                        <td>{{ $template->type->type_name}}</td>
                                        <td>{{ $template->description}}</td>
                                        <td>{{ $template->createdBy->name}} {{ $template->createdBy->last_name}}</td>
                                        <td>{{ $template->created_at->format('M. d, Y')}}</td>
                                        <td>
                                            <a href="">
                                                <x-primary-button> View </x-primary-button>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No Data found</td>
                                    </tr>
                                @endforelse
                        
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div> 

    <div class="container tab-pane fade" id="account-titles" role="tabpanel" aria-labelledby="account-titles-tab" wire:ignore.self>
        <div class="row">
            <div class="col-md-6">
                @if(auth()->user()->employee->getModulePermission('Accounting - Chart of Accounts') == 1 )
                    <x-primary-button x-on:click="$openModal('cardModal')" >+ New Title</x-primary-button>
                    <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                @endif
            </div>
            <div class="col-md-6">
                <h4 class="text-end">Account Titles - Summary <i class="bi bi-file-text"></i></h4>
            </div>
        </div>

        <div class="card mt-3 mb-3">
            <div class="card-body">
                <div style="height: 500px; overflow-x: auto; display: block;">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Account Code</th>
                                <th>Account Title</th>
                                <th>Description</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($accountTitles as $title)
                                <tr>
                                    <td>{{ $title->account_code }}</td>
                                    <td>{{ $title->account_title }}</td>
                                    <td class="truncate max-w-[150px]" title="{{$title->description}}" style="cursor: help">{{  Str::limit($title->description ?? 'N/A' , 30) }}</td>
                                    <td>
                                        <span @if($title->is_active == 1) class="badge bg-success" @else class="badge bg-danger" @endif>
                                            {{ $title->is_active == 1 ? 'ACTIVE':'INACTIVE' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No account titles found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="container tab-pane fade" id="job-order" role="tabpanel" aria-labelledby="job-order-tab" wire:ignore.self>
        <div class="row">
                <div class="col-md-6">
                    @if(auth()->user()->employee->getModulePermission('Accounting - Account Types') == 1 )
                      <x-primary-button x-on:click="$openModal('cardModalType')" >+ New Type</x-primary-button>
                        <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4 class="text-end">Account Types - Summary <i class="bi bi-file-text"></i></h4>
                </div>
            </div>
        <div class="card mt-3 mb-3">
            <div class="card-body">
                <div style="height: 500px; overflow-x: auto; display: block;">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Account Type</th>
                                <th>Description</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($accountTypes as $type)
                                <tr>
                                    <td>{{ $type->type_name }}</td>
                                    <td class="truncate max-w-[150px]" title="{{ $type->description ?? 'N/A' }}" style="cursor: help">{{ Str::limit($type->description ?? 'N/A' , 30) }}</td>
                                    <td>
                                        <span @if($type->is_active == 1) class="badge bg-success" @else class="badge bg-danger" @endif>
                                            {{ $type->is_active == 1 ? 'ACTIVE':'INACTIVE' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No account types found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>


        {{-- wire ui modal --}}
    <x-modal-card title="Account Title - Create" name="cardModal" wire:ignore.self>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-input 
                label="Account Title" 
                placeholder="Title name ..."
                wire:model="accountTitle"
                />
                <x-input 
                    label="Account Code" 
                    placeholder="Enter account code ..."
                    wire:model="accountCode"
                    />
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-select 
                label="Parent Title" 
                placeholder="Select parent title ..."
                :options="$accountTitles"
                option-value="id"
                :min-items-for-search="0"
                option-label="account_title"
                wire:model="parentTitle"
                />
                <x-select 
                label="Account Type" 
                placeholder="Select account type ..."
                :options="$accountTypes"
                option-value="id"
                :min-items-for-search="0"
                option-label="type_name"
                wire:model="accountType"
                />
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-2">
               <x-select
                    label="Normal Balance"
                    placeholder="Select normal balance ..."
                    :options="['CREDIT', 'DEBIT']"
                    wire:model="normalBalance"
                />

                <x-textarea label="Description" placeholder="write description" wire:model="titleDescription"/>
        </div>
        
    
        <x-slot name="footer" class="flex justify-between gap-x-4">
    
            <div class="flex gap-x-4">
                <x-button flat label="Cancel" x-on:click="close" />
    
                <x-primary-button wire:loading.attr="disabled" wire:click="saveAccountTitle" wire:loading.attr="disabled">
                    <span wire:loading wire:target="saveAccountTitle"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>&nbsp;Saving...</span>
                    <span wire:loading.remove wire:target="saveAccountTitle">Save</span>
                 </x-primary-button>
            </div>
        </x-slot>
    </x-modal-card>

     <x-modal-card title="Account Type - Create" name="cardModalType" wire:ignore.self>
        
            <x-input 
                label="Account Type" 
                placeholder="Type name ..."
                wire:model="typeName"
                />
                <x-textarea label="Description" placeholder="write description" wire:model="titleDescription"/>

       

        <x-slot name="footer" class="flex justify-between gap-x-4">
    
            <div class="flex gap-x-4">
                <x-button flat label="Cancel" x-on:click="close" />
    
                <x-primary-button wire:loading.attr="disabled" wire:click="saveAccountType" wire:loading.attr="disabled">
                    <span wire:loading wire:target="saveAccountType"><i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>&nbsp;Saving...</span>
                    <span wire:loading.remove wire:target="saveAccountType">Save</span>
                 </x-primary-button>
            </div>
        </x-slot>
    </x-modal-card>

      <x-notifications />

      
</div>



