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
        <div class="tab-content" id="jobOrderTabContent">
            <div class="tab-pane fade show active" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Pending Orders</h3>
                            </div>
                            <ul class="col-md-2 text-right"></ul>
                            <div class="col-md-4"
                                style="position : sticky; top: 0; z-index: 1000; background-color: white;">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control"
                                        placeholder="Search Order Number / Table Number" aria-label="Search Menu"
                                        aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="list-group list-group-horizontal justify-content-center mb-4"
                                    style="position: sticky; top: 0; z-index: 1000; background-color: white;">
                                    <a href="#" class="list-group-item list-group-item-action active"
                                        onclick="filterMenu('New Orders')">All</a>
                                    <a href="#" class="list-group-item list-group-item-action"
                                        onclick="filterMenu('Partially Prepared')">Partially Prepared</a>
                                    <a href="#" class="list-group-item list-group-item-action"
                                        onclick="filterMenu('drinks')">Served</a>
                                    <a href="#" class="list-group-item list-group-item-action"
                                        onclick="filterMenu('all')">New
                                        Orders</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row justify-content-center" id="orderContainer">
                            @foreach ($orders as $order)
                                <div class="col-md-3 card mr-2 mt-3 draggable" draggable="true"
                                    id="order-{{ $order->id }}">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4>{{ $order->order_number }}</h4>
                                                <span style="font-size: smaller;">Table : <span>
                                                        {{ $order->tables->table_name ?? 'N/A' }}</span></span>
                                            </div>
                                            <div style="text-align: right;position: absolute; right: 1%; top: 1%;">
                                                <h6>E lapsed : <span class="lapsed-time"
                                                        data-time="{{ $order->updated_at }}"></span></h6>
                                            </div>
                                            <div class="card-body" style="height: 200px; overflow-y: auto;">
                                                <table class="table">
                                                    <th>
                                                    <td>Item</td>
                                                    <td>Qty</td>
                                                    <td>Marked</td>
                                                    </th>


                                                    @foreach ($order->order_details as $detail)
                                                        <tr>
                                                            <td></td>
                                                            <td>{{ $detail->menu->menu_name }}</td>
                                                            <td>{{ $detail->qty }}x</td>
                                                            <td><input type="checkbox" name="item_checked[]" value="1">
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                </table>

                                            </div>
                                            <div>

                                                <x-primary-button> {{ __('Cancel') }}</x-primary-button>
                                                <x-primary-button>{{ __('Ready') }}</x-primary-button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
