<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\COATemplateName;
use App\Models\Accounting\COATransactionTemplate;
use App\Models\Accounting\COATransactionTemplateDetail;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\AccountType;
use App\Models\Signatory;
use App\Models\Module;
use App\Models\Company;
use Livewire\Component;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Query;
use Illuminate\Http\Request;

class TransactionTemplateCreate extends Component
{
    //data
    public $templateNames = [];
    public $templates = [];
    public $chartOfAccounts = [];
    public $chartOfAccountsHeaders = [];
    public $accountTypes = [];
    public $selectedTemplate;
    public $companies = [];
    public $companyOptions = [];
    public $allAccountTitles;
    public $approvers;
    public $isEditable = true;
    public $saveAs = "DRAFT";
    public $action = "create";
    public $isApprover = false;
    public $viewedTemplate;

    // selections for create
    public $selectedCompanyId;
    public $selectedTransactionTypeId; // id

    public $selectedTemplateNameId; // id

    public $description;
    public $selectedTitleParent;
    public $selectedTitles = [];
    public $approverId;

    // express creation
    public $newTypeName;
    public $newTemplateName;


    protected $messages = [
        'selectedTransactionTypeId.required' => 'Transaction type is required.',
        'selectedTemplateNameId.required' => 'Template name is required.',
        'description.required' => 'Description is required.',
        'description.string' => 'Description must be a string.',
        'description.max' => 'Description must not exceed 255 characters.',
        'selectedTitles.required' => 'At least two titles must be selected.',
        'newTypeName.required' => 'Type name is required.',
        'newTypeName.string' => 'Type name must be a string.',
        'newTypeName.max' => 'Type name must not exceed 255 characters.',
        'newTemplateName.required' => 'Template name is required.',
        'newTemplateName.string' => 'Template name must be a string.',
        'newTemplateName.max' => 'Template name must not exceed 255 characters.',
        'selectedTransactionTypeId.exists' => 'Selected transaction type does not exist.',
        'selectedTemplateNameId.exists' => 'Selected template name does not exist.',
        'selectedCompanyId.required' => 'Company selection is required.',
    ];
    public function render()
    {
        return view('livewire.accounting.transaction-template-create');
    }

    public function mount(Request $request)
    {
        if ($request->has('template') && $request->has('action')) {
            if($request->query('action') == 'view'){
                 $this->getTamplateDetails($request->query('template'));
            }else{
                $this->getTamplateDetails($request->query('template'));
                $this->isApprover = true;
                $this->isEditable = false;
            }
        } else{
            $this->action = 'view';
        }
        $this->fetchData();
    }
    public function fetchData()
    {
        $this->companies = Company::where('company_status', 'ACTIVE')->get();
        $this->selectedCompanyId = auth()->user()->branch->company_id;
        $this->companyOptions = $this->companies->map(function ($company) {
            return [
                'id' => $company->id,
                'name' => $company->company_name . ' (' . $company->company_code . ')',
            ];
        })->values()->toArray();

        $companyId = $this->selectedCompanyId ?: auth()->user()->branch->company_id;

        $this->templateNames = COATemplateName::where('company_id', $companyId)->where('is_active', 1)->get();
        $this->accountTypes = AccountType::where('company_id', $companyId)->where('is_active', 1)->get();
        $parentIds = ChartOfAccount::where('is_active', true)->whereNotNull('parent_id')->distinct('parent_id')->pluck('parent_id')->toArray();
        $this->chartOfAccountsHeaders = ChartOfAccount::where('is_active', true)->whereIn('id', $parentIds)->get();
        $this->chartOfAccounts = ChartOfAccount::where('company_id', $companyId)->where('is_active', true)->get();
        $this->allAccountTitles = $this->chartOfAccounts;

        $module = Module::where('module_name', 'Accounting - Template Management')->first();
        $this->approvers = Signatory::with('employees')->where('branch_id', auth()->user()->branch_id)->where('module_id', $module->id)->where('signatory_type', 'APPROVER')->get()
            ->map(function ($user) {
                return [
                    'id' => $user->employees->id,
                    'full_name' => $user->employees->name . ' ' . $user->employees->middle_name . ' ' . $user->employees->last_name,
                ];
            });

            }


    public function getTamplateDetails($id)
    {
        $this->viewedTemplate = COATransactionTemplate::where('id', $id)->first();

        // prticulars
        foreach ($this->viewedTemplate->transactionDetails  as $particulars) {
            if ($particulars->type == 'DEBIT') {
                $this->addDebit($particulars->id, $particulars->accountTitle->account_title);
            } else {
                $this->addCredit($particulars->id, $particulars->accountTitle->account_title);
            }
        }

        $this->isEditable =  ($this->viewedTemplate->status == 'DRAFT' && $this->viewedTemplate->approved_date == NULL);

        //info
        $this->selectedCompanyId = $this->viewedTemplate->company_id;
        $this->selectedTransactionTypeId = $this->viewedTemplate->transaction_type;
        $this->selectedTemplateNameId = $this->viewedTemplate->template_name_id;
        $this->description = $this->viewedTemplate->description;
        $this->approverId = $this->viewedTemplate->approved_by;
    }

    public function updatedSelectedCompanyId()
    {
        $this->selectedTitleParent = null;
        $this->selectedTitles = [];

        $this->fetchData();
    }

