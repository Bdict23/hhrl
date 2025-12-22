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
use Illuminate\Support\Facades\Auth;

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
        ];
    }


    public function resetInputFields()
    {
        $this->amountReceived = 0.00;
        $this->selectedPaymentType = null;
        $this->customerName = '';
        $this->splitPayments = [];
        $this->change = "₱ 0.00";
    }

    public function fetchOrders()
    {
        $this->orders = Order::where([['branch_id', Auth::user()->branch->id],['order_status', 'SERVED']])->with('order_details','tables','order_details.menu.price_levels','order_details.OrderDiscounts.discount')->get();
        $this->paymentTypes = PaymentType::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->get();
       
    }
    public function mount()
    {
        $this->fetchOrders();
        $this->discounts = Discount::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->where('type','SINGLE')->get();
        $this->totalAmountDue = 0.00;
    }
    public function updateTotalAmountDue()
    {   
        // Calculate total order-level discounts
        $orderDiscountSum = OrderDiscount::where('order_id', $this->selectedOrderId)
            ->whereNull('order_detail_id')
            ->with('discount')
            ->get()
            ->sum(function($od) {
                return $od->discount->amount > 0 
                    ? $od->discount->amount
                    : ($od->discount->percentage / 100) * $this->grossAmount;
            });
        
           
        // Reset split payments whenever total amount due is updated and amount received changes
        $this->splitPayments = [];
        $this->paymentTypes = PaymentType::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->get();
        $this->amountReceived = 0.00;
        $this->change = "₱ 0.00";


        $this->totalAmountDue = $this->selectedOrderDetails->sum(function($detail) {
            $itemTotal = $detail->qty * ($detail->priceLevel->amount ?? 0);
            // Subtract discounts
            $discountTotal = $detail->orderDiscounts->sum(function($od) use ($detail) {
                return $od->discount->amount > 0 
                    ? $od->discount->amount * $detail->qty
                    : ($od->discount->percentage / 100) * ($detail->priceLevel->amount ?? 0) * $detail->qty;
            });
            return $itemTotal - $discountTotal;
        });
        $this->totalDiscountAmount = $this->selectedOrderDetails->sum(function($detail) {
            return $detail->orderDiscounts->sum(function($od) use ($detail) {
                return $od->discount->amount > 0 
                    ? $od->discount->amount * $detail->qty
                    : ($od->discount->percentage / 100) * ($detail->priceLevel->amount ?? 0) * $detail->qty;
            });
        });
        $this->totalDiscountAmount += $orderDiscountSum;
        $this->totalAmountDue -= $orderDiscountSum;

    }
    public function selectedOrder($orderId)
    {
        // Reset split payments whenever total amount due is updated and amount received changes
        $this->splitPayments;
        $this->paymentTypes = PaymentType::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->get();
        $this->amountReceived = 0.00;
        $this->change = "₱ 0.00";

        $this->selectedOrderId = $orderId;
        $this->selectedOrderDetails = OrderDetail::where('order_id', $orderId)->with('priceLevel')->get();
        // Calculate initial total amount due
        $this->grossAmount = $this->selectedOrderDetails->sum(function($detail) {
            return $detail->qty * ($detail->priceLevel->amount ?? 0);
        });

        $selectedOrderDiscounts = OrderDiscount::where('order_id', $orderId)
            ->whereNull('order_detail_id')
            ->get();
        // Auto-apply discounts that have auto_apply = true
        $autoApplyDiscounts = Discount::where('branch_id', auth()->user()->branch->id)
            ->where('status', 'ACTIVE')
            ->where('type', 'WHOLE')
            ->where('auto_apply', true)
            ->get();

        foreach ($autoApplyDiscounts as $discount) {
            // Check if already applied
            $exists = OrderDiscount::where([
                ['order_id', $orderId],
                ['order_detail_id', null],
                ['discount_id', $discount->id]
            ])->exists();

            if (!$exists) {
                // Apply the discount automatically
                OrderDiscount::create([
                    'order_id' => $orderId,
                    'order_detail_id' => null,
                    'discount_id' => $discount->id
                ]);
            }
        }

        // Refresh the selected order discounts after auto-apply
        $selectedOrderDiscounts = OrderDiscount::where('order_id', $orderId)
            ->whereNull('order_detail_id')
            ->get();

        $this->perOrderDiscounts = Discount::where('branch_id', auth()->user()->branch->id)
            ->where('status', 'ACTIVE')
            ->where('type','WHOLE')
            ->whereNotIn('id', $selectedOrderDiscounts->pluck('discount_id'))
            ->get();
         $this->appliedDiscounts = OrderDiscount::where('order_id', $orderId)->with('discount')->get();
        $this->updateTotalAmountDue();
    }

    public function selectedItem($itemId)
    {
        $this->selectedItemId = $itemId;
        $this->selectedItemDiscounts = OrderDiscount::where([
            ['order_id', $this->selectedOrderId],
            ['order_detail_id', $itemId]
        ])->with('discount')->get();
        $this->discounts = Discount::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->where('type','SINGLE')->whereNotIn('id', $this->selectedItemDiscounts->pluck('discount_id'))->get();
    }

    public function selectedDiscounts($discountId, $isChecked)
    {
        $has_code = Discount::where('id', $discountId)->whereNotNull('code')->exists();
        if($has_code && $isChecked){
           $this->dispatch('RequestDiscountCode', discountId: $discountId);
            return;
        }

        if ($isChecked) {
            // Validate discount amount doesn't exceed item total
            $orderDetail = OrderDetail::with('priceLevel')->find($this->selectedItemId);
            $discount = Discount::find($discountId);
            
            if ($orderDetail && $discount) {
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
                    $this->dispatch('DiscountExceedsTotal', discountId: $discountId);
                    return;
                }
            }
            
            OrderDiscount::updateOrCreate(
                [
                    'order_id' => $this->selectedOrderId,
                    'order_detail_id' => $this->selectedItemId,
                    'discount_id' => $discountId
                ]
            );
            $this->updateTotalAmountDue();
            $this->selectedOrder($this->selectedOrderId);
        } else {
            OrderDiscount::where([
                ['order_id', $this->selectedOrderId],
                ['order_detail_id', $this->selectedItemId],
                ['discount_id', $discountId]
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
            OrderDiscount::updateOrCreate(
                [
                    'order_id' => $this->selectedOrderId,
                    'order_detail_id' => null,
                    'discount_id' => $discountId
                ]
            );
            $this->updateTotalAmountDue();
            $this->selectedOrder($this->selectedOrderId);
        } else {
            OrderDiscount::where([
                ['order_id', $this->selectedOrderId],
                ['order_detail_id', null],
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
                OrderDiscount::updateOrCreate(
                    [
                        'order_id' => $this->selectedOrderId,
                        'order_detail_id' => null,
                        'discount_id' => $discountId
                    ]
                );

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
                        'discount_id' => $discountId
                    ]
                );

                $this->fetchOrders();
                $this->updateTotalAmountDue();
                $this->selectedOrder($this->selectedOrderId);

                return [
                    'success' => true,
                    'message' => 'Discount code applied successfully!'
                ];
        
    }
    public function addSplitPayment($paymentTypeId)
    {
        $paymentType = PaymentType::find($paymentTypeId);
        if ($paymentType) {
            $this->splitPayments[] = [
                'id' => uniqid(),
                'payment_type_id' => $paymentTypeId,
                'payment_type_name' => $paymentType->payment_type_name,
                'amount' => 0
            ];
        }
    }

    public function removeSplitPayment($id)
    {
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
        // dd($this->amountReceived);
        $this->validate(
            $this->rules()
        );
        //create invoice record
        $invoice = Invoice::create([
            'order_id' => $this->selectedOrderId,
            'invoice_number' => $this->invoiceNumber,
            'invoice_type' => 'SALES',
            'customer_name' => $this->customerName == '' ? 'N/A' : $this->customerName,
            'status' => 'CLOSED',
            'payment_mode' => 'CASH',
            'amount' => $this->totalAmountDue,
            'branch_id' => auth()->user()->branch->id,
            'prepared_by' => auth()->user()->employee->id,
        ]);

        //check payment type if its split or not

        if(count($this->splitPayments) > 0 && $this->selectedPaymentType == "SPLIT"){
            foreach($this->splitPayments as $splitPayment){
            
               Payment::create([
                    'order_id' => $this->selectedOrderId,
                    'invoice_id' => $invoice->id,
                    'total_amount' => $this->totalAmountDue,
                    'amount_received' => $splitPayment['amount'],
                    'payment_type_id' => $splitPayment['payment_type_id'],
                    'type' => 'SALES',
                    'prepared_by' => auth()->user()->employee->id,
                ]);
            }
        } else {
            // Single payment
            Payment::create([
                'order_id' => $this->selectedOrderId,
                'amount' => $this->totalAmountDue,
                'prepared_by' => auth()->user()->employee->id,
                'type' => 'SALES',
                'invoice_id' => $invoice->id,
                'payment_type_id' => $this->selectedPaymentType,
            ]);
        }

        // Update order status to COMPLETED
        Order::where('id', $this->selectedOrderId)->update([
            'order_status' => 'COMPLETED',
            'payment_status' => 'PAID'
        ]);


        // After saving payment, reset input fields and refresh orders
        $this->resetInputFields();
        $this->fetchOrders();
        $this->selectedOrderId = null;
        $this->selectedOrderDetails = null;
        $this->totalAmountDue = 0.00;
        $this->dispatch('PaymentSaved', invoiceId: $invoice->id);
    }

    public function render()
    {
        return view('livewire.restaurant.invoicing');
    }
}
