<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\Backorder;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;
use App\Models\Cardex;
use App\Models\Receiving;

class BackOrderShow extends Component
{
    public  $requestInfo;
    public $backorderItems = [];
    public $poNo= [];
    public $backOrders = [];
    public $receivingList = [];
    public $totalRegCost= 0;
    public $receivingCount =0;
    public $totalToReceiveCost;
    public $showModal = false;


     public function openReceivingNumber($receivingNo,$requisitionId)
    {
      //redirect to receiving page with the selected receing id request
      return redirect()->to('/receive_stock?receiving-number=' . $receivingNo . '&requisition-id=' . $requisitionId);
    }

    public function render()
    {
        return view('livewire.inventory.back-order-show');
    }

    public function mount(Request $request){
       if(auth()->user()->employee->getModulePermission('Back Orders') == 2 ){
              return redirect()->to('dashboard');
         }else{
            $this->requestInfo = RequisitionInfo::where('requisition_number', $request->query('requisition-number'))->first();
            $this->receivingList = Receiving::where('requisition_id',$this->requestInfo->id)->get();
            $this->receivingCount  = $this->receivingList->count();
            $details = RequisitionDetail::with('items','cost')->where('requisition_info_id', $this->requestInfo->id)->get();

            foreach($details as  $item){
                $cardex = new Cardex();
                $this->backorderItems [$item->item_id] =
                ['item_id' => $item->item_id,
                'req_qty' => $item->qty,
                'received' => $cardex->totalInByRequisition($item->requisition_info_id, $item->item_id) ?? 0,
                'lacking' =>  $item->qty - $cardex->totalInByRequisition($item->requisition_info_id, $item->item_id) ?? 0,
                'new_cost'  => $item->items->costPrice->amount ?? 0,
                'req_cost'  => $cardex->reqisteredPriceByReceiving($item->requisition_info_id, $item->item_id) ?? ($item->items->costPrice->amount ?? 0),
            ];
            }
            // dd($this->backorderItems);

            $this->backOrders = Backorder::with('cardex','requisitionInfo')->where('requisition_id',$this->requestInfo->id)->get();
            foreach($this->backOrders as $boItem){
                $this->totalRegCost +=  $this->backorderItems[$boItem->item_id]['req_cost'] * $this->backorderItems[$boItem->item_id]['received'] ;
                $this->totalToReceiveCost += $this->backorderItems[$boItem->item_id]['new_cost']  * $this->backorderItems[$boItem->item_id]['lacking'] ;
            }
        }
    }
}
