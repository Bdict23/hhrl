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
use App\Models\CancellationOfOrder as Cancellation;
use App\Models\ItemWasteLog;
use App\Models\PriceLevel;
use App\Models\Invoice;
use App\Models\Payment;
USE App\Models\OrderDiscount;


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
    public $reasonForCancelationOfItems;
    public $orderId;
    public $isAdmin = false;

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
        if(auth()->user()->employee->getModulePermission('Restaurant - Monitor') !=2 ){
            if(auth()->user()->employee->getModulePermission('Restaurant - Monitor') == 1){
                $this->isAdmin = true;
            }
        $this->fetchData();
        }else{
            return abort(403, 'Unauthorized action.');
        }
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
            $order_details->is_prepared = true;
            $order_details->served_by = auth()->user()->employee->id;
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
           try{ $payload = [
                'action' => 'refreshOrders',
                'branch_id' => Auth::user()->branch->id,
            ];
            event(new RemoteActionTriggered($payload, auth()->id()));
             }catch(\Exception $e){
             $this->dispatch('error', ['message' => 'Please Manual Refresh the other module for them to get updated', 'title' => 'Notification error']);
        }

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
                $order_details->is_prepared = false;
                $order_details->served_by = null;
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
                ->update(['status' => 'SERVING',
                          'is_prepared' => true,
                          ]);

           try{ // Dispatch remote action event
            $payload = [
                'action' => 'deployOrder',
                'branch_id' => Auth::user()->branch->id,
            ];
            event(new RemoteActionTriggered($payload, auth()->id()));
             }catch(\Exception $e){
             $this->dispatch('error', ['message' => 'Please Manual Refresh the other module for them to get updated', 'title' => 'Notification error']);
        }
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
            

            try{// Dispatch remote action event
            $payload = [
                'action' => 'refreshOrders',
                'branch_id' => Auth::user()->branch->id,
            ];
            event(new RemoteActionTriggered($payload, auth()->id()));
             }catch(\Exception $e){
             $this->dispatch('error', ['message' => 'Please Manual Refresh the other module for them to get updated', 'title' => 'Notification error']);
        }
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
        try{
            $payload = [
                'action' => 'refreshOrders',
                'branch_id' => Auth::user()->branch->id,
            ];
            event(new RemoteActionTriggered($payload, auth()->id()));
             }catch(\Exception $e){
             $this->dispatch('error', ['message' => 'Please Manual Refresh the other module for them to get updated', 'title' => 'Notification error']);
        }
    }
    public function render()
    {
        return view('livewire.orders', ['orders' => $this->orders]);
    }

    // CHOSE OPTION TO CANCEL (ITEM CANCELLATION LEVEL 1-6)
    public function openCancelOptionsModal($orderId)
    {
        $this->dispatch('open-cancel-options-modal', ['orderId' => $orderId]);
    }
    // (ITEM CANCELLATION LEVEL 2-6)
    public function openCancelItemsModal($orderId)
    {
        $this->orderId = $orderId;
        $this->orderDetails = order_detail::where('order_id', $orderId)->where('status', '!=', 'CANCELLED')->get();
        $this->dispatch('open-cancel-items-modal', ['orderId' => $orderId]);
    }

    public function cancelOrder($orderId){
        $this->dispatch('open-cancel-reason-modal', ['orderId' => $orderId]);
        $this->orderId = $orderId;
    }


    //STORE ORDER ITEM FOR CANCELLATION (ITEM CANCELLATION LEVEL 3-6)
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
    
    // VALIDATE ITEM CANCELLATION TO TRIGGER PASSWORD MODAL (ITEM CANCELLATION 4-6)
    public function cancelSelectedItems()
    {
          $this->validate([
            'selectedItems2Cancel' => 'required|array|min:1',
            'reasonForCancelationOfItems' => 'required|string'
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

    //CHECK IF HAS PASSWORD (ITEM CANCELLATION 5-6) (ORDER CANCELLATION 1)
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

    //COMPARE PASSWORD
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

    // (ITEM CANCELLATION LEVEL 6-6)
    public function proccessOrderItemsCancelation(){
        $order = order::find($this->orderId);
        $status = $order->payment_status;
        $itemsToCancel = $this->selectedItems2Cancel;
        $amount2Refund = 0.00;
        $itemCount = order_detail::where('order_id', $this->orderId)->where('status', '!=', 'CANCELLED')->count();
        $items = order_detail::where('order_id', $this->orderId)->where('status', '!=', 'CANCELLED')->whereIn('id', $itemsToCancel)->with('priceLevel')->get(); // get all items except cancelled ones to avoid duplication


        if($items->count() == $itemCount){
            $this->processCancelOrder();
        }else{
             
            // INSERT NEW CANCELLATION LOGS
                    $cancellation = new Cancellation();
                    $cancellation->branch_id = auth()->user()->branch->id;
                    $cancellation->order_id = $this->orderId;
                    $cancellation->reason_code = $this->reasonForCancelationOfItems;
                    $cancellation->cancelled_by = auth()->user()->emp_id;
                    $cancellation->save();
                    $cancellationID = $cancellation->id;

 
                    //UPDATE ORDER DETAILS AND INSERTION OF WASTE LOG IF PREPARED
                    foreach($items as $item){

                        // CHECK AND SUM ALL REFUNDABLE ITEMS
                        if($item->marked == true){
                            $discounts = orderDiscount::where('order_id',$this->orderId)
                                ->where('order_detail_id', $item->id)
                                ->get();
                            foreach($discounts as $discount){$discount->update(['status' => 'CANCELLED'])->save();}
                            $amount2Refund += (($item->priceLevel->amount * $item->qty) - $discounts->sum('calculated_amount'));
                        }
                        //INSERT TO WASTE LOG IF PREPARED
                        if($item->is_prepared == 1){
                            $waste = new ItemWasteLog();
                            $waste->branch_id = auth()->user()->branch->id;
                            $waste->cancellation_id = $cancellationID;
                            $waste->waste_selling_price = (PriceLevel::find($item->price_level_id)->amount * $item->qty);
                            $waste->price_level_srp = (PriceLevel::find($item->price_level_id)->id);
                            $waste->waste_cost = (PriceLevel::find($item->price_level_cost)->amount * $item->qty);
                            $waste->price_level_cost = (PriceLevel::find($item->price_level_cost)->id);
                            $waste->order_detail_id = $item->id;
                            $waste->created_at = Carbon::now('Asia/Manila');
                            $waste->updated_at = Carbon::now('Asia/Manila');
                            $waste->save();

                        }
                        // UPDATE ORDER DETAIL STATUS TO CANCELLED
                        $item->status = 'CANCELLED';
                        $item->save();
                    }
                    
                    if($status == 'PAID' || $status == 'PARTIAL'){
                        
                         // PROCESS REFUND
                            //UPDATE INVOICE STATUS TO CANCELLED
                            $invoice = Invoice::where('order_id', $this->orderId)->first();
                            $invoice->status = 'PARTIAL_REFUND';
                            $invoice->adjusted_amount = $amount2Refund;
                            $invoice->amount = $invoice->amount - $amount2Refund;
                            $invoice->updated_by = auth()->user()->emp_id;
                            $invoice->updated_at = Carbon::now('Asia/Manila');
                            $invoice->save();
                            
                        //check payment based on invoice id
                            $payments = Payment::where('invoice_id', $invoice->id)->get();
                            foreach($payments as $payment){
                                // INSERT PAYMENT AS REFUNDED
                                $refundedPayment = new Payment();
                                $refundedPayment->branch_id = $payment->branch_id;
                                $refundedPayment->customer_id = $payment->customer_id;
                                $refundedPayment->invoice_id = $payment->invoice_id;
                                $refundedPayment->amount = $amount2Refund;
                                $refundedPayment->status = $payment->status;
                                $refundedPayment->payment_type_id = $payment->payment_type_id;
                                $refundedPayment->type = 'REFUND';
                                $refundedPayment->updated_by = auth()->user()->emp_id;
                                $refundedPayment->prepared_by = $payment->prepared_by;
                                $refundedPayment->payment_parent = $payment->id;
                                $refundedPayment->created_at = Carbon::now('Asia/Manila');
                                $refundedPayment->updated_at = Carbon::now('Asia/Manila');
                                $refundedPayment->save();
                            }

                            // update table if order status is completed
                            if($order->order_status == 'COMPLETED'){
                                $table = table::find($order->table_id);
                                if ($table) {
                                    $table->availability = 'VACANT';
                                    $table->save();
                                }
                            }
                       
                    }
                    
            }
        // Reset selected items after cancellation
        $this->selectedItems2Cancel = [];
        try{ $payload = [
                'action' => 'refreshOrders',
                'branch_id' => Auth::user()->branch->id,
            ];
            event(new RemoteActionTriggered($payload, auth()->id()));
             }catch(\Exception $e){
             $this->dispatch('error', ['message' => 'Please Manual Refresh the other module for them to get updated', 'title' => 'Notification error']);
        }

    }

    public function resetCancelDetails(){
        $this->selectedItems2Cancel = [];
    }

    // for order cancelation
    public function submitCancelReason($reason){
        //validate
        $this->reasonForCancelationOfItems = $reason;
        $this->validate([
            'reasonForCancelationOfItems' => 'required|string'
        ], [ 
            'reasonForCancelationOfItems.required' => 'Please specify the reason for cancelation.',
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

    // PROCESS THE CANCELLATION FOR WHOLE ORDER
    public function processCancelOrder(){
        //order status
        $order = order::find($this->orderId);
        $status = $order->payment_status;
        
        if($status != 'REFUNDED'){
            //PROCESS REFUND AND WASTE LOGSS AND CANCELLED LOGS(ORDER ONLY)
                // SET ORDER STATUS TO CANCELLED
                if($status == 'PAID' || $status == 'PARTIAL'){
                $order->update([
                'order_status' => 'CANCELLED',
                'payment_status' => 'REFUNDED',
                ]);
                }else{
                    $order->update([
                    'order_status' => 'CANCELLED',
                    ]);
                }
                // INSERT NEW CANCELLATION LOGS
                $cancellation = new Cancellation();
                $cancellation->branch_id = auth()->user()->branch->id;
                $cancellation->order_id = $this->orderId;
                $cancellation->reason_code = $this->reasonForCancelationOfItems;
                $cancellation->cancelled_by = auth()->user()->emp_id;
                $cancellation->save();

                $cancellationID = $cancellation->id;
                // check if order has served items if true insert to waste logs
                $items = order_detail::where('order_id', $this->orderId)->where('status', '!=', 'CANCELLED')->get(); // get all items except cancelled ones to avoid duplication
                
                //UPDATE ORDER DETAILS AND INSERTION OF WASTE LOG IF PREPARED
                foreach($items as $item){
                     //INSERT TO WASTE LOG IF PREPARED
                    if($item->is_prepared == 1){
                        $waste = new ItemWasteLog();
                        $waste->branch_id = auth()->user()->branch->id;
                        $waste->cancellation_id = $cancellationID;
                        $waste->waste_selling_price = (PriceLevel::find($item->price_level_id)->amount * $item->qty);
                        $waste->price_level_srp = (PriceLevel::find($item->price_level_id)->id);
                        $waste->waste_cost = (PriceLevel::find($item->price_level_cost)->amount * $item->qty);
                        $waste->price_level_cost = (PriceLevel::find($item->price_level_cost)->id);
                        $waste->order_detail_id = $item->id;
                        $waste->created_at = Carbon::now('Asia/Manila');
                        $waste->updated_at = Carbon::now('Asia/Manila');
                        $waste->save();

                    }
                    // UPDATE ORDER DETAIL STATUS TO CANCELLED
                    $item->status = 'CANCELLED';
                    $item->save();
                }
                  
                if($status == 'PAID' || $status == 'PARTIAL'){
                    // PROCESS REFUND
                        //UPDATE INVOICE STATUS TO CANCELLED
                        $invoice = Invoice::where('order_id', $this->orderId)->first();
                        $invoice->status = 'CANCELLED';
                        $invoice->adjusted_amount = $invoice->amount;
                        $invoice->amount = 0;
                        $invoice->updated_by = auth()->user()->emp_id;
                        $invoice->updated_at = Carbon::now('Asia/Manila');
                        $invoice->save();
                        
                    //check payment based on invoice id
                        $payments = Payment::where('invoice_id', $invoice->id)->get();
                        foreach($payments as $payment){
                            // INSERT PAYMENT AS REFUNDED
                            $refundedPayment = new Payment();
                            $refundedPayment->branch_id = $payment->branch_id;
                            $refundedPayment->customer_id = $payment->customer_id;
                            $refundedPayment->invoice_id = $payment->invoice_id;
                            $refundedPayment->amount = $payment->amount;
                            $refundedPayment->status = $payment->status;
                            $refundedPayment->payment_type_id = $payment->payment_type_id;
                            $refundedPayment->type = 'REFUND';
                            $refundedPayment->updated_by = auth()->user()->emp_id;
                            $refundedPayment->prepared_by = $payment->prepared_by;
                            $refundedPayment->payment_parent = $payment->id;
                            $refundedPayment->created_at = Carbon::now('Asia/Manila');
                            $refundedPayment->updated_at = Carbon::now('Asia/Manila');
                            $refundedPayment->save();
                        }
                    // CANCEL ALL DISCOUNTS
                        $orderDiscounts = OrderDiscount::where('order_id', $this->orderId)->get();;
                        foreach($orderDiscounts as $od){
                            $od->status = 'CANCELLED';
                            $od->save();
                        }
                }
                // UPDATE TABLE AVAILABITLITY
                $table = table::find($order->table_id);
                if ($table) {
                    $table->availability = 'VACANT';
                    $table->save();
                }
    
        
        }

        try{ $payload = [
                'action' => 'refreshOrders',
                'branch_id' => Auth::user()->branch->id,
            ];
            event(new RemoteActionTriggered($payload, auth()->id()));
             }catch(\Exception $e){
             $this->dispatch('error', ['message' => 'Please Manual Refresh the other module for them to get updated', 'title' => 'Notification error']);
        }

    }

}
