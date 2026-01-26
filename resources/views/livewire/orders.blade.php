<div class="tab-content alert-success" id="jobOrderTabContent">
    
    {{-- FLOOR ORDERS TAB --}}
    <div class="tab-pane fade {{ $activeTab == 'invoice' ? 'show active' : '' }}" id="invoice" role="tabpanel" aria-labelledby="invoice-tab" wire:ignore.self>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6 d-flex align-items-center">
                        <h3> FLOOR ORDERS</h3>
                        <img style="color : rgb(116, 116, 125)" src="{{ asset('images/restaurant_floor.png') }}" alt="Floor Icon" style="width:40px; height:40px; " class="mb-2 ml-2">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="list-group list-group-horizontal justify-content-center mb-4"
                            style="position: sticky; top: 0; z-index: 1000; background-color: white;">
                            <button class="list-group-item list-group-item-action active" id="activeOrdersTab"
                                onclick="filterOrders('active')">ACTIVE ORDERS</button>
                            <button class="list-group-item list-group-item-action" id="servedTab"
                                onclick="filterOrders('Served')">SERVED</button>
                            <button class="list-group-item list-group-item-action" id="completedTab"
                                onclick="filterOrders('completed')">COMPLETED</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                
                {{-- active orders --}}
                <div class="row justify-content-center" id="orderContainerActive">
                    @foreach ($activeOrders as $order)
                        <div class="col-md-3 card mr-2 mt-3 draggable" draggable="true" id="order-{{ $order->id }}" wire:key="floor-order-{{ $order->id }}">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>{{ $order->order_number }}</h4>
                                            <span 
                                            @if($order->payment_status == 'PAID') class="badge bg-success badge-sm" @endif
                                             @if($order->payment_status == 'UNPAID') class="badge bg-danger badge-sm" @endif
                                              @if($order->payment_status == 'PARTIAL') class="badge bg-warning badge-sm" @endif>
                                                {{ $order->payment_status}}
                                            </span>
                                        

                                        <div class="text-muted row">
                                            <div class="col-md-6 text-muted"> <span style="font-size: smaller;">
                                                    {{ $order->tables->table_name ?? 'TAKE OUT' }}</span>
                                            </div>
                                        </div>
                                        {{-- <span style="font-size: smaller;">Status : <span>
                                                <span style="position: relative; right: -5%; top: 1%; ">New</span> --}}
                                    </div>
                                    <div style="text-align: center;position: absolute; right: 1%; top: 1%;">
                                        <span class="lapsed-time" data-time="{{ $order->created_at }}"></span>
                                    </div>
                                    @if($isAdmin)
                                        <div style="text-align: right;position: absolute; right: 1%; top: 1%;">
                                            <x-dropdown>
                                                <x-slot name="trigger">
                                                        <i class="bi bi-three-dots-vertical" style="cursor: pointer;"></i>
                                                </x-slot>
                                                <x-slot name="content">
                                                    <x-dropdown-link class="no-underline" wire:click="openCancelOptionsModal({{ $order->id }})">
                                                        {{ __('Cancelation') }}
                                                    </x-dropdown-link>
                                                </x-slot>
                                            </x-dropdown>
                                        </div>
                                    @endif
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
                                                    <td><i @if($detail->status == 'SERVING' ) style="color: green;" title="Deployed" class = "bi bi-circle-fill blink" @elseif($detail->status == 'PENDING') style="color: orange;" class = "bi bi-circle-fill" title="Pending" @elseif($detail->status == 'SERVED') style="color: green;" title="Served" class = "bi bi-circle-fill" @endif></i></td>
                                                    <td>{{ $detail->menu->menu_name }}</td>
                                                    <td style="text-align:center;">{{ $detail->qty }}x</td>
                                                    <td style="text-align:right;"><input id="checkbox{{ $detail->id }}" type="checkbox" name="item_checked[]"
                                                            wire:click="markItem({{ $detail->id }}, $event.target.checked)"
                                                            value="1" {{ $detail->status == 'SERVED' ? 'checked' : '' }}
                                                            class="{{ ($detail->status == 'CANCELLED') ? 'd-none' : '' }}"
                                                            {{ in_array($order->order_status, ['CANCELLED', 'COMPLETED', 'SERVED']) || !$isAdmin ? 'disabled' : '' }}>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                    <div class="card-footer text-center">
                                        {{-- <x-danger-button class="{{ $order->order_status != 'PENDING' ? 'd-none' : '' }}"
                                            wire:click="cancelOrder({{ $order->id }})">{{ __('Cancel') }}</x-danger-button> --}}
                                        @if($isAdmin)
                                            <x-primary-button style="background-color: rgb(68, 146, 219);"
                                                class="{{ $order->order_status != 'SERVING' ? 'd-none' : '' }}"
                                                wire:click="serveOrder({{ $order->id }})">{{ __('SERVED') }}</x-primary-button>
                                        @endif
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
                    @endforeach
                </div>

                {{-- served orders --}}
                 <div class="row justify-content-center" id="orderContainerServed" style="display: none;">
                    @foreach ($servedOrders as $order)
                        <div class="col-md-3 card mr-2 mt-3 draggable" draggable="true" id="order-{{ $order->id }}" wire:key="floor-order-{{ $order->id }}">
                            
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>{{ $order->order_number }}</h4>
                                            <span 
                                            @if($order->payment_status == 'PAID') class="badge bg-success badge-sm" @endif
                                             @if($order->payment_status == 'UNPAID') class="badge bg-danger badge-sm" @endif
                                              @if($order->payment_status == 'PARTIAL') class="badge bg-warning badge-sm" @endif>
                                                {{ $order->payment_status}}
                                            </span>
                                        

                                        <div class="text-muted row">
                                            <div class="col-md-6 text-muted"> <span style="font-size: smaller;">
                                                    {{ $order->tables->table_name ?? '(TAKE OUT)' }}</span>
                                            </div>
                                        </div>
                                        {{-- <span style="font-size: smaller;">Status : <span>
                                                <span style="position: relative; right: -5%; top: 1%; ">New</span> --}}
                                    </div>
                                    <div style="text-align: right;position: absolute; right: 1%; top: 1%;">
                                       <i class="bi bi-three-dots-vertical"></i>
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
                                                    <td style="text-align:right;"><input id="checkbox{{ $detail->id }}" type="checkbox" name="item_checked[]"
                                                            wire:click="markItem({{ $detail->id }}, $event.target.checked)"
                                                            value="1" {{ $detail->status == 'SERVED' ? 'checked' : '' }}
                                                            class="{{ ($detail->status == 'CANCELLED') ? 'd-none' : '' }}"
                                                            {{ in_array($order->order_status, ['CANCELLED', 'COMPLETED', 'SERVED']) ? 'disabled' : '' }}>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </table>

                                    </div>
                                    <div class="card-footer text-center">
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
                    @endforeach
                </div>

                {{-- completed orders --}}
                  <div class="row justify-content-center" id="orderContainerCompleted" style="display: none;">
                    @foreach ($completedOrders as $order)
                        <div class="col-md-3 card mr-2 mt-3 draggable" draggable="true" id="order-{{ $order->id }}" wire:key="floor-order-{{ $order->id }}">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>{{ $order->order_number }}</h4>
                                            <span 
                                            @if($order->payment_status == 'PAID') class="badge bg-success badge-sm" @endif
                                             @if($order->payment_status == 'UNPAID') class="badge bg-danger badge-sm" @endif
                                              @if($order->payment_status == 'PARTIAL') class="badge bg-warning badge-sm" @endif>
                                                {{ $order->payment_status}}
                                            </span>
                                        

                                        <div class="text-muted row">
                                            <div class="col-md-6 text-muted"> <span style="font-size: smaller;">
                                                    {{ $order->tables->table_name ?? 'TAKE OUT' }}</span>
                                            </div>
                                        </div>
                                        {{-- <span style="font-size: smaller;">Status : <span>
                                                <span style="position: relative; right: -5%; top: 1%; ">New</span> --}}
                                    </div>
                                    <div style="text-align: right;position: absolute; right: 1%; top: 1%;">
                                        <h6 class="text-muted">Completed : {{ $order->updated_at->format('h:i:s A') }}</h6>
                                    </div>
                                    <div class="card-body" style="height: 250px; overflow-y: auto;">
                                        <table class="table">
                                            <th style="position: sticky; top: 0; z-index: 1000; background-color: rgb(230, 225, 225);">
                                            <td style="position: sticky; top: 0; z-index: 1000; background-color: rgb(230, 225, 225);">
                                                Items</td>
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
                                                    <td style="text-align:right;"><input id="checkbox{{ $detail->id }}" type="checkbox" name="item_checked[]"
                                                            wire:click="markItem({{ $detail->id }}, $event.target.checked)"
                                                            value="1" {{ $detail->status == 'SERVED' ? 'checked' : '' }}
                                                            class="{{ ($detail->status == 'CANCELLED') ? 'd-none' : '' }}"
                                                            {{ in_array($order->order_status, ['CANCELLED', 'COMPLETED', 'SERVED']) ? 'disabled' : '' }}>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </table>

                                    </div>
                                    <div class="card-footer text-center">
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
                <div class="row justify-content-center" id="orderContainerKitchen">
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
                                        <h6>Lapsed Time: <span class="lapsed-time" data-time="{{ $order->created_at }}"></span></h6>
                                    </div>
                                    <div class="card-body" style="height: 250px; overflow-y: auto;">
                                        <table class="table">
                                            <th style="position: sticky; top: 0; z-index: 1000; background-color: rgb(230, 225, 225);">
                                            <td style="position: sticky; top: 0; z-index: 1000; background-color: rgb(230, 225, 225);">
                                                Item</td>
                                            <td style="position: sticky; top: 0; z-index: 1000; background-color: rgb(230, 225, 225);">
                                                Qty</td>
                                
                                            </th>

                                            @foreach ($order->order_details_unmarked as $detail)
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

    {{-- to cancel items modal --}}
    <div class="modal fade modal-lg" id="cancelItemsModal" tabindex="-1" aria-labelledby="cancelItemsModalLabel" aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelItemsModalLabel">Cancel Specific Items &nbsp;<i class="bi bi-x-circle"></i></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="itemsToCancel">Select Items to Cancel:</label>
                            <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ced4da; padding: 10px;">
                                    <table class="table table-sm table-bordered">
                                        <th>
                                            <td>Items</td>
                                            <td>Qty</td>
                                            <td>Action</td>
                                        </th>
                                        <table-body>
                                            @foreach ($orderDetails as $detail)
                                                <tr>
                                                    <td></td>
                                                    <td>{{ $detail->menu->menu_name }}</td>
                                                    <td style="text-align:center;">{{ $detail->qty }}x</td>
                                                    <td style="text-align:right;">
                                                        <input class="form-check-input" type="checkbox" wire:change="selectedItem({{ $detail->id }}, $event.target.checked)">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table-body>
                                        <thead> @error('selectedItems2Cancel') <span class="text-danger">{{ $message }}</span> @enderror</thead>
                                    </table>
                                </div>
                                <div>
                                    <div class="form-group mt-3">
                                        <label for="cancelReason" class="form-label fw-bold">Reason for Cancellation</label>
                                        <textarea 
                                            class="form-control" 
                                            name="reason" 
                                            id="cancelReason" 
                                            cols="30" 
                                            rows="5" 
                                            placeholder="Please provide a reason for canceling these items..."
                                            style="resize: vertical; border-radius: 0.375rem;"
                                            wire:model="reasonForCancelation"
                                        ></textarea>
                                        @error('reasonForCancelation') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger" wire:click="cancelSelectedItems" >Cancel Selected Items</button>
                    </div>
                </div>
            </div>
        </div>

    <script>
        // Listen for open-cancel-options-modal event
        window.addEventListener('open-cancel-options-modal', event => {
            const data = event.detail[0];
            Swal.fire({
                title: 'Cancelation',
                text: 'Select an option to cancel:',
                icon: 'info',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Cancel this Order',
                denyButtonText: 'Cancel Specific Items',
                allowOutsideClick: false,
                allowEscapeKey: false,
                denyButtonColor: '#F25E86',
                confirmButtonColor: '#F2385A',
                cancelButtonText: 'Abort'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.cancelOrder(data.orderId);
                } else if (result.isDenied) {
                    @this.openCancelItemsModal(data.orderId); 
                }
            });
        });

        // LISTEN FOR OPEN-CANCEL-REASON TEXT AREA MODAL
        window.addEventListener('open-cancel-reason-modal', event => {
            const data = event.detail[0];
            Swal.fire({
                title: 'Enter Cancelation Reason',
                input: 'textarea',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocomplete: 'off',
                    'data-form-type': 'other',
                    rows: 4,
                    placeholder: 'Type your reason here...'
                },
                didOpen: () => {
                    // Disable autocomplete on the form element
                    const swalForm = Swal.getPopup().querySelector('form');
                    if (swalForm) {
                        swalForm.setAttribute('autocomplete', 'off');
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                preConfirm: (reason) => {
                    if (!reason) {
                        Swal.showValidationMessage('Reason is required');
                    } else {
                        return @this.submitCancelReason(reason)
                            .then(response => {
                                if (!response || !response.success) {
                                    throw new Error(response?.message || 'Failed to cancel order');
                                }
                                return response;
                            })
                            .catch(error => {
                                Swal.showValidationMessage(
                                    error.message || `Request failed: ${error}`
                                );
                            });
                    }
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    // Check if password verification is required
                    if (result.value.requiresPassword) {
                        // Password modal will be shown by the event listener
                        // Don't show success message yet
                    } else {
                        // No password required, order was canceled
                        Swal.fire({
                            icon: 'success',
                            title: 'Order Canceled Successfully',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        @this.makeChanges();
                    }
                }
            });
        });

        window.addEventListener('open-canceling-byOrder-password-modal',event =>{
              Swal.fire({
                title: 'Enter Canceling Password',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocomplete: 'off',
                    'data-form-type': 'other'
                },
                didOpen: () => {
                    // Disable autocomplete on the form element
                    const swalForm = Swal.getPopup().querySelector('form');
                    if (swalForm) {
                        swalForm.setAttribute('autocomplete', 'off');
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                preConfirm: (password) => {
                    return @this.verifyCancelingPassword(password)
                        .then(response => {
                            if (!response || !response.success) {
                                throw new Error(response?.message || 'Invalid password');
                            }
                            return response;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(
                                error.message || `Request failed: ${error}`
                            );
                        });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Canceled Successfully',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    @this.processCancelOrder();
                } else if (result.isDismissed) {
                    // reset order details 
                   @this.resetCancelDetails();
                }
            });
        });

        // Listen for open-cancel-items-modal
        window.addEventListener('open-cancel-items-modal', event => {
            //open cancelItemsModal
            var cancelItemsModal = new bootstrap.Modal(document.getElementById('cancelItemsModal'));
            cancelItemsModal.show();
        });


        // FUNCTION TO FILTER ORDERS BASED ON STATUS
        function filterOrders(status) {
            // Hide all orders
            const orders = document.querySelectorAll('#orderContainerActive , #orderContainerServed , #orderContainerCompleted');
            orders.forEach(order => {
                order.style.display = 'none';
            });


            // Update active tab button styling
            document.getElementById('activeOrdersTab').classList.remove('active');
            document.getElementById('servedTab').classList.remove('active');
            document.getElementById('completedTab').classList.remove('active');

            if (status === 'active') {
                document.getElementById('activeOrdersTab').classList.add('active');
                document.querySelectorAll('#orderContainerActive').forEach(order => {
                    order.style.display = 'flex';
                });
            } else if (status === 'Served') {
                document.getElementById('servedTab').classList.add('active');
                document.querySelectorAll('#orderContainerServed').forEach(order => {
                    order.style.display = 'flex';
                });
            } else if (status === 'completed') {
                document.getElementById('completedTab').classList.add('active');
                document.querySelectorAll('#orderContainerCompleted').forEach(order => {
                    order.style.display = 'flex';
                });
            }
        }
        // Listen for open-unmarking-password-modal event (outside DOMContentLoaded so it persists)
        window.addEventListener('open-unmarking-password-modal', event => {
            
            
            const data = event.detail[0];

            // Open swal modal to ask for password
            Swal.fire({
                title: 'Enter Unmarking Password',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocomplete: 'off',
                    'data-form-type': 'other'
                },
                didOpen: () => {
                    // Disable autocomplete on the form element
                    const swalForm = Swal.getPopup().querySelector('form');
                    if (swalForm) {
                        swalForm.setAttribute('autocomplete', 'off');
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                preConfirm: (password) => {
                    return @this.verifyUnmarkingPassword(data.orderId, password)
                        .then(response => {
                            if (!response || !response.success) {
                                throw new Error(response?.message || 'Invalid password');
                            }
                            return response;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(
                                error.message || `Request failed: ${error}`
                            );
                        });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Item Unmarked Successfully',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    @this.makeChanges();
                } else if (result.isDismissed) {
                    // Refresh component to reset checkbox state
                    document.getElementById('checkbox' + data.orderId).checked = true;
                }
            });
        });

        //listen for open-canceling-password-modal
        window.addEventListener('open-canceling-password-modal', event => {
            
            // Hide the modal before showing SweetAlert to prevent aria-hidden focus issues
            const cancelItemsSelection =  bootstrap.Modal.getInstance(document.getElementById('cancelItemsModal'));
            if (cancelItemsSelection) {
                cancelItemsSelection.hide();
            }
            
            const data = event.detail[0];

            // Open swal modal to ask for password
            Swal.fire({
                title: 'Enter Canceling Password',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocomplete: 'off',
                    'data-form-type': 'other'
                },
                didOpen: () => {
                    // Disable autocomplete on the form element
                    const swalForm = Swal.getPopup().querySelector('form');
                    if (swalForm) {
                        swalForm.setAttribute('autocomplete', 'off');
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                preConfirm: (password) => {
                    return @this.verifyCancelingPassword(password)
                        .then(response => {
                            if (!response || !response.success) {
                                throw new Error(response?.message || 'Invalid password');
                            }
                            return response;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(
                                error.message || `Request failed: ${error}`
                            );
                        });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Canceled Successfully',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    @this.proccessOrderItemsCancelation();
                } else if (result.isDismissed) {
                    // reset order details 
                   @this.resetCancelDetails();
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            //blink effect for deployed items
            const style = document.createElement('style');
            style.type = 'text/css';
            style.innerHTML = `
                @keyframes blink {
                    0% { opacity: 1; }
                    50% { opacity: 0; }
                    100% { opacity: 1; }
                }
                .blink {
                    animation: blink 1s infinite;
                }
            `;
            document.getElementsByTagName('head')[0].appendChild(style);

            // Listen for tab changes
            const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabButtons.forEach(button => {
                button.addEventListener('shown.bs.tab', function(event) {
                    const targetId = event.target.getAttribute('data-bs-target').substring(1);
                    @this.call('setActiveTab', targetId);
                });
            });
        });

        document.addEventListener('error', event => {
            const data = event.detail[0];
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
            });
            if(data.orderId){
                // Refresh component to reset checkbox state
                document.getElementById('checkbox' + data.orderId).checked = true;
            }
            
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
