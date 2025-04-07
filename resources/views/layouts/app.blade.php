<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="font-sans antialiased">

    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            <div class="modal fade" id="cardexModalv2" tabindex="-1" aria-labelledby="cardexModalLabel" aria-hidden="true" hidden>
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cardexModalLabel">Cardex Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @livewire('inventory.cardex')
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="cardexModal" tabindex="-1" aria-labelledby="cardexModalLabel" aria-hidden="true"
    hidden>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cardexModalLabel">Cardex Details</h5>
                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
            </div>
            <div class="modal-body">
                @livewire('inventory.cardex')

                {{-- <form id="cardexForm">
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
                </table> --}}
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
            </div>
        </div>
    </div>
</div>
            {{ $slot }}
        </main>
    </div>

    <!-- Cardex Modal -->



</body>

</html>
