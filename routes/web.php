<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\supplier;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReceivingController;
use App\Http\Controllers\CardexController;
use App\Models\Branch;
use App\Models\Company;
use App\Models\requisitionInfos;
use App\Models\requisitionDetails;
use App\Models\employees;
use App\Models\items;
use App\Models\requisitionTypes;
use App\Http\Controllers\MenusController;
use App\Http\Livewire\PendingOrders;
use App\Http\Livewire\SearchOrderNumber;
use App\Http\Controllers\InvoicingController;
use App\Http\Controllers\InventoryAdjustmentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SettingsController;

Route::get('/', function () {
    $suppliers = supplier::all(); // Fetching all suppliers from the database
    return view('supplier_list', compact('suppliers')); // Passing data to the view
})->middleware(['auth', 'verified'])->name('suppliers');
Route::post('/create_supplier', [SupplierController::class, 'store'])->middleware(['auth', 'verified'])->name('suppliers.store');

Route::post('/update_supplier', [SupplierController::class, 'update'])->middleware(['auth', 'verified'])->name('suppliers.update');

Route::get('/supplier_list', [SupplierController::class, 'index'])->middleware(['auth', 'verified'])->name('suppliers');

Route::get('/supplier_deactivate/{id}', [BranchController::class, 'deactivate'])->name('supplier.deactivate');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//route for company
Route::post('/company_store', [CompanyController::class, 'store'])->middleware(['auth', 'verified'])->name('company.store');

Route::get('/company_list', [CompanyController::class, 'index'])->middleware(['auth', 'verified'])->name('companies');

Route::post('/company_update', [CompanyController::class, 'update'])->middleware(['auth', 'verified'])->name('company.update');

Route::get('/company_show/{id}', [CompanyController::class, 'show'])->middleware(['auth', 'verified'])->name('company.show');

Route::get('/company_deactivate/{id}', [BranchController::class, 'deactivate'])->middleware(['auth', 'verified'])->name('company.deactivate');


//route for branch
Route::get('/branch_list', [BranchController::class, 'index'])->middleware(['auth', 'verified'])->name('branch.index');

Route::post('/branch_create', [BranchController::class, 'store'])->middleware(['auth', 'verified'])->name('branch.store');

Route::post('/branch_update', [BranchController::class, 'update'])->middleware(['auth', 'verified'])->name('branch.update');

Route::get('/branch_show/{id}', [BranchController::class, 'show'])->middleware(['auth', 'verified'])->name('branch.show');

Route::get('/branch_deactivate/{id}', [BranchController::class, 'deactivate'])->middleware(['auth', 'verified'])->name('branch.deactivate');



// Static Home Page Route
Route::get('/branch/branch_create', function () {
    $companies = Company::all();
    return view('branch.branch_create', compact('companies'));
})->middleware(['auth', 'verified']);


//route for purchase order

Route::get('/purchase_order', [PurchaseOrderController::class, 'po_summary'])->middleware(['auth', 'verified'])->name('purchase_order.po_summary'); // Route to show the purchase order summary

Route::get('/printPO/{id}', [PurchaseOrderController::class, 'printPO'])->middleware(['auth', 'verified'])->name('po.print'); // Route to print the purchase order

Route::get('/po_printed/{id}', [PurchaseOrderController::class, 'po_printed'])->middleware(['auth', 'verified'])->name('po.printed'); // Route to mark the purchase order as printed

Route::get('/approval_request_list', [PurchaseOrderController::class, 'approval_request_list'])->middleware(['auth', 'verified'])->name('approval_request_list'); // Route to show the approval request list

Route::get('/review_request_list', [PurchaseOrderController::class, 'review_request_list'])->middleware(['auth', 'verified'])->name('review_request_list'); // Route to show the review request list

Route::post('/update-requisition-status/{id}', [PurchaseOrderController::class, 'poReviewed'])->name('update.requisition.status'); // Route to update the requisition status (reviewed, approved, rejected)

Route::post('/purchase_order/store', [PurchaseOrderController::class, 'storePO'])->middleware(['auth', 'verified'])->name('purchase_order.store'); // Route to store the purchase order

Route::get('/po_edit/{id}', [PurchaseOrderController::class, 'po_edit'])->middleware(['auth', 'verified'])->name('po.edit'); // Route to edit the purchase order

Route::put('/purchase_order/update/{id}', [PurchaseOrderController::class, 'po_update'])->middleware(['auth', 'verified'])->name('purchase_order.update');

Route::get('/show/{id}', [PurchaseOrderController::class, 'show'])->middleware(['auth', 'verified'])->name('po.show'); // Route to show the purchase order details (summary)

Route::get('/show_request_review/{id}', [PurchaseOrderController::class, 'show_review_request'])->middleware(['auth', 'verified'])->name('po.show_request_review'); // Route to show the purchase order details (review)

Route::get('/show_request_approval/{id}', [PurchaseOrderController::class, 'show_approval_request'])->middleware(['auth', 'verified'])->name('po.show_request_approval'); // Route to show the purchase order details (approval)

Route::get('/po_create', [PurchaseOrderController::class, 'newpo'])->middleware(['auth', 'verified'])->name('po.create'); // Route to create a new purchase order

Route::get('/get-po-details/{poNumber}', [ReceivingController::class, 'getPODetails'])->middleware(['auth', 'verified'])->name('po.po_receive'); // Route to get PO details
// Route for storing receiving data
Route::post('/receiving/store', [ReceivingController::class, 'po_store'])->middleware(['auth', 'verified'])->name('receiving.store');



Route::get('/welcome', function () {
    return view('welcome');
});

// Route for receiving stocks
Route::get('/receive_stock', [ReceivingController::class, 'getPODetails'])->middleware(['auth', 'verified'])->name('po.receive_stock');

