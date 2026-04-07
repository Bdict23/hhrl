<?php

namespace App\Livewire\Accounting;

use Livewire\Component;
use App\Models\CashflowAccountTitle;
use App\Models\Company;
use App\Models\Branch;
use WireUi\Traits\WireUiActions;

class CashflowTitleSummary extends Component
{
    use WireUiActions;
    public $cashFlowsTitles = [];
    public $businessUnits = [];
    public $branches = [];

    //create form
    public $selectedBusinessUnitId;
    public $selectedBranchId;
    public $titleName;
    public $description;
    public $type;

    // update 
    public $selectedTitleId;


    public function render()
    {
        return view('livewire.accounting.cashflow-title-summary');
    }

    public function mount(){
        $this->fetchData();
    }
    public function fetchData(){
        $this->cashFlowsTitles = CashflowAccountTitle::all();
        $this->businessUnits = Company::where('company_status', 'ACTIVE')->get();
    }
    public function edit($id){
        $title = CashflowAccountTitle::find($id);
        if($title){
            $this->modal()->open('updateCardModal');
            $this->selectedBusinessUnitId = $title->company_id;
            $this->selectedBranchId = $title->branch_id;
            $this->titleName = $title->title;
            $this->description = $title->description;
            $this->type = $title->type;
            $this->selectedTitleId = $title->id;
        }
    }
  

    public function updatedSelectedBusinessUnitId($value){
        if($value){
            $this->branches = Branch::where('company_id', $value)->get();
        }else{             
            $this->branches = [];
        }
    }

    public function save(){
        $this->validate([
            'selectedBusinessUnitId' => 'required',
            'selectedBranchId' => 'required',
            'titleName' => 'required|string|max:255',
            'type' => 'required|in:COLLECTION,LESS',
        ]);

        $title = new CashflowAccountTitle();
        $title->company_id = $this->selectedBusinessUnitId;
        $title->branch_id = $this->selectedBranchId;
        $title->title = $this->titleName;
        $title->description = $this->description;
        $title->type = $this->type;
        $title->status = 'ACTIVE';
        $title->save();

        // Reset form fields
        $this->selectedBusinessUnitId = null;
        $this->selectedBranchId = null;
        $this->titleName = null;
        $this->description = null;
        $this->type = null;

        $this->successNotificationTitle('Cashflow Title Saved!', 'The cashflow title has been successfully saved.');
        $this->modal()->close('cardModal');
        // Refresh data
        $this->fetchData();

    }


    public function successNotificationTitle($title, $message): void
    {
        $this->notification()->send([
            'icon' => 'success',
            'title' => $title ,
            'description' => $message ,
        ]);
    }

    public function changeStatus($id){
        $title = CashflowAccountTitle::find($id);
        if($title){
            $title->status = $title->status == 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
            $title->save();
            $this->fetchData();
            $this->successNotificationTitle('Cashflow Title Status Changed!', 'The cashflow title status has been successfully changed.');
        }
    }

    public function update(){
        $this->validate([
            'selectedBusinessUnitId' => 'required',
            'selectedBranchId' => 'required',
            'titleName' => 'required|string|max:255',
            'type' => 'required|in:COLLECTION,LESS',
        ]);

        $title = CashflowAccountTitle::find($this->selectedTitleId);
        if($title){
            $title->company_id = $this->selectedBusinessUnitId;
            $title->branch_id = $this->selectedBranchId;
            $title->title = $this->titleName;
            $title->description = $this->description;
            $title->type = $this->type;
            $title->save();

            // Reset form fields
            $this->selectedBusinessUnitId = null;
            $this->selectedBranchId = null;
            $this->titleName = null;
            $this->description = null;
            $this->type = null;
            $this->selectedTitleId = null;

            $this->successNotificationTitle('Cashflow Title Updated!', 'The cashflow title has been successfully updated.');
            $this->modal()->close('updateCardModal');
            // Refresh data
            $this->fetchData();
        }
    }

}
