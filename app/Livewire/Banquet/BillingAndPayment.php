<?php

namespace App\Livewire\Banquet;

use Livewire\Component;
use WireUi\Traits\WireUiActions;
use App\Models\Discount;
use App\Models\EventDiscount;
use App\Models\BanquetEvent;
use App\Models\PaymentType;
use App\Models\Invoice;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\Payment;
use App\Models\CashierShift;
use Carbon\Carbon;
use App\Models\EventMenu;
use App\Models\EventService;
use App\Models\EventVenue;

class BillingAndPayment extends Component
{
    use WireUiActions;

    // Payment properties

        // mounted properties
            public $perOrderDiscounts;
            public $events;
            public $paymentTypes;
            public $shift_id;


        // for per item discount
            public $selectedMenuItemId;
            public $selectedServiceDiscountId;
            public $selectedVenueDiscountId;
            public $selectedFoodDiscountId;
            public $selectedServiceId;
            public $selectedVenueId;
            public $perItemDiscounts = []; // all available per item discounts for the dropdown
            public $selectedItemDiscounts = [];


        // for split payment
            public $splitPayments = [];
            public $selectedSplitId = [];
            public $paymentTypesToSplit = [];
            public $difference = 0.00;
        // display properties
            public $subTotal = 0.00;
            public $payment_totalDiscountAmount = 0.00;
            public $payment_totalAmountDue = 0.00;
        // Entry properties
            public $invoiceNumber;
            public $amountReceived;
            public $changeAmount = 0.00;
            public $selectedEventId;
            public $selectedEvent;
            public $selectedPaymentTypeId;
            public $selectedPerOrderDiscountIds = [];


    // payment summary tab
        public $invoices;
        public $totalDiscountAmount = 0.00;
        public $totalAmountDue = 0.00;
        public $grossAmount = 0.00;
        public $customer_name = 'N/A';
        public $showEvent;
        public $paymentMethod = 'CASH';
        public $payments;
        public $from_date;
        public $to_date;
        public $discountDetails;


     public function mount()
    {
        $this->checkShiftStatus();
    }

    public function fetchData(){
        $this->mountPaymentTab();
        $this->paymentSummaryMount();
    }
    public function render()
    {
        return view('livewire.banquet.billing-and-payment');
    }

    // PAYMENT TAB METHODS
        // Mounted properties
        public function mountPaymentTab(){
            $this->events = BanquetEvent::where('status', 'CONFIRMED')->get();
            $this->paymentTypes = PaymentType::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->get()->toArray();
            $this->paymentTypesToSplit = PaymentType::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->get()->toArray(); // for split payment dropdown

            // append SPLIT to payment types
            $this->paymentTypes[] = ['id' => 'SPLIT', 'payment_type_name' => 'SPLIT'];

            $this->perItemDiscounts = Discount::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->where('type','SINGLE')->get();
            $this->perOrderDiscounts = Discount::where('branch_id', auth()->user()->branch->id)
                ->where('status', 'ACTIVE')
                ->where('type','WHOLE')
                ->get();
        }
        public function updatedSelectedEventId($value)
        {
            $this->selectedEvent = BanquetEvent::with(['eventMenus', 'eventServices', 'equipmentRequests'])->find($value);
            if($this->selectedEvent){
                $this->calculateAmountDue();
            }

        }
        public function updatedSelectedPaymentTypeId($value)
        {
            if(!$this->selectedEventId){
                $this->notify('No Event Selected', 'error', 'Please select an event before selecting payment type.');
                $this->selectedPaymentTypeId = null;
                return;
            }
            if($value === 'SPLIT'){
                $this->modal()->open('splitPaymentModal');
            }else{
                $this->selectedPaymentTypeId = $value;
            }

        }

        public function addToSplitPayments(){

            $this->splitPayments[] = [
                'type' => $this->paymentTypesToSplit[array_search($this->selectedSplitId, array_column($this->paymentTypesToSplit, 'id'))]['payment_type_name'] ?? 'Unknown',
                'payment_type_id' => $this->selectedSplitId,
                'amount' => null,
            ];
            $this->selectedSplitId = null;
            return $this->splitPayments;
        }