Route::get('/get-cardex-data/{itemCode}', [CardexController::class, 'getCardexData'])->middleware(['auth', 'verified']);

require __DIR__.'/auth.php';




// Route for sales order
Route::get('/sales_order', function(){
    return view('sales.sales_order');
});

Route::get('/tnx', function(){
    return view('sales.thank_you_message');
});



Route::get('/reservations_lists', function(){
    return view('sales.reservations_lists');
});

Route::get('/order_menu', [MenusController::class, 'menu_list'])->middleware(['auth', 'verified'])->name('menus.list'); // Route to show the menu list

// Route for CREATE MENU
Route::get('/create_menu', [MenusController::class, 'createMenu'])->middleware(['auth', 'verified'])->name('menus.create'); // Route to create a new purchase order

// Route for storing receiving data
Route::post('/save_menu', [MenusController::class, 'store_menu'])->middleware(['auth', 'verified'])->name('menu.store');

// Route for storing order data
Route::post('/order_store', [MenusController::class, 'order_store'])->middleware(['auth', 'verified'])->name('order.store');

// Route to show the thank you message
Route::get('/thank_you_message/{order}', [MenusController::class, 'thankYouMessage'])->name('sales.thank_you_message');

//Route to view the lists of menus that is for approval
Route::get('/menu_approval_lists', [MenusController::class, 'menu_approvals'])->middleware(['auth', 'verified'])->name('menus.approvals');

//Route to view the details of the menu that is for approval
Route::get('/menu_approval_show/{menu}', [MenusController::class, 'menuApprovalShow'])->middleware(['auth', 'verified'])->name('menu_approval_show');

//Route to update the approval status of the menu
Route::post('/menu_approved/{id}', [MenusController::class, 'menuApproved'])->name('menu.approved');

//Route to the lists of menus that is for review
Route::get('/menu_review_lists', [MenusController::class, 'menu_review_lists'])->middleware(['auth', 'verified'])->name('menus.reviews');

//Route to the details of the menu that is for review
Route::get('/menu_review_show/{menu}', [MenusController::class, 'menuReviewShow'])->middleware(['auth', 'verified'])->name('menu_review_show');

//Route to update the review status of the menu
Route::post('/menu_reviewed/{id}', [MenusController::class, 'menuReviewed'])->name('menu.reviewed');

// Route for pending orders
Route::get('/orders_lists', [MenusController::class, 'orders_lists'])->middleware(['auth', 'verified'])->name('orders.list');

// Route for allocating orders
Route::get('/allocate_order',[MenusController:: class, 'allocate_order_lists'])->middleware(['auth', 'verified'])->name('allocate_orders');

// Route for allocating orders
Route::post('/allocate_order', [MenusController::class, 'allocate_order'])->middleware(['auth', 'verified'])->name('allocate-order');

// Livewire::component('search-order-number', SearchOrderNumber::class);

// Route for invoicing
Route::get('/invoicing', [InvoicingController::class, 'index'])->middleware(['auth', 'verified'])->name('invoicing');

// Route for storing payment data
Route::post('/store_payment', [InvoicingController::class, 'storePayment'])->middleware(['auth', 'verified'])->name('store.payment');

//route for daily sales report
Route::get('/daily_sales_report', [InvoicingController::class, 'daily_sales_report'])->middleware(['auth', 'verified'])->name('daily_sales_report');


Route::get('/item_withdrawal', [InventoryAdjustmentController::class, 'NewItemwithdrawal'])->middleware(['auth', 'verified'])->name('withdrawal.index'); // Route to show the raw materials requisition form


Route::get('/branch_department', [DepartmentController::class, 'index'])->middleware(['auth', 'verified'])->name('departments.index'); // Route to show the raw materials requisition form

// Route for departments
Route::post('/departments/store', [DepartmentController::class, 'store'])->middleware(['auth', 'verified'])->name('departments.store');


// Route for settings page
Route::get('/settings', [SettingsController::class, 'index'])->middleware(['auth', 'verified'])->name('settings.index');


// Routes for storing categories and classifications
Route::post('/settings/category/store', [SettingsController::class, 'storeCategory'])->name('settings.category.store');
Route::post('/settings/classification/store', [SettingsController::class, 'storeClassification'])->name('settings.classification.store');


// Route for storing a new withdarawal
Route::post('/item_withdrawal', [InventoryAdjustmentController::class, 'storeWithdrawal'])->middleware(['auth', 'verified'])->name('withdrawal.store');
// Route for withdrawal summary
Route::get('/withdrawal_summary', [InventoryAdjustmentController::class, 'withdrawalSummary'])->middleware(['auth', 'verified'])->name('withdrawal.summary');

// Route for withdrawal review
Route::get('/withdrawal_review', [InventoryAdjustmentController::class, 'withdrawalReview'])->middleware(['auth', 'verified'])->name('withdrawal.review');

// Route for withdrawal approval
Route::get('/withdrawal_approval', [InventoryAdjustmentController::class, 'withdrawalApproval'])->middleware(['auth', 'verified'])->name('withdrawal.approval');

// Route for withdrawal show
Route::get('/withdrawal/{id}', [InventoryAdjustmentController::class, 'showWithdrawal'])->name('withdrawal.show');

// Edit withdrawal
Route::get('/withdrawal/{id}/edit', [InventoryAdjustmentController::class, 'editWithdrawal'])->name('withdrawal.edit');

// view and update withdrawal
Route::get('/withdrawal/{id}/view', [InventoryAdjustmentController::class, 'viewAndUpdateWithdrawal'])->name('withdrawal.view');

// Route for updating withdrawal
Route::get('/withdrawal/{id}/print', [InventoryAdjustmentController::class, 'printWidthrawal'])->name('withdrawal.print');
