<div>

    <div class="text-center">
        <h1 class="text-2xl font-bold">Customers List</h1>
        @if (auth()->user()->employee->getModulePermission('Gate Entrance') == 1)
            <button class="btn btn-success" wire:click="ChangeComponent('add')">+Add Customer</button>
        @endif
        
        <button class="btn btn-success" wire:click="ChangeComponent('list')">List Customer</button>
        <div class="mt-4 p-4 mx-auto">
                @if ($renderComponent == 'add')
                    <livewire:gate-entrance.customer.add-customer />
                @elseif ($renderComponent == 'list')
                    <livewire:gate-entrance.customer.customers-list />
                @endif
         
        </div>
    </div>
</div>
