<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\CashDrawer;
use App\Models\CashierShift;
use App\Models\Denomination;
use App\Models\ShiftDenomination;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\BranchMenu;
use App\Models\BranchMenuRecipe;



class MakeOpenShift extends Component
{
    public $drawers;
    public $coinDenominations = [];
    public $drawerId;
    public $billDenominations = [];
    public $denominationCounts = [];
    public $totalBeginningBalance = 0;


    

    protected $rules = [
        'drawerId' => 'required|exists:cash_drawers,id',
        'denominationCounts.*' => 'nullable|numeric|min:0',
    ];
    protected $messages = [
        'drawerId.required' => 'Please select a cash drawer.',
        'drawerId.exists' => 'The selected cash drawer is invalid.',
        'denominationCounts.*.numeric' => 'Please enter a valid number for denomination counts.',
        'denominationCounts.*.min' => 'Denomination counts cannot be negative.',
    ];
    public function render()
    {
        // Calculate total beginning balance in real-time
        $this->calculateTotal();
        return view('livewire.transactions.make-open-shift');
    }
    
    public function calculateTotal()
    {
        $total = 0;
        
        // Calculate bills total
        foreach ($this->billDenominations as $denomination) {
            $count = floatval($this->denominationCounts[$denomination->id] ?? 0);
            $total += $count * floatval($denomination->value);
        }
        
        // Calculate coins total
        foreach ($this->coinDenominations as $denomination) {
            $count = floatval($this->denominationCounts[$denomination->id] ?? 0);
            $total += $count * floatval($denomination->value);
        }
        
        $this->totalBeginningBalance = $total;
    }
    
    public function mount()
    {
        if(auth()->user()->employee->getModulePermission('Restaurant - Order Billing') == 1 )
        {
            $this->drawers = CashDrawer::where('branch_id', auth()->user()->branch_id)
                            ->where('drawer_status', 'ACTIVE')
                            ->get();
        $this->coinDenominations = Denomination::where('type', 'coin')
                                    ->orderBy('value', 'desc')
                                    ->get();
        $this->billDenominations = Denomination::where('type', 'bill')
                                    ->orderBy('value', 'desc')
                                    ->get();
        }else{
            return abort(403, 'Unauthorized action.');
              }
    }
    
    public function submitShift()
    {
        try {
           
        $this->calculateTotal();
        // Validate inputs
        $this->validate([
            'drawerId' => [
                'required',
                'exists:cash_drawers,id', // Ensure the drawer exists
                function ($attribute, $value, $fail) { // Custom validation to check if drawer is already in use
                    $openShift = CashierShift::where('drawer_id', $value)
                        ->where('shift_status', 'OPEN')
                        ->exists();
                    
                    if ($openShift) {
                        $fail('This drawer is already in use by an open shift.');
                    }
                }
            ],
            'totalBeginningBalance' => 'required|numeric|min:0',
        ]);

        // check if cashier already has an open shift
        $existingShift = CashierShift::where('cashier_id', auth()->user()->employee->id)
            ->where('shift_status', 'OPEN')
            ->first();
            if ($existingShift) {
                throw new \Exception('You already have an open shift. Please close it before opening a new one.');
            }

        $curYear = now()->year;
        $branchId = auth()->user()->branch_id;
        $yearlyCount = CashierShift::where('branch_id', $branchId)
            ->whereYear('created_at', $curYear)
            ->count() + 1;
        $reference = 'CS-' . auth()->user()->branch->branch_code . '-' . now()->format('my') . '-' . str_pad($yearlyCount, 2, '0', STR_PAD_LEFT);

        // Create new cashier shift
        $shift = CashierShift::create([
            'reference' => $reference,
            'cashier_id' => auth()->user()->employee->id,
            'branch_id' => auth()->user()->employee->branch_id,
            'drawer_id' => $this->drawerId,
            'shift_status' => 'OPEN',
            'shift_started' => now(),
            'starting_cash' => $this->totalBeginningBalance,
        ]);

         // check invoices table for any transactions made by the cashier before the shift was opened
            $pendingTransactions = Invoice::whereDate('created_at', now()->toDateString())->get();

        // if there are pending transactions, save the denomination counts
        if(!$pendingTransactions->isEmpty()) {
         // save denomination counts on bills only those with counts greater than 0
            foreach ($this->billDenominations as $denomination) {
                $count = $this->denominationCounts[$denomination->id] ?? 0;
                if ($count > 0) {
                    ShiftDenomination::create([
                        'denomination_id' => $denomination->id,
                        'quantity' => $count,
                        'amount' => $count * $denomination->value,
                        'counter_type' => 'STARTING_CASH',
                        'shift_id' => $shift->id,
                    ]);
                }
            }
            // save denomination counts on coins
            foreach ($this->coinDenominations as $denomination) {
                $count = $this->denominationCounts[$denomination->id] ?? 0;
                if ($count > 0) {
                    ShiftDenomination::create([
                        'denomination_id' => $denomination->id,
                        'quantity' => $count,
                        'amount' => $count * $denomination->value,
                        'counter_type' => 'STARTING_CASH',
                        'shift_id' => $shift->id,
                    ]);
                }
            }
        }

          // if there are no pending transactions, update menu to default qty 0
            // if ($pendingTransactions->isEmpty()) {
            //     $branchMenuIds = BranchMenu::where('branch_id', auth()->user()->branch_id)
            //         ->pluck('id');
            //     BranchMenuRecipe::whereIn('branch_menu_id', $branchMenuIds)
            //         ->update(['bal_qty' => \DB::raw('default_qty')]);
            // }
       

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Cashier shift opened successfully!']);
    }
    catch (\Exception $e) {
       
        $this->dispatch('alert', ['type' => 'error', 'message' => 'Error opening cashier shift: ' . $e->getMessage()]);
      }
    }
}
