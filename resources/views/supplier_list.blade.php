@extends('layouts.master')
@section('content')

    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboards') }}
        </h2>
    </x-slot>

    <div class="dashboard">
        <header>
            <h2>Supplier List</h2>
            <button class="add-btn" type="button" data-bs-toggle="modal" data-bs-target="#supplierModal">+ Add
                Supplier</button>
        </header>
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="position: sticky; top: 0;">Supplier Name</th>
                        <th style="position: sticky; top: 0;">Supplier Address</th>
                        <th style="position: sticky; top: 0;">Supplier Code</th>
                        <th style="position: sticky; top: 0;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->supp_name }}</td>
                            <td>{{ $supplier->supp_address }}</td>
                            <td>{{ $supplier->supplier_code }}</td>
                            <td>
                                <div class="button-group">
                                    <button onclick='getSupplier({{ json_encode($supplier) }})' class="action-btn btn-sm"
                                        data-bs-target="#supplierViewModal" data-bs-toggle="modal">View</button>
                                    <button class="btn btn-info btn-sm" data-bs-target="#supplierUpdateModal"
                                        data-bs-toggle="modal"
                                        onclick='getSupplier({{ json_encode($supplier) }})'>Edit</button>
                                    <a type="button" class="btn btn-danger btn-sm"
                                        href="{{ route('supplier.deactivate', $supplier->id) }}">Remove</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>


    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    @endif
    <!-- Modal create-->
    <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierModalLabel">Supplier Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form -->
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf
                        <div class="row ">
                            <div class="col-md-6">
                                <label for="supp_name" class="form-label">Name <span style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="name" name="supp_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="postal_address" class="form-label">Postal ID <span
                                        style="color: red;">*</span></label>
                                <input type="number" class="form-control" id="postal_address" name="postal_address"
                                    placeholder="ex. 8200" required>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-md-6">
                                <label for="contact_no_1" class="form-label">Contact No. 1</label>
                                <input type="text" class="form-control" id="contact1" name="contact_no_1">
                            </div>
                            <div class="col-md-6">
                                <label for="supp_address" class="form-label">Address <span
                                        style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="address" name="supp_address" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="contact_no_2" class="form-label">Contact No. 2</label>
                                <input type="text" class="form-control" id="contact2" name="contact_no_2">
                            </div>
                            <div class="col-md-6">
                                <label for="tin_number" class="form-label">TIN No.</label>
                                <input type="text" class="form-control" id="tin_number" name="tin_number">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person">
                            </div>
                            <div class="col-md-6">
                                <label for="input_tax" class="form-label">Input Tax <span
                                        style="color: red;">*</span></label>
                                <select name="input_tax" id="InputTax" class="form-select" required>
                                    <option value="NON-VAT">NON-VAT</option>
                                    <option value="VATABLE">VATABLE</option>
                                    <option value="UNDECLARED">UNDECLARED</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="supplier_code" class="form-label">Supplier Code</label>
                                <input type="text" class="form-control" id="supplier_code" name="supplier_code">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" placeholder="Description"></textarea>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input type="submit" value="Save" CLASS = "btn btn-outline-success">
                </div>
            </div>
            </form>
        </div>
    </div>

    <!-- Modal  update-->
    <div class="modal fade" id="supplierUpdateModal" tabindex="-1" aria-labelledby="supplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierModalLabel">Update Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form -->
                    <form action="{{ route('suppliers.update') }}" method="POST">
                        @csrf

                        <div class="row ">
                            <div class="col-md-6">
                                <label for="supp_name" class="form-label">Name <span style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="supp_name" name="supp_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="postal_address" class="form-label">Postal ID <span
                                        style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="postal_address1" name="postal_address"
                                    placeholder="ex. 8200">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="contact_no_1" class="form-label">Contact No. 1 <span
                                        style="color: rgb(129, 127, 127); font-size: x-small;">(optional)</span></label>
                                <input type="text" class="form-control" id="contact_no_1" name="contact_no_1">
                            </div>
                            <div class="col-md-6">
                                <label for="supp_address" class="form-label">Address <span
                                        style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="supp_address" name="supp_address">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="contact_no_2" class="form-label">Contact No. 2 <span
                                        style="color: rgb(129, 127, 127); font-size: x-small;">(optional)</span></label>
                                <input type="text" class="form-control" id="contact_no_2" name="contact_no_2">
                            </div>
                            <div class="col-md-6">
                                <label for="tin_number2" class="form-label">TIN No. <span
                                        style="color: rgb(129, 127, 127); font-size: x-small;">(optional)</span></label>
                                <input type="text" class="form-control" id="tin_number2" name="tin_number">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">Contact Person <span
                                        style="color: rgb(129, 127, 127); font-size: x-small;">(optional)</span></label>
                                <input type="text" class="form-control" id="contact_person1" name="contact_person">
                            </div>
                            <div class="col-md-6">
                                <label for="InputTax2" class="form-label">Input Tax <span
                                        style="color: red;">*</span></label>
                                <select name="input_tax" id="InputTax2" class="form-select" required>
                                    <option value="NON-VAT">NON-VAT</option>
                                    <option value="VATABLE">VATABLE</option>
                                    <option value="UNDECLARED">UNDECLARED</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="supplier_code" class="form-label">Supplier Code <span
                                        style="color: rgb(129, 127, 127); font-size: x-small;">(optional)</span></label>
                                <input type="text" class="form-control" id="supplier_code1" name="supplier_code">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span
                                        style="color: rgb(129, 127, 127); font-size: x-small;">(optional)</span></label>
                                <input type="email" class="form-control" id="email1" name="email">
                            </div>
                        </div>
                        <input type="hidden" id="id" name='id'>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="description2" class="form-label">Description <span
                                        style="color: rgb(129, 127, 127); font-size: x-small;">(optional)</span></label>
                                <textarea name="description" id="description2" class="form-control" placeholder="Description"></textarea>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input type="submit" value="Update" CLASS = "btn btn-outline-success">
                </div>
            </div>
            <input type="submit" value="Update" CLASS = "btn btn-outline-success">
            </form>
        </div>
    </div>

    <!-- Modal view -->
    <div class="modal fade" id="supplierViewModal" tabindex="-1" aria-labelledby="supplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierModalLabel">Supplier Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form -->
                    <form>
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label for="supp_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="supp_name2" name="supp_name" readonly
                                    disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="postal_address" class="form-label">Postal ID</label>
                                <input type="text" class="form-control" id="postal_address2" name="postal_address"
                                    readonly disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="contact_no_1" class="form-label">Contact No. 1</label>
                                <input type="text" class="form-control" id="2contact_no_1" name="contact_no_1"
                                    readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="supp_address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="supp_address2" name="supp_address"
                                    readonly disabled>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-md-6">
                                <label for="contact_no_2" class="form-label">Contact No. 2</label>
                                <input type="text" class="form-control" id="2contact_no_2" name="contact_no_2"
                                    readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="tin_number3" class="form-label">TIN No.</label>
                                <input type="text" class="form-control" id="tin_number3" name="tin_number" readonly
                                    disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person2" name="contact_person"
                                    readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="InputTax3" class="form-label">Input Tax</label>
                                <select name="input_tax" id="InputTax3" class="form-select" disabled>
                                    <option value="NON-VAT">NON-VAT</option>
                                    <option value="VATABLE">VATABLE</option>
                                    <option value="UNDECLARED">UNDECLARED</option>
                                </select>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-md-6">
                                <label for="supplier_code" class="form-label">Supplier Code</label>
                                <input type="text" class="form-control" id="supplier_code2" name="supplier_code"
                                    readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email2" name="email" readonly
                                    disabled>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-md-12">
                                <label for="description3" class="form-label">Description</label>
                                <textarea name="description" id="description3" class="form-control" placeholder="Description" readonly disabled></textarea>
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                </div>
            </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function getSupplier(data) {
            console.log(data);
            document.getElementById('supp_name').value = data.supp_name;
            document.getElementById('supp_name2').value = data.supp_name;
            document.getElementById('supp_address').value = data.supp_address;
            document.getElementById('supp_address2').value = data.supp_address;
            document.getElementById('postal_address1').value = data.postal_address;
            document.getElementById('postal_address2').value = data.postal_address;
            document.getElementById('contact_no_1').value = data.contact_no_1;
            document.getElementById('2contact_no_1').value = data.contact_no_1;
            document.getElementById('contact_no_2').value = data.contact_no_2;
            document.getElementById('2contact_no_2').value = data.contact_no_2;
            document.getElementById('tin_number2').value = data.tin_number;
            document.getElementById('tin_number3').value = data.tin_number;
            document.getElementById('contact_person1').value = data.contact_person;
            document.getElementById('contact_person2').value = data.contact_person;
            document.getElementById('InputTax2').value = data.input_tax;
            document.getElementById('InputTax3').value = data.input_tax;
            document.getElementById('supplier_code1').value = data.supplier_code;
            document.getElementById('supplier_code2').value = data.supplier_code;
            document.getElementById('email1').value = data.email;
            document.getElementById('email2').value = data.email;
            document.getElementById('description2').value = data.description;
            document.getElementById('description3').value = data.description;
            document.getElementById('id').value = data.id;


        }
    </script>
@endsection
