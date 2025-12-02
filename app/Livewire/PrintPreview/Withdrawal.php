<?php

namespace App\Livewire\PrintPreview;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\Withdrawal as WithdrawalModel;
use App\Models\Cardex;


class Withdrawal extends Component
{
    public $withdrawalId;
    public $withdrawalData;
    public $branchName;
    public $hasReviewer = false; // check if reviewer is required
    public $reference;
    public $selectedDepartment;
    public $useDate;
    public $spanDate;
    public $remarks;
    public $reviewer;
    public $approver;
    public $isAlreadyFinal = false;
    public $finalStatus = false;
    public $haveSpan = false;
    public $eventId;
    public $eventName;
    public $selectedItems = [];
    public $overallTotal = 0.00;

    public function render()
    {
        return view('livewire.print-preview.withdrawal');
    }

    public function mount(Request $request = null)
    {
        $this->hasReviewer = auth()->user()->branch->getBranchSettingConfig('Allow Reviewer on Withdrawal') == 1 ? true : false;
        $this->branchName = auth()->user()->branch->branch_name;
        if ($request->has('withdrawal-id')) {
            $this->withdrawalId = $request->input('withdrawal-id');
            $this->fetchData();
        }
    }

    public function fetchData()
    {
        $this->withdrawalData = WithdrawalModel::with('department', 'approvedBy', 'reviewedBy', 'cardex.item','preparedBy')->findOrFail($this->withdrawalId);
        $this->hasReviewer = $this->withdrawalData->reviewed_by != null ? true : false;
        $this->reference = $this->withdrawalData->reference_number;
        $this->useDate = $this->withdrawalData->usage_date;
        $this->spanDate = $this->withdrawalData->useful_date;
        $this->remarks = $this->withdrawalData->remarks;
        $this->reviewer = $this->withdrawalData->reviewed_by;
        $this->approver = $this->withdrawalData->approved_by;
        $this->isAlreadyFinal = $this->withdrawalData->withdrawal_status != 'PREPARING' ? true : false;
        $this->finalStatus = $this->isAlreadyFinal;
        $this->haveSpan = $this->withdrawalData->useful_date != null ? true : false;
        $this->selectedItems = [];
        

        if ($this->withdrawalData->event_id != null) {
            $this->eventId = $this->withdrawalData->event_id;
            $event = $this->withdrawalData->event;
            $this->eventName = $event->event_name ?? '';
        } else {
            $this->eventId = null;
            $this->eventName = null;
        }
        foreach ($this->withdrawalData->cardex as $item) {
            $totalIn = Cardex::where('status', 'final')->where('item_id', $item->item_id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_in');
            $totalOut = Cardex::where('status', 'final')->where('item_id', $item->item_id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_out');
            $totalReserved = Cardex::where('status', 'reserved')->where('item_id', $item->item_id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_out');
            $totalBal = $totalIn - $totalOut;
            $totalAvailable = $totalBal - $totalReserved;
            
            if ($item['qty_out'] > 0) {
                $this->selectedItems[] = [
                    'id' => $item['item_id'],
                    'requested_qty' => (float) $item['qty_out'] ? (float) $item['qty_out'] : 0,
                    'total_balance' =>  $totalBal,
                    'total_available' => $totalAvailable,
                    'code' => $item->item->item_code ?? 'N/A',
                    'name' => $item->item->item_description,
                    'unit' => $item->item->uom->unit_symbol,
                    'category' => $item->item->category->category_name,
                    'classification' => $item->item->classification->classification_name ?? 'N/A',
                    'barcode' => $item->item->item_barcode ?? 'N/A',
                    // 'location' => $item->item->location->location_name ?? 'N/A',
                    'uom' => $item->item->uom->unit_name ?? 'N/A',
                    'brand' => $item->item->brand->brand_name ?? 'N/A',
                    'status' => $item->item->item_status,
                    'cost' => $item->item->costPrice->amount,
                    'costId' => $item->item->costPrice->id,
                    'total' => $item['qty_out'] * ($item->item->costPrice->amount ?? 0),
                ];
            }
            $this->overallTotal += (float) $item['qty_out'] * (float) $item->item->costPrice->amount;

        }

    }
}
