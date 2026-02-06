<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Discount;
use App\Models\OrderDiscount;
use App\Models\PaymentType;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Table;
use App\Models\CashierShift;
use App\Models\RecipeCardex;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Events\RemoteActionTriggered; 

class Invoicing extends Component
{
    public $orders;
    public $selectedOrderDetails;
    public $selectedOrderId;
    public $selectedItemId;
    public $discounts;
    public $perOrderDiscounts;
    public $paymentTypes;
    public $totalDiscountAmount = 0.00;
    public $totalAmountDue = 0.00;
    public $grossAmount = 0.00;
    public $splitPayments = [];
    public $selectedPaymentType;
    public $amountReceived = 0.00;
    public $customerName = '';
    public $change = "₱ 0.00";
    public $appliedDiscounts;
    public $selectedItemDiscounts;

    //form inputs
    public $invoiceNumber;

    //validation rules
    protected function rules()
    {
        return [
            'invoiceNumber' => 'nullable|max:255|unique:invoices,invoice_number',
            'amountReceived' => 'required|numeric|min:' . $this->totalAmountDue,
            'selectedPaymentType' => 'required|not_in:NONE',
        ];
    }

    // modify error messages
    protected $messages = [
        'selectedPaymentType.not_in' => 'Please select a valid payment type.',
        'amountReceived.min' => 'Amount received must be at least equal to the total amount due.',
    ];

    protected $listeners = [
    'RemoteActionTriggered' => 'handleRemoteExecute',
];

    public function handleRemoteExecute($payload){

        $data = $payload['payload'] ?? $payload;

        if(($data['action'] == 'newOrder' || $data['action'] == 'refreshOrders') && $data['branch_id'] == Auth::user()->branch->id ) {
            $this->mount();
            if($this->selectedOrderId != null){
                $this->selectedOrder($this->selectedOrderId);
            }
        }
    }


    public function resetInputFields()
    {
        $this->amountReceived = 0.00;
        $this->selectedPaymentType = null;
        $this->selectedOrderDetails = [];
        $this->selectedOrderId  = null;
        $this->totalDiscountAmount = 0.00;
        $this->totalAmountDue = 0.00;
        $this->grossAmount = 0.00;
        $this->customerName = '';
        $this->splitPayments = [];
        $this->appliedDiscounts = null;
        $this->selectedItemDiscounts = null;
        $this->change = "₱ 0.00";
        $this->orders = [];
        $this->selectedItemId = null;
        $this->discounts = Discount::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->where('type','SINGLE')->get();

    }

    public function refreshOrders()
    {
        $this->resetInputFields();
        $this->mount();
    }

    public function checkShiftStatus()
    {
        $openShift = CashierShift::where('cashier_id', auth()->user()->employee->id)
            ->where('shift_status', 'OPEN')
            ->first();

        if ($openShift) {
                    $this->fetchOrders();
        }else{
          session()->flash('error', 'Please open a shift first before proceeding to invoicing.');
          $this->redirectRoute('make.open.shift', navigate: true);
        }
    }

