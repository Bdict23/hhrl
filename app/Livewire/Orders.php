<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\order;
use App\Models\OrderDetail as order_detail;
use Illuminate\Support\Facades\Auth;
use App\Events\RemoteActionTriggered; // Import the event
use App\Models\Order as orderModel; // Renaming to avoid conflict with the Livewire component name

class Orders extends Component
{
    public $orders;
    public $newOrders;
    public $completedOrders;
    public $forServingOrders;
    public $markedAsCompleted = '1';
    public $unitId;
    public $activeTab = 'invoice'; // Track active tab

  protected $listeners = [
    'RemoteActionTriggered' => 'handleRemoteExecute',
    'tabChanged' => 'setActiveTab',
];

   
  public function doAction()
    {
        $payload = [
            'unitId' => $this->unitId,
            'action' => 'doSomething',
            // any other data
        ];

        // dispatch broadcast event — server sends to Pusher
        event(new RemoteActionTriggered($payload, auth()->id()));
    }


     public function handleRemoteExecute($payload)
{
    // Extract the actual payload if it's nested
    $data = $payload['payload'] ?? $payload;
    
    // Check if action key exists
    if (!isset($data['action'])) {
        \Log::warning('RemoteActionTriggered received without action key', ['payload' => $payload]);
        return;
    }
    
    if($data['action'] == 'newOrder' && $data['branch_id'] == Auth::user()->branch->id ) {
        $this->newOrders = orderModel::where('order_status', 'PENDING')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', now())->get();
        session()->flash('success', 'New Order Received!');
        $this->dispatch('dispatch-success');
    }else if($data['action'] == 'deployOrder' && $data['branch_id'] == Auth::user()->branch->id ) {
      $this->forServingOrders = orderModel::where('order_status', 'SERVING')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', now())->get();
    $this->newOrders = orderModel::where('order_status', 'PENDING')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', now())->get();
       
    }else{
        return;
    }

    
    

}

    public function setActiveTab($tabId)
    {
        $this->activeTab = $tabId;
    }

    public function mount()
    {
        $this->fetchData();
    }
    public function fetchData()
    {
        $this->orders = orderModel::where('order_status', '!=', 'FOR ALLOCATION')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', now())->get();
        $this->newOrders = orderModel::where('order_status', 'PENDING')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', now())->get();
        $this->forServingOrders = orderModel::where('order_status', 'SERVING')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', now())->get();
        $this->completedOrders = orderModel::where('order_status', 'COMPLETED')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', now())->get();
    }

    public function markItem($orderId, $markedAs)
    {
        if ($markedAs) {
            $this->markedAsCompleted = '1';
        } else {
            $this->markedAsCompleted = '0';
        }

        $order_details = order_detail::find($orderId);

        if ($order_details) {
            $order_details->marked = $this->markedAsCompleted;
            $order_details->save();
        } else {
            // Handle the case where the order is not found
            session()->flash('error', 'Order not found.');
        }
    }

    public function deployOrder($orderId)
    {
         // Dispatch remote action event
            $payload = [
                'action' => 'deployOrder',
                'branch_id' => Auth::user()->branch->id,
            ];
            event(new RemoteActionTriggered($payload, auth()->id()));

        $order = order::find($orderId);
        if ($order) {
            $order->update([
                'order_status' => 'SERVING',
                'ini' => auth()->id(),
                
            ]);

        } else {
            // Handle the case where the order is not found
            session()->flash('error', 'Order not found.');
        }
    }

    public function serveOrder($orderId)
    {
        $order = order::find($orderId);
        if ($order) {
            $order->update([
                'order_status' => 'SERVED',
            ]);

            // Dispatch remote action event
            $payload = [
                'action' => 'serveOrder',
                'branch_id' => Auth::user()->branch->id,
            ];
            event(new RemoteActionTriggered($payload, auth()->id()));

            $this->refreshOrders();
        } else {
            // Handle the case where the order is not found
            session()->flash('error', 'Order not found.');
        }
    }

    public function refreshOrders()
    {
        $this->fetchData();
    }

    public function render()
    {
        return view('livewire.orders', ['orders' => $this->orders]);
    }
}
