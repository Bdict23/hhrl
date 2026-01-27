<div>
    <div>
            @if (session()->has('success'))
        <div class="alert alert-success" id="success-message">
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
            @endif
            @if (session()->has('error'))
        <div class="alert alert-danger" id="success-message">
            {{ session('error') }}
            <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
            @endif
        <form id="poForm" method="POST" wire:submit.prevent="savePayment">
            @csrf
            <div class="row me-3 w-100">
                {{-- LEFT --}}
                <div class=" col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <header>
                                <div class="me-3">
                                    <x-secondary-button onclick="history.back()" type="button"><i class="bi bi-arrow-90deg-left"></i>&nbsp; Back</x-secondary-button>
                                    <x-secondary-button type="button" wire:click="refreshOrders"><i class="bi bi-arrow-clockwise"></i> Refresh</x-secondary-button>
                                    <x-primary-button type="button" data-bs-toggle="modal" data-bs-target="#AddOrderModal">
                                        <i class="bi bi-cart-check-fill"></i>
                                        Order Number</x-primary-button>
                                    &nbsp;
                                </div>
                                
                                 <h4>SALES ORDER</h4>
                            </header>
                        </div>
                        <div class=" card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="search" name="search"
                                        placeholder="Search">
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="orderNo">Current Order :</label>
                                        </div>
                                        <div class="col-md-6">

                                            <select id="orderNo" class="form-select" wire:model="selectedOrderId" wire:change="selectedOrder($event.target.value)">
                                                <option value="">-- Select --</option>
                                                @forelse ($orders as $order)
                                                    <option value="{{ $order->id }}" wire>{{ $order->order_number }} ({{ $order->tables->table_name ?? 'TAKE OUT' }})</option>
                                                @empty
                                                    <option value="">No Orders</option>
                                                @endforelse

                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="me-3" style="height: 320px; overflow-y: auto;">
                                <table class="table table-striped table-hover me-3">
                                    <thead class="thead-dark me-3">
                                        <tr style="font-size: smaller;">
                                            <th>Item</th>
                                            <th>QTY</th>
                                            <th>PRICE</th>
                                            <th>Less</th>
                                            <th>SUB TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemTableBody">

                                        @forelse ($selectedOrderDetails ?? [] as $items)
                                            <tr style="font-size: smaller;">
                                                <td>{{ $items->menu->menu_name }}</td>
                                                <td style="text-align: left;">{{ $items->qty }}</td>
                                                <td style="style=text-align: left;">
                                                   ₱ {{ number_format($items->priceLevel->amount ?? 0, 2) }}
                                                </td>
                                                <td style="text-align: left;"><button type="button" onclick="uncheckAllCheckboxes()" 
                                                    data-bs-toggle="modal" data-bs-target="#AddDiscountModal"
                                                     wire:click="selectedItem({{ $items->id }})" class="btn btn-link" style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">
                                                   
                                                        ₱ {{ number_format($items->orderDiscounts->sum(function($orderDiscount) use ($items) {
                                                            return $orderDiscount->discount->amount > 0.00 
                                                                ? $orderDiscount->discount->amount * $items->qty
                                                                : ($orderDiscount->discount->percentage / 100) * ($items->priceLevel->amount ?? 0) * $items->qty;
                                                        }), 2) }}
                                                        <i class="bi bi-pencil-square" style="font-size: 0.8em;"></i>
                                                    
                                                </button></td>
                                                <td class="total-price" style="text-align: left;">
                                                    @php
                                                        $itemTotal = $items->qty * ($items->priceLevel->amount ?? 0);
                                                        $discountTotal = $items->orderDiscounts->sum(function($orderDiscount) use ($items) {
                                                            return $orderDiscount->discount->amount > 0.00 
                                                                ? $orderDiscount->discount->amount * $items->qty
                                                                : ($orderDiscount->discount->percentage / 100) * ($items->priceLevel->amount ?? 0) * $items->qty;
                                                        });
                                                        $finalTotal = $itemTotal - $discountTotal;
                                                    @endphp
                                                    ₱ {{ number_format($finalTotal, 2) }}
                                                </td>
                                            </tr>
                                            
                                        @empty
                                            <tr>
                                                <td colspan="5" style="text-align: center;">No orders selected.</td>
                                            </tr>
                                            
                                        @endforelse
                                       
                                    </tbody>
                                </table>
                            </div>


                            <div class="card-footer row mt-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control form-control-sm" id="order_id"
                                        name="order_id" hidden>
                                        <h4>TOTAL PAYABLE</h4>
                                </div>
                                <div class="col-md-6 alert alert-secondary text-end" role="alert">
                                    <h4 class="alert-heading " id="sumTotal" value="{{ $totalAmountDue }}">₱ <b style="color: green">{{ number_format($totalAmountDue, 2) }}</b></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT  --}}
                <div class="col-md-5">
                    <div class="card me-3">
                        <div class="card-body">
                            <div class="alert alert-primary" role="alert"
                                style="background-color: #f2f4f7; height: 100%;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="alert-heading" style="font-size: smaller;">Invoice Number</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-end">
                                            <input class="form-control form-control-sm text-center" id="invoiceNumber"
                                                name="invoiceNumber" style="font-size: smaller;"
                                                placeholder="Enter Invoice Number" wire:model="invoiceNumber" @if($selectedOrderId == null) disabled @endif>
                                                @error('invoiceNumber')
                                                    <div class="text-danger" style="font-size: smaller;">{{ $message }}</div>
                                                @enderror
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-12">
                                    <label for="grandTotal" class="form-label" style="font-size: smaller;">Customer</label>
                                    <input type="text" class="form-control form-control-sm text-center"
                                        id="customer" name="customer"  placeholder="(Optional)" wire:model="customerName" @if($selectedOrderId == null) disabled @endif>
                                        @error('customerName')
                                            <div class="text-danger" style="font-size: smaller;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                     <div class="col-md-6">
                                        <label for="discount" class="form-label"
                                            style="font-size: smaller;">Initial Amount Due</label>
                                       <div class="input-group">
                                         <input class="form-control form-control-sm" id="discount" name="discount"
                                             min="0"  readonly value="₱ {{ number_format($grossAmount, 2) }}" disabled
                                             style="text-align: center;">
                                       </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="discount" class="form-label"
                                            style="font-size: smaller;">Total Discount Applied</label>
                                       <div class="input-group">
                                         <input class="form-control form-control-sm" id="discount" name="discount"
                                             min="0"  readonly value="₱ {{ number_format($totalDiscountAmount, 2) }}" disabled
                                             style="text-align: center;">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#ViewDiscountsModal">
                                           <i class="bi bi-eye-fill"></i>
                                        </button>
                                       </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="orderDiscount" class="form-label mt-3"
                                            style="font-size: smaller;">Order Discount</label>
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100"  @if($selectedOrderId == null) disabled @endif
                                            data-bs-toggle="modal" data-bs-target="#AddOrderDiscountModal">
                                           <i class="bi bi-plus-circle-dotted"></i> Add Order Discount
                                        </button>
                                    </div>
                                </div>
                                <script>
                                    function updateGrandTotal() {
                                        const discount = parseFloat(document.getElementById('discount').value) || 0;
                                        let grandTotal = 0;
                                        document.querySelectorAll('.total-price').forEach(cell => {
                                            grandTotal += parseFloat(cell.textContent);
                                        });
                                        grandTotal -= discount;
                                        document.getElementById('grandTotal').value = grandTotal.toFixed(2);
                                    }
                                </script>

                            </div>


                            <div class="alert" style="background-color: #f2f4f7;" role="alert">
                                <h5 class="card-title">Payment Details</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="paymentMethod" class="form-label">Payment Method</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select name="paymentMethod" id="paymentMethod" class="form-control text-center"
                                            required onchange="handlePaymentMethodChange()" wire:model="selectedPaymentType" @if($selectedOrderId == null) disabled @endif>
                                            <option value="NONE">-- SELECT --</option>
                                           @foreach ($paymentTypes as $type)
                                               <option value="{{ $type->id }}" @if($selectedPaymentType == $type->id) selected @endif>{{ $type->payment_type_name }}</option>
                                           @endforeach
                                           
                                            <option value="SPLIT" @if($selectedPaymentType == 'SPLIT') selected @endif>SPLIT</option>
                                        </select>
                                            @error('selectedPaymentType')
                                                <div class="text-danger text-end" style="font-size: smaller;">{{ $message }}</div>
                                            @enderror
                                    </div>
                                </div>

                                <div class="row mt-2" id="viewSplitButton" style="display: none;" wire:ignore.self>
                                    <div class="col-md-12 text-right">
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#SplitPaymentModal" @if($selectedOrderId == null) disabled @endif>
                                         <i class="bi bi-eye"></i>   View Split Payments
                                        </button>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-5">
                                        <label for="time" class="form-label">Amount Received</label>
                                    </div>
                                    <div class="col-md-7" wire:ignore>
                                        <input type="number" class="form-control" id="amountReceived" min="0"  id="amountReceived"
                                            name="amountReceived" required wire:model="amountReceived" onchange="updateChange()" onkeyup="updateChange()" step="0.10">
                                    </div>
                                     @error('amountReceived')
                                                <div class="text-danger text-end" style="font-size: smaller;">{{ $message }}</div>
                                            @enderror
                                </div>

                                <div class="row mt-2">

                                    <div class="col-md-3">
                                        <label class="form-label">Change</label>
                                    </div>

                    <div class="col-md-9 text-center">
                        <input type="text" class="form-control text-center" id="change"
                            name="change" readonly disabled wire:ignore>
                                    <div class= "row">
                                        <div class="col-md-8 mt-3">
                                            <button type="submit" @if($selectedOrderId == null) disabled @endif class="btn btn-success"><i class="bi bi-cash-stack"></i>&nbsp;Save Payment</button>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
        {{-- view all discounts applied summary modal --}}
        <div class="modal fade" id="ViewDiscountsModal" tabindex="-1" aria-labelledby="ViewDiscountsModalLabel" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ViewDiscountsModalLabel">Applied Discounts Summary</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body card">
                        <div class="card-header">
                            <h5>Discount Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">

                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Title</th>
                                            <th>Deducted</th>
                                            <th>Deducted to</th>
                                            <th>Action</th>                                       
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @forelse ($appliedDiscounts ?? [] as $appliedDiscount)
                                           <tr>
                                                <td>{{ $appliedDiscount->discount->title }}</td>
                                                <td>{{ $appliedDiscount->discount->description}}</td>
                                                <td>
                                                    @if($appliedDiscount->discount->amount > 0.00)
                                                        ₱ {{ number_format($appliedDiscount->discount->amount) }}
                                                    @else
                                                        {{ $appliedDiscount->discount->percentage  }} %
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($appliedDiscount->discount->type == 'SINGLE')
                                                        <span class="badge text-bg-primary">Per Item</span>
                                                    @else
                                                        <span class="badge text-bg-success">Per Order</span>
                                                        @if($appliedDiscount->discount->auto_apply == 1)
                                                            <span class="badge text-bg-info">Auto</span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" wire:click="removeAppliedDiscount({{ $appliedDiscount->id }})" class="btn btn-sm btn-danger" @if($appliedDiscount->discount->auto_apply == 1) disabled @endif><i class="bi bi-trash"></i></button>
                                                </td>
                                           </tr>
                                       @empty
                                           <tr>
                                                <td colspan="4" style="text-align: center;">No discounts applied.</td>
                                           </tr>
                                       @endforelse
                                    </tbody>
                                </table>
                              
                            </div>
                        <datagrid></datagrid>
                        <div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- Add Order Modal -->
    <div class="modal fade" id="AddOrderModal" tabindex="-1" aria-labelledby="AddOrderModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AddOrderModalLabel">Add Order Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body card">
                    <div class="card-header">
                        <h5>Customer List</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">

                            <input type="text" class="form-control" id="newCustomerName" placeholder="Search">
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Order Number</th>
                                    <th>Table</th>
                                    <th>Customer</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td style="text-align: center;">{{ $order->order_number }}</td>
                                        <td style="text-align: center;">{{ $order->tables->table_name ?? 'TAKE OUT' }}</td>
                                        <td style="text-align: center;">{{ $order->customer_name ?? 'N/A' }}</td>
                                        @php
                                            $latestSrpPrice = [];
                                            foreach ($order->order_details as $detail) {
                                                $latestSrpPrice[] =
                                                    $detail->menu
                                                        ->price_levels()
                                                        ->latest()
                                                        ->where('price_type', 'RATE')
                                                        ->first()->amount ?? '0.00';
                                            }
                                        @endphp
                                        <td style="text-align: center;"><x-primary-button type="button"
                                               wire:click="selectedOrder({{ $order->id }})">Select</x-primary-button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- add discount modal FOR items--}}
    <div class="modal fade" id="AddDiscountModal" tabindex="-1" aria-labelledby="AddDiscountModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AddDiscountModalLabel">Add Discount</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <div class="modal-body card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h4 role="status">Discount Details</h4>
                                <div class="spinner-border spinner-border-sm ms-auto" aria-hidden="true" wire:loading></div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">

                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Discount Title</th>
                                            <th>Description</th>
                                            <th>Rate/Value</th>
                                            <th>Action</th>                                        
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($discounts ?? [] as $discount)
                                        <tr wire:loading.remove>
                                            <td>{{ $discount->title }}</td>
                                            <td>{{ $discount->description }}</td>
                                            <td>
                                                @if($discount->amount == '0.00')
                                                    {{ $discount->percentage }}%
                                                @else
                                                    ₱ {{ number_format($discount->amount, 2) }}
                                                @endif
                                            </td>
                                            <td>
                                                <input type="checkbox" class="form-check-input discount-checkbox" name="discount[]" value="{{ $discount->id }}" data-discount-id="{{ $discount->id }}" wire:click="selectedDiscounts({{ $discount->id }}, $event.target.checked)">
                                            </td>
                                        </tr>
                                        @empty
                                        <tr wire:loading.remove>
                                            <td colspan="4" class="text-center">No discounts available.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <div class="modal-footer">
                                    <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Done</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Split Payment Modal --}}
    <div class="modal fade" id="SplitPaymentModal" tabindex="-1" aria-labelledby="SplitPaymentModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="SplitPaymentModalLabel">Split Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <label for="splitPaymentType" class="form-label mb-0">Select Payment Type</label>
                                    <select id="splitPaymentType" class="form-select">
                                        <option value=""><i class="bi bi-wallet2"></i> Select</option>
                                        @foreach ($paymentTypes ?? [] as $type)
                                            <option value="{{ $type->id }}" data-name="{{ $type->payment_type_name }}">{{ $type->payment_type_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mt-4">
                                    <button type="button" class="btn btn-success btn-sm" onclick="addSplitPaymentRow()">
                                        <i class="bi bi-plus-circle"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info" role="alert">
                                <strong>Total Amount:</strong><span>₱ </span> <span id="splitTotalAmount">{{ number_format($totalAmountDue, 2) }}</span>
                            </div>
                            <table class="table table-bordered table-hover" id="splitPaymentTable">
                                <thead>
                                    <tr>
                                        <th>Payment Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="splitPaymentTableBody">
                                    @forelse ($splitPayments as $payment)
                                        <tr>
                                            <td>{{ $payment['paymentTypeName'] }}</td>
                                            <td>
                                                <input type="number" class="form-control" 
                                                       id="splitAmount_{{ $payment['id'] }}" 
                                                       value="{{ number_format($payment['amount'], 2) }}" 
                                                       min="0" 
                                                       step="0.01"
                                                       onchange="updateSplitAmount({{ $payment['id'] }}, this.value)">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeSplitPayment({{ $payment['id'] }})">
                                                    <i class="bi bi-trash"></i> Remove
                                                </button>
                                            </td>
                                        </tr>
                                        
                                    @empty
                                        <tr id="noSplitPayments">
                                            <td colspan="3" class="text-center">No split payments added yet.</td>
                                        </tr>
                                    @endforelse 
                                    
                                </tbody>
                            </table>
                            <div class="mt-2">
                                <strong>Total Split Amount:</strong> <span id="totalSplitAmount" wire:ignore>₱ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <x-danger-button data-bs-dismiss="modal">Close</x-danger-button>
                    <x-primary-button type="button" onclick="applySplitPayments()">Apply Split</x-primary-button>
                </div>
            </div>
        </div>
    </div>

    {{-- add order discount modal --}}
    <div class="modal fade" id="AddOrderDiscountModal" tabindex="-1" aria-labelledby="AddOrderDiscountModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AddOrderDiscountModalLabel">Add Order Discount</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <div class="modal-body card">
                        <div class="card-header">
                            <h5>Discount Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">

                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Discount Title</th>
                                            <th>Description</th>
                                            <th>Rate/Value</th>
                                            <th>Action</th>                                        
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($perOrderDiscounts ?? [] as $discount)
                                        <tr>
                                            <td>{{ $discount->title }}</td>
                                            <td>{{ $discount->description }}</td>
                                            <td>
                                                @if($discount->amount == '0.00')
                                                    {{ $discount->percentage }}%
                                                @else
                                                    ₱ {{ number_format($discount->amount, 2) }}
                                                @endif
                                            </td>
                                            <td>
                                                <input id="order-discount[{{ $discount->id }}]" type="checkbox" class="form-check-input order-discount-checkbox" name="order_discount[]" value="{{ $discount->id }}" data-discount-id="{{ $discount->id }}" wire:click="selectedOrderDiscounts({{ $discount->id }}, $event.target.checked)">
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No discounts available.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                              
                            </div>
                        <datagrid></datagrid>
                        <div>
                        <div class="modal-footer">
                            <x-primary-button data-bs-dismiss="modal">Done</x-primary-button>
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <script>

        function uncheckAllCheckboxes() {
            updateChange();
              // Uncheck all discount checkboxes
            document.querySelectorAll('.discount-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            // Uncheck all order discount checkboxes
            document.querySelectorAll('.order-discount-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            // Uncheck all order discount checkboxes
            document.querySelectorAll('.order-discount-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
       
        function updateChange() {
            const amountReceived = parseFloat(document.getElementById('amountReceived').value) || 0;
            const totalAmountDue = parseFloat(document.getElementById('sumTotal').getAttribute('value')) || 0;
            const change = amountReceived - totalAmountDue;
            if (change < 0) {
                document.getElementById('change').style.color = 'red';
                document.getElementById('change').value = `₱ ${change.toFixed(2)}`;
                return;
            }
            document.getElementById('change').style.color = 'black';
            document.getElementById('change').value = `₱ ${change.toFixed(2)}`;

        }

         // Split Payment Logic
        let splitPayments = [];
        let splitPaymentCounter = 0;

        // Initialize split payments from Livewire on page load
        document.addEventListener('DOMContentLoaded', function() {

            updateChange();
          
            document.querySelectorAll('.discount-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });

            
            loadSplitPaymentsFromLivewire();
            let amountReceivedInput = document.getElementById('amountReceived');
            let paymentMethodSelect = document.getElementById('paymentMethod');
            if (paymentMethodSelect.value === 'SPLIT' || paymentMethodSelect.value === 'NONE') {
                 if (amountReceivedInput) {
                    amountReceivedInput.disabled = true;
                    amountReceivedInput.value = '';
                }}
        });

        // Livewire hook to restore split payments after component updates
        document.addEventListener('livewire:init', () => {
            updateChange();
            console.log('Livewire initialized - setting up commit hook for split payments');
            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                succeed(({ snapshot, effect }) => {
                    loadSplitPaymentsFromLivewire();
                });
            });
        });

        // Load split payments from Livewire property
        function loadSplitPaymentsFromLivewire() {
            const livewireSplitPayments = @json($splitPayments ?? []);
            if (livewireSplitPayments && livewireSplitPayments.length > 0) {
                splitPayments = livewireSplitPayments;
                // Update counter to avoid ID conflicts
                splitPaymentCounter = Math.max(...splitPayments.map(p => parseInt(p.id) || 0), 0);
                updateSplitPaymentTable();
            }
            
            // Update total amount in split modal
            const totalAmountDue = @json($totalAmountDue ?? 0);
            const splitTotalElement = document.getElementById('splitTotalAmount');
            if (splitTotalElement) {
                splitTotalElement.textContent = parseFloat(totalAmountDue).toFixed(2);
            }
            
            // Restore payment method selection and button visibility
            const paymentMethod = document.getElementById('paymentMethod');
            const viewSplitButton = document.getElementById('viewSplitButton');
            if (paymentMethod && paymentMethod.value === 'SPLIT' && viewSplitButton) {
                viewSplitButton.style.display = 'block';
            }
        }

        // Handle payment method change
        function handlePaymentMethodChange() {
            const paymentMethod = document.getElementById('paymentMethod').value;
            const viewSplitButton = document.getElementById('viewSplitButton');
            const amountReceivedInput = document.getElementById('amountReceived');

            
            if (paymentMethod === 'SPLIT') {
                viewSplitButton.style.display = 'block';
                // Disable amount received field for split payment
                if (amountReceivedInput) {
                    amountReceivedInput.disabled = true;
                    amountReceivedInput.value = '';
                }
                // Open modal automatically
                const modal = new bootstrap.Modal(document.getElementById('SplitPaymentModal'));
                modal.show();
            } else if (paymentMethod === 'NONE') {
                 if (amountReceivedInput) {
                    amountReceivedInput.disabled = true;
                    amountReceivedInput.value = '';
                }
                viewSplitButton.style.display = 'none';
                // Enable amount received field for other payment methods
                splitPayments = [];
                updateSplitPaymentTable();
                
                // Clear split payments in Livewire component
                @this.set('splitPayments', []);
            } else {
                viewSplitButton.style.display = 'none';
                // Enable amount received field for other payment methods
                if (amountReceivedInput) {
                    amountReceivedInput.disabled = false;
                    amountReceivedInput.value = '';
                }
                splitPayments = [];
                updateSplitPaymentTable();
                
                // Clear split payments in Livewire component
                @this.set('splitPayments', []);
            }
        }

        // Add split payment row
        function addSplitPaymentRow() {
            const selectElement = document.getElementById('splitPaymentType');
            const paymentTypeId = selectElement.value;
            const paymentTypeName = selectElement.options[selectElement.selectedIndex].getAttribute('data-name');
            
            if (!paymentTypeId) {
                Swal.fire({
                    title: 'Error',
                    text: 'Please select a payment type first',
                    icon: 'error'
                });
                return;
            }

            // Add to splitPayments array
            const id = ++splitPaymentCounter;
            splitPayments.push({
                id: id,
                paymentTypeId: paymentTypeId,
                paymentTypeName: paymentTypeName,
                amount: 0
            });

            // Update table and recalculate
            updateSplitPaymentTable();
            redistributeAmounts();
            
            // Sync to Livewire component
            @this.set('splitPayments', splitPayments);
            
            // Reset select
            selectElement.value = '';
        }

        // Remove split payment
        function removeSplitPayment(id) {
            splitPayments = splitPayments.filter(payment => payment.id !== id);
            updateSplitPaymentTable();
            redistributeAmounts();
            
            // Sync to Livewire component
            @this.set('splitPayments', splitPayments);
        }

        // Update split payment table
        function updateSplitPaymentTable() {
            const tbody = document.getElementById('splitPaymentTableBody');
            tbody.innerHTML = '';

            if (splitPayments.length === 0) {
                tbody.innerHTML = '<tr id="noSplitPayments"><td colspan="3" class="text-center">No split payments added yet</td></tr>';
                document.getElementById('totalSplitAmount').textContent = '₱ 0.00';
                return;
            }

            splitPayments.forEach(payment => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${payment.paymentTypeName}</td>
                    <td>
                        <input type="number" class="form-control" 
                               id="splitAmount_${payment.id}" 
                               value="${payment.amount.toFixed(2)}" 
                               min="0" 
                               step="0.01"
                               onchange="updateSplitAmount(${payment.id}, this.value)">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeSplitPayment(${payment.id})">
                            Remove
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            updateTotalSplitAmount();
        }

        // Redistribute amounts equally
        function redistributeAmounts() {
            if (splitPayments.length === 0) return;

            // Get the total amount from the split modal (which has the correct $totalAmountDue value)
            const splitTotalElement = document.getElementById('splitTotalAmount');
            let grandTotal = 0;
            
            if (splitTotalElement && splitTotalElement.textContent) {
                // Remove currency symbol, commas, and spaces, then parse
                const cleanValue = splitTotalElement.textContent.replace(/[₱,\s]/g, '');
                grandTotal = parseFloat(cleanValue) || 0;
            }

            // Divide equally among all splits
            const amountPerSplit = grandTotal / splitPayments.length;
            
            splitPayments.forEach(payment => {
                payment.amount = amountPerSplit;
            });

            updateSplitPaymentTable();
        }

        // Update specific split amount
        function updateSplitAmount(id, newValue) {
            const newAmount = parseFloat(newValue) || 0;
            
            // Get the total amount from the split modal
            const splitTotalElement = document.getElementById('splitTotalAmount');
            let grandTotal = 0;
            
            if (splitTotalElement && splitTotalElement.textContent) {
                const cleanValue = splitTotalElement.textContent.replace(/[₱,\s]/g, '');
                grandTotal = parseFloat(cleanValue) || 0;
            }

            // Find the payment being updated
            const paymentIndex = splitPayments.findIndex(p => p.id === id);
            if (paymentIndex === -1) return;

            // Calculate total of other payments
            let otherTotal = 0;
            splitPayments.forEach((payment, index) => {
                if (index !== paymentIndex) {
                    otherTotal += payment.amount;
                }
            });

            // Check if new amount would exceed total
            if (otherTotal + newAmount > grandTotal) {
                Swal.fire({
                    title: 'Amount Exceeds Total',
                    text: 'The split amounts cannot exceed the total amount',
                    icon: 'error'
                });
                // Reset to previous value
                document.getElementById(`splitAmount_${id}`).value = splitPayments[paymentIndex].amount.toFixed(2);
                return;
            }

            // Update the amount
            splitPayments[paymentIndex].amount = newAmount;

            // Redistribute remaining amount among other payments
            const remaining = grandTotal - newAmount;
            const otherPaymentsCount = splitPayments.length - 1;
            
            if (otherPaymentsCount > 0) {
                const amountPerOther = remaining / otherPaymentsCount;
                splitPayments.forEach((payment, index) => {
                    if (index !== paymentIndex) {
                        payment.amount = amountPerOther;
                    }
                });
            }

            updateSplitPaymentTable();
            
            // Sync to Livewire component
            @this.set('splitPayments', splitPayments);
        }

        // Update total split amount display
        function updateTotalSplitAmount() {
            const total = splitPayments.reduce((sum, payment) => sum + payment.amount, 0);
            document.getElementById('totalSplitAmount').textContent = '₱ ' + total.toFixed(2);
        }

        // Apply split payments
        function applySplitPayments() {
            
            // Get the total amount from the split modal
            const splitTotalElement = document.getElementById('splitTotalAmount');
            let grandTotal = 0;
            
            if (splitTotalElement && splitTotalElement.textContent) {
                const cleanValue = splitTotalElement.textContent.replace(/[₱,\s]/g, '');
                grandTotal = parseFloat(cleanValue) || 0;
            }
            
            const totalSplit = splitPayments.reduce((sum, payment) => sum + payment.amount, 0);

            if (splitPayments.length === 0) {
                Swal.fire({
                    title: 'No Splits Added',
                    text: 'Please add at least one split payment',
                    icon: 'warning'
                });
                return;
            }

            if (Math.abs(totalSplit - grandTotal) > 0.01) {
                Swal.fire({
                    title: 'Amount Mismatch',
                    text: `Total split amount (₱${totalSplit.toFixed(2)}) must equal the grand total (₱${grandTotal.toFixed(2)})`,
                    icon: 'error'
                });
                return;
            }

            // Update amount received field with total split amount
            const amountReceivedInput = document.getElementById('amountReceived');
            if (amountReceivedInput) {
                amountReceivedInput.value = grandTotal.toFixed(2);
            }
            @this.set('amountReceived', grandTotal.toFixed(2));

            // Sync final split payments to Livewire component
            @this.set('splitPayments', splitPayments);

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('SplitPaymentModal'));
            modal.hide();

            Swal.fire({
                title: 'Success',
                text: 'Split payments applied successfully',
                icon: 'success',
                timer: 2000
            });
                    // call update change to refresh change field
            updateChange();
        }

        function showAlert() {
           let timerInterval;
            Swal.fire({
            title: "Applying Changes",
            html: "please wait...",
            timer: 1000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                timer.textContent = `${Swal.getTimerLeft()}`;
                }, 100);
            },
            willClose: () => {
                clearInterval(timerInterval);
            }
            }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                console.log("I was closed by the timer");
            }
            });
        }

        window.addEventListener('RequestDiscountCode', event => {
            const discountId = event.detail.discountId;
            const checkbox = document.querySelector(`.discount-checkbox[data-discount-id="${discountId}"]`);
            
            // Hide the modal before showing SweetAlert to prevent aria-hidden focus issues
            const discountModal = bootstrap.Modal.getInstance(document.getElementById('AddDiscountModal'));
            if (discountModal) {
                discountModal.hide();
            }

            Swal.fire({
                title: "Enter discount code",
                input: "text",
                inputAttributes: {
                    autocapitalize: "off"
                },
                showCancelButton: true,
                confirmButtonText: "Apply",
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                preConfirm: async (code) => {
                    try {
                        const response = await @this.applyDiscountWithCode(code, discountId);
                        
                        if (response.success) {
                            return response;
                        } else {
                            Swal.showValidationMessage(response.message);
                            return false;
                        }
                    } catch (error) {
                        Swal.showValidationMessage("An error occurred while applying the discount code.");
                        return false;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    // Success - show success message
                    // Swal.fire({
                    //     title: "Success!",
                    //     text: result.value.message,
                    //     icon: "success",
                    //     timer: 2000
                    // });
                } else {
                    // Cancelled or error - uncheck the checkbox
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                }
                
                // Re-open the discount modal after SweetAlert is closed
                setTimeout(() => {
                    const modal = new bootstrap.Modal(document.getElementById('AddDiscountModal'));
                    modal.show();
                }, result.isConfirmed ? 200 : 0);
            });
        });

        window.addEventListener('RequestOrderDiscountCode', event => {
            const discountId = event.detail.discountId;
            const checkbox = document.getElementById(`order-discount[${discountId}]`);
            
            // Hide the modal before showing SweetAlert to prevent aria-hidden focus issues
            const discountModal = bootstrap.Modal.getInstance(document.getElementById('AddOrderDiscountModal'));
            if (discountModal) {
                discountModal.hide();
            }

            Swal.fire({
                title: "Enter order discount code",
                input: "text",
                inputAttributes: {
                    autocapitalize: "off"
                },
                showCancelButton: true,
                confirmButtonText: "Apply",
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                preConfirm: async (code) => {
                    try {
                        const response = await @this.applyOrderDiscountWithCode(code, discountId);
                        
                        if (response.success) {
                            return response;
                        } else {
                            Swal.showValidationMessage(response.message);
                            return false;
                        }
                    } catch (error) {
                        Swal.showValidationMessage("An error occurred while applying the order discount code.");
                        return false;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    // Success - show success message
                } else {
                    // Cancelled or error - uncheck the checkbox
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                }
                
                // Re-open the order discount modal after SweetAlert is closed
                setTimeout(() => {
                    const modal = new bootstrap.Modal(document.getElementById('AddOrderDiscountModal'));
                    modal.show();
                }, result.isConfirmed ? 200 : 0);
            });
        });

        // Handle discount exceeds total event
        window.addEventListener('DiscountExceedsTotal', event => {
            const discountId = event.detail.discountId;
            // Find and uncheck the specific checkbox that triggered the error
            const checkbox = document.querySelector(`.discount-checkbox[data-discount-id="${discountId}"]`);
            if (checkbox) {
                checkbox.checked = false;
            }
            
            Swal.fire({
                title: 'Cannot Apply Discount',
                text: 'Total discount amount exceeds the item total. Please remove some discounts first.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });

        window.addEventListener('PaymentSaved', event => {
            Swal.fire({
                title: 'Payment Saved',
                text: 'The payment has been saved successfully.',
                icon: 'success',
                timer: 2000
            });
            document.getElementById('amountReceived').disabled = true;
            document.getElementById('change').value = '₱ 0.00';
        });
    </script>
    </div>
</div>
