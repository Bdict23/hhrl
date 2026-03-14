<?php

namespace App\Livewire\Transactions;
use App\Models\AdvancesForLiquidation;
use App\Models\ModulePermission;
use App\Models\Module;
use App\Models\Signatory;
use Livewire\Component;
use Illuminate\Http\Request;
use Carbon\Carbon;


class AdvancesForLiquidationView extends Component
{
    // loaded
    public $isCreate = true;
    public $currentAFLStatus;
    public $saveAsStatus = 'DRAFT';
    public $disbursers = [];
    public $approvers = [];
    public $selectedDisburser;
    public $hasReturnedAmount = false;
    
    // setters
    public $totalAFLAmount;

    //inputs
    public $reference;
    public $disburserId; // employee id of disburser
    public $amountReceive; // amount received from disburser
    public $amountReturn; 
    public $approverId; // signatory id
    public $notes; // notes for the transaction

    protected $messages = [
        'disburserId.required' => 'The disburser field is required.',
        'disburserId.exists' => 'The selected disburser is invalid.',
        'amountReceive.required' => 'The amount received field is required.',
        'amountReceive.numeric' => 'The amount received must be a number.',
        'amountReceive.min' => 'The amount received must be at least 1.00.',
        'approverId.required' => 'The approver field is required.',
        'approverId.exists' => 'The selected approver is invalid.',
        'saveAsStatus.required' => 'The save as status field is required.',
        'saveAsStatus.in' => 'The save as status must be either DRAFT or FINAL.',
        'notes.string' => 'The notes must be a string.',
        'notes.max' => 'The notes may not be greater than 500 characters.',
    ];

    public function mount(Request $request){
        if($request->has('AFL-id')){
            $this->isCreate = false;
            $aflId = $request->query('AFL-id');
            $this->loadExistingAFLData($aflId);
        }
        $this->fetchData();
    }
    public function fetchData(){
        $moduleId = Module::where('module_name', 'Advances For Liquidation')->first()->id;
        $this->disbursers = ModulePermission::where('module_id', $moduleId)->where('access', 1)->get();
        $this->approvers = Signatory::where([['signatory_type', 'APPROVER'],['module_id', $moduleId  ],['branch_id', auth()->user()->branch_id]])->get();;

    }

    public function render()
    {
        return view('livewire.transactions.advances-for-liquidation-view');
    }

    private function loadExistingAFLData($aflId){
        $afl = AdvancesForLiquidation::findOrFail($aflId);
        $this->reference = $afl->reference;
        $this->disburserId = $afl->received_by;
        $this->amountReceive = $afl->amount_received;
        $this->amountReturn = $afl->amount_returned > 0 ? $afl->amount_returned : null;
        $this->approverId = $afl->approved_by;
        $this->notes = $afl->notes;
        $this->currentAFLStatus = $afl->status;
        $this->hasReturnedAmount = $afl->amount_returned > 0 ? true : false;

        $this->totalAFLAmount = $afl->amount_received - $afl->amount_returned; // Calculate total AFL amount based on received and returned amounts
    }

    public function saveAFL(){
        $this->validate([
            'disburserId' => 'required|exists:employees,id',
            'amountReceive' => 'required|numeric|min:1',
            'approverId' => 'required|exists:employees,id',
            'notes' => 'nullable|string|max:500',
            'saveAsStatus' => 'required|in:DRAFT,OPEN',
        ]);

        $curYear = now()->year;
        $branchId = auth()->user()->branch_id;
        $yearlyCount = AdvancesForLiquidation::where('branch_id', $branchId)
            ->whereYear('created_at', $curYear)
            ->count() + 1;
        $reference = 'AFL-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);

        $afl = AdvancesForLiquidation::create([
            'company_id' => auth()->user()->branch->company_id,
            'branch_id' => auth()->user()->branch_id,
            'reference' => $reference,
            'amount_received' => $this->amountReceive,
            'date_received' => Carbon::now('Asia/Manila'),
            'prepared_by' => auth()->user()->emp_id,
            'received_by' => $this->disburserId,
            'approved_by' => $this->approverId,
            'notes' => $this->notes,
            'status' => $this->saveAsStatus,
            'created_at' => Carbon::now('Asia/Manila'),
        ]);
        // Optionally, you can emit an event or redirect after saving
        $this->dispatch('showAlert', ['type' => 'success', 'title' => 'Success', 'message' => 'Advances for Liquidation saved successfully!']);
        $this->reset();

    }

    public function updateAFL(){
        
        $this->validate([
            'disburserId' => 'required|exists:employees,id',
            'amountReceive' => 'required|numeric|min:1',
            'approverId' => 'required|exists:employees,id',
            'saveAsStatus' => 'required|in:DRAFT,OPEN',
            'notes' => 'nullable|string|max:500',
        ]);
        $afl = AdvancesForLiquidation::where('reference', $this->reference)->firstOrFail();
        $afl->update([
            'amount_received' => $this->amountReceive,
            'received_by' => $this->disburserId,
            'approved_by' => $this->approverId,
            'notes' => $this->notes,
            'status' => $this->saveAsStatus,
            'updated_at' => Carbon::now('Asia/Manila'),
        ]);
        $this->currentAFLStatus = $afl->status; // Update the current status after saving
        // Optionally, you can emit an event or redirect after updating
        $this->dispatch('showAlert', ['type' => 'success', 'title' => 'Success', 'message' => 'Advances for Liquidation updated successfully!']);
    }

    public function updatedAmountReceive(){
        if($this->amountReceive < 0 || !is_numeric($this->amountReceive)){
             $this->totalAFLAmount = 0.00;
            return;
        }
        $this->totalAFLAmount = $this->amountReceive;
    }

    public function updatedAmountReturn(){
        if($this->amountReturn<0 || !is_numeric($this->amountReturn) ){
            return;
        }
         $this->totalAFLAmount = $this->amountReceive - $this->amountReturn;
    }

    public function returnAmount(){
        // validates total amount ensures not negative
        if($this->totalAFLAmount < 0){
            $this->dispatch('showAlert', ['type' => 'error', 'title' => 'Error', 'message' => 'The total AFL amount should not be negative!']);

        }else{
            $this->dispatch('showConfirmOption');
        }
    }
    public function returnAmountConfirm(){
        $afl = AdvancesForLiquidation::where('reference', $this->reference)->firstOrFail();
        $afl->update([
            'amount_returned' => $this->amountReturn,
            'date_returned' => Carbon::now('Asia/Manila'),
        ]);
        $this->dispatch('showAlert', ['type' => 'success', 'title' => 'Success', 'message' => 'Excess amount returned successfully!']);
        $this->hasReturnedAmount = true;
    }
}
