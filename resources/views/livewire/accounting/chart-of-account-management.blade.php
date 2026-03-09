{{-- <div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto space-y-6">
        
        <h2 class="text-2xl font-bold text-gray-800 border-b pb-2">Accounting Setup: Master Data & Templates</h2>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="bg-white p-4 rounded-lg shadow border-t-4 border-blue-500">
                <h3 class="font-semibold text-lg mb-4 flex items-center">
                    <span class="bg-blue-100 text-blue-600 p-1 rounded mr-2">1</span> Account Types
                </h3>
                <form wire:submit.prevent="saveAccountType" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type Name</label>
                        <input type="text" wire:model="type_name" placeholder="e.g., Asset, Expense" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Save Type</button>
                </form>

                <div class="mt-6 border-t pt-4">
                    <span class="text-xs font-bold uppercase text-gray-500">Existing Types</span>
                    <ul class="mt-2 divide-y divide-gray-100">
                        <li class="py-2 text-sm text-gray-600 flex justify-between">
                            <span>Asset</span>
                            <span class="text-gray-400 italic text-xs">Normal: DR</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow border-t-4 border-green-500">
                <h3 class="font-semibold text-lg mb-4 flex items-center">
                    <span class="bg-green-100 text-green-600 p-1 rounded mr-2">2</span> Account Titles (COA)
                </h3>
                <form wire:submit.prevent="saveAccountTitle" class="space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Account Code</label>
                            <input type="text" wire:model="acc_code" placeholder="5001" class="w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Parent (Optional)</label>
                            <select wire:model="parent_id" class="w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm">
                                <option value="">None</option>
                                </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Account Name</label>
                        <input type="text" wire:model="acc_name" placeholder="e.g., Internet Bill" class="w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Normal Balance</label>
                        <select wire:model="normal_balance" class="w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm text-xs">
                            <option value="Debit">Debit (+ DR / - CR)</option>
                            <option value="Credit">Credit (+ CR / - DR)</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition">Register Title</button>
                </form>
            </div>

            <div class="bg-white p-4 rounded-lg shadow border-t-4 border-purple-500">
                <h3 class="font-semibold text-lg mb-4 flex items-center">
                    <span class="bg-purple-100 text-purple-600 p-1 rounded mr-2">3</span> Transaction Template
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 font-bold text-purple-700">Template Name</label>
                        <input type="text" wire:model="template_name" placeholder="e.g., Monthly Internet Payment" class="w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm">
                    </div>

                    <div class="p-3 bg-purple-50 rounded-md border border-purple-100">
                        <span class="text-xs font-bold text-purple-800 uppercase">Set DR/CR Rules</span>
                        
                        <div class="mt-2 space-y-2">
                            <div class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <label class="text-[10px] font-bold text-gray-500">DEBIT SIDE</label>
                                    <select wire:model="template_dr" class="w-full border-gray-300 rounded-md text-xs">
                                        <option>Select Title...</option>
                                        </select>
                                </div>
                                <div class="w-12 h-9 bg-gray-200 flex items-center justify-center text-xs font-bold rounded">DR</div>
                            </div>

                            <div class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <label class="text-[10px] font-bold text-gray-500">CREDIT SIDE</label>
                                    <select wire:model="template_cr" class="w-full border-gray-300 rounded-md text-xs">
                                        <option>Select Title...</option>
                                        </select>
                                </div>
                                <div class="w-12 h-9 bg-gray-200 flex items-center justify-center text-xs font-bold rounded">CR</div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-md hover:bg-purple-700 shadow-md">
                        Build Template
                    </button>
                </div>
            </div>

        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gray-100 px-4 py-3 border-b">
                <h3 class="font-bold text-gray-700">Saved Transaction Templates</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Template Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase text-blue-600 font-bold">Debit (DR)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase text-red-600 font-bold">Credit (CR)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">Internet Bill Payment</td>
                        <td class="px-6 py-4 text-sm text-blue-600 font-semibold">Internet Expense</td>
                        <td class="px-6 py-4 text-sm text-red-600 font-semibold">Cash on Hand</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <button class="text-red-500 hover:underline">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div> --}}

<div class="overflow-x-auto">
    <div class="container mb-3">
            <div class="row">
                <div class="col-md-6">
                    @if(auth()->user()->employee->getModulePermission('Accounting - COA Management') == 1 )
                        <a href="/coa-management?action=new" style="text-decoration: none; color: white;"><x-primary-button >+ New Template</x-primary-button></a>
                        <x-primary-button>Export<i class="bi bi-box-arrow-up"></i></x-primary-button>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4 class="text-end">Chart of Accounts Templates - Summary <i class="bi bi-file-text"></i></h4>
                </div>
            </div>
        </div>
        <div class="card mt-3 mb-3">  
            {{-- <div class=" card-header d-flex justify-content-between mx-2">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="input-group">
                                <label for="from_date" class="input-group-text">From:</label>
                                <input wire:model="fromDate" type="date" id="from_date" name="from_date" value="{{ date('Y-m-d') }}"
                                    class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="input-group">
                                <label for="to_date" class="input-group-text">To:</label>
                                <input wire:model="toDate" type="date" id="to_date" name="to_date" value="{{ date('Y-m-d') }}"
                                    class="form-control form-control-sm">
                                <button wire:click="search" class="btn btn-primary input-group-text">
                                    <span wire:loading.remove>Search <i class="bi bi-search"></i></span>
                                    <span wire:loading>Searching&nbsp;<span class="spinner-border spinner-border-sm" role="status"></span></span>
                                </button>  
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

        <div class="card-body ">
                <div style="height: 500px; overflow-x: auto; display: block;">
                    <table class="table table-striped table-hover table-sm " >
                        <thead class="table-dark">
                            <tr>
                                <th>Business Unit</th>
                                <th>Type</th>
                                <th>Transaction</th>
                                <th>Particulars</th>
                                <th>Description</th>
                                <th>Created By</th>
                                <th>Created Date</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($templates as $template)
                               
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



