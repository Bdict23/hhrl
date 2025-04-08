<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Department;
use App\Models\selectedItems;
use App\Models\Employee;
use App\Models\Withdrawal as WithdrawalModel;
use App\Models\WithdrawalItem;
use App\Models\Signatory;
use App\Models\Category;
use App\Models\Cardex;
use App\Models\Item;


class Withdrawal extends Component
{

    // Custom Columns Properties
    public $avlBal = false;
    public $avlQty = true;
    public $code = true;
    public $location = false;
    public $uom = true;
    public $brand = false;
    public $status = false;
    public $category = false;
    public $classification = false;
    public $barcode = false;


    public $departments = []; // display dept on ui
    public $selectedDepartment = null; // selected dept from user
    public $myCardexItems = []; // display items on ui particularly on modal
    public $selectedItems = []; // selected items from user and display on ui
    public $reviewers = []; // display reviewers on ui
    public $reviewer = null; // selected reviewer from user
    public $approvers = []; // display approvers on ui
    public $approver = null; // selected approver from user
    public $categories = []; // display categories on ui
    public $finalStatus = false; // selected final status from user
    public $haveSpan = false; // selected span status from user
    public $spanDate = null; // selected span date from user
    public $useDate = null; // selected use date from user


    public function mount()
    {
        $this->departments = Department::where('branch_id', auth()->user()->branch_id)->get();
        $myItems = Item::where([['company_id', auth()->user()->branch->company_id],['item_status','ACTIVE']])->get();

        // Calculate total balance for each item using the Cardex model
        $this->myCardexItems = $myItems->map(function ($item) {
            $totalIn = Cardex::where('status', 'final')->where('item_id', $item->id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_in');
            $totalOut = Cardex::where('status', 'final')->where('item_id', $item->id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_out');
            $totalReserved = Cardex::where('status', 'reserved')->where('item_id', $item->id)->where('source_branch_id',auth()->user()->branch_id)->sum('qty_out');
            $item->total_balance = $totalIn - $totalOut;
            $item->total_reserved = $totalReserved;
            $item->total_available = $item->total_balance - $totalReserved;

            return $item;
        });
        $this->reviewers = Signatory::with('employees')->where([['signatory_type', 'reviewer'],['branch_id',auth()->user()->branch_id]])->get();
        $this->approvers = Signatory::with('employees')->where('signatory_type', 'approver')->get();
        $this->categories = Category::where([['company_id', auth()->user()->branch->company_id],['status','ACTIVE'],['category_type','ITEM']])->get();
    }


    public function addItem($itemId, $balance, $available)
    {
        $item = Item::with('uom','category','location','brand','classification','costPrice')->find($itemId);
        if (!$item) {
            session()->flash('error', 'Item not found.');
            return;
        }
        if ($item->costPrice == null) {
            session()->flash('error', 'Item has no cost price.');
            return;
        }
        if ($item) {
            $this->selectedItems[] = [
                'id' => $item->id,
                'total_balance' => $balance,
                'total_available' => $available,
                'code' => $item->item_code ?? 'N/A',
                'name' => $item->item_description,
                'unit' => $item->uom->unit_symbol,
                'category' => $item->category->category_name,
                'classification' => $item->classification->classification_name ?? 'N/A',
                'barcode' => $item->item_barcode ?? 'N/A',
                'requested_qty' => 0,
                'location' => $item->location->location_name ?? 'N/A',
                'uom' => $item->uom->unit_name ?? 'N/A',
                'brand' => $item->brand->brand_name ?? 'N/A',
                'status' => $item->item_status,
                'cost' => $item->costPrice->amount,
                'total' => 0,
            ];
        }
    }
    public function removeItem($index)
    {
        unset($this->selectedItems[$index]);
        $this->selectedItems = array_values($this->selectedItems);
    }
    public function store()
    {
        $this->validate([
            'selectedDepartment' => 'required',
            'itemsOnCardex' => 'required|array|min:1',
            'reviewer' => 'required',
            'approver' => 'required',
        ]);

        $withdrawal = WithdrawalModel::create([
            'department_id' => $this->selectedDepartment,
            'reviewer_id' => $this->reviewer,
            'approver_id' => $this->approver,
            'final_status' => $this->finalStatus,
            'have_span' => $this->haveSpan,
            'span_date' => $this->spanDate,
            'use_date' => $this->useDate,
        ]);

        foreach ($this->selectedItems as $item) {
            WithdrawalItem::create([
                'withdrawal_id' => $withdrawal->id,
                'item_id' => $item['id'],
                // Add other necessary fields here
            ]);
        }

        session()->flash('message', 'Withdrawal request created successfully.');
        return redirect()->route('withdrawals.index');
    }
    public function updatedHaveSpan($value)
    {
        $this->spanDate = null;
        $this->useDate = null;
        if ($value) {
            $this->useDate = now()->format('Y-m-d');
        }
    }


    public function render()
    {
        return view('livewire.inventory.withdrawal');
    }
}
