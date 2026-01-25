<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\order;
use App\Models\OrderDetail as order_detail;
use Illuminate\Support\Facades\Auth;
use App\Events\RemoteActionTriggered; // Import the event
use App\Models\Order as orderModel; // Renaming to avoid conflict with the Livewire component name
use App\Models\BranchSettingConfig;
use App\Models\ProgramSetting;
use Carbon\Carbon;
use App\Models\Table;
class Orders extends Component
{
    public $orders;
    public $newOrders;
    public $completedOrders;
    public $servedOrders;
    public $activeOrders;
    public $markedAsCompleted = '1';
    public $unitId;
    public $activeTab = 'invoice'; // Track active tab

    public  $orderDetails = [];
    public $selectedItems2Cancel;
    public $reasonForCancelation;
    public $orderId;

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
            $this->newOrders = orderModel::where('order_status', 'PENDING')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->get();
            $this->activeOrders = orderModel::whereIn('order_status', ['SERVING','PENDING'])->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->get();
            session()->flash('success', 'Order List Updated');
            $this->dispatch('dispatch-success');
        }else if($data['action'] == 'deployOrder' && $data['branch_id'] == Auth::user()->branch->id ) {
            $this->newOrders = orderModel::where('order_status', 'PENDING')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->get();
            $this->activeOrders = orderModel::whereIn('order_status', ['SERVING','PENDING'])->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->get();
        }else if($data['action'] == 'refreshOrders' && $data['branch_id'] == Auth::user()->branch->id ) {
            $this->activeOrders = orderModel::whereIn('order_status', ['SERVING','PENDING'])->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->get();
            $this->completedOrders = orderModel::where('order_status', 'COMPLETED')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->get();
            $this->servedOrders = orderModel::where('order_status', 'SERVED')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->get();
            $this->newOrders = orderModel::where('order_status', 'PENDING')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->get();

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
        $this->orders = orderModel::where('order_status', '!=', 'FOR ALLOCATION')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->get();
        $this->newOrders = orderModel::where('order_status', 'PENDING')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->get();
        $this->activeOrders = orderModel::whereIn('order_status', ['SERVING', 'PENDING'])->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->get();
        $this->completedOrders = orderModel::where('order_status', 'COMPLETED')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->get();
        $this->servedOrders = orderModel::where('order_status', 'SERVED')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', Carbon::today('Asia/Manila'))->get();
        }
 
    public function markItem($orderId, $markedAs)
    {   //check if the selected item is being marked 
        $checkOrderDetail = order_detail::find($orderId);
        if($checkOrderDetail->marked == true && $markedAs == false){
           $this->dispatch('error', ['message' => 'This item has already been PAID and cannot be unmarked.','orderId' => $orderId]);
            return;
        }

        // check if unmarking password is set and item is being unmarked
        if($this->hasUnmarkingPassword() && $markedAs == false){
            $this->dispatch('open-unmarking-password-modal', ['orderId' => $orderId]);
            return;
        }
        if ($markedAs) {
            $this->markedAsCompleted = 'SERVED';
        } else {
            $this->markedAsCompleted = 'PENDING';
        }

        $order_details = order_detail::find($orderId);
        
        if ($order_details) {
            $order_details->status = $this->markedAsCompleted;
            $order_details->save();

            // check if there are pe
            $unmarkedCount = order_detail::where('order_id', $order_details->order_id)
                ->where('status', 'PENDING')
                ->exists();
            
            // Update order status to SERVING if no 
            if (!$unmarkedCount) {
                $order = orderModel::find($order_details->order_id);
                if ($order && $order->order_status == 'PENDING') {
                    $order->order_status = 'SERVING';
                    $order->save();
                }
            } 

            // Dispatch remote action event
            $payload = [
                'action' => 'refreshOrders',
                'branch_id' => Auth::user()->branch->id,
            ];
            event(new RemoteActionTriggered($payload, auth()->id()));

        } else {
            // Handle the case where the order is not found
            session()->flash('error', 'Order not found.');
        }
    }

    public function verifyUnmarkingPassword($orderId, $inputPassword)
    {
        // Get Setting Id
        $settingId = ProgramSetting::where('setting_key', 'unmarking_password')->first()->id ?? null;

        if (!$settingId) {
            return [
                'success' => false,
                'message' => 'Unmarking password is not set.'
            ];
        }

        $config = BranchSettingConfig::where('branch_id', Auth::user()->branch->id)
            ->where('setting_id', $settingId)
            ->first();
        if ($config && $config->value2 === $inputPassword) {
            
            // Password is correct, proceed to unmark the item
            $this->markedAsCompleted = 'PENDING';

            $order_details = order_detail::find($orderId);
            if ($order_details) {
                $order_details->status = $this->markedAsCompleted;
                $order_details->save();
                
                // update order status to Pending if any item is unmarked
                $order = orderModel::find($order_details->order_id);
                if ($order && $order->order_status == 'SERVING') {
                    $order->order_status = 'PENDING';
                    $order->save();
                }
                return [
                    'success' => true,
                    'message' => 'Item unmarked successfully.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Order not found.'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Invalid password.'
            ];
        }
    }

    public function deployOrder($orderId)
    {
        $order = order::find($orderId);
        if ($order) {
            $order->update([
                'order_status' => 'SERVING',
                'ini' => auth()->id(),
            ]);

            //UPDATE ORDER DETAILS STATUS TO SERVING EXCEPT CANCELLED ITEMS
            order_detail::where('order_id', $orderId)
                ->whereNotIn('status', ['CANCELLED', 'SERVED'])
                ->update(['status' => 'SERVING']);

            // Dispatch remote action event
            $payload = [
                'action' => 'deployOrder',
                'branch_id' => Auth::user()->branch->id,
            ];
            event(new RemoteActionTriggered($payload, auth()->id()));
            
        } else {
            // Handle the case where the order is not found
            session()->flash('error', 'Order not found.');
        }
    }

    public function serveOrder($orderId)
    {
        //check order details if no pending items
        if(order_detail::where('order_id', $orderId)->where('status', 'SERVING')->exists()){
            $this->dispatch('error', ['message' => 'Cannot mark order as SERVED. There are still pending items in this order.']);
            return;
        }
        $order = order::find($orderId);
        if ($order) {
            // check if order is already paid
            if($order->payment_status == 'PAID'){
                    $order->update([
                    'order_status' => 'COMPLETED',
                ]);
                // Update table availability
                $table = table::find($order->table_id);
                if ($table) {
                    $table->availability = 'VACANT';
                    $table->save();
                }
            }else{
                    $order->update([
                    'order_status' => 'SERVED',
                ]);
            }
            

            // Dispatch remote action event
            $payload = [
                'action' => 'refreshOrders',
                'branch_id' => Auth::user()->branch->id,
            ];
            event(new RemoteActionTriggered($payload, auth()->id()));

        } else {
            // Handle the case where the order is not found
            session()->flash('error', 'Order not found.');
        }
    }

    public function hasUnmarkingPassword()
    {
        // Get Setting Id
        $settingId = ProgramSetting::where('setting_key', 'unmarking_password')->first()->id ?? null;

        if (!$settingId) {
            return false; // No setting found, no password required
        }
       
        $config = BranchSettingConfig::where('branch_id', Auth::user()->branch->id)
            ->where('setting_id', $settingId)
            ->first();
        
        if ($config) {
            return true; // Config exists, password required
        } else {
            return false; // No config, no password required
        }
    }

    //call from blade only
    public function makeChanges()
    {
        $payload = [
                'action' => 'refreshOrders',
                'branch_id' => Auth::user()->branch->id,
            ];
            event(new RemoteActionTriggered($payload, auth()->id()));
    }
    public function render()
    {
        return view('livewire.orders', ['orders' => $this->orders]);
    }

    // choose option first
    public function openCancelOptionsModal($orderId)
    {
        $this->dispatch('open-cancel-options-modal', ['orderId' => $orderId]);
    }
    public function openCancelItemsModal($orderId)
    {
        $this->orderDetails = order_detail::where('order_id', $orderId)->get();
        $this->dispatch('open-cancel-items-modal', ['orderId' => $orderId]);
    }

    public function cancelOrder($orderId){
        $this->dispatch('open-cancel-reason-modal', ['orderId' => $orderId]);
    }

    public function selectedItem( $detailId , $isSelected)
    {
       
       if ($isSelected) {
            // Add to selected items
            $this->selectedItems2Cancel[] = $detailId;
        } else {
            // Remove from selected items
            $this->selectedItems2Cancel = array_filter($this->selectedItems2Cancel, function($id) use ($detailId) {
                return $id != $detailId;
            });
        }

    }
    public function cancelSelectedItems()
    {
          $this->validate([
            'selectedItems2Cancel' => 'required|array|min:1',
            'reasonForCancelation' => 'required|string'
        ], [
            'selectedItems2Cancel.required' => 'Please select at least one item to cancel.',
            'selectedItems2Cancel.min' => 'Please select at least one item to cancel.',
        ]);

        if($this->hasCancelingPassword()){
            $this->dispatch('open-canceling-password-modal', ['orderId' => $this->orderId]);
            return;
        }else{
            $this->proccessOrderItemsCancelation();
        }   
    }

    public function hasCancelingPassword()
    {
        // Get Setting Id
        $settingId = ProgramSetting::where('setting_key', 'canceling_password')->first()->id ?? null;

        if (!$settingId) {
            return false; // No setting found, no password required
        }
       
        $config = BranchSettingConfig::where('branch_id', Auth::user()->branch->id)
            ->where('setting_id', $settingId)
            ->first();
        
        if ($config) {
            return true; // Config exists, password required
        } else {
            return false; // No config, no password required
        }
    }

    public function verifyCancelingPassword($inputPassword)
    {
        // Get Setting Id
        $settingId = ProgramSetting::where('setting_key', 'canceling_password')->first()->id ?? null;

        if (!$settingId) {
            return [
                'success' => false,
                'message' => 'Canceling password is not set.'
            ];
        }

        $config = BranchSettingConfig::where('branch_id', Auth::user()->branch->id)
            ->where('setting_id', $settingId)
            ->first();
        if ($config && $config->value2 === $inputPassword) {
            return [
                'success' => true,
                'message' => 'Password verified.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Invalid password.'
            ];
        }
    }

    

    public function proccessOrderItemsCancelation(){

        // Proceed with cancellation logic for selected items
        $itemsToCancel = $this->selectedItems2Cancel;

        // Reset selected items after cancellation
        $this->selectedItems2Cancel = [];

    }

    public function resetCancelDetails(){
        $this->selectedItems2Cancel = [];
    }

    // for order cancelation
    public function submitCancelReason($reason){
        //validate
        $this->reasonForCancelation = $reason;
        $this->validate([
            'reasonForCancelation' => 'required|string'
        ], [
            'reasonForCancelation.required' => 'Please specify the reason for cancelation.',
        ]);
        // check for password requirement
        if($this->hasCancelingPassword()){
            $this->dispatch('open-canceling-byOrder-password-modal', ['orderId' => $this->orderId]);
            return [
                'success' => true,
                'requiresPassword' => true,
                'message' => 'Password verification required.'
            ];
        }else{
            $this->processCancelOrder();
            return [
                'success' => true,
                'requiresPassword' => false,
                'message' => 'Order cancelled successfully.'
            ];
        }

    }

    public function processCancelOrder(){
        // proceed with order cancelation logic

        dd('success');
    }

}
