<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionInfo;
use App\Models\RequisitionDetail;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Item;
use App\Models\priceLevel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Signatory;
use App\Models\UnitConversion;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\Table;
use App\Models\Order;
use App\Models\Order_detail;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;
use Livewire\Livewire;
use App\Models\Module;
use App\Models\BranchMenu;
use App\Models\BranchMenuRecipe;
use App\Events\RemoteActionTriggered; 


class MenusController extends Controller
{
    public function createMenu(){
        $hasReviewer = auth()->user()->branch->getBranchSettingConfig('Allow Reviewer on Recipe') == 1 ? true : false;
        $suppliers = Supplier::where('supplier_status', 'ACTIVE')->get();
        $items = Item::with('priceLevel', 'units') // Added unitOfMeasures here
            ->where('item_status', 'ACTIVE')
            ->where('company_id', Auth::user()->branch->company_id)
            ->get();
        $categories = Category::where([['status', 'ACTIVE'], ['company_id', Auth::user()->branch->company_id], ['category_type', 'MENU']])->get();
        $module = Module::where('module_name', 'Recipe')->first();
        $approvers = Signatory::where([['signatory_type', 'APPROVER'], ['status', 'ACTIVE'], ['MODULE_ID', $module->id ], ['branch_id', Auth::user()->branch_id]])->get();
        $reviewers = Signatory::where([['signatory_type', 'REVIEWER'], ['status', 'ACTIVE'], ['MODULE_ID', $module->id], ['branch_id', Auth::user()->branch_id]])->get();
        return view('master_data.create_menu', compact('suppliers', 'items', 'approvers', 'reviewers','categories','hasReviewer'));
    }



