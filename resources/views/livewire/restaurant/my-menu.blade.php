<div>
   <div class="container mt-5">
        <div class="row" wire:ignore.self>

            <div class="col-md-12" style="position : sticky; top: 0; z-index: 1000;" wire:ignore>
                <div class="list-group list-group-horizontal justify-content-center mb-4"
                    style="position: sticky; top: 0; z-index: 1000; background-color: white;">
                         <a href="#" class="list-group-item list-group-item-action active" onclick="filterMenu('all')" id="category-all" wire:click.prevent="selectedCategory('all')">All</a>
                        @foreach ($menuCategories as $category)
                            <a href="#" class="list-group-item list-group-item-action lists" id="category-{{ $category->id }}"
                                onclick="filterMenu('{{ $category->id }}')" wire:click.prevent="selectedCategory('{{ $category->id }}')">{{ $category->category_name }} <span wire:loading wire:target="selectedCategory('{{ $category->id }}')"></span></a>
                        @endforeach
                </div>
            </div>

            <div class="col-md-9">
                <div class="row" id="menuContainer">
                    @foreach($menuItems as $index => $menu)
                    
                        <div class="col-md-4 mb-4 menu-item" data-category="{{ $menu->categories->id }}">
                            <div class="card">
                                
                                @if (isset($menu) && $menu->status == 'unavailable')
                                    <div class="text-center">
                                        <span class="unavailable-text">Unavailable</span>
                                    </div>
                                @endif
                                <div>
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span id="balQty-{{ $index }}" class="badge bg-success">AVAILABLE: {{ $menu->recipeCount->first()->bal_qty ?? 0 }}</span>
                                    </div>
                                    @if(($menu->recipeCount->first()->bal_qty ?? 0) == 0)
                                        <div class="position-absolute top-50 start-50 translate-middle">
                                            <span class="badge bg-secondary fs-4 opacity-75">UNAVAILABLE</span>
                                        </div>
                                    @endif
                                </div>
                                <img src="{{ asset('images/' . $menu->menu_image) }}" class="card-img-top"
                                    alt="{{ $menu->menu_image }}" style="width: 100%; height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $menu->menu_name }}</h5>
                                    <p class="card-text">{{ $menu->menu_description }}</p>
                                    <p class="card-text"><strong>Price:</strong>
                                        @php
                                            $mrp_price = $menu
                                                ->price_levels()
                                                ->latest()
                                                ->where('price_type', 'MRP')
                                                ->first();
                                        @endphp
                                        <span style="text-decoration: line-through;"
                                            {{ $mrp_price ? '₱' . $mrp_price->amount : 'hidden' }}>₱
                                            {{ $menu->price_levels()->latest()->where('price_type', 'MRP')->first()->amount ?? '0.00' }}</span>
                                        ₱
                                        {{ $menu->price_levels()->latest()->where('price_type', 'RATE')->first()->amount ?? '0.00' }}
                                    </p>
                                    <button class="btn btn-primary" @if(($menu->recipeCount->first()->bal_qty ?? 0)==0) disabled @endif
                                        onclick="addToOrder('{{ $menu->id }}','{{ $menu->menu_name }}', {{ $menu->price_levels()->latest()->where('price_type', 'RATE')->first()->amount ?? '0.00' }}, {{ $menu->recipeCount->first()->bal_qty ?? 0 }}, {{ $index }})"
                                        wire:click.prevent="updateQTY('{{ $menu->id }}')">Add to Order</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-3 card mt-4 p-6" style="height: 100%; position: sticky; top: 0; " wire:ignore>
                <div class="card-body">
                    <strong><span id="tableNumber">{{ $selectedTable->table_name }}</span></strong>
                    <h5 class="card-title mb-4 mt-4">Order Summary</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Items
                            <span id="totalItems">0</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Price
                            <span id="totalPrice">₱0.00</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total
                            <span id="total">₱0.00</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#orderSummaryModal">
                                View Order Summary
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <!-- Order Summary Modal -->
        <div class="modal fade" id="orderSummaryModal" tabindex="-1" aria-labelledby="orderSummaryModalLabel"
            aria-hidden="true" wire:ignore>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderSummaryModalLabel" >Order Summary</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('order.store') }}" method="POST">
                            @csrf
                            <input type="text" name="tableID" value="{{ $selectedTable->id }}" hidden>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="orderTableBody">
                                    <!-- Order items will be dynamically added here -->
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end">
                                <h5>Total Amount: ₱<span id="totalAmount">0.00</span></h5>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" onclick="hasUnsavedChanges = false;">Place Order</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>



         <script>
        let order = [];
        let hasUnsavedChanges = false;
        const ORDER_STORAGE_KEY = 'unsaved_order_data';

        // Load any existing unsaved order from localStorage on page load
        window.addEventListener('DOMContentLoaded', function() {
            const savedOrder = localStorage.getItem(ORDER_STORAGE_KEY);
            if (savedOrder) {
                // Clear localStorage since we're loading the page fresh
                localStorage.removeItem(ORDER_STORAGE_KEY);
            }
        });

        // Warn user before leaving page if there are unsaved orders
        window.addEventListener('beforeunload', function (e) {
            if (hasUnsavedChanges && order.length > 0) {
                // Save order to localStorage before leaving
                localStorage.setItem(ORDER_STORAGE_KEY, JSON.stringify(order));
                
                // Call rollback via Livewire
                @this.call('rollbackAllItems', order);
                
                e.preventDefault();
                e.returnValue = ''; 
                return 'You have unsaved orders. Are you sure you want to leave?';
            }
        });

        // Handle page unload (when user confirms leaving)
        window.addEventListener('unload', function() {
            if (hasUnsavedChanges && order.length > 0) {
                // Use sendBeacon for reliable delivery during page unload
                const formData = new FormData();
                formData.append('orderItems', JSON.stringify(order));
                
                // Make synchronous call to rollback
                @this.call('rollbackAllItems', order);
            }
        });

        function addToOrder(menu_id, name, price,bal_qty,index) {
            const existingItem = order.find(item => item.name === name);

            if (existingItem) {
                existingItem.quantity += 1;
                existingItem.bal_qty = bal_qty;
            } else {
                order.push({
                    menu_id,
                    name,
                    price,
                    quantity: 1,
                    bal_qty: bal_qty
                });
            }
            hasUnsavedChanges = true; // Mark as having unsaved changes
            updateOrderTable();
            updateOrderSummary();
            $('#orderSummaryModal').modal('show');
        }

        function updateOrderTable() {
            const orderTableBody = document.getElementById('orderTableBody');
            orderTableBody.innerHTML = '';

            let totalAmount = 0;

            order.forEach(item => {
                const total = item.price * item.quantity;
                totalAmount += total;

                const tr = document.createElement('tr');
                const canDecrease = item.quantity > 1;
                const canIncrease = item.quantity < item.bal_qty;
                tr.innerHTML = `
                    <td>${item.name}</td>
                    <td>
                         <span class="input-group input-group-sm" style="width: 120px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity('${item.name}', ${item.quantity - 1},'decrease')" ${!canDecrease ? 'disabled' : ''}>-</button>
                            <input type="number" name="order_qty[]" value="${item.quantity}" min="1" max="${item.bal_qty}" class="form-control text-center" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity('${item.name}', ${item.quantity + 1},'increase')" ${!canIncrease ? 'disabled' : ''}>+</button>
                            <input type="hidden" name="menu_id[]" value="${item.menu_id}">
                        </span>
                    </td>
                    <td>₱${item.price.toFixed(2)}</td>
                    <td>₱${total.toFixed(2)}</td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="removeFromOrder('${item.name}')">Remove</button>
                    </td>
                `;
                orderTableBody.appendChild(tr);
            });

            document.getElementById('totalAmount').textContent = totalAmount.toFixed(2);
        }

        function updateOrderSummary() {
            const totalItems = order.reduce((sum, item) => sum + item.quantity, 0);
            const totalPrice = order.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('totalPrice').textContent = `₱${totalPrice.toFixed(2)}`;
            document.getElementById('total').textContent = `₱${totalPrice.toFixed(2)}`;
        }

        function updateQuantity(name, quantity, action) {
            const item = order.find(item => item.name === name);
            if (item) {
                const newQuantity = parseInt(quantity);
                // Enforce minimum of 1 and maximum of available balance
                if (newQuantity < 1 || newQuantity > item.bal_qty) {
                    return;
                }
                item.quantity = newQuantity;
                @this.call('upQuantity', item.menu_id, item.quantity, action);
                hasUnsavedChanges = true; // Mark as having unsaved changes
                updateOrderTable();
                updateOrderSummary();
            }
        }

        function removeFromOrder(name) {
            const item = order.find(item => item.name === name);
            if (item) {
                @this.call('rollbackQTY', item.menu_id, item.quantity);
            }
            order = order.filter(item => item.name !== name);
            hasUnsavedChanges = order.length > 0; // Update flag based on remaining items
            updateOrderTable();
            updateOrderSummary();
        }

        function placeOrder() {
            // Logic to place the order
            alert('Order placed successfully!');
            // Clear unsaved changes flag when order is placed
            hasUnsavedChanges = false;
            order = [];
            localStorage.removeItem(ORDER_STORAGE_KEY);
            $('#orderSummaryModal').modal('hide');
            Livewire.emit('orderAdded'); // Trigger the Livewire event
        }

        function filterMenu(category) {
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                if (category === 'all' || item.getAttribute('data-category') == category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            // Remove 'active' from all category links
            document.querySelectorAll('.lists').forEach(link => {
                link.classList.remove('active');
            });
            document.getElementById('category-all').classList.remove('active');

            // Add 'active' to the selected category
            if (category === 'all') {
                document.getElementById('category-all').classList.add('active');
            } else {
                const activeLink = document.getElementById('category-' + category);
                if (activeLink) {
                    activeLink.classList.add('active');
                }
            }
        }
    </script>
</div>
