<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Audit;

class CompanyController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_code' => 'required|string|max:255',
            'company_tin' => 'required|string|max:255',
            'company_type' => 'required|string|max:255',
            'company_description' => 'required|string|max:255',
            ]);

            // save company record to the database and create an audit record
            $company = new Company($validatedData);
            $company->save();
            $audit = new Audit();
            $audit->company_id = $company->id;
            $audit->created_by = auth()->user()->emp_id;
            $audit->save();

            return redirect()->back()->with('success', 'Company added successfully!');
        } catch (\Exception $e) {
           dd($e->getMessage());
        }
    }

    //create company
    public function index()
    {
        // view only the created companies by the logged in user
        $auditCompanies = Audit::with('company')->where('created_by', auth()->user()->emp_id)->get();
        $companyIds = $auditCompanies->pluck('company.id')->toArray();
        $companies = Company::where('company_status', 'active')->whereIn('id', $companyIds)->get();

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