        public function calculateAmountDue(){
            // reset totals
            $this->payment_totalDiscountAmount = 0.00;
            $this->payment_totalAmountDue = 0.00;

            $this->subTotal = $this->selectedEvent->eventMenus->sum(function($em) {
                return $em->qty * ($em->price->amount ?? 0);
            }) + $this->selectedEvent->eventServices->sum(function($es) {
                return $es->qty * ($es->price->amount ?? 0);
            }) + $this->selectedEvent->eventVenues->sum(function($er) {
                return $er->qty * ($er->price->amount ?? 0);
            });

            // per-item discounts (food/event menus)
            if ($this->selectedEvent && !empty($this->selectedItemDiscounts)) {
                // loop through each selected menu item and calculate total discount for that item based on the selected discounts, then sum it to the total discount amount
                foreach ($this->selectedEvent->eventMenus as $eventMenu) {
                    $this->payment_totalDiscountAmount += $this->getFoodDiscountByMenuId($eventMenu->id);
                }
                // per-item discounts (services)
                foreach ($this->selectedEvent->eventServices as $eventService) {
                    $this->payment_totalDiscountAmount += $this->getServiceDiscountByServiceId($eventService->id);
                }
                // per-item discounts (venue)
                foreach ($this->selectedEvent->eventVenues as $eventVenue) {
                    $this->payment_totalDiscountAmount += $this->getVenueDiscountByVenueId($eventVenue->id);
                }
            }

            $this->payment_totalAmountDue = $this->subTotal - $this->payment_totalDiscountAmount;

            // apply per-order discounts on top of item-level total
            if(!empty($this->selectedPerOrderDiscountIds)){
                foreach($this->perOrderDiscounts as $discount){
                    if(in_array($discount->id, $this->selectedPerOrderDiscountIds)){
                        if($discount->amount > 0){
                            $this->payment_totalAmountDue -= $discount->amount;
                            $this->payment_totalDiscountAmount += $discount->amount;
                        } elseif($discount->percentage > 0){
                            $discountAmount = ($discount->percentage / 100) * $this->payment_totalAmountDue;
                            $this->payment_totalAmountDue -= $discountAmount;
                            $this->payment_totalDiscountAmount += $discountAmount;
                        }
                    }
                }
            }

            $this->updateChangeAmount();

        }

        public function updatedAmountReceived($value)
        {
            $this->updateChangeAmount();
        }

        private function updateChangeAmount(): void
        {
            $amountReceived = (float) ($this->amountReceived ?: 0);
            $amountDue = (float) ($this->payment_totalAmountDue ?: 0);
            $this->changeAmount = round($amountReceived - $amountDue, 2);
        }

        public function updatedSelectedPerOrderDiscountIds(){
            if(!$this->selectedEvent){
                $this->notify('No Event Selected', 'error', 'Please select an event before applying discounts.');
                $this->selectedPerOrderDiscountIds = [];
                return;
            }
                $this->calculateAmountDue();


        }

        // add per item discount to the selected discounts array
        public function addToFoodDiscounts(){
                if (!$this->selectedFoodDiscountId || !$this->selectedMenuItemId) {
                    return;
                }
                $discount = $this->perItemDiscounts->where('id', $this->selectedFoodDiscountId)->first();
                if (!$discount) return;

                if (!isset($this->selectedItemDiscounts[$this->selectedMenuItemId])) {
                    $this->selectedItemDiscounts[$this->selectedMenuItemId] = [];
                }
                $this->selectedItemDiscounts[$this->selectedMenuItemId][] = [
                    'title'       => $discount->title ?? 'Unknown Discount',
                    'value'      => $discount->amount > 0 ? $discount->amount : $discount->percentage . '%',
                    'discount_id' => $discount->id,
                    'amount' => $discount->amount > 0 ? $discount->amount : 0, // This will hold the actual discount amount to be applied, which can be set in the UI
                    'percentage' => $discount->amount > 0 ? 0 : $discount->percentage,
                    'type' => 'FOOD',
                ];
                // sum all the discounts for the selected menu item and calculate the new amount due



                $this->calculateAmountDue();
                $this->selectedFoodDiscountId = null;

        }

