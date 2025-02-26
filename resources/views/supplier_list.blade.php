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
                                    <button onclick='getSupplier({!! $supplier !!})' class="action-btn"
                                        data-bs-target="#supplierViewModal" data-bs-toggle="modal"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                            <path
                                                d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                            <path
                                                d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                        </svg></button>
                                    <button onclick='getSupplier({!! $supplier !!})' class="action-btn"
                                        data-bs-target="#supplierUpdateModal" data-bs-toggle="modal"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                                            <path
                                                d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z" />
                                        </svg></button>
                                    <button class="action-btn" data-bs-target="#supplierRemoveModal"
                                        data-bs-toggle="modal"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                            height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                            <path
                                                d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                        </svg></button>
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
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="supp_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="supp_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="postal_address" class="form-label">Postal Address</label>
                                <input type="text" class="form-control" id="postal_address" name="postal_address"
                                    required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_no_1" class="form-label">Contact No. 1</label>
                                <input type="text" class="form-control" id="contact1" name="contact_no_1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="supp_address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="supp_address" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_no_2" class="form-label">Contact No. 2</label>
                                <input type="text" class="form-control" id="contact2" name="contact_no_2">
                            </div>
                            <div class="col-md-6">
                                <label for="tax_payer_id" class="form-label">Tax Payer ID</label>
                                <input type="text" class="form-control" id="taxpayer_id" name="tax_payer_id"
                                    required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="input_tax" class="form-label">Input Tax</label>
                                <input type="text" class="form-control" id="input_tax" name="input_tax" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="supplier_code" class="form-label">Supplier Code</label>
                                <input type="text" class="form-control" id="supplier_code" name="supplier_code"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
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

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="supp_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="supp_name" name="supp_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="postal_address" class="form-label">Postal Address</label>
                                <input type="text" class="form-control" id="postal_address1" name="postal_address"
                                    required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_no_1" class="form-label">Contact No. 1</label>
                                <input type="text" class="form-control" id="contact_no_1" name="contact_no_1"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="supp_address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="supp_address" name="supp_address"
                                    required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_no_2" class="form-label">Contact No. 2</label>
                                <input type="text" class="form-control" id="contact_no_2" name="contact_no_2">
                            </div>
                            <div class="col-md-6">
                                <label for="tax_payer_id" class="form-label">Tax Payer ID</label>
                                <input type="text" class="form-control" id="tax_payer_id" name="tax_payer_id"
                                    required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person1" name="contact_person"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="input_tax" class="form-label">Input Tax</label>
                                <input type="text" class="form-control" id="input_tax1" name="input_tax">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="supplier_code" class="form-label">Supplier Code</label>
                                <input type="text" class="form-control" id="supplier_code1" name="supplier_code"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email1" name="email" required>
                            </div>
                        </div>
                        <input type="hidden" id="id" name='id'>
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
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="supp_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="supp_name2" name="supp_name">
                            </div>
                            <div class="col-md-6">
                                <label for="postal_address" class="form-label">Postal Address</label>
                                <input type="text" class="form-control" id="postal_address2" name="postal_address">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_no_1" class="form-label">Contact No. 1</label>
                                <input type="text" class="form-control" id="2contact_no_1" name="contact_no_1">
                            </div>
                            <div class="col-md-6">
                                <label for="supp_address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="supp_address2" name="supp_address">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_no_2" class="form-label">Contact No. 2</label>
                                <input type="text" class="form-control" id="2contact_no_2" name="contact_no_2">
                            </div>
                            <div class="col-md-6">
                                <label for="tax_payer_id" class="form-label">Tax Payer ID</label>
                                <input type="text" class="form-control" id="tax_payer_id2" name="tax_payer_id">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person2" name="contact_person">
                            </div>
                            <div class="col-md-6">
                                <label for="input_tax" class="form-label">Input Tax</label>
                                <input type="text" class="form-control" id="input_tax2" name="input_tax">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="supplier_code" class="form-label">Supplier Code</label>
                                <input type="text" class="form-control" id="supplier_code2" name="supplier_code">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email2" name="email">
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
            document.getElementById('tax_payer_id').value = data.tax_payer_id;
            document.getElementById('tax_payer_id2').value = data.tax_payer_id;
            document.getElementById('contact_person1').value = data.contact_person;
            document.getElementById('contact_person2').value = data.contact_person;
            document.getElementById('input_tax1').value = data.input_tax;
            document.getElementById('input_tax2').value = data.input_tax;
            document.getElementById('supplier_code1').value = data.supplier_code;
            document.getElementById('supplier_code2').value = data.supplier_code;
            document.getElementById('email1').value = data.email;
            document.getElementById('email2').value = data.email;
            document.getElementById('id').value = data.id;


        }
    </script>
@endsection
