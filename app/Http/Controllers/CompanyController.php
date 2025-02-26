<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Branch;



class CompanyController extends Controller
{    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_code' => 'required|string|max:255',
            'company_tin' => 'required|string|max:255',
            'company_type' => 'required|string|max:255',
            'company_description' => 'required|string|max:255',
        ]);
    
        Company::create($validatedData);
    
        return redirect()->back()->with('success', 'Company added successfully!');
    }


    //create company
    public function index()
    {
        $companies = Company::where('company_status', 'active')->get(); // Fetching all suppliers from the database
        // $branches = Branch::all();

        return view('company.company_list', compact('companies')); // Passing data to the view
    }


    public function update(Request $request)
    {
        try {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_code' => 'required|string|max:255',
            'company_tin' => 'required|string|max:255',
            'company_type' => 'required|string|max:255',
            'company_description' => 'required|string|max:255',
        ]);
        $company = Company::find($request->myid);
        // dd($company);
        $company->update($request->all());
        
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
       $company->company_status = 'inactive';
       $company->save();
       return redirect()->back()->with('success', 'Company deactivated successfully!');
   }

}
