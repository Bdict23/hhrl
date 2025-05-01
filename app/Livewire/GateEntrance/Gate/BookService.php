<?php

namespace App\Livewire\GateEntrance\Gate;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Leisure;
use App\Models\BookingRecords;
use App\Models\BookingService;
use App\Models\BookingPayments;
use App\Models\BookingDetails;
use PhpParser\Node\Expr\Cast\Double;



class BookService extends Component
{
    public $services;
    public $availed_services=[];
    public $customer;
    public $booking_number;
    public $booking_record;
    public $booking_service;
    public $booking_payment;
    public $details;
    public $or_number;

    public $booking_details =
    [
        [
            'customer_category' => 'Senior (70+)',
            'male_count' => 0,
            'female_count' => 0,
            'entrance_fee' => 70
        ],
        [
            'customer_category' => 'Adult (18F+ or 21+M)',
            'male_count' => 0,
            'female_count' => 0,
            'entrance_fee' => 100
        ],
        [
            'customer_category' => 'Child  (4+)',
            'male_count' => 0,
            'female_count' => 0,
            'entrance_fee' => 50
        ],
        [
            'customer_category' => 'Infant (0 to 3)',
            'male_count' => 0,
            'female_count' => 0,
            'entrance_fee' => 70
        ]
    ];

    public $id;//customer id
    public $total_customers = 0;
    public $total_entrance_payment = 0.0;
    public $total_service_payment = 0.0;
    public $total_payment = 0;
    public $total_payable = 0;
    public $balance = 0;

    public function updatedTotalPayment()
    {
         if ($this->total_payable > -1) {
            session()->flash('error', 'Invalid Amount');
            $this->total_payment = 0;
            return;
        }
        if ($this->total_payment <= -1) {
            session()->flash('error', 'Invalid Amount');
            $this->total_payment = 0;
            return;
        }
        $this->balance = (double)$this->total_payable - (double)$this->total_payment;
    }


    public function updatedBookingDetails()
    {
        $this->getTotalCount(); // <-- Rename it for clarity

        $this->total_payable=(double)$this->total_entrance_payment + (double)$this->total_service_payment;
    }

    public function getTotalCount()
    {
        $this->total_customers = collect($this->booking_details)->sum(fn ($d) =>
            (int)$d['male_count'] + (int)$d['female_count']
        );
        $this->total_entrance_payment = collect($this->booking_details)->sum(fn ($d) =>
            ((int)$d['male_count'] + (int)$d['female_count']) * (double)$d['entrance_fee']
        );
    }

    //
    public function updatedAvailedServices(){
        $this->getserviceTotalCount();
        $this->total_payable=(double)$this->total_entrance_payment + (double)$this->total_service_payment;

    }

    public function getserviceTotalCount()
    {
        $this->total_service_payment = collect($this->availed_services)->sum(callback: fn ($d) =>
            (int)$d['quantity'] * (double)$d['amount']
        );
    }
    //


    public function mount($id)
    {
        $this->id = $id;
        $this->customer = Customer::find($id);
        $this->services = Leisure::all();
        $this->details=new BookingDetails();
        $this->booking_record=new BookingRecords();
        $this->booking_service=new BookingService();
        $this->booking_payment=new BookingPayments();



        $this->booking_number = (string)$id."".date("His");
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
                'booking_records_id' => 0,
                'booking_payment_id' => 0,
            ];


            return $this->availed_services;

        } catch (\Exception $e) {

            // Handle the exception
            return $e->getMessage();
        }


    }

    public function submitBookingrecords()
    {
        try {
            // Here you can handle the form submission, e.g., save to the database
            // For demonstration, we'll just flash a message
            // You can also use the Customer model to save the data
            $this->booking_record = BookingRecords::create([
                'booking_number' => $this->booking_number,
                'customer_id' => $this->id,
                'branch_id' => 1,
                'booking_status' => 'Active',
            ]);

            session()->flash('message', 'Form submitted successfully!');

        } catch (\Exception $e) {
            session()->flash('message', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submitBookingServices(){
        try {
            foreach ($this->availed_services as &$service) {
                // Only save if there are customers in this category
                if ((int)$service['quantity'] > 0) {
                    $this->booking_service->create([
                        'booking_records_id' => $this->booking_record->id,
                        'leisure_id' => $service['leisure_id'],
                        'amount' => (int)$service['amount'],
                        'quantity' => (int)$service['quantity'],
                        'total_amount' => (int)$service['quantity'] * (int)$service['amount'],
                        'booking_payment_id' => $this->booking_payment->id,
                    ]);
                }
            }

           session()->flash('message', "new details Added");
        }catch(\Exception $e) {
            session()->flash('message', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submitBookingDetails()
    {
        try {
          foreach ($this->booking_details as &$detail) {
                // Only save if there are customers in this category
                if ((int)$detail['male_count'] > 0 || (int)$detail['female_count'] > 0) {
                    $this->details->create([
                        'booking_records_id' =>$this->booking_record->id,
                        'customer_category' => $detail['customer_category'],
                        'male_count' => (int)$detail['male_count'],
                        'female_count' => (int)$detail['female_count'],
                        'entrance_fee' => (int)$detail['entrance_fee'],
                        'total_count' => (int)$detail['male_count'] + (int)$detail['female_count'],
                        'total_amount' => (int)$detail['entrance_fee'] * ((int)$detail['male_count'] + (int)$detail['female_count']),
                    ]);
                }
            }

            session()->flash('message', "new details Added");
        } catch (\Exception $e) {
            session()->flash('message', 'An error occurred: ' . $e->getMessage()."\n booking_record id".json_encode($this->booking_record->id));
        }

    }

    public function submitBookingPayment()
    {
        try {
            $this->booking_payment = BookingPayments::create([
                'booking_records_id' => $this->booking_record->id,
                'amount_due' => (double)$this->total_payable,
                'amount_payed' => (double)$this->total_payment,
                'balance' => (double)$this->balance,
                'OR_number' => $this->or_number,
                'booking_number' => $this->booking_number,
                'payment_type' => 'Cash',
                'payment_status' => 'Paid',
            ]);

            session()->flash('payment_message', 'Payment successfully!');

        } catch (\Exception $e) {
            session()->flash('payment_message', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function submit(){
        $this->submitBookingrecords();
        $this->submitBookingPayment();
        $this->submitBookingServices();
        $this->submitBookingDetails();
    }



    public function render()
    {
        return view('livewire.gate-entrance.gate.book-service');
    }
}