    public function addDebit($titleId, $titleName)
    {
        if ($this->selectedTitles) {
            foreach ($this->selectedTitles as $title) {
                if ($title['id'] == $titleId) {
                    $this->dispatch('showAlert', ['timer' => 5000, 'type' => 'error', 'title' => 'Already Added!', 'message' => 'This title has already been added as ' . ($title['debit'] ? 'DEBIT' : 'CREDIT') . ' .']);
                    return;
                }
            }
        }
        $this->selectedTitles[] = [
            'id' => $titleId,
            'title' => $titleName,
            'debit' => 'XXXXXX',
            'credit' => '',
        ];
    }
    public function addCredit($titleId, $titleName)
    {
        if ($this->selectedTitles) {
            foreach ($this->selectedTitles as $title) {
                if ($title['id'] == $titleId) {
                    $this->dispatch('showAlert', ['timer' => 5000, 'type' => 'error', 'title' => 'Already Added!', 'message' => 'This title has already been added as ' . ($title['debit'] ? 'DEBIT' : 'CREDIT') . ' .']);
                    return;
                }
            }
        }
        $this->selectedTitles[] = [
            'id' => $titleId,
            'title' => $titleName,
            'debit' => '',
            'credit' => 'XXXXXX',
        ];
    }

    public function removeTitle($index)
    {
        unset($this->selectedTitles[$index]);
        $this->selectedTitles = array_values($this->selectedTitles);
    }

    // express creation for account type
    public function createType()
    {
        $this->validate([
            'newTypeName' => 'required|string|max:255',
        ]);

        $companyId = auth()->user()->branch->company_id;

        $type = new AccountType();
        $type->company_id = $companyId;
        $type->type_name = $this->newTypeName;
        $type->acct_code = $companyId . '-' . str_replace(' ', '_', strtolower($this->newTypeName));
        $type->is_active = 1;
        $type->created_by = auth()->user()->emp_id;
        $type->save();

        $this->accountTypes = AccountType::where('company_id', $companyId)->where('is_active', 1)->get();
        $this->selectedTransactionTypeId = $type->id;
        $this->newTypeName = '';
        $this->dispatch('close-type-modal');
    }

    // express creation for template name
    public function createTemplateName()
    {
        $this->validate([
            'newTemplateName' => 'required|string|max:255',
        ]);

        $companyId = $this->selectedCompanyId;

        $templateName = new COATemplateName();
        $templateName->company_id = $companyId;
        $templateName->template_name = $this->newTemplateName;
        $templateName->is_active = 1;
        $templateName->created_by = auth()->user()->emp_id;
        $templateName->save();

        $this->templateNames = COATemplateName::where('company_id', $companyId)->where('is_active', 1)->get();
        $this->selectedTemplateNameId = $templateName->id;
        $this->newTemplateName = '';
        $this->dispatch('close-template-name-modal');
    }

    // SAVING the template with details
    public function saveTemplate()
    {
        $this->validate([
            'selectedCompanyId' => 'required',
            'selectedTransactionTypeId' => 'required|exists:actng_account_types,id',
            'selectedTemplateNameId' => 'required|exists:actng_template_names,id',
            'description' => 'required|string|max:255',
            'selectedTitles' => 'required|array|min:2',
            'approverId' => 'required|exists:employees,id',
        ]);
        $companyId = $this->selectedCompanyId;
        $template = new COATransactionTemplate();
        $template->company_id = $companyId;
        $template->template_name_id = $this->selectedTemplateNameId;
        $template->transaction_type = $this->selectedTransactionTypeId;
        $template->description = $this->description;
        $template->is_active = 1;
        $template->created_by = auth()->user()->emp_id;
        $template->created_at = Carbon::now('Asia/Manila');
        $template->approved_by = $this->approverId;
        $template->save();

        foreach ($this->selectedTitles as $title) {
            $detail = new COATransactionTemplateDetail();
            $detail->template_id = $template->id;
            $detail->account_title_id = $title['id'];
            $detail->type = $title['debit'] ? 'DEBIT' : 'CREDIT';
            $detail->created_at =  Carbon::now('Asia/Manila');
            $detail->save();
        }
        $this->dispatch('showAlert', ['timer' => 5000, 'type' => 'success', 'title' => 'Template Created!', 'message' => 'The transaction template has been created successfully.']);
    }

    public function updatedSelectedTitleParent()
    {
        if ($this->selectedTitleParent != 'all') {
            $this->chartOfAccounts = $this->allAccountTitles->where('parent_id', $this->selectedTitleParent);
        } else {
            $this->chartOfAccounts = $this->allAccountTitles;
        }
    }

    public function resetForm()
    {
        $this->selectedTransactionTypeId = null;
        $this->selectedTemplateNameId = null;
        $this->description = '';
        $this->selectedTitleParent = null;
        $this->selectedTitles = [];
    }

    public function approverAction() {
        $this->validate([
            'saveAs' => 'required|in:APPROVED,REVISED',
        ]);
        $template =  $this->viewedTemplate;
        if($this->saveAs == 'APPROVED') {
        $template->update([
            'approved_date'=> Carbon::now('Asia/Manila'),
        ]);
        }else{
            $template->update([
            'status'=> 'DRAFT',
            ]);
        }

        $this->dispatch('showAlert', ['timer' => 5000, 'type' => 'success', 'title' => 'Success!', 'message' => 'Action successfully applied']);

    }
}