        // discount for service
        public function addToServiceDiscounts(){
            if (!$this->selectedServiceDiscountId || !$this->selectedServiceId) {
                return;
            }
            $discount = $this->perItemDiscounts->where('id', $this->selectedServiceDiscountId)->first();
            if (!$discount) return;
            if (!isset($this->selectedItemDiscounts[$this->selectedServiceId])) {
                $this->selectedItemDiscounts[$this->selectedServiceId] = [];
            }
            $this->selectedItemDiscounts[$this->selectedServiceId][] = [
                'title'       => $discount->title ?? 'Unknown Discount',
                'value'      => $discount->amount > 0 ? $discount->amount : $discount->percentage . '%',
                'discount_id' => $discount->id,
                'amount' => $discount->amount > 0 ? $discount->amount : 0, // This will hold the actual discount amount to be applied, which can be set in the UI
                'percentage' => $discount->amount > 0 ? 0 : $discount->percentage,
                'type' => 'SERVICE',
            ];
            $this->calculateAmountDue();
            $this->selectedServiceDiscountId = null;
        }

        // discount for venue
        public function addToVenueDiscounts(){
            if (!$this->selectedVenueDiscountId || !$this->selectedVenueId) {
                return;
            }
            $discount = $this->perItemDiscounts->where('id', $this->selectedVenueDiscountId)->first();
            if (!$discount) return;
            if (!isset($this->selectedItemDiscounts[$this->selectedVenueId])) {
                $this->selectedItemDiscounts[$this->selectedVenueId] = [];
            }
            $this->selectedItemDiscounts[$this->selectedVenueId][] = [
                'title'       => $discount->title ?? 'Unknown Discount',
                'value'      => $discount->amount > 0 ? $discount->amount : $discount->percentage . '%',
                'discount_id' => $discount->id,
                'amount' => $discount->amount > 0 ? $discount->amount : 0, // This will hold the actual discount amount to be applied, which can be set in the UI
                'percentage' => $discount->amount > 0 ? 0 : $discount->percentage,
                'type' => 'VENUE',
            ];
            $this->calculateAmountDue();
            $this->selectedVenueDiscountId = null;
        }

        // set the selected menu item for discount application
        public function setDiscountedFood($eventMenuId){
            $this->selectedMenuItemId = $eventMenuId;
            $this->modal()->open('foodDiscountModal');

        }
        //set the selected service for discount application
        public function setDiscountedService($eventServiceId){
            $this->selectedServiceId = $eventServiceId;
            $this->modal()->open('serviceDiscountModal');
        }
        // set the selected venue for discount application
        public function setDiscountedVenue($eventVenueId){
            $this->selectedVenueId = $eventVenueId;
            $this->modal()->open('venueDiscountModal');
        }

            public function removeFromFoodDiscounts($index){
                if(isset($this->selectedItemDiscounts[$this->selectedMenuItemId][$index])){
                    array_splice($this->selectedItemDiscounts[$this->selectedMenuItemId], $index, 1);
                }
                $this->calculateAmountDue();
            }

            public function removeFromServiceDiscounts($index){
                if(isset($this->selectedItemDiscounts[$this->selectedServiceId][$index])){
                    array_splice($this->selectedItemDiscounts[$this->selectedServiceId], $index, 1);
                }
                $this->calculateAmountDue();
            }

            public function removeFromVenueDiscounts($index){
                if(isset($this->selectedItemDiscounts[$this->selectedVenueId][$index])){
                    array_splice($this->selectedItemDiscounts[$this->selectedVenueId], $index, 1);
                }
                $this->calculateAmountDue();
            }

        // calculate total discount for a menu item based on the selected discounts
        public function getFoodDiscountByMenuId($eventMenuId)
        {
            if (!$this->selectedEvent) {
                return 0.00;
            }

            $eventMenu = $this->selectedEvent->eventMenus->firstWhere('id', $eventMenuId);
            if (!$eventMenu) {
                return 0.00;
            }

            $itemTotal = ($eventMenu->price->amount ?? 0) * ($eventMenu->qty ?? 0);
            $discounts = $this->selectedItemDiscounts[$eventMenuId] ?? [];
            $totalDiscount = 0.00;

            foreach ($discounts as $discount) {
                if (!empty($discount['amount']) && (float) $discount['amount'] > 0) {
                    $totalDiscount += (float) $discount['amount'];
                    continue;
                }

                if (!empty($discount['percentage']) && (float) $discount['percentage'] > 0) {
                    $totalDiscount += ((float) $discount['percentage'] / 100) * $itemTotal;
                }
            }

            return min($totalDiscount, $itemTotal);
        }

