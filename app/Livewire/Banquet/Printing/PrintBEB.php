<?php

namespace App\Livewire\Banquet\Printing;
use App\Models\BanquetProcurement;
use Illuminate\Http\Request;
use Livewire\Component;
use App\Models\PettyCashVoucher;

class PrintBEB extends Component
{
    public $banquetEventBudget = [];
    public $totalGrossOrder = 0;
    public $percentageBudget = 0;
    public $actualExpense = 0;
    public $variance = 0;
    public $grossIncome = 0;
    

    public function render()
    {
        return view('livewire.banquet.printing.print-b-e-b');
    }

    public function mount(Request $request)
    {
       if($request->has('beb-id')) {
           $bebId = $request->query('beb-id');
            $this->banquetEventBudget = BanquetProcurement::find($bebId);
            $totalPcvAmount = PettyCashVoucher::where('event_id', $this->banquetEventBudget->event_id)->sum('total_amount');
            $this->actualExpense = $totalPcvAmount;
            $this->variance = $this->banquetEventBudget->suggested_amount - $this->actualExpense;
            $this->calculateGrossOrderAmount();
            if (!$this->banquetEventBudget) {
                abort(404, 'Banquet Event Budget not found');
            }
       } else {
           abort(404, 'Event ID is required');
       }

    }


    public function calculateGrossOrderAmount(){
        if($this->banquetEventBudget->services_included == 1){
            $total = 0;
             $total +=
             isset($this->banquetEventBudget) && $this->banquetEventBudget->event->eventMenus ? 
             $this->banquetEventBudget->event->eventMenus->sum(function($menu) {
                    return $menu->price->amount * ($menu->qty ? $menu->qty : 1); }): 0;
                $total += isset($this->banquetEventBudget) && $this->banquetEventBudget->event->eventServices ?
                $this->banquetEventBudget->event->eventServices->sum(function($service) {
                    return $service->price->amount * ($service->qty ? $service->qty : 1); }) : 0;
            $this->totalGrossOrder = $total;
        }else {
             $this->totalGrossOrder =  isset($this->banquetEventBudget) && $this->banquetEventBudget->event->eventMenus ? 
             $this->banquetEventBudget->event->eventMenus->sum(function($menu) {
                    return $menu->price->amount * ($menu->qty ? $menu->qty : 1); }): 0;
        }

            $this->grossIncome = $this->totalGrossOrder - $this->actualExpense;
    } 
}
