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
public $pcrDate;

// filters
 public $statusCheckValue = 'ALL';
 public $fromDate;
 public $toDate;

 // for event CRS
 public $events;

 // FOR PCV CASH RETURN
 public $pettyCashVouchers;
 public $pettyCashVouchersWithoutCashReturn;
 public  $selectedPCV = [];
 public $selectedPCVId;
 public $returnAmountPCV;
 public $pcvNote;
 public $saveAsPcvCrs = 'DRAFT';
 PUBLIC $isFinal = false;

    public function render()
    {
        return view('livewire.transactions.cash-return-summary');
    }

    public function mount(){
        $this->fetchData();
    }

    public function fetchData(){
        $this->pcrDate = today('Asia/Manila')->format('M. d, Y');
        $this->cashReturns = CashReturn::with('pettyCashVoucher')->where('branch_id', auth()->user()->branch_id)->get();
        $this->pettyCashVouchers = PettyCashVoucher::where('branch_id' , auth()->user()->branch_id)->where('status', 'OPEN')->get();
        $this->pettyCashVouchersWithoutCashReturn = PettyCashVoucher::where('branch_id' , auth()->user()->branch_id)->whereDoesntHave('hasCashReturn')->where('status', 'OPEN')->get();
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
            'prepared_by' => auth()->user()->emp_id,
            'amount_returned' => $this->returnAmountPCV,
            'notes' => $this->pcvNote,
            'status' => $this->saveAsPcvCrs, // Use the selected status from the dropdown
        ]);
            if($this->saveAsPcvCrs == 'FINAL'){
                    $pettyCashVoucher = PettyCashVoucher::where('id', $this->selectedPCVId)->first();
                    if($pettyCashVoucher){
                        $pettyCashVoucher->update([
                            'status' => 'CLOSED',
                        ]);
                    }
                }
        $this->modal()->close('cardModal');
        $this->notify('Cash Return Saved', 'success', 'The cash return has been saved successfully.');
        $this->fetchData();
    }
    public function updatePcvCrs(){
        $this->validate([
            'returnAmountPCV' => 'required|numeric|min:0',
        ]);
        $cashReturn = CashReturn::where('pcv_id', $this->selectedPCVId)->first();
        if($cashReturn){
            $cashReturn->update([
                'amount_returned' => $this->returnAmountPCV,
                'notes' => $this->pcvNote,
                'status' => $this->saveAsPcvCrs, // Use the selected status from the dropdown
            ]);

            if($this->saveAsPcvCrs == 'FINAL'){
                $pettyCashVoucher = PettyCashVoucher::where('id', $this->selectedPCVId)->first();
                if($pettyCashVoucher){
                    $pettyCashVoucher->update([
                        'status' => 'CLOSED',
                    ]);
                }
            }
            $this->modal()->close('cardModalUpdate');
            $this->notify('Cash Return Updated', 'success', 'The cash return has been updated successfully.');
            $this->fetchData();
        }else{
            $this->notify('No Cash Return Found', 'error', 'No cash return record found for the selected PCV.');
        }
    }

    public function viewCashReturnPCV($pcvId){
        $cashReturn =  $this->cashReturns->where('pcv_id', $pcvId)->first();
        if($cashReturn){
            $this->cvReferenceNumber = $cashReturn->reference;
            $this->pcrDate = $cashReturn->created_at->format('M d, Y');
            $this->returnAmountPCV = $cashReturn->amount_returned;
            $this->pcvNote = $cashReturn->notes;
            $this->saveAsPcvCrs = $cashReturn->status;
            $this->selectedPCVId = $pcvId;
            $this->isFinal = $this->saveAsPcvCrs == 'FINAL' ? true : false;
            $this->selectedPCV =  PettyCashVoucher::where('id', $cashReturn->pcv_id)->get();

            //  $this->pettyCashVouchers->where('id', $cashReturn->pcv_id)->get();
            $this->modal()->open('cardModalUpdate');
        }else{
            $this->notify('No Cash Return Found', 'error', 'No cash return record found for the selected PCV.');
        }

    }
    public function viewCashReturnEvent($eventId){
        $cashReturn =  $this->cashReturns->where('event_id', $eventId)->first();
        if($cashReturn){
            $this->cvReferenceNumber = $cashReturn->reference;
            $this->pcrDate = $cashReturn->created_at->format('M d, Y');
            $this->returnAmountPCV = $cashReturn->amount_returned;
            $this->pcvNote = $cashReturn->notes;
            $this->saveAsPcvCrs = $cashReturn->status;
            $this->modal()->open('cardModal');
        }else{
            $this->notify('No Cash Return Found', 'error', 'No cash return record found for the selected event.');
        }
    }


        public function notify( $title , $icon, $description){
            $this->notification()->send([
                'title'       => $title,
                'description' => $description,
                'icon'        => $icon,
            ]);
    }

}
