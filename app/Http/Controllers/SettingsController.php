<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Company;
use App\Models\ItemType;
use App\Models\UOM;
use App\Models\Brand;
use App\Models\Audit;


class SettingsController extends Controller
{
    public function index()
    {
        $auditCompanies = Audit::with('company')->where('created_by', auth()->user()->emp_id)->get();
        $companyIds = $auditCompanies->pluck('company.id')->toArray();
        $companies = Company::where('company_status', 'ACTIVE')->whereIn('id', $companyIds)->get();
        $ItemCategories = Category::where([['company_id', auth()->user()->branch->company_id],['category_type', 'ITEM'], ['status', 'ACTIVE']])->get();
        $MenuCategories = Category::where([['company_id', auth()->user()->branch->company_id],['category_type', 'MENU'], ['status', 'ACTIVE']])->get();
        $classifications = Classification::whereNull('class_parent')->get();
        $sub_classifications = Classification::whereNotNull('class_parent')->get();
        $types = ItemType::all();
        $unit_of_measures = UOM::where('status', 'ACTIVE')->get(); //where('company_id', auth()->user()->branch->company_id)->get();
        $brands = Brand::where('status', 'ACTIVE')->get();

        return view('master_data.settings', compact('ItemCategories', 'MenuCategories', 'classifications', 'companies', 'sub_classifications', 'types', 'unit_of_measures', 'brands'));
    }
    public function storeCategory(Request $request)
    {
        // Logic to store category
        dd($request->all());
    }

    public function storeClassification(Request $request)
    {
        // Logic to store classification
    }
}