    public function fetchOrders() // all good
    {
        $this->orders = Order::where([['branch_id', Auth::user()->branch->id]])
            ->whereIn('order_status', ['SERVED','PENDING','SERVING'])
            ->where('payment_status', '!=', 'PAID')
            ->with('ordered_items','tables','ordered_items.menu.price_levels','ordered_items.OrderDiscounts.discount')
            ->get();
        $this->paymentTypes = PaymentType::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->get();
       
    }
    public function mount() // all good
    {
        $this->checkShiftStatus();
    }
    public function updateTotalAmountDue() // all good
    {   
        // Calculate total order-level discounts
        $orderDiscountSum = OrderDiscount::where('order_id', $this->selectedOrderId)
            ->with('discount')
            ->get()
            ->sum('calculated_amount');
        
           
        // Reset split payments whenever total amount due is updated and amount received changes
        $this->splitPayments = [];
        $this->paymentTypes = PaymentType::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->get();
        $this->amountReceived = 0.00;
        $this->change = "₱ 0.00";


        $this->totalAmountDue = $this->selectedOrderDetails->sum(function($detail) {
            $itemTotal = $detail->qty * ($detail->priceLevel->amount ?? 0);
            return $itemTotal;
        });
        
        
        $this->totalDiscountAmount = $orderDiscountSum;
        $this->totalAmountDue -= $orderDiscountSum;

    }
    public function selectedOrder($orderId)
    {
        try {
           
        // Reset split payments whenever total amount due is updated and amount received changes
        $this->splitPayments = [];
        $this->paymentTypes = PaymentType::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->get();
        $this->amountReceived = 0.00;
        $this->change = "₱ 0.00";
        $this->selectedOrderId = $orderId;
        $this->selectedOrderDetails = OrderDetail::where('order_id', $orderId)->where('marked', false)->whereIn('status', ['SERVED','PENDING','SERVING'])->with('priceLevel')->get();
        
        // Calculate initial total amount due
        $this->grossAmount = $this->selectedOrderDetails->sum(function($detail) {
            return $detail->qty * ($detail->priceLevel->amount ?? 0);
        }); // all good

        $selectedOrderDiscounts = OrderDiscount::where('order_id', $orderId)
            ->where('type', 'ORDER')
            ->get();
        // check for Auto-apply discounts
        $autoApplyDiscounts = Discount::where('branch_id', auth()->user()->branch->id)
            ->where('status', 'ACTIVE')
            ->where('type', 'WHOLE')
            ->where('auto_apply', true)
            ->get();
        
            // Delete existing auto-applied discounts to prevent duplicates
        foreach($autoApplyDiscounts as $discount) {
            OrderDiscount::where('order_id', $orderId)
                ->where('discount_id', $discount->id)
                ->where('type', 'ORDER')
                ->delete();
        }


        foreach ($autoApplyDiscounts as $discount) {  

                // Divide the discount amount among all items
                    $amountToDistribute = $discount->amount > 0 
                        ? $discount->amount 
                        : ($discount->percentage / 100) * $this->totalAmountDue; 
 
                // Apply the discount automatically each items
                foreach ($this->selectedOrderDetails as $detail) {
                    OrderDiscount::create([
                        'order_id' => $orderId,
                        'order_detail_id' => $detail->id,
                        'discount_id' => $discount->id,
                        'calculated_amount' => ($amountToDistribute / $this->selectedOrderDetails->count()),
                    ]);
                } 
            
        }

        // Refresh the selected order discounts after auto-apply
        $selectedOrderDiscounts = OrderDiscount::where('order_id', $orderId)
            ->where('type', 'ORDER')
            ->get();

        $this->perOrderDiscounts = Discount::where('branch_id', auth()->user()->branch->id)
            ->where('status', 'ACTIVE')
            ->where('type','WHOLE')
            ->whereNotIn('id', $selectedOrderDiscounts->pluck('discount_id'))
            ->get();
         $this->appliedDiscounts = OrderDiscount::where('order_id', $orderId)->with('discount')->get();
        $this->updateTotalAmountDue();
        } catch (\Exception $e) {
            $this->dispatch('error', 'An error occurred while selecting the order: ' . $e->getMessage());
        }
    }

    public function selectedItem($itemId)
    {
        $this->selectedItemId = $itemId;
        $this->selectedItemDiscounts = OrderDiscount::where([
            ['order_id', $this->selectedOrderId],
            ['order_detail_id', $itemId],
            ['type', 'ITEM'],
        ])->with('discount')->get();
        $this->discounts = Discount::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->where('type','SINGLE')->whereNotIn('id', $this->selectedItemDiscounts->pluck('discount_id'))->get();
    }

