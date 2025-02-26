<div class="row justify-content-center" id="orderContainer">
    {{-- wire:poll.3s='refreshOrders' --}}
    @foreach ($orders as $order)
        <div class="col-md-3 card mr-2 mt-3 draggable" draggable="true" id="order-{{ $order->id }}">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12">
                        <h4>{{ $order->order_number }}</h4>
                        <div class="text-muted row">
                            <div class="col-md-6 text-muted"> <span style="font-size: smaller;">
                                    {{ $order->tables->table_name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <span style="font-size: smaller;">Status : <span>
                                <span style="position: relative; right: -5%; top: 1%; ">New</span>
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
                    <div class="card-footer text-center">


                        <x-danger-button class="{{ $order->order_status != 'PENDING' ? 'd-none' : '' }}"
                            wire:click="cancelOrder({{ $order->id }})">{{ __('Cancel') }}</x-danger-button>
                        <x-primary-button class="{{ $order->order_status != 'PENDING' ? 'd-none' : '' }}"
                            wire:click="startOrder({{ $order->id }})">{{ __('Start') }}</x-primary-button>
                        <x-primary-button style="background-color: rgb(68, 146, 219);"
                            class="{{ $order->order_status != 'SERVING' ? 'd-none' : '' }}"
                            wire:click="serveOrder({{ $order->id }})">{{ __('Serve') }}</x-primary-button>
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
