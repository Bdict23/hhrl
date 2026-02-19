<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Withdrawal;
use App\Models\Cardex;
use App\Models\RecipeCardex;
use App\Models\ProductionOrder;
use Carbon\Carbon;
use App\Models\BranchMenuRecipe;
use App\Models\BranchMenu;

class ApproveWithdrawal extends Component
{

    public $withdrawals = [];
    public $withdrawal ;
    public $withdrawalDetails = [];

    //display data
    public $reference = '';
    public $department = '';
    public $approvedBy = '';
    public $preparedBy = '';
    public $preparedDate = '';
    public $useDate = '';
    public $validityDate = '';
    public $withdrawalRemarks = '';
    public $withdrawalId = '';
    public $overAllCost = 0;
    public $withdrawalInfo = [];

    //display block
    public $showWithdrawalSummary = true;
    public $showViewWithdrawal = false;


    public function mount()
    {
        $this->fetchData();
    }

    public function viewWithdrawalDetails($id)
    {
        if(auth()->user()->employee->getModulePermission('Approve Withdrawals') == 1)
        {
            // Fetch the withdrawal details

        $withdrawal = Withdrawal::with('department', 'approvedBy', 'reviewedBy', 'preparedBy', 'cardex','withdrawalType')
            ->where('id', $id)
            ->first(); // Retrieve the correct record or fail

            $this->reference = $withdrawal->reference_number;
            $this->department = $withdrawal->department->department_name;
            $this->approvedBy = $withdrawal->approvedBy->name;
            $this->preparedBy = $withdrawal->preparedBy->name;
            $this->preparedDate = $withdrawal->created_at->format('M. d, Y');
            $this->useDate = $withdrawal->usage_date ? \Carbon\Carbon::parse($withdrawal->usage_date)->format('M. d, Y') : null;
            $this->validityDate = $withdrawal->useful_date ? \Carbon\Carbon::parse($withdrawal->useful_date)->format('M. d, Y') : null;
            $this->withdrawalRemarks = $withdrawal->remarks;
            $this->withdrawalId = $withdrawal->id;
            $this->withdrawalInfo = $withdrawal;


        $this->withdrawalDetails = Cardex::with('item', 'priceLevel')
            ->where('withdrawal_id', $id)
            ->get();
            foreach ($this->withdrawalDetails as $withdrawalDetail) {
                $this->overAllCost += $withdrawalDetail->qty_out * $withdrawalDetail->priceLevel->amount;
            }
        $this->showWithdrawalSummary = false;
        $this->showViewWithdrawal = true;
        }
        else
        {
            return redirect()->to('/dashboard');
        }
    }

    public function fetchData()
    {
        // Fetch all withdrawals where reviewed_by is the current user's emp_id
        $this->showWithdrawalSummary = true;
        $this->showViewWithdrawal = false;
        $this->withdrawals = Withdrawal::with('department', 'approvedBy', 'reviewedBy', 'cardex.item')->where([['approved_by', auth()->user()->emp_id], ['withdrawal_status', 'FOR APPROVAL']])->get();
    }
    public function render()
    {
        return view('livewire.approve-withdrawal', [
            'withdrawals' => $this->withdrawals,
        ]);
    }
    public function backToSummary()
    {
        $this->showWithdrawalSummary = true;
        $this->showViewWithdrawal = false;
        $this->fetchData();
    }
    public function approveWithdrawal($id)
    {
        try{
        
        $withdrawal = Withdrawal::where('id', $id)->first();
        $productionOrder = $withdrawal->productionOrder ?? null;
        if ($withdrawal) {
            $withdrawal->withdrawal_status = 'APPROVED';
            $withdrawal->approved_date = now();
            $withdrawal->save();

            // Update the cardex status to 'FINAL'
            Cardex::where('withdrawal_id', $id)->update(['status' => 'FINAL']);
            
            // Refresh the data
            $this->fetchData();
        }
        if($productionOrder){
            // Get all branch menus for the current branch
            $branchMenus = BranchMenu::where('branch_id', auth()->user()->branch_id)->get();

            // update production order to completed and add recipes to inventory
            $productionOrder->status = 'COMPLETED';
            $productionOrder->save();
                foreach($productionOrder->productionMenus as $recipe){
                    // calculate the total quantity of the recipe to be added to inventory
                    $branchMenuRecipe = BranchMenuRecipe::whereIn('branch_menu_id', $branchMenus->pluck('id'))
                    ->where('menu_id', $recipe->menu_id)
                    ->get();
                    $cardex = RecipeCardex::where('menu_id', $recipe->menu_id)->where('branch_id', auth()->user()->branch_id)->get();
                    $availableBalance = $cardex->sum('qty_in') - $cardex->sum('qty_out');
                    if ($branchMenuRecipe) {
                        foreach ($branchMenuRecipe as $recipeItem) {
                            $recipeItem->bal_qty = $availableBalance + $recipe->qty;
                            $recipeItem->save();
                        }
                    } 

                    RecipeCardex::create([
                        'branch_id' => auth()->user()->branch_id,
                        'menu_id' => $recipe->menu_id,
                        'qty_in' => $recipe->qty,
                        'status' => 'FINAL',
                        'final_date' => Carbon::now('Asia/Manila'),
                        'created_at' => Carbon::now('Asia/Manila'),
                        'production_order_id' => $productionOrder->id,
                        'transaction_type' => 'PRODUCTION'
                    ]);

                    
                }
                // update production details status to completed
                    foreach($productionOrder->productionOrderDetails as $detail){
                        $detail->status = 'COMPLETED';
                        $detail->save();
                    }


        }
        $this->dispatch('showAlert', ['message' => 'Approved Successfully', 'type' => 'success']);
         $this->fetchData();
        } catch (\Exception $e) {
            $this->dispatch('showAlert', ['message' => 'An error occurred while approving the withdrawal.'. $e->getMessage(), 'type' => 'error']);
        }
    }

    public function rejectWithdrawal($id)
    {
        $withdrawal = Withdrawal::find($id);
        if ($withdrawal) {
            $withdrawal->withdrawal_status = 'REJECTED';
            $withdrawal->reviewed_date = now();
            $withdrawal->save();

            // Update the cardex status to 'CANCELLED'
            Cardex::where('withdrawal_id', $id)->update(['status' => 'CANCELLED']);

            // Refresh the data
            $this->fetchData();
            $this->showWithdrawalSummary = true;
            $this->showViewWithdrawal = false;
            return redirect()->route('withdrawal.approval');
        }
    }
}