    public function selectedDiscounts($discountId, $isChecked)
    {
        
        $has_code = Discount::where('id', $discountId)->whereNotNull('code')->exists();
        // Request discount code if needed
        if($has_code && $isChecked){
           $this->dispatch('RequestDiscountCode', discountId: $discountId);
            return;
        }

        if ($isChecked) {
            // Validate discount amount doesn't exceed item total
            $orderDetail = OrderDetail::with('priceLevel')->find($this->selectedItemId);
            $discount = Discount::find($discountId);
            
            //delete any existing order-level discounts for this order
                OrderDiscount::where([
                    ['order_id', $this->selectedOrderId],
                    ['type', 'ORDER'],
                ])->delete();

            if ($orderDetail && $discount) {
                $itemTotal = $orderDetail->qty * ($orderDetail->priceLevel->amount ?? 0);

                // Calculate current total discounts
                $currentDiscounts = OrderDiscount::where('order_detail_id', $this->selectedItemId)
                    ->where('order_id', $this->selectedOrderId)->where('status', 'APPLIED')->where('type', 'ITEM')
                    ->with('discount')
                    ->get()
                    ->sum('calculated_amount');

                // Calculate new discount amount
                $newDiscountAmount = $discount->amount > 0 
                    ? $discount->amount * $orderDetail->qty 
                    : ($discount->percentage / 100) * ($orderDetail->priceLevel->amount ?? 0) * $orderDetail->qty;

                
                // Check if total discounts would exceed item total
                if (($currentDiscounts + $newDiscountAmount) > $itemTotal) {
                    $this->dispatch('DiscountExceedsTotal', discountId: $discountId);
                    return;
                }
            }
            
            OrderDiscount::create(
                [
                    'order_id' => $this->selectedOrderId,
                    'order_detail_id' => $this->selectedItemId,
                    'discount_id' => $discountId,
                    'type' => 'ITEM',
                    'calculated_amount' => $currentDiscounts + $newDiscountAmount ,
                ]
            );
            $this->updateTotalAmountDue();
            $this->selectedOrder($this->selectedOrderId);
        } else {
            OrderDiscount::where([
                ['order_id', $this->selectedOrderId],
                ['order_detail_id', $this->selectedItemId],
                ['discount_id', $discountId],
            ])->delete();
            $this->updateTotalAmountDue();
            $this->selectedOrder($this->selectedOrderId);
        }
    }

    public function selectedOrderDiscounts($discountId, $isChecked)
    {
        $has_code = Discount::where('id', $discountId)->whereNotNull('code')->exists();
        if($has_code && $isChecked){
           $this->dispatch('RequestOrderDiscountCode', discountId: $discountId);
            return;
        }
        if ($isChecked) {
             // Divide the discount amount among all items
                    $amountToDistribute = $discount->amount > 0 
                        ? $discount->amount 
                        : ($discount->percentage / 100) * $this->totalAmountDue; 
 
                // Apply the discount automatically each items
                foreach ($this->selectedOrderDetails as $detail) {
                    OrderDiscount::create([
                        'order_id' => $this->selectedOrderId,
                        'order_detail_id' => $detail->id,
                        'discount_id' => $discount->id,
                        'calculated_amount' => ($amountToDistribute / $this->selectedOrderDetails->count()),
                    ]);
                } 
            $this->updateTotalAmountDue();
            $this->selectedOrder($this->selectedOrderId);
        } else {
            OrderDiscount::where([
                ['order_id', $this->selectedOrderId],
                ['type', 'ORDER'],
                ['discount_id', $discountId]
            ])->delete();
            $this->updateTotalAmountDue();
            $this->selectedOrder($this->selectedOrderId);
        }
        
    }
    public function applyOrderDiscountWithCode($code,$discountId){

        $discount = Discount::where('code', $code)
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('status', 'ACTIVE')
                    ->where('id', $discountId)
                    ->first();

                if (!$discount) {
                    return [
                        'success' => false,
                        'message' => 'Invalid discount code.'
                    ];
                }
                 // Divide the discount amount among all items
                    $amountToDistribute = $discount->amount > 0 
                        ? $discount->amount 
                        : ($discount->percentage / 100) * $this->totalAmountDue; 
 
                // Apply the discount automatically each items
                foreach ($this->selectedOrderDetails as $detail) {
                    OrderDiscount::create([
                        'order_id' => $this->selectedOrderId,
                        'order_detail_id' => $detail->id,
                        'discount_id' => $discount->id,
                        'calculated_amount' => ($amountToDistribute / $this->selectedOrderDetails->count()),
                    ]);
                } 

                $this->fetchOrders();
                $this->updateTotalAmountDue();
                $this->selectedOrder($this->selectedOrderId);

                return [
                    'success' => true,
                    'message' => 'Discount code applied successfully!'
                ];
        
    }

