<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Discount;

class Discounts extends Component
{
    public $discounts;
    public $add_code = false;
    public $discount_code;
    public $discount_title;
    public $discount_type = 'item';
    public $discount_ratevalue;
    public $discount_startDate;
    public $discount_endDate;
    public $discount_description;
    public $add_period = false;
    public $autoapply_discount = false;
    public $discount_ratevalue_type = 'Percentage';

    public function mount()
    {
        $this->fetchDiscounts();
    }
    public function render()
    {
        return view('livewire.settings.discounts');
    }
    
    public function fetchDiscounts()
    {
        $this->discounts = Discount::where('branch_id', auth()->user()->branch->id)->where('status', 'ACTIVE')->get();
    }

    public function storeDiscount()
    {
        $this->validate([
            'discount_title' => 'required|string|max:255',
            'discount_type' => 'required|in:item,order',
            'discount_ratevalue_type' => 'required|in:Amount,Percentage',
            'discount_ratevalue' => 'required|numeric|min:1',
            'discount_startDate' => 'nullable|date',
            'discount_endDate' => 'nullable|date|after_or_equal:discount_startDate',
            'discount_description' => 'nullable|string',
        ]);

        Discount::create([
            'code' => $this->discount_code == '' ? null : $this->discount_code,
            'title' =>  $this->discount_title,
            'type' =>  $this->discount_type == 'item' ? 'SINGLE' : 'WHOLE',
            'amount' => $this->discount_ratevalue_type == 'Amount' ? $this->discount_ratevalue : 0,
            'percentage' => $this->discount_ratevalue_type == 'Percentage' ? $this->discount_ratevalue : 0,
            'start_date' => $this->discount_startDate,
            'end_date' => $this->discount_endDate,
            'description' => $this->discount_description,
            'auto_apply' => $this->autoapply_discount ? 1 : 0,
            'branch_id' => auth()->user()->branch->id,
            'company_id' => auth()->user()->branch->company->id,
            'status' => 'ACTIVE',
            'created_by' => auth()->user()->emp_id,
            'updated_by' => auth()->user()->emp_id,
        ]);

        session()->flash('success', 'Discount code created successfully!');
        $this->dispatch('resetCreateDiscountForm');
        // Reset form fields
        $this->reset(['discount_code', 'discount_title', 'discount_type', 'discount_ratevalue', 'discount_startDate', 'discount_endDate', 'discount_description', 'discount_ratevalue_type', 'autoapply_discount']);
    }

    public function updatedAutoapplyDiscount($value)
    {
        if ($value) {
            $this->add_code = false;
            $this->discount_code = '';
            $this->discount_type = 'order';
        }else{
            $this->discount_type = 'item';
        }
    }

    public function updatedAddCode($value)
    {
        if ($value) {
            $this->autoapply_discount = false;
        }
    }

    public function deactivateDiscount($discountId)
    {
        $discount = Discount::findOrFail($discountId);
        $discount->status = 'INACTIVE';
        $discount->updated_by = auth()->user()->emp_id;
        $discount->save();

        session()->flash('success', 'Discount deactivated successfully!');
        $this->fetchDiscounts();
    }

}
