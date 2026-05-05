<div class="row">
    <div class="overflow-auto col-md-6" style="max-height: 450px;">
        <div  class="table-responsive">
            <table class="table table-sm">
                <thead class="z-0 sticky-top table-dark table-sm">
                    <tr>
                        <th>First name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Suffix</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customerList ?? [] as $customer)
                        <tr>
                            <td>{{ $customer->customer_fname ?? ''}}</td>
                            <td>{{ $customer->customer_mname?? ''}}</td>
                            <td>{{ $customer->customer_lname ?? ''}}</td>
                            <td>{{ $customer->suffix ?? ''}}</td>
                            <td><x-primary-button wire:click='selectedCustomer({{ $customer->id }})'>Select</x-primary-button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No Customers added</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row card">

             <div class="flex p-2 mb-3 col-md-12">
                <x-input
                    label="Firstname"
                    placeholder="your name"
                    wire:model='firstName'
                />
                <x-input
                    label="Middle name"
                    placeholder="your name"
                    wire:model='middleName'
                />
             </div>
            <div class="flex mb-3">
                <x-input
                    class="mr-3 col-md-6"
                    label="Lastname"
                    placeholder="your name"
                    wire:model='lastName'
                />
                <x-input
                    label="Suffix"
                    placeholder="(optional)"
                    wire:model='suffix'
                />
            </div>
            <div class="flex mb-3">
                <x-native-select
                    wire:model='gender'
                    class="col-md-3"
                    label="Gender"
                    placeholder="Select Gender"
                    :options="['MALE', 'FEMALE','NEUTRAL']"
                />
                <x-datetime-picker
                    class="col-md-9"
                    wire:model.live="birth"
                    label="Birth Date"
                    without-time="true"
                />
            </div>

            <div class="flex mb-3">
                <x-phone
                    wire:model="phone1"
                    label="Phone 1"
                    placeholder="Phone"
                    :mask="['(##) ###-###-###']"
                />
                <x-phone
                    wire:model="phone2"
                    label="Phone 2"
                    placeholder="Phone"
                    :mask="['(##) ###-###-###']"
                />
            </div>
             <div class="flex mb-3">
                <x-input
                    wire:model='email'
                    label="Email"
                    placeholder="Enter a valid email"
                />
                <x-input
                    wire:model='address'
                    label="Address"
                    placeholder="Enter a valid address"
                />
             </div>
             <div class="flex justify-end gap-3 mt-3 mb-3">
                @if (!$isNew)
                    <x-primary-button wire:click='update'>Update</x-primary-button>
                @else
                    <x-primary-button wire:click='create'>Create</x-primary-button>
                @endif
             </div>
        </div>

    </div>
      <x-notifications />
</div>
