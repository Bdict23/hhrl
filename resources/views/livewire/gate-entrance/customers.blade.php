<div>

    <div class="text-center">
        <h1 class="text-2xl font-bold">Customers Module</h1>
        @if (auth()->user()->employee->getModulePermission('Gate Entrance') == 1)
            <button class="btn btn-success" wire:click="ChangeComponent('add')">+Add Customer</button>
        @endif
        <button class="btn btn-success" wire:click="ChangeComponent('list')">List Customer</button>

        <p class="text-gray-600"></p>
       

        <div class="flex flex-wrap -mx-2 p-2">

            <div class="w-1/4 px-2 p-4">
            </div>

            <div class="w-2/4 px-2 border border-gray-300 rounded p-4">
                @if ($renderComponent == 'add')
                    <livewire:gate-entrance.customer.add-customer />
                @elseif ($renderComponent == 'list')
                    <livewire:gate-entrance.customer.customers-list />
                @endif



            </div>
            <div class="w-1/4 px-2 p-4">
            </div>
        </div>
    </div>
</div>