        public function getServiceDiscountByServiceId($eventServiceId)
        {
            if (!$this->selectedEvent) {
                return 0.00;
            }

            $eventService = $this->selectedEvent->eventServices->firstWhere('id', $eventServiceId);
            if (!$eventService) {
                return 0.00;
            }

            $itemTotal = ($eventService->price->amount ?? 0) * ($eventService->qty ?? 0);
            $discounts = $this->selectedItemDiscounts[$eventServiceId] ?? [];
            $totalDiscount = 0.00;

            foreach ($discounts as $discount) {
                if (!empty($discount['amount']) && (float) $discount['amount'] > 0) {
                    $totalDiscount += (float) $discount['amount'];
                    continue;
                }

                if (!empty($discount['percentage']) && (float) $discount['percentage'] > 0) {
                    $totalDiscount += ((float) $discount['percentage'] / 100) * $itemTotal;
                }
            }

            return min($totalDiscount, $itemTotal);
        }

        public function getVenueDiscountByVenueId($eventVenueId)
        {
            if (!$this->selectedEvent) {
                return 0.00;
            }

            $eventVenue = $this->selectedEvent->eventVenues->firstWhere('id', $eventVenueId);
            if (!$eventVenue) {
                return 0.00;
            }

            $itemTotal = ($eventVenue->price->amount ?? 0) * ($eventVenue->qty ?? 0);
            $discounts = $this->selectedItemDiscounts[$eventVenueId] ?? [];
            $totalDiscount = 0.00;

            foreach ($discounts as $discount) {
                if (!empty($discount['amount']) && (float) $discount['amount'] > 0) {
                    $totalDiscount += (float) $discount['amount'];
                    continue;
                }

                if (!empty($discount['percentage']) && (float) $discount['percentage'] > 0) {
                    $totalDiscount += ((float) $discount['percentage'] / 100) * $itemTotal;
                }
            }

            return min($totalDiscount, $itemTotal);
        }


        public function checkShiftStatus()
    {
        $openShift = CashierShift::where('cashier_id', auth()->user()->employee->id)
            ->where('shift_status', 'OPEN')
            ->first();

        if ($openShift) {
            $this->shift_id = $openShift->id;
                    $this->fetchData();
        }else{
          session()->flash('error', 'Please open a shift first before proceeding to invoicing.');
          $this->redirectRoute('make.open.shift', navigate: true);
        }
    }

