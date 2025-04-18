<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\Backorder;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;
use App\Models\Cardex;

class BackOrderShow extends Component
{
    public  $requisitionInfo;
    public $backorderItems = [];
    public $poNo= [];
    public $backOrders = [];

    public function render()
    {
        return view('livewire.inventory.back-order-show');
    }

    public function mount(Request $request){
       
        $this->requisitionInfo = RequisitionInfo::where('requisition_number', $request->query('requisition-number'))->first();
        $details = RequisitionDetail::with('items')->where('requisition_info_id', $this->requisitionInfo->id)->get();

        foreach($details as  $item){
            $cardex = new Cardex();
            $this->backorderItems [$item->item_id] =
            ['item_id' => $item->item_id,
            'req_qty' => $item->qty,
            'received' => $cardex->totalInByRequisition($item->requisition_info_id, $item->item_id),
            'lacking' =>  $item->qty - $cardex->totalInByRequisition($item->requisition_info_id, $item->item_id) ?? 0,
            // 'requisition_id' => $item->requisition_info_id
        ];
        }
        // dd($this->backorderItems);

        $this->backOrders = Backorder::with('cardex','requisitionInfo')->where('requisition_id',$this->requisitionInfo->id)->get();
        
    }
}
