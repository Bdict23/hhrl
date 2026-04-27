<div>

    <div class="d-flex justify-content-between">
        <div class="container d-flex align-content-center gap-x-2">
            @if (auth()->user()->employee->getModulePermission('Accounting - Template Management') == 1)
                @if ($action == 'view' && !$isApprover)
                    <x-select placeholder="Save as" :options="['DRAFT', 'FINAL']" wire:model='saveAs' />
                    <a>
                        <x-primary-button wire:loading.attr="disabled" wire:click="saveTemplate"
                            wire:loading.attr="disabled" class="whitespace-nowrap">
                            <span wire:loading wire:target="saveTemplate"><i class="spinner-border spinner-border-sm"
                                    role="status" aria-hidden="true"></i>&nbsp;Saving...</span>
                            <span wire:loading.remove wire:target="saveTemplate">Save</span>
                        </x-primary-button>
                    </a>
                    <a><x-danger-button wire:loading.attr="disabled" wire:click="resetForm">Reset</x-danger-button></a>
                @endif
                @if (!$isEditable && $isApprover && $viewedTemplate->approver_date == null)
                    <x-select placeholder="Save as" :options="['APPROVED', 'REVISED']" wire:model='saveAs' />
                    <a><x-primary-button wire:loading.attr="disabled" wire:click="approverAction"
                            wire:loading.attr="disabled" class="whitespace-nowrap">
                            <span wire:loading wire:target="approverAction"><i class="spinner-border spinner-border-sm"
                                    role="status" aria-hidden="true"></i>&nbsp;Saving...</span>
                            <span wire:loading.remove wire:target="approverAction">Save</span>
                        </x-primary-button>
                    </a>
                @endif
            @endif
            <a href="/accounting-chart-of-accounts-management"><x-secondary-button
                    wire:loading.attr="disabled">Summary</x-secondary-button></a>
        </div>
        <div class="container align-content-end">
            @if ($isApprover)
                <h2 class="pb-2 text-2xl font-bold text-gray-800 border-b">Transaction Template: Review
                </h2>
            @else
                <h2 class="pb-2 text-2xl font-bold text-gray-800 border-b">Transaction Template: Create
                </h2>
            @endif
        </div>
    </div>

    <div>
        <div class="container mt-3 row">
            <div class="col-md-6 col-lg-6">
                <div class="p-2 card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <strong>Particulars</strong>
                            @if (auth()->user()->employee->getModulePermission('Accounting - Template Management') == 1)
                                @if ($isEditable)
                                    <x-primary-button data-bs-toggle="modal" data-bs-target="#particularsModal"
                                        wire:loading.attr="disabled">+ Add</x-primary-button>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="mt-2 card">
                        <table class="table table-sm table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Title</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    @if ($isEditable)
                                        <th class="text-center">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($selectedTitles as $index => $title)
                                    <tr>
                                        <td>{{ $selectedTitles[$index]['title'] }}</td>
                                        <td>{{ $selectedTitles[$index]['debit'] }}</td>
                                        <td>{{ $selectedTitles[$index]['credit'] }}</td>
                                        @if ($isEditable)
                                            <td class="text-center"><button
                                                    wire:click="removeTitle({{ $index }})"
                                                    class="btn btn-sm btn-danger">
                                                    <span wire:loading.remove
                                                        wire:target="removeTitle({{ $index }})"><i
                                                            class="bi bi-trash"></i></span>
                                                    <span wire:loading wire:target="removeTitle({{ $index }})"
                                                        class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                </button></td>
                                        @endif
                                    </tr>
                                    {{-- @empty
                                    <td colspan="5" class="text-center"> No selected transaction</td> --}}
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card row">
                    <div class="mt-3 mb-3">
                        <x-select label="Business Unit" placeholder="Select some BU" :options="$companies"
                            option-label="company_name" option-value="id" wire:model="selectedCompanyId"
                            :min-items-for-search="0" :readonly="!$isEditable" />
                    </div>
                    @error('selectedCompanyId')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <div>
                        <label for="" class="form-label"> Account Type </label>
                        <div class="flex items-center mb-3 gap-x-1">
                            <x-select placeholder="Select some account type" :options="$accountTypes" option-label="type_name"
                                option-value="id" wire:model.live="selectedTransactionTypeId" :min-items-for-search="0"
                                class="border-solid" :readonly="!$isEditable" />
                            @if (auth()->user()->employee->getModulePermission('Accounting - Account Types') == 1)
                                @if ($isEditable)
                                    {{-- <x-mini-button label="New" icon="plus" outline hover="success"
                                        focus:solid.gray data-bs-toggle="modal" data-bs-target="#typeModal" /> --}}
                                @endif
                            @endif
                        </div>
                    </div>
                    @error('selectedTransactionTypeId')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <div>
                        <label for="" class="form-label"> Template Name </label>
                        <div class="flex items-center mb-3 gap-x-1">
                            <x-select placeholder="Select some template name" :options="$templateNames"
                                option-label="template_name" option-value="id" wire:model="selectedTemplateNameId"
                                :min-items-for-search="0" class="border border-solid" :readonly="!$isEditable" />
                            @if (auth()->user()->employee->getModulePermission('Accounting - Template Management') == 1)
                                @if ($isEditable)
                                    <x-mini-button label="New" icon="plus" outline hover="success"
                                        focus:solid.gray data-bs-toggle="modal"
                                        data-bs-target="#transactionNameModal" />
                                @endif
                            @endif
                        </div>
                    </div>
                    @error('selectedTemplateNameId')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <div class="mb-2">

                        <x-textarea label="Description" placeholder="Enter description here..." :readonly="!$isEditable"
                            wire:model="description" />
                    </div>


                    <x-select label="Approved By" placeholder="Select" :options="$approvers" option-label="full_name"
                        option-value="id" wire:model="approverId" :readonly="!$isEditable" class="mb-4" />
                </div>
            </div>

        </div>
    </div>



    {{-- Type Creation --}}
    <div class="modal fade" id="typeModal" tabindex="-1" aria-labelledby="typeModalLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="typeModalLabel">Account Type - Express Create</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="" class="form-label">Type Name</label>
                        <input type="text" class="form-control" placeholder="Enter account type name here..."
                            wire:model="newTypeName">
                    </div>
                    @error('newTypeName')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="modal-footer">
                    <x-secondary-button data-bs-dismiss="modal">Cancel</x-secondary-button>
                    <x-primary-button wire:click="createType" wire:loading.attr="disabled">
                        <span wire:loading wire:target="createType">Creating...</span>
                        <span wire:loading.remove wire:target="createType">Create</span>
                    </x-primary-button>
                </div>
            </div>
        </div>
    </div>


    {{-- Template Name Modal --}}
    <div class="modal fade" id="transactionNameModal" tabindex="-1" aria-labelledby="transactionNameModalLabel"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionNameModalLabel">Template Name - Express Create</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="" class="form-label">Template Name</label>
                        <input type="text" class="form-control" placeholder="Enter template name here..."
                            wire:model="newTemplateName">
                    </div>
                    @error('newTemplateName')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="modal-footer">
                    <x-secondary-button data-bs-dismiss="modal">Cancel</x-secondary-button>
                    <x-primary-button wire:click="createTemplateName" wire:loading.attr="disabled">
                        <span wire:loading wire:target="createTemplateName">Creating...</span>
                        <span wire:loading.remove wire:target="createTemplateName">Create</span>
                    </x-primary-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Particulars Modal --}}
    <div class="modal fade" id="particularsModal" tabindex="-1" aria-labelledby="particularsModalLabel"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="particularsModalLabel">Select Account Titles</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between">
                        <div class="container">
                            <div class="input-group">
                                <span class="input-group-text">
                                    Parent Title
                                </span>
                                <select name="" id="" class="form-select form-select-sm"
                                    wire:model.live="selectedTitleParent">
                                    <option value="all">All</option>
                                    @foreach ($chartOfAccountsHeaders as $header)
                                        <option value="{{ $header->id }}">{{ $header->account_title }} -
                                            {{ $header->account_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="container">
                            <div class="input-group">
                                <input id="searchCoaList" type="text" class="form-control"
                                    placeholder="Search..." aria-label="Search particular name"
                                    onkeyup="searchParticulars()">
                                <button class="btn btn-outline-secondary" type="button"><i
                                        class="bi bi-search"></i></button>
                            </div>
                            <script>
                                function searchParticulars() {
                                    var input, filter, table, tr, td, i, txtValue;
                                    input = document.getElementById("searchCoaList");
                                    filter = input.value.toUpperCase();
                                    table = document.querySelector("#coaList").closest("tbody");
                                    tr = table.getElementsByTagName("tr");

                                    for (i = 0; i < tr.length; i++) {
                                        td = tr[i].getElementsByTagName("td");
                                        let rowContainsFilter = false;

                                        for (let j = 0; j < td.length; j++) {
                                            if (td[j]) {
                                                txtValue = td[j].textContent || td[j].innerText;
                                                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                                    rowContainsFilter = true;
                                                    break;
                                                }
                                            }
                                        }

                                        tr[i].style.display = rowContainsFilter ? "" : "none";
                                    }
                                }
                            </script>
                        </div>
                    </div>
                    <div class="container overflow-auto" style="max-height: 400px;">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th> Title</th>
                                    <th>Account Code</th>
                                    <th class="text-center">ADD AS</th>
                                </tr>
                            </thead>
                            <tbody id="coaList">
                                @forelse ($chartOfAccounts as $coa)
                                    <tr>
                                        <td>{{ $coa->account_title }}</td>
                                        <td>{{ $coa->account_code }}</td>
                                        <td class="text-center whitespace-nowrap">
                                            <x-primary-button class="btn-sm"
                                                wire:click="addDebit({{ $coa->id }},'{{ $coa->account_title }}')">
                                                <span wire:loading
                                                    wire:target="addDebit({{ $coa->id }}, '{{ $coa->account_title }}')"><i
                                                        class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></i>&nbsp;Wait...</span>
                                                <span wire:loading.remove
                                                    wire:target="addDebit({{ $coa->id }},'{{ $coa->account_title }}')">Debit</span>
                                            </x-primary-button>
                                            <x-secondary-button class="btn-sm"
                                                wire:click="addCredit({{ $coa->id }},'{{ $coa->account_title }}')">
                                                <span wire:loading
                                                    wire:target="addCredit({{ $coa->id }}, '{{ $coa->account_title }}')"><i
                                                        class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></i>&nbsp;Wait...</span>
                                                <span wire:loading.remove
                                                    wire:target="addCredit({{ $coa->id }},'{{ $coa->account_title }}')">credit</span>
                                            </x-secondary-button>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        window.addEventListener('close-type-modal', event => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('typeModal'));
            modal.hide();

        });
        window.addEventListener('close-template-name-modal', event => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('transactionNameModal'));
            modal.hide();

        });

        window.addEventListener('showAlert', event => {
            const data = event.detail[0];
            Swal.fire({
                icon: data.type,
                title: data.title,
                text: data.message,
                timer: data.timer,
                showConfirmButton: false,
            });
        });
    </script>

</div>