        public function processPayment(){
            // validate entries
            $this->validate([
                'selectedEventId' => 'required',
                'selectedPaymentTypeId' => 'required',
                'amountReceived' => 'required|numeric|min:' . $this->payment_totalAmountDue,
                'invoiceNumber' => 'nullable|unique:invoices,invoice_number',
            ]);

            $curYear = now()->year;
            $branchId = auth()->user()->branch_id;
            $yearlyCount = Invoice::where('branch_id', $branchId)
                ->whereYear('created_at', $curYear)
                ->count() + 1;
            $reference = 'INV-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);

                // invoice
                $invoice = new Invoice();
                $invoice->reference = $reference;
                $invoice->invoice_number = $this->invoiceNumber;
                $invoice->invoice_type = 'OFFICIAL RECEIPT';
                $invoice->amount = $this->payment_totalAmountDue;
                $invoice->customer_name = ($this->selectedEvent->customer->customer_fname ?? 'N/A') . ' ' . ($this->selectedEvent->customer->customer_lname ?? '');
                $invoice->customer_id = $this->selectedEvent->customer_id;
                $invoice->event_id = $this->selectedEventId;
                $invoice->payment_mode = 'CASH';
                $invoice->status = 'CLOSED';
                $invoice->prepared_by = auth()->user()->emp_id;
                $invoice->branch_id = auth()->user()->branch->id;
                $invoice->original_amount = $this->subTotal;
                $invoice->adjusted_amount = $this->payment_totalAmountDue;
                $invoice->shift_id = $this->shift_id;
                $invoice->created_at = Carbon::now('Asia/Manila');
                $invoice->save();

                // payment
                if($this->selectedPaymentTypeId === 'SPLIT'){
                    foreach($this->splitPayments as $split){
                        $payment = new Payment();
                        $payment->invoice_id = $invoice->id;
                        $payment->amount = $split['amount'];
                        $payment->payment_type_id = $split['payment_type_id'];
                        $payment->type = 'BEO';
                        $payment->prepared_by = auth()->user()->emp_id;
                        $payment->created_at = Carbon::now('Asia/Manila');
                        $payment->customer_id = $this->selectedEvent->customer_id;
                        $payment->shift_id = $this->shift_id;
                        $payment->status = 'FULL';
                        $payment->branch_id = auth()->user()->branch_id;
                        $payment->save();
                    }
                } else {
                    $payment = new Payment();
                    $payment->invoice_id = $invoice->id;
                    $payment->amount = $this->payment_totalAmountDue;
                    $payment->payment_type_id = $this->selectedPaymentTypeId;
                    $payment->type = 'BEO';
                    $payment->prepared_by = auth()->user()->emp_id;
                    $payment->created_at = Carbon::now('Asia/Manila');
                    $payment->shift_id = $this->shift_id;
                    $payment->status = 'FULL';
                    $payment->customer_id = $this->selectedEvent->customer_id;
                    $payment->branch_id = auth()->user()->branch_id;
                    $payment->save();
                }

                // event discount
                if(!empty($this->selectedPerOrderDiscountIds)){
                    foreach($this->perOrderDiscounts as $discount){
                        if(in_array($discount->id, $this->selectedPerOrderDiscountIds)){
                            $orderDiscount = new EventDiscount();
                            $orderDiscount->event_id = $this->selectedEventId;
                            $orderDiscount->discount_id = $discount->id;
                            $orderDiscount->type = 'WHOLE';
                            $orderDiscount->created_by = auth()->user()->emp_id;
                            $orderDiscount->branch_id = auth()->user()->branch_id;
                                if($discount->amount > 0){
                                    $orderDiscount->amount = $discount->amount;
                                } elseif($discount->percentage > 0){
                                    $orderDiscount->amount = ($discount->percentage / 100) * $this->payment_totalAmountDue;
                                }
                            $orderDiscount->save();
                        }
                    }
                }
                // item discounts
                if(!empty($this->selectedItemDiscounts)){
                    foreach($this->selectedItemDiscounts as $discounted_item => $discounts){
                        foreach($discounts as $discount){
                            $orderDiscount = new EventDiscount();
                            $orderDiscount->event_id = $this->selectedEventId;
                            $orderDiscount->discount_id = $discount['discount_id'];
                            $orderDiscount->type = 'SINGLE';
                            $orderDiscount->created_by = auth()->user()->emp_id;
                            $orderDiscount->branch_id = auth()->user()->branch_id;
                            if($discount['type'] === 'FOOD'){
                                $orderDiscount->event_menu_id = $discounted_item;
                            }else if($discount['type'] === 'SERVICE'){
                                $orderDiscount->event_service_id = $discounted_item;
                            }else if($discount['type'] === 'VENUE'){
                                $orderDiscount->event_venue_id = $discounted_item;
                            }
                            if($discount['amount'] > 0){
                                $orderDiscount['amount'] = $discount['amount'];
                            } elseif($discount['percentage'] > 0){
                                $orderDiscount['amount'] = ($discount['percentage'] / 100) * $this->payment_totalAmountDue;
                            }

                            $orderDiscount->save();
                        }
                    }
                }

                //update evenbt to closed
                $event = BanquetEvent::find($this->selectedEventId);
                $event->status = 'CLOSED';
                $event->save();

                // reset payment tab
                $this->resetPaymentTab();

                // for now, we'll just show a success notification
                $this->notify('Payment Processed', 'success', 'The payment has been successfully processed.');
        }

