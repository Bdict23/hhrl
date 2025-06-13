<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Item;
use App\Models\Table;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Menu;
use App\Models\Category;
use App\Models\PriceLevel;
use App\Models\Invoice;
use App\Models\Payment;

use Illuminate\Support\Facades\Auth;



class InvoicingController extends Controller
{
    //
    public function index()
    {
        $orders = order::where([['branch_id', Auth::user()->branch->id],['order_status', 'SERVED']])->with('order_details','tables','order_details.menu.price_levels')->get();
        return view('transactions.invoicing', compact('orders'));
    }

    public function storePayment(Request $request){
            //dd(Auth::user()->emp_id);
        try {
            $order = order::find($request->order_id);
            $order->payment_status = 'PAID';
            $order->order_status = 'COMPLETED';
            $order->save();


            $invoice = new invoice();
            $invoice->order_id = $order->id;
            $invoice->invoice_type = 'SALES';
            $invoice->invoice_number = $request->invoiceNumber;
            $invoice->amount = $request->amountReceived;
            $invoice->customer_name = $request->customer;
            $invoice->payment_mode = 'CASH';
            $invoice->status = 'CLOSED';
            $invoice->prepared_by = Auth::user()->emp_id;
            $invoice->branch_id = Auth::user()->branch->id;
            $invoice->save();

            $payment = new payment();
            $payment->invoice_id = $invoice->id;
            $payment->amount = $request->amountReceived;
            $payment->payment_method = $request->paymentMethod;
            $payment->type = 'ITEM';
            $payment->prepared_by = Auth::user()->emp_id;
            $payment->status = 'FULL';
            $payment->save();
            

        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'An error occurred while recording the payment: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Payment has been successfully recorded');
    }

    public function printInvoice($id){
        $invoice = Invoice::find($id);
        return view('transactions.printInvoice', compact('invoice'));
    }

    public function daily_sales_report(){
        $invoices = Invoice::where([['branch_id', Auth::user()->branch->id],['invoice_type', 'SALES']])->with('customers', 'order','order.order_details','order.order_details.menu.price_levels','order.tables')->get();
        return view('transactions.daily_sales_report', compact('invoices'));
    }


}
