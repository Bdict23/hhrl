<div>

    <div class="text-center">
        <h1 class="text-2xl font-bold">Customers Module</h1>

        <button class="btn btn-success" wire:click="ChangeComponent('list')">List Customer</button>
        <button class="btn btn-success" wire:click="ChangeComponent('gate')">Active Customers</button>

        <p class="text-gray-600"></p>


        <div class="container mt-5">
            @switch ($renderComponent)
                @case('list')
                    <livewire:gate-entrance.customer.customers-list />
                    @break
                @case('gate')
                    <livewire:gate-entrance.gate-entrance/>
                    @break
                @default
            @endswitch
        </div>
    </div>
</div>
