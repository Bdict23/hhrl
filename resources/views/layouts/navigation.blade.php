<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        @if (auth()->user()->branch && auth()->user()->branch->company->company_logo)
                        <img src="{{ asset('images/' . auth()->user()->branch->company->company_logo) }}" alt="" style="height: 40px;">
                        @else
                       <x-application-logo class="block h-10 w-auto fill-current text-gray-600" />
                        @endif
                    </a>
                </div>
                @if (auth()->user()->employee->getGroupedModulePermissions('Master Data') !=2 
                    ||auth()->user()->employee->getGroupedModulePermissions('Item Management') !=2 
                    || auth()->user()->employee->getGroupedModulePermissions('Item Properties') !=2
                    || auth()->user()->employee->getGroupedModulePermissions('Price Levels') !=2 
                    || auth()->user()->employee->getGroupedModulePermissions('Business') !=2)
                     <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" style="margin-top: 23px">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button
                                    class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    <div>{{ __('Master Data') }}</div>
                                    <div class="ms-1">
                                        {{-- <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg> --}}
                                    </div>
                                </button>
                            </x-slot>
    
                            <x-slot name="content">
                                @if(auth()->user()->employee->getModulePermission('Suppliers') !=2)
                                    <x-dropdown-link :href="url('/supplier_list')" class="no-underline">
                                        {{ __('Supplier') }}
                                    </x-dropdown-link>
                                @endif
                                @if (auth()->user()->employee->getModulePermission('Companies') !=2)
                                    <x-dropdown-link :href="url('/company_list')" class="no-underline">
                                        {{ __('Company') }}
                                    </x-dropdown-link>
                                @endif
                                
                                @if (auth()->user()->employee->getModulePermission('Departments') !=2)
                                    <x-dropdown-link :href="url('/branch_department')" class="no-underline">
                                        {{ __('Departments') }}
                                    </x-dropdown-link>
                                @endif
                                
                                @if (auth()->user()->employee->getModulePermission('Branches') !=2)
                                    <x-dropdown-link :href="url('/branch_list')" class="no-underline">
                                        {{ __('Branches') }}
                                    </x-dropdown-link>
                                @endif
                               
                                @if (auth()->user()->employee->getModulePermission('User Access') !=2)
                                    <x-dropdown-link :href="url('/user-access')" class="no-underline">
                                        {{ __('User Access') }}
                                    </x-dropdown-link>
                                @endif

                                @if (auth()->user()->employee->getModulePermission('User Registration') !=2)
                                    <x-dropdown-link :href="route('register')" class="no-underline">
                                        {{ __('User Registration') }}
                                    </x-dropdown-link>
                                @endif
                                

                               @if (auth()->user()->employee->getGroupedModulePermissions('Item Management') !=2 
                                    || auth()->user()->employee->getGroupedModulePermissions('Item Properties') !=2
                                    || auth()->user()->employee->getGroupedModulePermissions('Price Levels') !=2 
                                    || auth()->user()->employee->getGroupedModulePermissions('Business') !=2)
                                   <x-dropdown-link :href="url('/settings')" class="no-underline">
                                    {{ __('Settings') }}
                                    </x-dropdown-link>
                               @endif
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif
                <!-- Dropdown Menu for Master Data -->
                @if (auth()->user()->employee->getGroupedModulePermissions('Purchasing') !=2 )
                    <!-- Dropdown Menu for Purchase Order -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" style="margin-top: 23px">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button
                                    class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    <div>{{ __('Purchasing') }}</div>
                                    <div class="ms-1">
                                        {{-- <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg> --}}
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                
                                @if (auth()->user()->employee->getModulePermission('Purchase Order') != 2 )
                                    <x-dropdown-link :href="url('/purchase_order')" class="no-underline">
                                        {{ __('PO Summary') }}
                                    </x-dropdown-link>
                                @endif
                          
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Dropdown Menu for Inventory -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" style="margin-top: 23px">
                    <x-dropdown>
                        <x-slot name="trigger">
                            <button
                                class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <div>{{ __('Inventory') }}</div>
                                <div class="ms-1">
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="url('/cardex')"  class="no-underline" data-bs-toggle="modal" onclick="unhideModal()"
                                data-bs-target="#cardexModal" >
                                {{ __('Cardex') }}
                            </x-dropdown-link>
                              @if(auth()->user()->employee->getModulePermission('Purchase Receive') != 2 )
                                    <x-dropdown-link :href="url('/receive_stock')" class="no-underline">
                                        {{ __('Receiving') }}
                                    </x-dropdown-link>
                                @endif
                            @if(auth()->user()->employee->getModulePermission('Back Orders') != 2 )
                                <x-dropdown-link :href="url('/back-orders')" class="no-underline">
                                    {{ __('Back Orders') }}
                                </x-dropdown-link>
                            @endif
                            @if (auth()->user()->employee->getModulePermission('Item Withdrawal') != 2 )
                                <x-dropdown-link :href="url('/item_withdrawal')" class="no-underline">
                                        {{ __('Item Withdrawal') }}
                                </x-dropdown-link>
                            @endif
                             @if (auth()->user()->employee->getModulePermission('Merchandise Inventory') != 2 )
                                <x-dropdown-link :href="url('/Merchandise-Inventory')" class="no-underline">
                                    {{ __('Merchnadise Inventory') }}
                                </x-dropdown-link>
                            @endif
                            @if (auth()->user()->employee->getModulePermission('Item Location') != 2 )
                                <x-dropdown-link :href="url('/item-location')" class="no-underline">
                                    {{ __('Item Location') }}
                                </x-dropdown-link>
                            @endif
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Dropdown for Transaction -->
                @if (auth()->user()->employee->getGroupedModulePermissions('Transaction') !=2)
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" style="margin-top: 23px">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button
                                    class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    <div>{{ __('Transaction') }}</div>
                                    <div class="ms-1">
                                    </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if (auth()->user()->employee->getModulePermission('Sales Order') != 2 )
                                <x-dropdown-link :href="url('/sales_order')" class="no-underline">
                                    {{ __('Sales Order') }}
                                </x-dropdown-link>
                                
                            @endif
                            @if (auth()->user()->employee->getModulePermission('Daily Sales') != 2 )
                                <x-dropdown-link :href="url('/daily_sales_report')" class="no-underline">
                                    {{ __('Daily Sales') }}
                                </x-dropdown-link>
                            @endif
                                <x-dropdown-link :href="url('/shifts-summary')" class="no-underline">
                                    {{ __('Shifts') }} &nbsp;<i class="bi bi-clock-history"></i>
                                </x-dropdown-link>
                        </x-slot>
                    </x-dropdown> 
                </div>
                @endif

                <!-- Dropdown Menu for Validation -->
                @if(auth()->user()->employee->getGroupedModulePermissions('Validations') !=2)
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" style="margin-top: 23px">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button
                                    class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    <div>{{ __('Validations') }}</div>
                                    <div class="ms-1">
                                        {{-- <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg> --}}
                                    </div>
                                </button>
                            </x-slot>
                        
                            <x-slot name="content">
                                @if (auth()->user()->employee->getModulePermission('PO Review') == 1 )
                                    <x-dropdown-link :href="url('/review_request_list')" class="no-underline" style="font-size: smaller">
                                        {{ __('P.O - Review') }}
                                    </x-dropdown-link>
                                @endif
                                @if (auth()->user()->employee->getModulePermission('PO Approval') == 1 )
                                        <x-dropdown-link :href="url('/approval_request_list')" class="no-underline" style="font-size: smaller">
                                        {{ __('P.O - Approval') }}
                                        </x-dropdown-link>
                                        <hr>
                                @endif
                                
                                @if(auth()->user()->employee->getModulePermission('Review Withdrawals') == 1 )
                                    <x-dropdown-link :href="url('/withdrawal_review')" class="no-underline" style="font-size: smaller">
                                        {{ __('Withdrawal - Review') }}
                                </x-dropdown-link>
                                @endif
                                @if (auth()->user()->employee->getModulePermission('Approve Withdrawals') == 1 )
                                    <x-dropdown-link :href="url('/withdrawal_approval')" class="no-underline" style="font-size: smaller">
                                        {{ __('Withdrawal - Approval') }}
                                    </x-dropdown-link>           
                                    <hr>                    
                                @endif
                                @if (auth()->user()->employee->getModulePermission('Menu Approval') == 1 )
                                    <x-dropdown-link :href="url('/menu_approval_lists')" class="no-underline">
                                        {{ __('Recipe - Approval') }}
                                    </x-dropdown-link>
                                    <hr>
                                @endif
                                    <x-dropdown-link :href="url('/budget-proposal-approval-lists')" class="no-underline" style="font-size: x-small">
                                        {{ __('Budget Proposal - Approval') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="url('/equipment-request-approval-lists')" class="no-underline" style="font-size: x-small">
                                        {{ __('Equipment Request - Approval') }}
                                    </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Dropdown Menu for Banquet -->
                @if(auth()->user()->employee->getGroupedModulePermissions('Banquet') !=2)
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" style="margin-top: 23px">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button
                                    class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    <div>{{ __('Banquet') }}</div>
                                    <div class="ms-1">
                                        {{-- <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg> --}}
                                    </div>
                                </button>
                            </x-slot>
                        
                            <x-slot name="content">
                                @if (auth()->user()->employee->getModulePermission('Banquet Events') != 2 )
                                    <x-dropdown-link :href="url('/banquet-events-summary')" class="no-underline">
                                        {{ __('Events') }}
                                    </x-dropdown-link>
                                @endif
                                @if (auth()->user()->employee->getModulePermission('Banquet Equipment Request') != 2 )
                                    <x-dropdown-link :href="url('/equipment-requests-summary')" class="no-underline">
                                        {{ __('Equipment Request') }}
                                    </x-dropdown-link>
                                @endif
                                @if (auth()->user()->employee->getModulePermission('Banquet Procurement') != 2 )
                                    <x-dropdown-link :href="url('/banquet-procurement-lists')" class="no-underline">
                                        {{ __('Budget Proposal') }}
                                    </x-dropdown-link>
                                @endif
                                 
                                    
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                {{-- Dropdown for Restaurants --}}
             @if (auth()->user()->employee->getGroupedModulePermissions('Restaurants') !=2)
                   <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" style="margin-top: 23px">
                       <x-dropdown>
                           <x-slot name="trigger">
                               <button
                                   class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                   <div>{{ __('Restaurants') }}</div>
                                   <div class="ms-1">
                                            {{-- <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg> --}}
                                        </div>
                                    </button>
                                </x-slot>
                            
                                <x-slot name="content">
                                    @if (auth()->user()->employee->getModulePermission('Recipe Management') !=2)
                                        <x-dropdown-link :href="url('/recipe-lists')" class="no-underline">
                                            {{ __('Recipe Lists') }}
                                        </x-dropdown-link>
                                    @endif
                                    <x-dropdown-link :href="url('/daily-recipe-count')" class="no-underline">
                                            {{ __('Daily Recipe Count') }}
                                        </x-dropdown-link>
                                    @if (auth()->user()->employee->getModulePermission('Menu') !=2)
                                         <x-dropdown-link :href="url('/order_menu')" class="no-underline">
                                        {{ __('Menu') }}
                                        </x-dropdown-link>
                                    @endif
                                   {{-- @if (auth()->user()->employee->getModulePermission('Allocate Order Menu') !=2)
                                       <x-dropdown-link :href="url('/allocate_order')" class="no-underline">
                                           {{ __('Allocate Order') }}
                                       </x-dropdown-link>
                                   @endif --}}
                                    @if (auth()->user()->employee->getModulePermission('Menu - Orders') !=2)
                                        <x-dropdown-link :href="url('/orders_lists')" class="no-underline">
                                            {{ __('Monitor') }}
                                        </x-dropdown-link>
                                    @endif
                                    @if (auth()->user()->employee->getModulePermission('Menu Order Invoicing') !=2)
                                        <x-dropdown-link :href="url('/invoicing')" class="no-underline">
                                            {{ __('Billing') }}
                                        </x-dropdown-link>
                                   @endif
                                     @if (auth()->user()->employee->getModulePermission('Cancelation of Orders') !=2)
                                        <x-dropdown-link :href="url('/restaurant-cancelation')" class="no-underline">
                                            {{ __('Cancellation of Orders') }}
                                        </x-dropdown-link>
                                   @endif
                                </x-slot>
                            </x-dropdown>
                        </div>
                @endif
              

                    {{-- Ken Entrance module --}}
                        @if (auth()->user()->employee->getGroupedModulePermissions('Entrance') !=2)
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex" style="margin-top: 23px">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button
                                    class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    <div>Entrance</div>
                                    <div class="ms-1">
                                        {{-- <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg> --}}
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">   
                                @if (auth()->user()->employee->getModulePermission('Gate Entrance') !=2)
                                    <x-dropdown-link :href="route('gate.entrance.page')" class="no-underline">
                                        Gates
                                        </x-dropdown-link>
                                    <x-dropdown-link :href="route('customers.page')" class="no-underline">
                                            Customer
                                    </x-dropdown-link>
                                @endif
                                
                               
                                @if (auth()->user()->employee->getModulePermission('Leisures') !=2)
                                    <x-dropdown-link :href="route('leisures.page')" class="no-underline">
                                        Leisures
                                    </x-dropdown-link>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif
                {{-- end Ken Entrance module --}}
            </div>

                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ auth()->user()->name }}</div>

                                <div class="ms-1">
                                    {{-- <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg> --}}
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
                                    </i>{{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                            @if (auth()->user()->employee->getModulePermission('Menu Order Invoicing') !=2 && auth()->user()->employee->hasOpenShift() == false)
                                        <x-dropdown-link :href="url('/make-open-shift')" class="no-underline">
                                            {{ __('Open Cashier Shift') }} &nbsp;<i class="bi bi-box-seam"></i>
                                        </x-dropdown-link>
                                   @endif
                             @if (auth()->user()->employee->hasOpenShift() && auth()->user()->employee->getModulePermission('Menu Order Invoicing') !=2)
                                        <x-dropdown-link :href="url('/close-cashier-shift?shift=' . auth()->user()->employee->id . '&referenced=current')" class="no-underline">
                                            {{ __('Close Cashier Shift') }} &nbsp;<i class="bi bi-file-break"></i>
                                        </x-dropdown-link>
                                   @endif
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
                <x-responsive-nav-link :href="route('dashboard')" class="no-underline" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                <!-- Responsive Master Data Menu -->
                @if (auth()->user()->employee->getGroupedModulePermissions('Master Data') !=2 
                ||auth()->user()->employee->getGroupedModulePermissions('Item Management') !=2 
                || auth()->user()->employee->getGroupedModulePermissions('Item Properties') !=2
                || auth()->user()->employee->getGroupedModulePermissions('Price Levels') !=2 
                || auth()->user()->employee->getGroupedModulePermissions('Restaurant Management') !=2
                || auth()->user()->employee->getGroupedModulePermissions('Business') !=2)
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full text-left px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-700 transition duration-150 ease-in-out">
                        {{ __('Tools') }}
                    </button>
                    <div x-show="open" class="space-y-1 pl-4">
                        @if(auth()->user()->employee->getModulePermission('Suppliers') !=2)
                            <x-responsive-nav-link :href="route('suppliers')" class="no-underline" :active="request()->routeIs('suppliers')">
                                {{ __('Supplier') }}
                            </x-responsive-nav-link>
                        @endif
                        @if (auth()->user()->employee->getModulePermission('Companies') !=2)
                            <x-responsive-nav-link :href="route('companies')" class="no-underline" :active="request()->routeIs('companies')">
                                {{ __('Company') }}
                            </x-responsive-nav-link>
                        @endif
                        @if (auth()->user()->employee->getModulePermission('Departments') !=2)
                            <x-responsive-nav-link :href="route('departments.index')" class="no-underline" :active="request()->routeIs('departments.index')">
                                {{ __('Departments') }}
                            </x-responsive-nav-link>
                        @endif
                        @if (auth()->user()->employee->getModulePermission('Branches') !=2)
                            <x-responsive-nav-link :href="route('branch.index')" class="no-underline" :active="request()->routeIs('branch.index')">
                                {{ __('Branches') }}
                            </x-responsive-nav-link>
                        @endif
                        @if (auth()->user()->employee->getModulePermission('User Access') !=2)
                            <x-responsive-nav-link :href="url('/user-access')" class="no-underline">
                                {{ __('User Access') }}
                            </x-responsive-nav-link>
                        @endif
                        @if (auth()->user()->employee->getModulePermission('User Registration') !=2)
                            <x-responsive-nav-link :href="route('register')" class="no-underline">
                                {{ __('User Registration') }}
                            </x-responsive-nav-link>
                        @endif
                        @if (auth()->user()->employee->getGroupedModulePermissions('Item Management') !=2 
                            || auth()->user()->employee->getGroupedModulePermissions('Item Properties') !=2
                            || auth()->user()->employee->getGroupedModulePermissions('Price Levels') !=2
                            || auth()->user()->employee->getGroupedModulePermissions('Restaurant Management') !=2
                            || auth()->user()->employee->getGroupedModulePermissions('Business') !=2)
                            <x-responsive-nav-link :href="url('/settings')" class="no-underline">
                                {{ __('Settings') }}
                            </x-responsive-nav-link>
                        @endif
                    </div>
                </div>
                @endif

                @if (auth()->user()->employee->getGroupedModulePermissions('Purchasing') !=2 )
                <!-- Responsive Purchasing Menu -->
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="w-full text-left px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-700 transition duration-150 ease-in-out">
                            {{ __('Purchasing') }}
                        </button>
                        <div x-show="open" class="space-y-1 pl-4">
                            @if (auth()->user()->employee->getModulePermission('Purchase Order') != 2 )
                                <x-responsive-nav-link :href="url('/purchase_order')" class="no-underline">
                                    {{ __('PO Summary') }}
                                </x-responsive-nav-link>
                            @endif
                            @if(auth()->user()->employee->getModulePermission('Purchase Receive') != 2 )
                                <x-responsive-nav-link :href="url('/receive_stock')" class="no-underline">
                                    {{ __('Receiving') }}
                                </x-responsive-nav-link>
                            @endif
                        </div>
                    </div>
                @endif


                <!-- Responsive Inventory Menu -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full text-left px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-700 transition duration-150 ease-in-out">
                        {{ __('Inventory') }}
                    </button>
                    <div x-show="open" class="space-y-1 pl-4">
                        <x-responsive-nav-link  class="no-underline"  class="no-underline" data-bs-toggle="modal" onclick="unhideModal()"
                        data-bs-target="#cardexModal" >
                            {{ __('Cardex') }}
                        </x-responsive-nav-link>
                        @if(auth()->user()->employee->getModulePermission('Back Orders') != 2 )
                            <x-responsive-nav-link :href="url('/back-orders')" class="no-underline" >
                                {{ __('Back Order') }}
                            </x-responsive-nav-link>
                        @endif
                        @if (auth()->user()->employee->getModulePermission('Item Withdrawal') != 2 )
                            <x-responsive-nav-link :href="url('/item_withdrawal')" class="no-underline">
                                {{ __('Item Withdrawal') }}
                            </x-responsive-nav-link>
                        @endif
                         @if (auth()->user()->employee->getModulePermission('Merchandise Inventory') != 2 )
                            <x-responsive-nav-link :href="url('/Merchandise-Inventory')" class="no-underline">
                                {{ __('Merchandise Inventory') }}
                            </x-responsive-nav-link>
                        @endif
                        @if (auth()->user()->employee->getModulePermission('Item Location') != 2 )
                            <x-responsive-nav-link :href="url('/item-location')" class="no-underline">
                                    {{ __('Item-Location') }}
                            </x-responsive-nav-link>
                        @endif
                    </div>
                </div>


                <!-- Responsive Validation Menu -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full text-left px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-700 transition duration-150 ease-in-out">
                        {{ __('Validations') }}
                    </button>
                    <div x-show="open" class="space-y-1 pl-4">
                        <x-responsive-nav-link :href="url('/review_request_list')" class="no-underline">
                            {{ __('P.O - Review') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="url('/approval_request_list')" class="no-underline">
                            {{ __('P.O - Approval') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="url('/withdrawal_review')" class="no-underline">
                            {{ __('Withdrawal - Review') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="url('/withdrawal_approval')" class="no-underline">
                            {{ __('Withdrawal - Approval') }}
                        </x-responsive-nav-link>
                    </div>
                </div>
                <!-- Responsive Entrance menu -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full text-left px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-700 transition duration-150 ease-in-out">
                        {{ __('Entrance') }}
                    </button>
                    <div x-show="open" class="space-y-1 pl-4">
                        <x-responsive-nav-link :href="route('gate.entrance.page')" class="no-underline">
                            {{ __('Gates') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('customers.page')" class="no-underline">
                            {{ __('Customers') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('leisures.page')" class="no-underline">
                            {{ __('Leisures') }}
                        </x-responsive-nav-link>
                    </div>
                </div>
            
                
                <x-responsive-nav-link :href="route('menus.create')" class="no-underline" :active="request()->routeIs('menus.create')">
                    {{ __('Create Menu') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="url('/menu_lists')" class="no-underline">
                    {{ __('Menu Lists') }}
                </x-responsive-nav-link>

            

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
        </div>
</nav>





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

    function unhideModal2() {
        // Show the modal after data is fetched
        const cardexModal = document.getElementById('cardexModalv2');
        cardexModal.removeAttribute('hidden');
        const modal = new bootstrap.Modal(cardexModal);
        modal.show();
    }

    function closeModal() {
            var modal = document.getElementById('getPONumberModal');
            var modalInstance = bootstrap.Modal.getInstance(modal);
            modalInstance.hide();
        }
</script>