        public function resetPaymentTab(){
            $this->invoiceNumber = null;
            $this->amountReceived = null;
            $this->changeAmount = 0.00;
            $this->selectedPerOrderDiscountIds = [];
            $this->selectedItemDiscounts = [];
            $this->selectedEventId = null;
            $this->selectedEvent = null;
            $this->selectedPaymentTypeId = null;
            $this->splitPayments = [];
            $this->selectedSplitId = null;
            $this->payment_totalAmountDue = 0.00;
            $this->payment_totalDiscountAmount = 0.00;
            $this->subTotal = 0.00;

            $this->selectedMenuItemId = null;
            $this->selectedServiceId = null;
            $this->selectedVenueId = null;
            $this->selectedFoodDiscountId = null;
            $this->selectedServiceDiscountId = null;
            $this->selectedVenueDiscountId = null;

            $this->events = BanquetEvent::where('status', 'CONFIRMED')->get();

        }

        public function removeFromSplitPayments($index){
            if(isset($this->splitPayments[$index])){
                array_splice($this->splitPayments, $index, 1);
            }
        }

    //END OF PAYMENT TAB METHODS




    // payment summary methods

        public function paymentSummaryMount(){
            $this->from_date = date('Y-m-d');
            $this->to_date = date('Y-m-d');
            $this->invoices = Invoice::where([['branch_id', auth()->user()->branch->id],['invoice_type', 'OFFICIAL RECEIPT']])
                ->whereDate('created_at', now('Asia/Manila')->toDateString())
                ->with('customers', 'order','order.order_details','order.order_details.menu.price_levels','order.tables')
                ->get();

        }
        public function filterInvoicesByDate()
        {
            $this->validate([
                        'from_date' => 'required|date',
                        'to_date' => 'required|date|after_or_equal:from_date',
                    ]);
            $this->invoices = Invoice::where([['branch_id', auth()->user()->branch->id],['invoice_type', 'BEO']])
                ->whereBetween('created_at', [$this->from_date . ' 00:00:00', $this->to_date . ' 23:59:59'])
                ->with('customers', 'order','order.order_details','order.order_details.menu.price_levels','order.tables')
                ->get();
        }
        public function viewInvoiceDetails($eventId, $invoiceId)
        {   //fetch discount details
            $this->discountDetails = EventDiscount::where('event_id', $eventId)
                ->with('discount')
                ->get();
                // dd($this->discountDetails);
            // calculate gross amount
            $this->grossAmount = EventMenu::where('event_id', $eventId)
                ->with('price')
                ->get()
                ->sum(function($detail) {
                    return $detail->qty * ($detail->price->amount ?? 0);
                });

            $this->grossAmount += EventService::where('event_id', $eventId)
                ->with('price')
                ->get()
                ->sum(function($detail) {
                    return $detail->qty * ($detail->price->amount ?? 0);
                });

            $this->grossAmount += EventVenue::where('event_id', $eventId)
                ->with('price')
                ->get()
                ->sum(function($detail) {
                    return $detail->qty * ($detail->price->amount ?? 0);
                });
            // Calculate total order-level discounts
            $orderDiscountSum = EventDiscount::where('event_id', $eventId)
                ->where('type', 'WHOLE')
                ->with('discount')
                ->get()
                ->sum(function($od) {
                    return $od->discount->amount > 0
                        ? $od->discount->amount
                        : ($od->discount->percentage / 100) * $this->grossAmount;
                });
            $eventInfo = BanquetEvent::find($eventId);
            $this->showEvent = $eventInfo;
            $invoice = Invoice::where('event_id', $eventId)->where('id', $invoiceId)->with('payments','payments.payment_type')->first();
            $this->payments = Payment::where('invoice_id', $invoice->id)->with('payment_type')->get();
            if($this->payments->where('type', '!=', 'REFUND')->count() == 1){
                $this->paymentMethod = $this->payments->first()->payment_type->payment_type_name;
            } else {
                $this->paymentMethod = 'SPLIT';
            }
            $this->customer_name = $invoice->customer_name ?? 'N/A';


            $this->totalDiscountAmount = $this->grossAmount - $invoice->amount;
            $this->totalAmountDue  = $invoice->amount;

        }

    // end of payment summary methods


    // common methods
    public function notify($title, $icon, $description) : void
    {
        $this->notification()->send([
            'icon' => $icon,
            'title' => $title,
            'description' => $description,
        ]);
    }
}