    public function applyDiscountWithCode($code,$discountId){
        $discount = Discount::where('code', $code)
                    ->where('branch_id', auth()->user()->branch->id)
                    ->where('status', 'ACTIVE')
                    ->where('id', $discountId)
                    ->first();

                if (!$discount) {
                    return [
                        'success' => false,
                        'message' => 'Invalid discount code.'
                    ];
                }

                // Validate discount amount doesn't exceed item total
                $orderDetail = OrderDetail::where('item_id', $this->selectedItemId)->where('order_id', $this->selectedOrderId)->with('priceLevel')->first();
                
                if ($orderDetail) {
                    $itemTotal = $orderDetail->qty * ($orderDetail->priceLevel->amount ?? 0);
                    
                    // Calculate current total discounts
                    $currentDiscounts = OrderDiscount::where('order_detail_id', $this->selectedItemId)
                        ->with('discount')
                        ->get()
                        ->sum(function($od) use ($orderDetail) {
                            return $od->discount->amount > 0 
                                ? $od->discount->amount * $orderDetail->qty
                                : ($od->discount->percentage / 100) * ($orderDetail->priceLevel->amount ?? 0) * $orderDetail->qty;
                        });
                    
                    // Calculate new discount amount
                    $newDiscountAmount = $discount->amount > 0 
                        ? $discount->amount * $orderDetail->qty
                        : ($discount->percentage / 100) * ($orderDetail->priceLevel->amount ?? 0) * $orderDetail->qty;
                    
                    // Check if total discounts would exceed item total
                    if (($currentDiscounts + $newDiscountAmount) > $itemTotal) {
                        return [
                            'success' => false,
                            'message' => 'Total discount amount exceeds item total. Cannot apply this discount.'
                        ];
                    }
                }

                OrderDiscount::updateOrCreate(
                    [
                        'order_id' => $this->selectedOrderId,
                        'order_detail_id' => $this->selectedItemId,
                        'discount_id' => $discountId,
                        'calculated_amount' => $discount->amount > 0 
                            ? $discount->amount * $orderDetail->qty
                            : ($discount->percentage / 100) * ($orderDetail->priceLevel->amount ?? 0) * $orderDetail->qty,
                    ]
                );

                $this->fetchOrders();
                $this->updateTotalAmountDue();
                $this->selectedOrder($this->selectedOrderId);

                return [
                    'success' => true,
                    'message' => 'Discount code applied successfully!'
                ];
        
    } //end applyDiscountWithCode
    

    public function removeSplitPayment($id)
    {
        dd($this->splitPayments);
        $this->splitPayments = array_values(array_filter($this->splitPayments, function($payment) use ($id) {
            return $payment['id'] !== $id;
        }));
    }
    public function removeAppliedDiscount($appliedDiscountId)
    {
        OrderDiscount::where('id', $appliedDiscountId)->delete();
        $this->selectedOrder($this->selectedOrderId);
        $this->updateTotalAmountDue();
    }


