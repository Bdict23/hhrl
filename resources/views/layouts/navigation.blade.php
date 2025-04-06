<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Dropdown Menu for Master Data -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" style="margin-top: 23px">
                    <x-dropdown>
                        <x-slot name="trigger">
                            <button
                                class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>{{ __('Master Data') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="url('/supplier_list')" class="no-underline">
                                {{ __('Supplier') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/company_list')" class="no-underline">
                                {{ __('Company') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/branch_department')" class="no-underline">
                                {{ __('Departments') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/branch_list')" class="no-underline">
                                {{ __('Branches') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/create_menu')" class="no-underline">
                                {{ __('Create Menu') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="url('/menu_lists')" class="no-underline">
                                {{ __('Menu Lists') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/settings')" class="no-underline">
                                {{ __('Settings') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Dropdown Menu for Purchase Order -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" style="margin-top: 23px">
                    <x-dropdown>
                        <x-slot name="trigger">
                            <button
                                class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>{{ __('Purchase Order') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="url('/purchase_order')" class="no-underline">
                                {{ __('PO Summary') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/receive_stock')" class="no-underline">
                                {{ __('Receiving') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Dropdown Menu for Inventory -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" style="margin-top: 23px">
                    <x-dropdown>
                        <x-slot name="trigger">
                            <button
                                class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>{{ __('Inventory') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="url('/cardex')" class="no-underline" data-bs-toggle="modal"
                                data-bs-target="#cardexModal" onclick="unhideModal()">
                                {{ __('Cardex') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/back_orders_summary')" class="no-underline">
                                {{ __('Back Orders') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/Allocate_Item')" class="no-underline">
                                {{ __('Item Allocation') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/item_withdrawal')" class="no-underline">
                                {{ __('Item Withdrawal') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Dropdown Menu for Sales Order -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" style="margin-top: 23px">
                    <x-dropdown>
                        <x-slot name="trigger">
                            <button
                                class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>{{ __('Sales') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="url('/sales_order')" class="no-underline">
                                {{ __('Sales Order') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/order_menu')" class="no-underline">
                                {{ __('Order Menu') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <x-dropdown>
                        <x-slot name="trigger">
                            <button
                                class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>{{ __('Service') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="url('/reservations_lists')" class="no-underline">
                                {{ __('Resevations') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/allocate_order')" class="no-underline">
                                {{ __('Allocate Order') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/orders_lists')" class="no-underline">
                                {{ __('Orders') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>


                    <x-dropdown>
                        <x-slot name="trigger">
                            <button
                                class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>{{ __('Transaction') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="url('/invoicing')" class="no-underline">
                                {{ __('Sales Invoice') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/daily_sales_report')" class="no-underline">
                                {{ __('Daily Sales') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>


                    <x-dropdown>
                        <x-slot name="trigger">
                            <button
                                class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>{{ __('Validations') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="url('/review_request_list')" class="no-underline">
                                {{ __('P.O - Review') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/approval_request_list')" class="no-underline">
                                {{ __('P.O - Approval') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/menu_approval_lists')" class="no-underline">
                                {{ __('Menu Approval') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="url('/menu_review_lists')" class="no-underline">
                                {{ __('Menu Review') }}
                                <x-dropdown-link :href="url('/withdrawal_review')" class="no-underline">
                                    {{ __('Withdrawal - Review') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="url('/withdrawal_approval')" class="no-underline">
                                    {{ __('Withdrawal - Approval') }}
                                </x-dropdown-link>
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="no-underline">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" class="no-underline"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('profile.edit')" class="no-underline">
                                {{ __('Employees') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="no-underline">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" class="no-underline"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Cardex Modal -->
<div class="modal fade" id="cardexModal" tabindex="-1" aria-labelledby="cardexModalLabel" aria-hidden="true"
    hidden>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cardexModalLabel">Cardex Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="cardexForm">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="itemCode" class="form-label">Item Code</label>
                            <input type="text" class="form-control" id="itemCode" name="item_code"
                                onkeypress="fetchCardexData(event)">
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <input type="text" class="form-control" id="description" name="description"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" readonly>
                        </div>

                        <div class="col-md-2">
                            <label for="price" class="form-label">Price</label>
                            <input type="text" class="form-control" id="price" name="price" readonly>
                        </div>

                        <div class="col-md-3">
                            <label for="totalBalance" class="form-label">Total Balance</label>
                            <input type="text" class="form-control" id="totalBalance" name="total_balance"
                                readonly>
                        </div>
                    </div>
                </form>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>In</th>
                            <th>Out</th>
                            <th>Balance</th>
                            <th>Transaction</th>
                        </tr>
                    </thead>
                    <tbody id="cardexTableBody">
                        <!-- Table rows will be dynamically added here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
            </div>
        </div>
    </div>
</div>

<script>
    function fetchCardexData(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const itemCode = document.getElementById('itemCode').value;
            if (itemCode) {
                fetch(`/get-cardex-data/${itemCode}`)
                    .then(response => {
                        if (!response.ok) {
                            console.error(response);
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }
                        document.getElementById('description').value = data.description;
                        document.getElementById('location').value = data.location;
                        document.getElementById('price').value = data.price;
                        document.getElementById('totalBalance').value = data.total_balance;
                        console.log(data.total_balance);
                        const tableBody = document.getElementById('cardexTableBody');
                        tableBody.innerHTML = '';
                        let runningBalance = 0;
                        data.cardex.forEach(row => {
                            runningBalance += row.qty_in - row.qty_out;
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${new Date(row.created_at).toLocaleDateString()}</td>
                                <td>${row.qty_in}</td>
                                <td>${row.qty_out}</td>
                                <td>${runningBalance}</td>
                                <td>${row.transaction_type}</td>
                            `;
                            tableBody.appendChild(tr);
                        });

                    })
                    .catch(error => console.error('Error fetching cardex data:', error));
            }
        }
    }

    function unhideModal() {
        // Show the modal after data is fetched
        const cardexModal = document.getElementById('cardexModal');
        cardexModal.removeAttribute('hidden');
        const modal = new bootstrap.Modal(cardexModal);
        modal.show();
    }

    document.getElementById('cardexModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('cardexModal').setAttribute('hidden', true);
    });
</script>
