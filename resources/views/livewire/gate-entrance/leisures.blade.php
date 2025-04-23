<div>

    <div class="text-center">
        <h1 class="text-2xl font-bold">Services</h1>
        <p class="text-gray-600">HHRL</p>
        @if (session()->has('message'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        <div class="flex flex-wrap -mx-2 p-2">

            <div class="w-1/6 px-2 p-4">
            </div>

            <div class="w-2/6 px-2 border border-black p-4">
                <div class="font-bold"> New Service</div>
                <hr>
                <form wire:submit.prevent="createLeisure" action="">
                    @csrf
                    <div class="flex flex-wrap mx-2">
                        <div class="w-3/3 p-4">
                            <label for="name" class="">Leisure
                                Name:</label>
                            <input type="text" id="name" wire:model="name"
                                class="border border-gray-300 rounded p-2 mx-2" placeholder="Leisure Name">
                            @error('name')
                                <span class="text-red-500 text-xs italic">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="flex flex-wrap mx-2">
                        <div class="w-3/3 p-4">
                            <label for="description" class=""> Description: </label>
                            <input type="text" id="description" wire:model="description"
                                class="border border-gray-300 rounded p-2 mx-2" placeholder="Description">
                            @error('description')
                                <span class="text-red-500 text-xs italic">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="flex flex-wrap mx-2">

                        <div class="w-3/3 p-4">
                            <label for="description">Amount:</label>
                            <input type="number" id="description" wire:model="amount"
                                class="border border-gray-300 rounded p-2 mx-2" placeholder="Amount">
                            @error('description')
                                <span class="text-red-500 text-xs italic">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="flex flex-wrap mx-2">
                        {{-- hidden input --}}
                        <input type="hidden" id="status" wire:model="status" value="1">
                        <input type="hidden" id="branch_id" wire:model="branch_id" value="1">



                        <input type="submit" value="Create Leisure"
                            class="bg-blue-500 hover:bg-blue-100 text-white font-bold py-2 px-4 rounded mx-2 mt-2">
                    </div>
                </form>


            </div>

            <div class="w-2/6 px-2  p-4">
                <div class="font-bold"> Services List</div>
                <hr>
                <br>
                <table class="table-auto border-collapse border border-gray-300 w-full p-4">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 p-4"> Name</th>
                            <th class="border border-gray-300 p-4">Description</th>
                            <th class="border border-gray-300 p-4">Amount</th>
                            <th class="border border-gray-300 p-4">Status</th>
                            <th class="border border-gray-300 p-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leisures as $leisure)
                            <tr>
                                <td class="border border-gray-300 p-2">{{ $leisure->name }}</td>
                                <td class="border border-gray-300 p-2">{{ $leisure->description }}</td>
                                <td class="border border-gray-300 p-2">{{ $leisure->amount }}</td>
                                <td class="border border-gray-300 p-2">
                                    @if ($leisure->status == 1)
                                        Active
                                    @else
                                        Inactive
                                    @endif
                                </td>
                                <td class="border border-gray-300 p-2 ">
                                    <button wire:click="editLeisure({{ $leisure->id }})"
                                        class="bg-blue-500 hover:bg-blue-100 text-white font-bold py-2 px-4 rounded mx-2 mt-2">
                                        Edit
                                    </button>
                                    <button wire:click="deleteLeisure({{ $leisure->id }})"
                                        class="bg-red-500 hover:bg-red-100 text-white font-bold py-2 px-4 rounded mx-2 mt-2">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <div class="w-1/6 px-2 p-4">
            </div>

        </div>


    </div>




</div>
