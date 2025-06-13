@extends('layouts.master')
@section('content')
    <div class="container mt-5">
        <div class="row">

            <div class="col-md-12" style="position : sticky; top: 0; z-index: 1000;">
                <div class="list-group list-group-horizontal justify-content-center mb-4"
                    style="position: sticky; top: 0; z-index: 1000; background-color: white;">
                    {{-- @foreach ($tables as $table)
                        <a href="{{ route('sales.order_menu', ['table_id' => $table->id]) }}"
                            class="list-group-item list-group-item-action {{ $table->id == $table_id ? 'active' : '' }}">
                            Table {{ $table->table_number }}
                        </a>
                    @endforeach --}}
                    <a href="#" class="list-group-item list-group-item-action active" onclick="filterMenu('all')">All</a>
                    <a href="#" class="list-group-item list-group-item-action"
                        onclick="filterMenu('DESERTS')">Desserts</a>
                    <a href="#" class="list-group-item list-group-item-action" onclick="filterMenu('DISH')">Dish</a>
                    <a href="#" class="list-group-item list-group-item-action"
                        onclick="filterMenu('DRINKS')">Drinks</a>
                    <a href="#" class="list-group-item list-group-item-action"
                        onclick="filterMenu('OTHERS')">Others</a>
                </div>
            </div>

            <div class="col-md-9">
                <div class="row" id="menuContainer">
                    @foreach ($menus as $menu)
                        <div class="col-md-4 mb-4 menu-item" data-category="{{ $menu->categories->category_name }}">
                            <div class="card">
                                @if (isset($menu) && $menu->status == 'unavailable')
                                    <div class="text-center">
                                        <span class="unavailable-text">Unavailable</span>
                                    </div>
                                @endif
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
                                    <button class="btn btn-primary"
                                        onclick="addToOrder('{{ $menu->id }}','{{ $menu->menu_name }}', {{ $menu->price_levels()->latest()->where('price_type', 'SRP')->first()->amount ?? '0.00' }})">Add
                                        to Order</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-3 card mt-4 p-6" style="height: 100%; position: sticky; top: 0; ">
                <div class="card-body">
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
                            Discount
                            <span id="discount">₱0.00</span>
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
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderSummaryModalLabel">Order Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('order.store') }}" method="POST">
                        @csrf
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
                    <button type="submit" class="btn btn-primary">Place Order</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let order = [];

        function addToOrder(menu_id, name, price) {
            const existingItem = order.find(item => item.name === name);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                order.push({
                    menu_id,
                    name,
                    price,
                    quantity: 1
                });
            }
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
                tr.innerHTML = `
                    <td>${item.name}</td>
                    <td>
                        <input type="number" name="order_qty[]" value="${item.quantity}" min="1" class="form-control" onchange="updateQuantity('${item.name}', this.value)">
                        <input type="hidden" name="menu_id[]" value="${item.menu_id}">
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

        function updateQuantity(name, quantity) {
            const item = order.find(item => item.name === name);
            if (item) {
                item.quantity = parseInt(quantity);
                updateOrderTable();
                updateOrderSummary();
            }
        }

        function removeFromOrder(name) {
            order = order.filter(item => item.name !== name);
            updateOrderTable();
            updateOrderSummary();
        }

        function placeOrder() {
            // Logic to place the order
            alert('Order placed successfully!');
            $('#orderSummaryModal').modal('hide');
            Livewire.emit('orderAdded'); // Trigger the Livewire event
        }

        function filterMenu(category) {
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                if (category === 'all' || item.getAttribute('data-category') === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
@endsection
