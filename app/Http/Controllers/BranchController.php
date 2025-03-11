<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Company;
use App\Models\RequisitionInfo;

class BranchController extends Controller
{
    //Pag save sa branch
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'branch_name' => 'required|string|max:255',
            'branch_address' => 'required|string|max:255',
            'branch_code' => 'required|string|max:255',
            'branch_type' => 'required|string|max:255',
            'branch_email' => 'required|email',
            'branch_cell' => 'required|string|max:255',
        ]);

        $branch = new Branch($validatedData);
        $branch->company()->associate(Company::find($request->company_id));
        $branch->save();
        return redirect()->back()->with('success', 'Branch added successfully!');
    }

public function index()
{
   // dd($request->id);
   $branches = Branch::where([['branch_status', 'active'],['company_id', auth()->user()->branch->company_id]])->get();
   return view('branch.branch_list', compact('branches'));
 }

 //update branch
 public function update(Request $request)
 {
    $validatedData = $request->validate([
        'branch_name' => 'required|string|max:255',
        'branch_address' => 'required|string|max:255',
        'branch_code' => 'required|string|max:255',
        'branch_type' => 'required|string|max:255',
        'branch_email' => 'required|email',
        'branch_cell' => 'required|string|max:255',
    ]);
    //dd($request->all());
    $branch = Branch::find($request->branch_id);
    $branch->update($request->all());
    return redirect()->back()->with('success', 'Branch updated successfully!');
 }

 //show function

 public function show($id)
 {
     $branch = Branch::find($id);
     $company  = Company::find($branch->company_id);
     return view('branch.branch_details', compact('branch','company'));
 }

 public function deactivate($id)

 {
    $branch = Branch::find($id);
    $branch->branch_status = 'inactive';
    $branch->save();
    return redirect()->back()->with('success', 'Branch deactivated successfully!');
}



}
