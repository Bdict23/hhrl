<div class="tab-content alert-success" id="jobOrderTabContent">
    
    {{-- FLOOR ORDERS TAB --}}
    <div class="tab-pane fade {{ $activeTab == 'invoice' ? 'show active' : '' }}" id="invoice" role="tabpanel" aria-labelledby="invoice-tab" wire:ignore.self>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <h3> FLOOR ORDERS</h3>
                    </div>
                    <ul class="col-md-2 text-right"></ul>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="list-group list-group-horizontal justify-content-center mb-4"
                            style="position: sticky; top: 0; z-index: 1000; background-color: white;">
                            <a href="#" class="list-group-item list-group-item-action active"
                                onclick="filterOrders('all')">ORDER UP</a>
                            <a href="#" class="list-group-item list-group-item-action"
                                onclick="filterOrders('Serving')">SERVED</a>
                            <a href="#" class="list-group-item list-group-item-action"
                                onclick="filterOrders('Ready')">COMPLETED</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row justify-content-center" id="orderContainer">
                    @foreach ($forServingOrders as $order)
                        <div class="col-md-3 card mr-2 mt-3 draggable" draggable="true" id="order-{{ $order->id }}" wire:key="floor-order-{{ $order->id }}">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>{{ $order->order_number }}</h4>
                                        <div class="text-muted row">
                                            <div class="col-md-6 text-muted"> <span style="font-size: smaller;">
                                                    {{ $order->tables->table_name ?? 'TAKE OUT' }}</span>
                                            </div>
                                        </div>
                                        {{-- <span style="font-size: smaller;">Status : <span>
                                                <span style="position: relative; right: -5%; top: 1%; ">New</span> --}}
                                    </div>
                                    <div style="text-align: right;position: absolute; right: 1%; top: 1%;">
                                        <h6>Lapsed Time: <span class="lapsed-time" data-time="{{ $order->updated_at }}"></span></h6>
                                    </div>
                                    <div class="card-body" style="height: 250px; overflow-y: auto;">
                                        <table class="table">
                                            <th style="position: sticky; top: 0; z-index: 1000; background-color: rgb(230, 225, 225);">
                                            <td style="position: sticky; top: 0; z-index: 1000; background-color: rgb(230, 225, 225);">
                                                Item</td>
                                            <td style="position: sticky; top: 0; z-index: 1000; background-color: rgb(230, 225, 225);">
                                                Qty</td>
                                            <td style="position: sticky; top: 0; z-index: 1000; background-color: rgb(230, 225, 225);">
                                                Marked</td>
                                            </th>

                                            @foreach ($order->order_details as $detail)
                                                <tr>
                                                    <td></td>
                                                    <td>{{ $detail->menu->menu_name }}</td>
                                                    <td style="text-align:center;">{{ $detail->qty }}x</td>
                                                    <td style="text-align:right;"><input type="checkbox" name="item_checked[]"
                                                            wire:click="markItem({{ $detail->id }}, $event.target.checked)"
                                                            value="1" {{ $detail->marked == 1 ? 'checked' : '' }}
                                                            {{ in_array($order->order_status, ['PENDING', 'CANCELLED', 'COMPLETED', 'SERVED']) ? 'disabled' : '' }}>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </table>

                                    </div>
                                    <div class="card-footer text-center" 
                                    {{-- wire:poll.1s='refreshOrders' --}}
                                    >
                                        {{-- <x-danger-button class="{{ $order->order_status != 'PENDING' ? 'd-none' : '' }}"
                                            wire:click="cancelOrder({{ $order->id }})">{{ __('Cancel') }}</x-danger-button> --}}
                                        <x-primary-button style="background-color: rgb(68, 146, 219);"
                                            class="{{ $order->order_status != 'SERVING' ? 'd-none' : '' }}"
                                            wire:click="serveOrder({{ $order->id }})">{{ __('SERVED') }}</x-primary-button>
                                        <h6 class=" {{ $order->order_status != 'COMPLETED' ? 'd-none' : '' }}"
                                            style="font-size: smaller;">
                                            COMPLETED</h6>
                                        <h6 class=" {{ $order->order_status != 'CANCELLED' ? 'd-none' : '' }}"
                                            style="font-size: smaller;">
                                            CANCELLED</h6>
                                        <h6 class=" {{ $order->order_status != 'SERVED' ? 'd-none' : '' }}"
                                            style="font-size: smaller;">
                                            SERVED</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
    {{-- KITCHEN ORDERS TAB --}}
     <div class="tab-pane fade {{ $activeTab == 'job-order' ? 'show active' : '' }}" id="job-order" role="tabpanel" aria-labelledby="job-order-tab" wire:ignore.self>
        <div class="row">
            @if (session()->has('success'))
                <div class="alert alert-success" id="success-message">
                    {{ session('success') }}
                    <button type="button" class="btn-close btn-sm float-end" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row justify-content-center" id="orderContainer">
                    @forelse ($newOrders as $order)
                        <div class="col-md-3 card mr-2 mt-3 draggable" draggable="true" id="order-{{ $order->id }}" wire:key="kitchen-order-{{ $order->id }}">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>{{ $order->order_number }}</h4>
                                        <div class="text-muted row">
                                            <div class="col-md-6 text-muted"> <span style="font-size: smaller;">
                                                    {{ $order->tables->table_name ?? 'TAKE OUT' }}</span>
                                            </div>
                                        </div>
                                        {{-- <span style="font-size: smaller;">Status : <span>
                                                <span style="position: relative; right: -5%; top: 1%; ">New</span> --}}
                                    </div>
                                    <div style="text-align: right;position: absolute; right: 1%; top: 1%;">
                                        <h6>Lapsed Time: <span class="lapsed-time" data-time="{{ $order->updated_at }}"></span></h6>
                                    </div>
                                    <div class="card-body" style="height: 250px; overflow-y: auto;">
                                        <table class="table">
                                            <th style="position: sticky; top: 0; z-index: 1000; background-color: rgb(230, 225, 225);">
                                            <td style="position: sticky; top: 0; z-index: 1000; background-color: rgb(230, 225, 225);">
                                                Item</td>
                                            <td style="position: sticky; top: 0; z-index: 1000; background-color: rgb(230, 225, 225);">
                                                Qty</td>
                                
                                            </th>

                                            @foreach ($order->order_details as $detail)
                                                <tr>
                                                    <td></td>
                                                    <td>{{ $detail->menu->menu_name }}</td>
                                                    <td style="text-align:center;">{{ $detail->qty }}x</td>
                                                   
                                                </tr>
                                            @endforeach

                                        </table>

                                    </div>
                                    <div class="card-footer text-center">
                                        <x-primary-button class="{{ $order->order_status != 'PENDING' ? 'd-none' : '' }}"
                                            wire:click="deployOrder({{ $order->id }})">{{ __('ORDER UP') }}</x-primary-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-md-12 text-center mt-5">
                            <h4>No New Orders</h4>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for tab changes
            const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabButtons.forEach(button => {
                button.addEventListener('shown.bs.tab', function(event) {
                    const targetId = event.target.getAttribute('data-bs-target').substring(1);
                    @this.call('setActiveTab', targetId);
                });
            });
        });

        // Preserve tab state after Livewire updates
        document.addEventListener('livewire:update', function() {
            const activeTab = @this.get('activeTab');
            const tabToShow = document.getElementById(activeTab);
            const tabButton = document.querySelector('[data-bs-target="#' + activeTab + '"]');
            
            if (tabToShow && tabButton) {
                // Remove active classes from all tabs
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });
                document.querySelectorAll('[data-bs-toggle="tab"]').forEach(btn => {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-selected', 'false');
                });
                
                // Add active classes to current tab
                tabToShow.classList.add('show', 'active');
                tabButton.classList.add('active');
                tabButton.setAttribute('aria-selected', 'true');
            }
        });
       window.addEventListener('dispatch-success', event => {
                   setTimeout(function() {
                    document.getElementById('success-message').style.display = 'none';
                }, 1500);
                
                });
    </script>
</div>
