<?php

namespace App\Livewire\Accounting;
use App\Models\Accounting\COATemplateName;
use App\Models\Accounting\COATransactionTemplate;
use App\Models\Accounting\COATransactionTemplateDetail;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\AccountType;
use Livewire\Component;
use Carbon\Carbon;
use WireUi\Traits\WireUiActions;

class ChartOfAccountManagement extends Component
{
    use WireUiActions;

    public $templates = [];
    public $accountTitles = [];
    public $accountTypes = [];

    // for create account title
    public $accountTitle;
    public $accountCode;
    public $accountType;
    public $parentTitle;
    public $normalBalance;

    // for create account type
    public $typeName;

     protected $messages = [
        'accountTitle.required' => 'Account title is required.',
        'accountTitle.string' => 'Account title must be a string.',
        'accountTitle.max' => 'Account title must not exceed 255 characters.',
        'accountCode.required' => 'Account code is required.',
        'accountCode.string' => 'Account code must be a string.',
        'accountCode.max' => 'Account code must not exceed 50 characters.',
        'accountCode.unique' => 'The account code has already been taken.',
        'parentTitle.exists' => 'Selected parent title does not exist.',
        'accountLabel.required' => 'Account label is required.',
        'accountLabel.string' => 'Account label must be a string.',
        'accountLabel.max' => 'Account label must not exceed 255 characters.',
        'normalBalance.required' => 'Normal balance is required.',
        'typeName.required' => 'Account type name is required.',
        'typeName.string' => 'Account type name must be a string.',
        'typeName.max' => 'Account type name must not exceed 255 characters.',
     ];
    
    public function render()
    {
        return view('livewire.accounting.chart-of-account-management');
    }

    public function mount(){
        $this->fetchtemplate();
        $this->fetchAccountTitles();
        $this->fetchAccountTypes();
    }

    public function fetchtemplate(){
        $this->templates = COATransactionTemplate::all();
    }

    public function fetchAccountTitles()
    {
        $this->accountTitles = ChartOfAccount::all()->sortBy('account_code');
    }
    public function fetchAccountTypes()
    {
        $this->accountTypes = AccountType::all();
    }

    public function saveAccountTitle()
    {
        $this->validate([
            'accountTitle' => 'required|string|max:255',
            'accountCode' => 'required|string|max:50|unique:actng_chart_of_accounts,account_code',
            'parentTitle' => 'nullable|exists:actng_chart_of_accounts,id',
            'normalBalance' => 'required|in:DEBIT,CREDIT',
            'accountType' => 'required|exists:actng_account_types,id',
        ]);

        $account = new ChartOfAccount();
        $account->company_id = auth()->user()->branch->company_id;
        $account->account_title = $this->accountTitle;
        $account->transaction_type = $this->accountType;
        $account->account_code = $this->accountCode;
        $account->parent_id = $this->parentTitle;
        $account->normal_balance = $this->normalBalance;
        $account->created_by = auth()->user()->id;
        $account->created_at = Carbon::now('Asia/Manila');
        $account->save();

        // Reset form fields
        $this->reset(['accountTitle', 'accountCode', 'parentTitle', 'normalBalance', 'accountType']);
        $this->modal()->close('cardModal');
        $this->fetchAccountTitles();
        $this->successNotificationTitle();

        // Refresh account titles list
        $this->fetchAccountTitles();
    }

        public function saveAccountType(){
        $this->validate([
            'typeName' => 'required|string|max:255',
        ]);
        $companyId = auth()->user()->branch->company_id;
        $type = new AccountType();
        $type->company_id = $companyId;
        $type->type_name = $this->typeName;
        $type->created_by = auth()->user()->id;
        $type->created_at = Carbon::now('Asia/Manila');
        $type->save();

        // Reset form fields
        $this->reset(['typeName']);
        $this->modal()->close('cardModalType');
        $this->fetchAccountTypes();
        $this->successNotificationType();
    }

        public function successNotificationTitle(): void
    {
        $this->notification()->send([
            'icon' => 'success',
            'title' => 'Account Title Saved!',
            'description' => 'The account title has been successfully saved.',
        ]);
    }

    public function successNotificationType(): void
    {
        $this->notification()->send([
            'icon' => 'success',
            'title' => 'Account Type Saved!',
            'description' => 'The account type has been successfully saved.',
        ]);
    }

}
