<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\OrderDiscount;
use App\Models\Discount;
use App\Models\Payment;

class DailySalesReport extends Component
{
    public $invoices;
    public $totalDiscountAmount = 0.00;
    public $totalAmountDue = 0.00;
    public $selectedOrderDetails;
    public $grossAmount = 0.00;
    public $customer_name = 'N/A';
    public $table_name = 'TAKE OUT';
    public $order_number = 'N/A';
    public $paymentMethod = 'CASH';
    public $payments;
    public $from_date;
    public $to_date;

    public function fetchData()
    {
        $this->from_date = date('Y-m-d');
        $this->to_date = date('Y-m-d');
        $this->invoices = Invoice::where([['branch_id', Auth::user()->branch->id],['invoice_type', 'SALES']])
            ->whereDate('created_at', now()->toDateString())
            ->with('customers', 'order','order.order_details','order.order_details.menu.price_levels','order.tables')
            ->get();

    }
    public function mount()
    {
        $this->fetchData();
    }
    public function render()
    {
        return view('livewire.transactions.daily-sales-report');
    }

    public function filterInvoicesByDate()
    {
        $this->validate([
                    'from_date' => 'required|date',
                    'to_date' => 'required|date|after_or_equal:from_date',
                ]);
        $this->invoices = Invoice::where([['branch_id', Auth::user()->branch->id],['invoice_type', 'SALES']])
            ->whereBetween('created_at', [$this->from_date . ' 00:00:00', $this->to_date . ' 23:59:59'])
            ->with('customers', 'order','order.order_details','order.order_details.menu.price_levels','order.tables')
            ->get();
    }


    public function viewInvoiceDetails($orderId)
    {   
        // Calculate total order-level discounts
        $orderDiscountSum = OrderDiscount::where('order_id', $orderId)
            ->whereNull('order_detail_id')
            ->with('discount')
            ->get()
            ->sum(function($od) {
                return $od->discount->amount > 0 
                    ? $od->discount->amount
                    : ($od->discount->percentage / 100) * $this->grossAmount;
            });
        $orderInfo = Order::find($orderId);
        $invoice = Invoice::where('order_id', $orderId)->with('payments','payments.payment_type')->first();
        $this->payments = Payment::where('invoice_id', $invoice->id)->with('payment_type')->get();
        if($this->payments->count() == 1){
            $this->paymentMethod = $this->payments->first()->payment_type->payment_type_name;
        } else {
            $this->paymentMethod = 'SPLIT';
        }
        $this->customer_name = $invoice->customer_name ?? 'N/A';


        $this->table_name = $orderInfo->tables->table_name ?? 'Take Out';
        $this->order_number = $orderInfo->order_number;

        $this->selectedOrderDetails = OrderDetail::where('order_id', $orderId)->where('marked', true)->with('priceLevel','menu')->get();
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
}
