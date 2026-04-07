<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\CashReturn;
use App\Models\PettyCashVoucher;
use App\Models\PCVDetail;
use WireUi\Traits\WireUiActions;
use Carbon\Carbon;
use App\Models\Module;
use App\Models\Signatory;


class CashReturnSummary extends Component
{
    use WireUiActions;

// fetched data
public $cashReturns = [];
public $cvReferenceNumber;
// filters
 public $statusCheckValue = 'ALL';
 public $fromDate;
 public $toDate;

 // for event CRS
 public $events;

 // FOR PCV CASH RETURN
 public $pettyCashVouchers;
 public  $selectedPCV = [];
 public $selectedPCVId;
 public $returnAmountPCV;
 public $pcvNote;
 public $saveAsPcvCrs = 'DRAFT';

    public function render()
    {
        return view('livewire.transactions.cash-return-summary');
    }

    public function mount(){
        $this->fetchData();
    }

    public function fetchData(){
        $this->cashReturns = CashReturn::where('branch_id', auth()->user()->branch_id)->get();
        $this->pettyCashVouchers = PettyCashVoucher::where('branch_id' , auth()->user()->branch_id)->where('status', 'OPEN')->get();
        // $pcvModuleId = Module::where('module_name', 'Cash Flow')->first()->id;
        // $this->pcvReturnApprovers = Signatory::with('employees')
        //     ->where('branch_id', auth()->user()->branch_id)
        //     ->where('module_id', $pcvModuleId)
        //     ->where('signatory_type', 'APPROVER')
        //     ->get()
        //     ->map(function ($user) {
        //         return [
        //             'id' => $user->employees->id,
        //             'full_name' => $user->employees->name . ' ' . $user->employees->middle_name. ' ' . $user->employees->last_name,
        //         ];
        //     });
    }

    public function search()
    {
        // Implement your search logic here based on the selected status and date range
        // You can emit an event or update a property to trigger the search results to be displayed
    }

    public  function updatedSelectedPCVId(){
        $pcv = PettyCashVoucher::where('id',$this->selectedPCVId)->get();
        if($pcv){
            $this->selectedPCV = $pcv;
        }
    }

    public function savePcvCrs(){
        $this->validate([
            'selectedPCVId' => 'required',
            'returnAmountPCV' => 'required|numeric|min:0',
        ]);
        $currentYear = now()->year;
        $branchId = auth()->user()->branch_id;
        $yearlyCount = CashReturn::where('branch_id', $branchId)
            ->whereYear('created_at', $currentYear)
            ->count();
        $this->cvReferenceNumber = 'PCR-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);
        $cashReturn = CashReturn::create([
            'reference' => $this->cvReferenceNumber,
            'branch_id' => auth()->user()->branch_id,
            'pcv_id' => $this->selectedPCVId,
            'prepared_by' => auth()->user()->id,
            'amount_returned' => $this->returnAmountPCV,
            'notes' => $this->pcvNote,
            'status' => $this->saveAsPcvCrs, // Use the selected status from the dropdown
        ]);
        $this->modal()->close('cardModal');
        $this->notify('Cash Return Saved', 'success', 'The cash return has been saved successfully.');
        $this->fetchData();
    }


        public function notify( $title , $icon, $description){
            $this->notification()->send([
                'title'       => $title,
                'description' => $description,
                'icon'        => $icon,
            ]);
    }

}
