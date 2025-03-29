@extends('layouts.master')

@section('content')
    
    <div class="container mt-5">
        <ul class="nav nav-tabs" id="jobOrderTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="invoice-tab" data-bs-toggle="tab" data-bs-target="#invoice" type="button"
                    role="tab" aria-controls="invoice" aria-selected="true">Orders</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="job-order-tab" data-bs-toggle="tab" data-bs-target="#job-order" type="button"
                    role="tab" aria-controls="job-order" aria-selected="false">
                    Completed Orders</button>
            </li>
        </ul>
        <div class="tab-content alert-success" id="jobOrderTabContent">
            <div class="tab-pane fade show active" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Pending Orders</h3>
                            </div>
                            <ul class="col-md-2 text-right"></ul>

                            {{-- @livewire('search-order-number') --}}

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="list-group list-group-horizontal justify-content-center mb-4"
                                    style="position: sticky; top: 0; z-index: 1000; background-color: white;">
                                    <a href="#" class="list-group-item list-group-item-action active"
                                        onclick="filterOrders('all')">All</a>
                                    <a href="#" class="list-group-item list-group-item-action"
                                        onclick="filterOrders('Serving')">Serving</a>
                                    <a href="#" class="list-group-item list-group-item-action"
                                        onclick="filterOrders('Ready')">Ready</a>
                                    <a href="#" class="list-group-item list-group-item-action"
                                        onclick="filterOrders('Partially Prepared')">Partially Prepared</a>
                                    <a href="#" class="list-group-item list-group-item-action"
                                        onclick="filterOrders('Served')">Served</a>
                                    <a href="#" class="list-group-item list-group-item-action"
                                        onclick="filterOrders('New Orders')">New Orders</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        {{-- livewire component to display orders --}}
                        @livewire('orders', ['orders' => \App\Models\Order::where('order_status', '!=', 'FOR ALLOCATION')->get()])
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('script')
    <script>
        function updateLapsedTime() {
            const lapsedTimeElements = document.querySelectorAll('.lapsed-time');
            lapsedTimeElements.forEach(element => {
                const createdAt = new Date(element.getAttribute('data-time'));
                const now = new Date();
                const diff = Math.floor((now - createdAt) / 1000); // Difference in seconds

                const hours = Math.floor(diff / 3600);
                const minutes = Math.floor((diff % 3600) / 60);
                const seconds = diff % 60;

                element.textContent = `${hours}h ${minutes}m ${seconds}s`;
            });
        }

        setInterval(updateLapsedTime, 1000);
        // Drag and Drop functionality
        const draggables = document.querySelectorAll('.draggable');
        const container = document.getElementById('orderContainer');

        draggables.forEach(draggable => {
            draggable.addEventListener('dragstart', () => {
                draggable.classList.add('dragging');
            });

            draggable.addEventListener('dragend', () => {
                draggable.classList.remove('dragging');
            });
        });

        container.addEventListener('dragover', e => {
            e.preventDefault();
            const afterElement = getDragAfterElement(container, e.clientY);
            const draggable = document.querySelector('.dragging');
            if (afterElement == null) {
                container.appendChild(draggable);
            } else {
                container.insertBefore(draggable, afterElement);
            }
        });

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.draggable:not(.dragging)')];

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    return {
                        offset: offset,
                        element: child
                    };
                } else {
                    return closest;
                }
            }, {
                offset: Number.NEGATIVE_INFINITY
            }).element;
        }
    </script>
@endsection
