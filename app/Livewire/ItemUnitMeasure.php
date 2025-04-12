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


    protected $rules = [
        'unit_name' => 'required|string|max:55',
        'unit_symbol' => 'required|string|max:25',
        'unit_description' => 'nullable|string|max:100',
    ];

    public function mount()
    {
        $this->fetchData();
    }

    public function showAddUnitOfMeasure()
    {
        $this->fetchData();
    }

    public function store()
    {

        $this->validate();
        $unit_of_measure = new UOM();
        $unit_of_measure->unit_name = $this->unit_name;
        $unit_of_measure->unit_description = $this->unit_description;
        $unit_of_measure->unit_symbol = $this->unit_symbol;
        $unit_of_measure->company_id = auth()->user()->branch->company_id;
        $unit_of_measure->status = 'ACTIVE';
        $unit_of_measure->created_by = auth()->user()->emp_id;
        $unit_of_measure->save();
        $this->reset();
        $this->fetchData();

        $this->dispatch('clearUOMForm');
        session()->flash('success', 'Unit of Measure Created Successfully');

    }

    public function editUOM($id)
    {
        $this->unit_of_measure = UOM::find($id);
        $this->unit_name = $this->unit_of_measure->unit_name;
        $this->unit_symbol = $this->unit_of_measure->unit_symbol;
        $this->unit_description = $this->unit_of_measure->unit_description;
    }

    public function updateUOM()
    {
        $unit_of_measure = UOM::find($this->unit_of_measure->id);
        $unit_of_measure->unit_name = $this->unit_name;
        $unit_of_measure->unit_symbol = $this->unit_symbol;
        $unit_of_measure->unit_description = $this->unit_description;
        $unit_of_measure->save();
        $this->reset();
        $this->fetchData();
        $this->dispatch('clearUOMModalFormUpdate');
        session()->flash('success', 'Unit of Measure Updated Successfully');
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
        return view('livewire.item-unit-measure');
    }

    public function deactivate($id)
    {
            $unit_of_measure = UOM::find($id);
            $unit_of_measure->status = 'INACTIVE';
            $unit_of_measure->save();
            $this->fetchData();
}
}
