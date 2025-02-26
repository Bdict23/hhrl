
@extends('layouts.master')
@section('content')
 <div>
    <div class="form-container">

    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        <table class="form-table">
            <tr>
                <th colspan="4">SUPPLIER</th>
            </tr>
            <tr>
                <td>Name</td>
                <td><input type="text" name="supp_name" required></td>
                <td>Postal Address</td>
                <td><input type="text" name="postal_address" required></td>
            </tr>
            <tr>
                <td>Contact No. 1</td>
                <td><input type="text" name="contact_no_1" required></td>
                <td>Address</td>
                <td><input type="text" name="supp_address" required></td>
            </tr>
            <tr>
                <td>Contact No. 2</td>
                <td><input type="text" name="contact_no_2"></td>
                <td>Tax Payer ID</td>
                <td><input type="text" name="tax_payer_id" required></td>
            </tr>
            <tr>
                <td>Contact Person</td>
                <td><input type="text" name="contact_person" required></td>
                <td>Input Tax</td>
                <td><input type="text" name="input_tax"></td>
            </tr>
            <tr>
                <td>Supplier Code</td>
                <td><input type="text" name="supplier_code" required></td>
                <td>Email</td>
                <td><input type="email" name="email" required></td>
            </tr>
            <tr>
                <td><input type="submit" value="Save" CLASS = "btn btn-outline-success"></td>
                <td>
                  
                
              
                </td>
            </tr>
        </table>
    </form>

   
        

</div>




@endsection

