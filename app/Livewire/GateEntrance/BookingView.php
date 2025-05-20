<?php

namespace App\Livewire\GateEntrance;

use App\Models\Leisure;
use Livewire\Component;
use App\Models\BookingRecords;
use App\Models\BookingService;
use App\Models\BookingPayments;

class BookingView extends Component
{
    public $booking_number;
    public $customer_booking;
    public $services;
    public $availed_services = [];
    public $total_service_payment = 0.0;
    public $total_payment = 0.0;
    public $balance = 0.0;
    public $booking_service;
    public $booking_payment;

    public $customer;
    public $message='';


    public function mount($booking_number)
    {
        $this->booking_number = $booking_number;
        $this->customer_booking = BookingRecords::where('booking_number', $this->booking_number)->first();
        $this->services = Leisure::all();
        $this->booking_service = new BookingService();
    }
    public function updatedAvailedServices()
    {
       $this->total_service_payment = collect($this->availed_services)->sum(callback: fn ($d) =>
            (int)$d['quantity'] * (double)$d['amount']
        );
    }
    public function updatedTotalPayment()
    {
         if ($this->total_service_payment <= 0) {
            session()->flash('error', 'No Payable Amount Available');
            return $this->total_payment=0;
        }
        if ($this->total_payment <= -1) {
            session()->flash('error', 'Please enter a valid payment amount.');
            return $this->total_payment=0;
        }
        $this->balance =  (double)$this->total_service_payment - (double)$this->total_payment ;
    }

    public function PaymentSecurity()
    {
         if ($this->total_service_payment <= 0) {
            session()->flash('error', 'No Payable Amount Available');
            return $this->total_payment=0;
        }
        if ($this->total_payment <= -1) {
            session()->flash('error', 'Please enter a valid payment amount.');
            return $this->total_payment=0;
        }

    }

    public function addService($id)
    {
        try {
            $service = Leisure::find($id);
            $this->availed_services[] =
            [
                'name' => $service->name,
                'leisure_id' => $service->id,
                'amount' => $service->amount,
                'quantity' => 0,
                'unit' => $service->unit,
                'booking_records_id' => 0,
                'booking_payment_id' => 0,
            ];


            return $this->availed_services;

        } catch (\Exception $e) {

            // Handle the exception
            return $e->getMessage();
        }
    }

    public function removeService($index)
    {
        unset($this->availed_services[$index]);
        $this->availed_services = array_values($this->availed_services);
        $this->updatedAvailedServices();
    }

    public function saveBookingPayment()
    {
        try {
            $status = $this->total_payment >= $this->total_service_payment ? 'Paid' : 'Partial';

            $this->booking_payment = BookingPayments::create([
                'booking_records_id' => $this->customer_booking->id,
                'amount_due' => (double)$this->total_service_payment,
                'amount_payed' => (double)$this->total_payment,
                'balance' => (double)$this->balance,
                'OR_number' => 'OR-'.date('YmdHis'),
                'booking_number' => $this->customer_booking->booking_number,
                'payment_type' => 'Cash',
                'payment_status' => $status,
            ]);

            $this->message .= "\n Payment Saved";
        } catch (\Throwable $th) {
             $this->message .= '\n An error occurred: ' . $th->getMessage();
        }
    }
    public function saveBookingService()
    {
        try {
             foreach ($this->availed_services as &$service) {
                // Only save if there are customers in this category
                if ((int)$service['quantity'] > 0) {
                    $this->booking_service->create([
                        'booking_records_id' =>  $this->customer_booking->id,
                        'leisure_id' => $service['leisure_id'],
                        'amount' => (int)$service['amount'],
                        'quantity' => (int)$service['quantity'],
                        'total_amount' => (int)$service['quantity'] * (int)$service['amount'],
                        'booking_payment_id' => $this->booking_payment->id,
                    ]);
                }
            }
            $this->message .= "\n Service Added";

        } catch (\Throwable $th) {
              $this->message .= '\n An error occurred: ' . $th->getMessage();
        }
    }

    public function Submit(){
        try {
            //code...
             if ($this->total_payment <= 0) {
                 session()->flash('error', 'Please enter a valid payment amount.');
                return;
            }else if ($this->total_payment < $this->total_service_payment) {
                session()->flash('error', 'Payment amount cannot be less than the total service payment.');
                return;
            }
            $this->saveBookingPayment();

            $this->saveBookingService();

            $this->total_service_payment = 0.0;
            $this->total_payment = 0.0;
            $this->balance = 0.0;
            $this->availed_services = [];
            $this->reset(['total_payment', 'availed_services']);

            session()->flash('message', $this->message);
        } catch (\Throwable $th) {
            session()->flash('message', 'An error occurred: ' . $th->getMessage()." ".$this->message);
        }
    }



    public function CheckOut()
    {
        try {
            $this->customer_booking->update([
                'booking_status' => 'Completed',
            ]);
            session()->flash('message', 'Check out successfully!');
        } catch (\Throwable $th) {
            session()->flash('message', 'An error occurred: ' . $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.gate-entrance.booking-view');
    }
}
