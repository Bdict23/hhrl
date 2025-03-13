<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UOM;
use App\Models\Audit;
use App\Models\Company;

class ItemUnitMeasure extends Component
{

    public $unit_of_measures;
    public $unit_of_measure;

    public $unit_name;
    public $unit_symbol;
    public $unit_description;
    public $company_id;

    public $companies;

    public $AddUnitOfMeasureTab = 0;
    public $UnitOfMeasureListTab = 0;

    protected $rules = [
        'unit_name' => 'required|string|max:255',
        'unit_symbol' => 'required|string|max:255',
        'unit_description' => 'nullable|string|max:255',
        'company_id' => 'required|integer',
    ];

    public function mount()
    {
        $this->fetchData();
    }

    public function showAddUnitOfMeasure()
    {
        $this->AddUnitOfMeasureTab = 1;
        $this->UnitOfMeasureListTab = 0;
        $this->fetchData();
    }

    public function store()
    {

        $this->AddUnitOfMeasureTab = 1;
        $this->UnitOfMeasureListTab = 0;
        $this->validate();
        $unit_of_measure = new UOM();
        $unit_of_measure->unit_name = $this->unit_name;
        $unit_of_measure->unit_description = $this->unit_description;
        $unit_of_measure->unit_symbol = $this->unit_symbol;
        $unit_of_measure->company_id = $this->company_id;
        $unit_of_measure->status = 'ACTIVE';
        $unit_of_measure->created_by = auth()->user()->emp_id;
        $unit_of_measure->save();
        $this->fetchData();

        $this->unit_name = '';
        $this->unit_description = '';
        $this->unit_symbol = '';
        $this->company_id = '';

        $this->AddUnitOfMeasureTab = 0;
        $this->UnitOfMeasureListTab = 1;
    }

    public function fetchData()
    {
        // view only the created companies by the logged in user
        $auditCompanies = Audit::with('company')->where('created_by', auth()->user()->emp_id)->get();
        $companyIds = $auditCompanies->pluck('company.id')->toArray();
        $this->companies = Company::where('company_status', 'ACTIVE')->whereIn('id', $companyIds)->get();
        $this->unit_of_measures = UOM::where('status', 'ACTIVE')->get();
    }
    public function render()
    {
        return view('livewire.item-unit-measure', [
            'unit_of_measures' => $this->unit_of_measures,
            'companies' => $this->companies,
        ]);
    }
}
