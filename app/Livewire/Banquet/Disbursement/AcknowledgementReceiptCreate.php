<?php

namespace App\Livewire\Banquet\Disbursement;

use Livewire\Component;
use App\Models\AcknowledgementReceipt;
use App\Models\Customer;
use App\Models\Bank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PettyCashVoucher;
use App\Models\BanquetEvent as Event;
use App\Models\BanquetProcurement;




class AcknowledgementReceiptCreate extends Component
{
    // data
    public $expenses = [];
    public $customers = [];
    public $banks = [];
    public $events = [];
    public $validEventIds = [];
    public $isCreate = true;
    public $currentARStatus = 'DRAFT';
    public $acknowledgementReceiptID;
    public $reference;

    // inputs
    public $customerId;
    public $checkNumber;
    public $checkAmount;
    public $checkDate;
    public $bankId;
    public $accountName;
    public $amountInWords;
    public $notes;
    public $checkStatus = 'CURRENT';
    public $saveAsStatus = 'DRAFT';
    public $eventId;

    //selected customer and bank
    public $selectedCustomer;
    public $selectedBank;
    public $selectedEvent;


    // customer registration
    public $customerFirstName;
    public $customerMiddleName;
    public $customerLastName;
    public $customerSuffix;
    public $customerGender;
    public $customerBirthdate;
    public $customerEmail;
    public $customerPhone;
    public $customerAddress;

    // bank registration
    public $bankName;
    public $bankCode;
    public $bankAddress;
    public $bankContactNumber;
    public $bankEmail;

    protected $messages = [
        'customerId.required' => 'Customer is required.',
        'customerId.exists' => 'Selected customer does not exist.',
        'checkAmount.required' => 'Check amount is required.',
        'checkAmount.numeric' => 'Check amount must be a number.',
        'checkAmount.min' => 'Check amount cannot be negative.',
        'bankId.required' => 'Bank is required.',
        'bankId.exists' => 'Selected bank does not exist.',
        'checkStatus.required' => 'Check status is required.',
        'checkStatus.in' => 'Check status must be either CURRENT or POST-DATED.',
        'saveAsStatus.required' => 'Save as status is required.',
        'saveAsStatus.in' => 'Save as status must be either DRAFT or FINAL.',
        'notes.max' => 'Notes cannot exceed 1000 characters.',
        'eventId.exists' => 'Selected event does not exist.',
    ];

    public function render()
    {
        return view('livewire.banquet.disbursement.acknowledgement-receipt-create');
    }

    public function mount(Request $request = null)
    {
        if(auth()->user()->employee->getModulePermission('Acknowledgement Receipt') != 2 ){
            if($request && $request->has('AR-id')){
                $this->customers = Customer::where('branch_id', auth()->user()->branch_id)->get();
                $this->acknowledgementReceiptID = $request->query('AR-id');
                $this->loadExistingAR($this->acknowledgementReceiptID);
            }else{
                $this->fetchData();
            }
            
        }else{ return redirect()->to('dashboard');}
    }

    PUBLIC FUNCTION updated($name, $value){
        if($name == 'checkAmount'){
            $this->updatedCheckAmount($value);
        }
    }

    public function fetchData(){
        $this->customers = Customer::where('branch_id', auth()->user()->branch_id)->get();
        $this->banks = Bank::where('branch_id', auth()->user()->branch_id)->get();
        $this->validEventIds = BanquetProcurement::where('branch_id', auth()->user()->branch_id)->where('status', 'APPROVED')->pluck('event_id')->toArray();
        $this->events = Event::where('end_date', '>=', Carbon::now('Asia/Manila')->startOfDay())
                                ->whereIn('id', $this->validEventIds)->get();
    }

    // upon update on check amount the amount in words will be updated
    public function updatedCheckAmount($value)
    {   
        if($value < 0){
            $this->checkAmount = 0;
            $this->amountInWords = $this->convertNumberToWords(0);
            return;
        }
        $this->amountInWords = $this->convertNumberToWords($value);
    }

    // convert number to words
    private function convertNumberToWords($number)
    {
        // Separate pesos and centavos
        $pesos = floor($number);
        $centavos = round(($number - $pesos) * 100);
        
        // Convert pesos to words
        $pesosInWords = $this->numberToWords($pesos);
        $result = ucfirst($pesosInWords) . ' PESO' . ($pesos != 1 ? 'S' : '');
        
        // Add centavos if any
        if ($centavos > 0) {
            $centavosInWords = $this->numberToWords($centavos);
            $result .= ' AND ' . $centavosInWords . ' CENTAVO' . ($centavos != 1 ? 'S' : '');
        }
        
        return $result . ' ONLY';
    }

