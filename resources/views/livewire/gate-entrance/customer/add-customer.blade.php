<div>
    {{-- In work, do what you enjoy. --}}

    <div class="row">
        <div class="font-bold col-4"></div>
        <div class="font-bold col-4"> New Customers</div>
        @if (session()->has('message'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

    </div>
    <hr>
    <form wire:submit.prevent="submit" action="">
        <div class="flex flex-wrap -mx-2 p-2">
            @csrf
            <div class="w-1/2 px-2 p-1">
                <label class="p-2" for="">Last Name:</label>
                <input type="text" class="border border-gray-300 rounded p-2  mx-2" wire:model="lname"
                    placeholder="Last Name" required>
            </div>

            <div class="w-1/2 px-2 p-1">
                <label class="" for="">First Name:</label>
                <input type="text" class="border border-gray-300 rounded p-2  mx-2" wire:model="fname"
                    placeholder="First Name" required>
            </div>

        </div>
        <div class="flex flex-wrap -mx-2 p-2">

            <div class="w-1/2 px-2 p-1">
                <label class="p-2" for="">Middle Name</label>
                <input type="text" class="border border-gray-200 rounded p-2 mx-2" wire:model="mname"
                    placeholder="Middle Name" required>
            </div>

            <div class="w-1/2 px-2">
                <label class=" p-2" for="">Suffix:</label>
                <input type="text" class="border border-gray-300 rounded p-2 mx-2" wire:model="suffix"
                    placeholder="Suffix" required>
            </div>
        </div>

        <div class="flex flex-wrap -mx-2">

            <div class="w-2/4 px-2">
                <label class="p-2" for="">Gender</label>
                <select class="border border-gray-300 rounded p-2 mx-2" wire:model="gender" id="">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="w-2/4 px-2">
                <label class=" p-2" for="">Email:</label>
                <input type="email" class="border border-gray-300 rounded p-2 mx-2" wire:model="email"
                    placeholder="@Email">
            </div>

        </div>


        <div class="flex flex-wrap -mx-2 p-2">
            <div class="w-2/4 px-2">
                <label class=" p-2" for="">Contact No.#:</label>
                <input type="text" class="border border-gray-300 rounded p-2 mx-2" wire:model="contact_no_1"
                    placeholder="+63">
            </div>

            <div class="w-2/4 px-2">
                <label class=" p-2" for="">Address:</label>
                <input type="text" class="border border-gray-300 rounded p-2 mx-2" wire:model="customer_address"
                    placeholder="Address">
            </div>

        </div>
        <div class="flex flex-wrap -mx-2 p-2">
            <div class="w-2/4 px-2">
                <label class=" p-2" for="">Birth Day</label>
                <input type="Date" class="border border-gray-300 rounded p-2 mx-2" wire:model="bday">
            </div>
        </div>
        <hr>

        <div class="flex flex-wrap -mx-2 p-2">
            <div class="w-2/10 px-2">
                <label class=" p-2" for="">Contact Person:</label>
                <input type="text" class="border border-gray-300 rounded p-2 mx-2" wire:model="contact_person"
                    placeholder="Contact Person Name">
            </div>
            <div class="w-2/10 px-2">
                <label class=" p-2" for="">Contact Number:</label>
                <input type="text" class="border border-gray-300 rounded p-2 mx-2" wire:model="contact_no_2"
                    placeholder="Contact Number">
            </div>

        </div>
        <div class="flex flex-wrap -mx-2 p-2">
            <div class="w-2/10 px-2">
                <label class="p-2" for="">Relation</label>
                <input type="text" class="border border-gray-300 rounded p-2 mx-2" wire:model="relation"
                    placeholder="Relation">
            </div>

            {{-- hidden input --}}
            <input type="text" class="border border-gray-300 rounded p-2 mx-2" wire:model="branch_id" value='1'
                value="{{ Auth::user()->branch_id }}" hidden>
        </div>

        <input type="submit" value="Submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded mt-4">
    </form>
</div>
