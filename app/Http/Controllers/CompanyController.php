<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Audit;

class CompanyController extends Controller
{
 
    public function update(Request $request)
    {
        try {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_code' => 'required|string|max:255',
            'company_tin' => 'required|string|max:255',
            'company_type' => 'required|string|max:255',
            'company_description' => 'required|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $company = Company::find($request->myid);
        
        // Update company fields
        $company->company_name = $request->company_name;
        $company->company_code = $request->company_code;
        $company->company_tin = $request->company_tin;
        $company->company_type = $request->company_type;
        $company->company_description = $request->company_description;
        
        // Handle file upload only if a new file is provided
        if ($request->hasFile('company_logo')) {
            $logoName = time().'.'.$request->company_logo->extension();
            $request->company_logo->move(public_path('images'), $logoName);
            $company->company_logo = $logoName;
        }
        
        $company->save();

        return redirect()->back()->with('success', 'Company updated successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->withErrors($e->getMessage())->withInput();
      }
    }

    public function show($id)
    {
        $company = Company::with(['branches'=> function($query){
            $query->where('branch_status', 'active');
        }])->find($id);

        return view('company.company_details', compact('company'));
    }

    public function deactivate($id)
    {
       $company = Company::find($id);
       $company->company_status = 'INACTIVE';
       $company->save();
       return redirect()->back()->with('success', 'Company deactivated successfully!');
   }

}