    private function numberToWords($number)
    {
        $ones = ['', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE'];
        $tens = ['', '', 'TWENTY', 'THIRTY', 'FORTY', 'FIFTY', 'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'];
        $teens = ['TEN', 'ELEVEN', 'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN', 'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'];

        if ($number == 0) return 'ZERO';

        $words = '';

        // Billions
        if ($number >= 1000000000) {
            $words .= $this->numberToWords(floor($number / 1000000000)) . ' BILLION ';
            $number %= 1000000000;
        }

        // Millions
        if ($number >= 1000000) {
            $words .= $this->numberToWords(floor($number / 1000000)) . ' MILLION ';
            $number %= 1000000;
        }

        // Thousands
        if ($number >= 1000) {
            $words .= $this->numberToWords(floor($number / 1000)) . ' THOUSAND ';
            $number %= 1000;
        }

        // Hundreds
        if ($number >= 100) {
            $words .= $ones[floor($number / 100)] . ' HUNDRED ';
            $number %= 100;
        }

        // Tens and ones
        if ($number >= 20) {
            $words .= $tens[floor($number / 10)] . ' ';
            $number %= 10;
        } elseif ($number >= 10) {
            $words .= $teens[$number - 10] . ' ';
            $number = 0;
        }

        if ($number > 0) {
            $words .= $ones[$number] . ' ';
        }

        return trim($words);
    }

    public function  selectCustomer($id){
        $this->selectedCustomer = Customer::find($id);
        if($this->selectedCustomer) {
            $this->customerId = $this->selectedCustomer->id;
            $this->accountName = $this->selectedCustomer->customer_fname . ' ' . $this->selectedCustomer->customer_mname . ' ' . $this->selectedCustomer->customer_lname . ' ' . $this->selectedCustomer->customer_suffix;
        }else{
            $this->dispatch('showAlert', ['type' => 'error','title' => 'Error', 'message' => 'Customer not found.']);
        }
         $this->dispatch('hideCustomerListModal');

    }

    public function registerCustomer(){

        // validation
        $this->validate([
            'customerFirstName' => 'required|string|max:100',
            'customerMiddleName' => 'nullable|string|max:100',
            'customerLastName' => 'required|string|max:100',
            'customerSuffix' => 'nullable|string|max:10',
            'customerGender' => 'required|in:Male,Female,Neutral',
            'customerBirthdate' => 'nullable|date',
            'customerEmail' => 'nullable|email|max:100',
            'customerPhone' => 'nullable|string|max:20',
            'customerAddress' => 'nullable|string|max:500',
        ]);

        $customer = Customer::create([
            'branch_id' => auth()->user()->branch_id,
            'customer_fname' => $this->customerFirstName,
            'customer_mname' => $this->customerMiddleName,
            'customer_lname' => $this->customerLastName,
            'customer_suffix' => $this->customerSuffix,
            'customer_gender' => $this->customerGender,
            'customer_birthdate' => $this->customerBirthdate,
            'customer_email' => $this->customerEmail,
            'customer_phone' => $this->customerPhone,
            'customer_address' => $this->customerAddress,
        ]);
        if($customer) {
            $this->dispatch('resetCustomerRegistrationForm');
            $this->dispatch('showAlert', ['type' => 'success','title' => 'Success', 'message' => 'Customer registered successfully.']);
            $this->selectCustomer($customer->id);
        }else{
            $this->dispatch('showAlert', ['type' => 'error','title' => 'Error', 'message' => 'Failed to register customer.']);
        }
    }

    public function selectBank($id){
        $this->selectedBank = Bank::find($id);
        if($this->selectedBank) {
            $this->bankId = $this->selectedBank->id;
        }else{
            $this->dispatch('showAlert', ['type' => 'error','title' => 'Error', 'message' => 'Bank not found.']);
        }
         $this->dispatch('hideBankListModal');
    }

    public function registerBank(){
        // validation
        $this->validate([
            'bankName' => 'required|string|max:100',
            'bankCode' => 'nullable|string|max:20|unique:banks,bank_code',
            'bankAddress' => 'nullable|string|max:500',
            'bankContactNumber' => 'nullable|string|max:20',
            'bankEmail' => 'nullable|email|max:100',
        ]);

        $bank = Bank::create([
            'branch_id' => auth()->user()->branch_id,
            'bank_name' => $this->bankName,
            'bank_code' => $this->bankCode,
            'bank_address' => $this->bankAddress,
            'contact_number' => $this->bankContactNumber,
            'email' => $this->bankEmail,
        ]);
        if($bank) {
            $this->dispatch('resetBankRegistrationForm');
            $this->dispatch('showAlert', ['type' => 'success','title' => 'Success', 'message' => 'Bank registered successfully.']);
            $this->selectBank($bank->id);
        }else{
            $this->dispatch('showAlert', ['type' => 'error','title' => 'Error', 'message' => 'Failed to register bank.']);
        }
    }

    public function selectEvent($id){
        $this->selectedEvent = Event::find($id);
        if($this->selectedEvent) {
            $this->eventId = $this->selectedEvent->id;
        }else{
            $this->dispatch('showAlert', ['type' => 'error','title' => 'Error', 'message' => 'Event not found.']);
        }   
        $this->dispatch('hideEventListModal');
    }

    public function saveAcknowledgementReceipt(){
        // validation
        $this->validate([
            'customerId' => 'required|exists:customers,id',
            'checkNumber' => 'required|string|max:50|unique:acknowledgement_receipts,check_number',
            'checkAmount' => 'required|numeric|min:0',
            'checkDate' => 'required|date',
            'bankId' => 'required|exists:banks,id',
            'accountName' => 'required|string|max:255',
            'amountInWords' => 'required|string|max:500',
            'saveAsStatus' => ['required', Rule::in(['DRAFT', 'OPEN'])],
            'checkStatus' => ['required', Rule::in(['CURRENT', 'POST-DATED'])],
            'notes' => 'nullable|string|max:1000',
            'eventId' => 'nullable|exists:banquet_events,id',
        ]);

        $curYear = now()->year;
        $branchId = auth()->user()->branch_id;
        $yearlyCount = AcknowledgementReceipt::where('branch_id', $branchId)
            ->whereYear('created_at', $curYear)
            ->count() + 1;
        $reference = 'AR-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);
        
    
        $acknowledgementReceipt = AcknowledgementReceipt::create([
            'branch_id' => auth()->user()->branch_id,
            'company_id' => auth()->user()->branch->company_id,
            'event_id' => $this->selectedEvent ? $this->selectedEvent->id : null,
            'reference' => $reference,
            'status' => Str::upper($this->saveAsStatus),
            'customer_id' => $this->customerId,
            'check_number' => $this->checkNumber,
            'check_amount' => $this->checkAmount,
            'check_date' => $this->checkDate,
            'bank_id' => $this->bankId,
            'account_name' => $this->accountName,
            'amount_in_words' => $this->amountInWords,
            'notes' => $this->notes,
            'check_status' => Str::upper($this->checkStatus),
            'created_by' => auth()->user()->emp_id,
        ]);

        if($acknowledgementReceipt) {
            $this->dispatch('showAlert', ['type' => 'success','title' => 'Success', 'message' => 'Acknowledgement Receipt created successfully.']);
            // reset form
            $this->reset();
            $this->mount();
        }else{
            $this->dispatch('showAlert', ['type' => 'error','title' => 'Error', 'message' => 'Failed to create Acknowledgement Receipt.']);
        }
    }

    public function loadExistingAR($id){
        $data = AcknowledgementReceipt::find($id);

        if($data){
            $this->reference = $data->reference;
            $this->checkNumber =  $data->check_number;
            $this->checkAmount = $data->check_amount;
            $this->checkDate =  $data->check_date;
            $this->accountName =  $data->account_name;
            $this->amountInWords =  $data->amount_in_words;
            $this->notes =  $data->notes;
            $this->checkStatus =  $data->check_status;
            $this->saveAsStatus =  $data->status;

            $this->selectBank($data->bank_id);
            $this->selectCustomer($data->customer_id);

            // load expesses using PCV linked to this AR
            $this->expenses = PettyCashVoucher::where('acknowledgement_receipt_id', $id)->get();

            $this->currentARStatus = $data->status;
            $this->isCreate = false;



        }
        
    }

    public function updateAcknowledgementReceipt(){

    $this->validate([
            'customerId' => 'required|exists:customers,id',
            'checkNumber' => 'required|string|max:50',
            'checkAmount' => 'required|numeric|min:0',
            'checkDate' => 'required|date',
            'bankId' => 'required|exists:banks,id',
            'accountName' => 'required|string|max:255',
            'amountInWords' => 'required|string|max:500',
            'saveAsStatus' => ['required', Rule::in(['DRAFT', 'OPEN'])],
            'checkStatus' => ['required', Rule::in(['CURRENT', 'POST-DATED'])],
            'notes' => 'nullable|string|max:500',
            'eventId' => 'nullable|exists:banquet_events,id',
        ]);

        $updateAR = AcknowledgementReceipt::where('id',$this->acknowledgementReceiptID)->first();
        $updateAR->update([
            'customer_id' => $this->customerId,
            'status' => $this->saveAsStatus,
            'event_id' => $this->selectedEvent ? $this->selectedEvent->id : null,
            'check_number' => $this->checkNumber,
            'check_amount' => $this->checkAmount,
            'check_date' => $this->checkDate,
            'bank_id' => $this->bankId,
            'account_name' => $this->accountName,
            'amount_in_words' => $this->amountInWords,
            'check_status' => $this->checkStatus,
            'notes' => $this->notes,
            'updated_by' => auth()->user()->emp_id,
        ]);
        $updateAR->save();

        $this->dispatch('showAlert', ['type' => 'success','title' => 'Success', 'message' => 'Acknowledgement Receipt updated successfully.']);


    }
}