    public function savePayment()
    {
        $this->validate(
            $this->rules()
        );
            if(Order::find($this->selectedOrderId)->payment_status == 'SERVING'){
                //check if there are pending items
                if(OrderDetail::where('order_id', $this->selectedOrderId)->where('status', 'SERVING')->exists()){
                    $this->dispatch('error', 'Order are served partially. Cancel pending items before proceeding to payment.');
                    return;
                }
            }
        //calculate all invoices for the selected order
        $orderRound = Invoice::where('order_id', $this->selectedOrderId)->get();
        $invoiceCount = $orderRound->count();
            if($invoiceCount > 0){
                //update oder details order round
                OrderDetail::where('order_id', $this->selectedOrderId)->where('marked', false)->update([
                    'order_round' => $invoiceCount + 1
                ]);  
                }   
        $curYear = now()->year;
        $branchId = auth()->user()->branch_id;
        $yearlyCount = Invoice::where('branch_id', $branchId)
            ->whereYear('created_at', $curYear)
            ->count() + 1;
        $reference = 'INV-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);

        //create invoice record
        $invoice = Invoice::create([
            'order_id' => $this->selectedOrderId,
            'invoice_number' => $this->invoiceNumber,
            'reference' => $reference,
            'invoice_type' => 'SALES',
            'order_round' => $invoiceCount + 1,
            'customer_name' => $this->customerName == '' ? 'N/A' : $this->customerName,
            'status' => 'CLOSED',
            'payment_mode' => 'CASH',
            'amount' => $this->totalAmountDue,
            'branch_id' => auth()->user()->branch->id,
            'prepared_by' => auth()->user()->employee->id,
            'created_at' => Carbon::now('Asia/Manila'),
            'updated_at' => Carbon::now('Asia/Manila'),
            'original_amount' => $this->totalAmountDue,
        ]);

        //check payment type if its split or not

        if(count($this->splitPayments) > 0 && $this->selectedPaymentType == "SPLIT"){
            foreach($this->splitPayments as $splitPayment){
            
               Payment::create([
                    'order_id' => $this->selectedOrderId,
                    'branch_id' => auth()->user()->branch->id,
                    'invoice_id' => $invoice->id,
                    'amount' => $splitPayment['amount'],
                    'payment_type_id' => $splitPayment['paymentTypeId'],
                    'type' => 'SALES',
                    'prepared_by' => auth()->user()->employee->id,
                    'created_at' => Carbon::now('Asia/Manila'),
                    'updated_at' => Carbon::now('Asia/Manila'),
                ]);
            }
        } else {
            // Single payment
            Payment::create([
                'order_id' => $this->selectedOrderId,
                'branch_id' => auth()->user()->branch->id,
                'amount' => $this->totalAmountDue,
                'prepared_by' => auth()->user()->employee->id,
                'type' => 'SALES',
                'invoice_id' => $invoice->id,
                'payment_type_id' => $this->selectedPaymentType,
                'created_at' => Carbon::now('Asia/Manila'),
                'updated_at' => Carbon::now('Asia/Manila'),
            ]);
        }
        // check order details has pending items
        $hasPendingItems = OrderDetail::where('order_id', $this->selectedOrderId)->where('status', 'PENDING')->exists();
        if (!$hasPendingItems) {
            // Update order status to COMPLETED
            Order::where('id', $this->selectedOrderId)->update([
                'order_status' => 'COMPLETED',
                'payment_status' => 'PAID',
                'updated_at' => Carbon::now('Asia/Manila'),
            ]);
            // Update table availability to VACANT
            $order = Order::find($this->selectedOrderId);
              Table::where('id', $order->table_id)->update([
                'availability' => 'VACANT'
            ]);
        }else{
            // Update order payment status to PAID
            Order::where('id', $this->selectedOrderId)->update([
                'payment_status' => 'PAID',
                'updated_at' => Carbon::now('Asia/Manila'),
            ]);

         
        }


        // update order details status to COMPLETED
        OrderDetail::where('order_id', $this->selectedOrderId)->whereIn('status', ['SERVED','PENDING'])->update([
            'marked' => true,
            'updated_at' => Carbon::now('Asia/Manila'),
        ]);

        // update recipe cardex to final
        $orderDetails = OrderDetail::where('order_id', $this->selectedOrderId)->get();
        foreach($orderDetails as $detail){
            RecipeCardex::where('branch_id', auth()->user()->branch->id)
                ->where('menu_id', $detail->item_id)
                ->where('status', 'TEMP')
                ->where('transaction_type', 'SALES')
                ->where('order_id', $this->selectedOrderId)
                ->update([
                    'status' => 'FINAL',
                    'final_date' => Carbon::now('Asia/Manila'),
                    'updated_at' => Carbon::now('Asia/Manila'),
                ]);
        }

        // After saving payment, reset input fields and refresh orders
        $this->resetInputFields();
        $this->fetchOrders();
        $this->selectedOrderId = null;
        $this->selectedOrderDetails = null;
        $this->totalAmountDue = 0.00;
        $this->dispatch('PaymentSaved', invoiceId: $invoice->id);

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
        return view('livewire.restaurant.invoicing');
    }
}
