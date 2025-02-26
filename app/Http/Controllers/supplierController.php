<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\supplier;

class SupplierController extends Controller
{
    public function store(Request $request)
    { 
        
        // Mag Validate sa form data
        $validatedData = $request->validate([
            'supp_name' => 'required|string|max:255',
            'postal_address' => 'required|string|max:255',
            'contact_no_1' => 'required|string|max:255',
            'supp_address' => 'required|string|max:255',
            'contact_no_2' => 'nullable|string|max:255',
            'tax_payer_id' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'input_tax' => 'nullable|string|max:255',
            'supplier_code' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email',
        ]);

        // Mag Create supplier record
        Supplier::create($validatedData);

     
        return redirect()->back()->with('success', 'Supplier added successfully!');
    }


    public function update(Request $request)
    { 
        try {
            $supplier = Supplier::find($request->id);

            // Mag Validate sa form data
            $validatedData = $request->validate([
                'supp_name' => 'required|string|max:255',
                'postal_address' => 'required|string|max:255',
                'contact_no_1' => 'required|string|max:255',
                'supp_address' => 'required|string|max:255',
                'contact_no_2' => 'nullable|string|max:255',
                'tax_payer_id' => 'required|string|max:255',
                'contact_person' => 'required|string|max:255',
                'input_tax' => 'nullable|string|max:255',
                'supplier_code' => 'required|string|max:255',
                'email' => 'required|email|unique:suppliers,email,' . $supplier->id,
            ]);

            // Mag update supplier record
            $supplier->update($validatedData);

            return redirect()->back()->with('success', 'Supplier updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function index()
    {
        $suppliers = supplier::where('supp_status', 'active')->get(); // Fetching all suppliers from the database
        return view('supplier_list', compact('suppliers')); // Passing data to the view
    }


    public function deactivate($id)

    {
       $supplier = supplier::find($id);
       $supplier->supp_status = 'inactive';
       $supplier->save();
       return redirect()->back()->with('success', 'Supplier deactivated successfully!');
   }
    
   

}