    public function store_menu(Request $request){
        //  dd($request->all());
        try {
            $hasReviewer = auth()->user()->branch->getBranchSettingConfig('Allow Reviewer on Recipe') == 1 ? true : false;
            $validated = $request->validate([
                'menu_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
            ]);

            $imageName = time().'.'.$request->menu_image->extension();
            $request->menu_image->move(public_path('images'), $imageName);

            $menu = new Menu();
            $menu->menu_image = $imageName;
            $menu->menu_code = $request->menu_code;
            $menu->recipe_type = $request->menu_type;
            $menu->menu_name = $request->menu_name;
            $menu->status = 'FOR APPROVAL';
            $menu->menu_description = $request->menu_description;
            $menu->category_id = $request->category_id;
            $menu->approver_id = $request->approver_id;
            $menu->reviewer_id = $hasReviewer ? $request->reviewer_id : null;
            $menu->created_by = Auth::user()->emp_id;
            $menu->company_id = Auth::user()->branch->company_id;
            $menu->save();

            $qty = $request->input('qty', []);
            $priceID = $request->input('price_level_id', []);
            $items = $request->input('item_id', []);
            $uomId = $request->input('uom_id', []);

            foreach ($items as $index => $value) {
               $recipe = new Recipe();
                  $recipe->menu_id = $menu->id;
                  $recipe->item_id = $items[$index];
                  $recipe->qty = $qty[$index];
                  $recipe->price_level_id = $priceID[$index];
                  $recipe->uom_id = $uomId[$index];
                  $recipe->save();

            }

            return redirect()->to('/create_menu');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }


    public function menu_list(){
        // $tables
        $weekOfDay = strtolower(Carbon::now()->format('D'));
        $branchMenuAvailable = BranchMenu::where('branch_id', Auth::user()->branch->id)
            ->where('is_available', '1')->where($weekOfDay, '1')->where('start_date', '<=', Carbon::now())
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', Carbon::now());
            })
            ->pluck('id')
            ->toArray();
        $branchRecipeAvailable = BranchMenuRecipe::whereIn('branch_menu_id', $branchMenuAvailable)
            ->pluck('menu_id')
            ->toArray();
        $menus = Menu::with('categories', 'price_levels', 'recipes')
            ->where('company_id', Auth::user()->branch->company_id)
            ->where('status', 'AVAILABLE')
            ->where('recipe_type', 'Ala Carte')
            ->whereIn('id', $branchRecipeAvailable)
            ->get();
            // DD($menus, Auth::user()->branch->company_id);
        return view('sales.order_menu', compact('menus'));
    }


    public function order_store(Request $request){
        try {
           
            $orderNumber = Order::whereDate('created_at', Carbon::today())
                ->where('branch_id', Auth::user()->branch->id)
                ->max('order_number') ?? 0;

            $order = new Order();
            $order->order_number = $orderNumber + 1;
            $order->created_at = Carbon::now('Asia/Manila');
            $order->updated_at = Carbon::now('Asia/Manila');
            $order->sales_rep_id = Auth::user()->emp_id;
            $order->branch_id = Auth::user()->branch->id;
            $order->order_status = 'PENDING';
            $order->table_id = $request->tableID;
            $order->save();
            Table::where('id', $request->tableID)
                ->update(['availability' => 'OCCUPIED']);

            $menu_id = $request->input('menu_id', []);
            $qty = $request->input('order_qty', []);
            
            foreach ($menu_id as $index => $value) {
                $order_details = new Order_detail();
                $order_details->order_id = $order->id;
                $order_details->menu_id = $menu_id[$index];
                $order_details->qty = $qty[$index];
                $order_details->save();
            }
            
             $payload = [
                'action' => 'newOrder',
                'branch_id' => Auth::user()->branch->id,
            ];
            \Log::info('About to trigger RemoteActionTriggered event', ['payload' => $payload]);
            event(new RemoteActionTriggered($payload, Auth::user()->branch->id));
            \Log::info('RemoteActionTriggered event dispatched');

           return view('sales.thank_you_message', compact('order'));
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    // review lists
    public function menu_review_lists(){
        $menus = Menu::with('categories', 'price_levels', 'approver', 'reviewer', 'preparer')->where([['company_id', Auth::user()->branch->company_id], ['status', 'FOR REVIEW'], ['reviewer_id', Auth::user()->emp_id]])->get();

        return view('master_data.menu_review_lists', compact('menus'));
    }

    public function menu_approvals(){
        $menus = Menu::with('categories', 'price_levels', 'approver', 'reviewer', 'preparer')->where([['company_id', Auth::user()->branch->company_id], ['status', 'FOR APPROVAL'], ['approver_id', Auth::user()->emp_id]])->get();

        return view('master_data.menu_approval_lists', compact('menus'));
    }

    // approval show
    public function menuApprovalShow($menuId) {
        $menus = Menu::with('categories', 'price_levels', 'approver', 'reviewer', 'preparer', 'recipes')->findOrFail($menuId);
        // dd($menus->recipes);
        return view('master_data.menu_approval_show', compact('menus'));
    }

    // update approval status
    public function menuApproved(Request $request, $id) {
        try {

            $menu = Menu::findOrFail($id);
            if ($request->status === 'APPROVED') {
                $menu->status = 'AVAILABLE';
            } elseif ($request->status === 'REJECTED') {
                $menu->status = 'REJECTED';
            }
            $menu->save();

            return redirect()->route('menus.approvals')->with('status', 'Menu status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    //review show
    public function menuReviewShow($menuId) {
        $menus = Menu::with('categories', 'price_levels', 'approver', 'reviewer', 'preparer', 'recipes')->findOrFail($menuId);
        $hasReviewer = auth()->user()->branch->getBranchSettingConfig('Allow Reviewer on Recipe') == 1 ? true : false;
        // dd($menus->recipes);
        return view('master_data.menu_review_show', compact('menus','hasReviewer'));
    }

    // update review status
    public function menuReviewed(Request $request, $id) {
        try {
            $menu = Menu::findOrFail($id);
            if ($request->status === 'REVIEWED') {
                $menu->status = 'FOR APPROVAL';
            } elseif ($request->status === 'REWORK') {
                $menu->status = 'PENDING';
            }
            $menu->save();

            return redirect()->route('menus.reviews')->with('status', 'Menu status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    // order lists
    public function orders_lists(){
        $orders = Order::with('order_details', 'tables')->where([['branch_id', Auth::user()->branch->id], ['order_status', 'PENDING']])->where('created_at', '>=', now())->get();
         //dd($orders);
        return view('sales.order_lists');
    }

    // allocate orders lists
    public function allocate_order_lists(){
        $orders = Order::with('order_details', 'tables')
            ->where('branch_id', Auth::user()->branch->id)
            ->where('order_status', '!=', 'PENDING')
            ->whereDate('created_at', '>=', now()->toDateString())
            ->get();
        $tables = Table::where([['branch_id', Auth::user()->branch->id], ['status', 'ACTIVE'], ['availability', 'VACANT']])->get();
        $totalPrice = 0;

        return view('sales.allocate_orders', compact('orders', 'tables'));
    }


    // allocate orders
    public function allocate_order(Request $request)
    {
        $order = Order::find($request->orderId);
        if ($order) {
            $order->update([
                'order_status' => 'PENDING',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now('Asia/Manila'),
                'table_id' => $request->tableid,
                'customer_name' => $request->customerName
            ]);
            return redirect()->route('allocate_orders')->with('status', 'Order allocated successfully.');
        } else {
            return redirect()->back()->withErrors(['Order not found.']);
        }
    }

}
