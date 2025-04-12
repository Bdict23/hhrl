<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function store(Request $request)
    {

        // Mag Validate sa form data
        $validatedData = $request->validate([
            'supp_name' => 'required|string|max:155',
            'postal_address' => 'nullable|string|max:25',
            'contact_no_1' => 'nullable|string|max:25',
            'supp_address' => 'nullable|string|max:155',
            'contact_no_2' => 'nullable|string|max:55',
            'tin_number' => 'nullable|string|unique:suppliers,tin_number',
            'contact_person' => 'nullable|string|max:100',
            'input_tax' => 'nullable|string|max:55',
            'supplier_code' => 'nullable|string|max:55',
            'email' => 'nullable|email|unique:suppliers,email',
            'description' => 'nullable|string|max:255',
        ]);

        // Mag Create supplier record

        $validatedData['company_id'] = auth()->user()->emp_id;
        $supplier = new Supplier($validatedData);
        $supplier->company_id = auth()->user()->branch->company_id;
        $supplier->save();



        return redirect()->back()->with('success', 'Supplier added successfully!');
    }


    public function update(Request $request)
    {
        try {
            $supplier = Supplier::find($request->id);

            // Mag Validate sa form data
            $validatedData = $request->validate([
                'supp_name' => 'required|string|max:155',
                'postal_address' => 'nullable|string|max:55',
                'contact_no_1' => 'nullable|string|max:55',
                'supp_address' => 'nullable|string|max:55',
                'contact_no_2' => 'nullable|string|max:155',
                'tin_number' => 'nullable|string|max:55',
                'contact_person' => 'nullable|string|max:100',
                'input_tax' => 'required|string|max:55',
                'supplier_code' => 'nullable|string|max:55|unique:suppliers,supplier_code,' . $supplier->id,
                'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
                'description' => 'nullable|string|max:255',
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
        $suppliers = Supplier::where('company_id', auth()->user()->branch->company_id)->get();

        if ($suppliers->isEmpty()) {
            return view('supplier_list', compact('suppliers'))->with('error', 'No supplier found!');
        }

        return view('supplier_list', compact('suppliers'));
    }


    public function deactivate($id)

    {
       $supplier = Supplier::find($id);
       $supplier->supplier_status = 'INACTIVE';
       $supplier->save();
       return redirect()->back()->with('success', 'Supplier deactivated successfully!');
   }



}
