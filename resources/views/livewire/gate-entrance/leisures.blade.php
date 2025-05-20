<div class="container mx-auto p-4">

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
                @if (session()->has('delete_message'))
                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                        {{ session('delete_message') }}
                    </div>
                @endif
            </div>

            <div class="w-4/6 px-2  p-4">
                <div class="font-bold"> Services List</div>
                <hr class="my-2">
                <button type="button" style="margin-left: 85%" class="btn btn-success my-2" data-bs-toggle="modal"
                    data-bs-target="#AddService">Add Service</button>
                <br>
                <table class="table table-striped-columns border-collapse border border-gray-300 w-full p-4">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leisures as $leisure)
                            <tr>
                                <td>{{ $leisure->name }}</td>
                                <td>{{ $leisure->description }}</td>
                                <td>{{ $leisure->amount.' '.$leisure->unit }}</td>
                                <td>
                                    @if ($leisure->status == 1)
                                        Active
                                    @else
                                        Inactive
                                    @endif
                                </td>
                                <td class="border border-gray-300 p-2 ">
                                    <button data-bs-toggle="modal" data-bs-target="#UpdateService"
                                        wire:click="editLeisure({{ $leisure->id }})"
                                        class="bg-blue-500 hover:bg-blue-100 text-white font-bold py-2 px-4 rounded mx-2 mt-2">
                                        Edit
                                    </button>
                                    <button wire:click="deleteLeisure({{ $leisure->id }})"
                                        class="btn btn-{{$leisure->status==1?'danger':'success'}}  text-white font-bold py-2 px-4 rounded mx-2 mt-2">
                                        @if ($leisure->status == 1)
                                            Delete
                                        @else
                                            Restore
                                        @endif

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

    <!-- Services Modal -->
    <div wire:ignore.self class="modal fade" id="AddService" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form wire:submit.prevent="createLeisure" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">New Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        {{-- body --}}

                        @if (session()->has('create_message'))
                            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                                {{ session('create_message') }}
                            </div>
                        @endif

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
                            <div class="w-3/3 p-4">
                                <label for="description">Unit Price:</label>
                                <input type="text" id="description" wire:model="unit"
                                    class="border border-gray-300 rounded p-2 mx-2" placeholder="ex. per hour, per day, etc...">
                                @error('unit')
                                    <span class="text-red-500 text-xs italic">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="flex flex-wrap mx-2">
                            {{-- hidden input --}}
                            <input type="hidden" id="status" wire:model="status" value="1">
                            <input type="hidden" id="branch_id" wire:model="branch_id" value="1">
                        </div>
                        {{-- end body --}}
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Service Modal -->

    @if ($isOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md">
                <h2 class="text-lg font-bold mb-4">Edit Item</h2>
                @if (session()->has('update_message'))
                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                        {{ session('update_message') }}
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" wire:model="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea wire:model="description"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>

                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" wire:model="amount" step="0.01"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

                </div>

                <div class="flex justify-end space-x-2">
                    <button wire:click="closeModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button wire:click="updateLeisure" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Update
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
<script>
    window.addEventListener('show-edit-modal', event => {
        const modal = new bootstrap.Modal(document.getElementById('UpdateService'));
        modal.show();
    });
</script>
