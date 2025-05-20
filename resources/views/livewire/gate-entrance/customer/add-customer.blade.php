<div>
    {{-- In work, do what you enjoy. --}}
    {{-- nothing! --}}


    <div class="container mt-5 d-flex justify-content-center">
        <div class="card" style="width: 50em">
            <div class="card-header">
                <h5 class="card-title">Customer Registration</h5>
                <h6 class="card-subtitle mb-2 text-muted">Please fill in the form below</h6>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="submit" action="">
                    @if ($type == 1)
                        <div class="row">
                            @csrf
                            <div class="row p-2">
                                <label class="p-2 col-2 text-center" for="">Last Name <span
                                        class="text-danger">*</span>:</label>
                                <div class="col-5">
                                    <input type="text"class="form-control" wire:model="lname"
                                        placeholder="Last Name " required>
                                </div>
                            </div>
                            <div class="row p-2">
                                <label class="p-2 col-2 text-center" for="">First Name <span
                                        class="text-danger">*</span>:</label>
                                <div class="col-5">
                                    <input type="text"class="form-control" wire:model="fname"
                                        placeholder="First Name " required>
                                </div>
                            </div>
                            <div class="row p-2">
                                <label class="p-2 col-2 text-center" for="">Middle Name:</label>
                                <div class="col-5">
                                    <input type="text"class="form-control" wire:model="mname"
                                        placeholder="Middle Name">
                                </div>
                            </div>
                            <div class="row p-2">
                                <label class="p-2 col-2 text-center" for="">Suffix:</label>
                                <div class="col-5">
                                    <select name="suffix" id="" class="form-control">
                                        <option value="" selected disabled>Optional</option>
                                        <option value="Sr">Sr</option>
                                        <option value="Sr">Sr</option>
                                        <option value="Jr">Jr</option>
                                        <option value="I">I</option>
                                        <option value="II">II</option>
                                        <option value="III">III</option>
                                        <option value="IV">IV</option>
                                        <option value="V">V</option>
                                        <option value="VI">VI</option>
                                        <option value="VII">VII</option>
                                        <option value="VIII">VIII</option>
                                        <option value="IX">IX</option>
                                        <option value="X">X</option>
                                        <option value="XI">XI</option>
                                        <option value="XII">XII</option>
                                        <option value="XIII">XIII</option>
                                        <option value="XIV">XIV</option>
                                        <option value="XV">XV</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row p-2">
                                <label class="p-2 col-2 text-center" for="">Gender <span
                                        class="text-danger">*</span>:</label>
                                <div class="col-5">
                                    <select class="form-control" wire:model="gender" id="" required>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row p-2">
                                <label class="p-2 col-2 text-center" for="">Birth Date <span
                                        class="text-danger">*</span>:</label>
                                <div class="col-5">
                                    <input type="Date" class="form-control" wire:model="bday" required>
                                    @if (session()->has('date_error'))
                                        <div class="alert alert-danger mt-2">
                                            {{ session('date_error') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <hr>
                    <select class="form-control" name="" id="" value="{{ $type }}"
                        wire:model.live="type">
                        <option value="1" wire:model="customer_type">Member</option>
                        <option value="0" wire:model="customer_type">Walk-in</option>
                    </select>

                    <div class="flex flex-wrap -mx-2 p-2">
                        {{-- hidden input --}}
                        <input type="text" class="border border-gray-300 rounded p-2 mx-2" wire:model="branch_id"
                            value="{{ Auth::user()->branch_id }}" hidden>
                    </div>


                    <input type="submit" value="Proceed"
                        class="bg-blue-500 text-white font-bold py-2 px-4 rounded mt-4">
                    @if (session()->has('message'))
                        <div class="alert alert-danger mt-2">
                            {{ session('message') }}
                        </div>
                    @endif
                </form>


            </div>
        </div>
    </div>

</div>
