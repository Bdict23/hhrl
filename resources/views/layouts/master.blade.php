<x-app-layout>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laravel</title>
        {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> 
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>

    <style>
        .short-description {
            display: inline;
        }

        .full-description {
            display: none;
        }

        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 300px;
            height: 100vh;
            background: #fff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .sidebar h6 {
            font-weight: bold;
            margin-top: 20px;
        }

        .sidebar .nav-link {
            color: #333;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .sidebar .nav-link:hover {
            background-color: #f1f1f1;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
        }

        .store-table {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-add {
            background-color: #5cb85c;
            color: white;
            border-radius: 5px;
            padding: 10px 15px;
            border: none;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .dashboard {
            width: 90%;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }



        

        td {
            color: #555;
            font-size: small;
        }

        .action-btn {
            margin-right: 5px;
            padding: 5px 10px;
            background-color: #6c63ff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .action-btn:last-child {
            background-color: #ff4d4d;
        }

        .action-btn:hover {
            opacity: 0.9;
        }

        .button-group {
            display: flex;
            gap: 5px;
            /* Adjust spacing between buttons as needed */
        }


        .shadow-box {
            width: 200px;
            /* Adjust as needed */
            height: 100px;
            /* Adjust as needed */
            background-color: #f5f5f5;
            /* Placeholder background color */
            margin: 50px auto;
            position: relative;
            border: 1px solid #ddd;
            /* Optional border */
        }

        .shadow-box::after {
            content: "";
            position: absolute;
            bottom: -15px;
            /* Distance of shadow from the box */
            left: 50%;
            width: 70%;
            /* Width of the shadow */
            height: 20px;
            /* Height of the shadow */
            background: rgba(0, 0, 0, 0.2);
            /* Shadow color */
            border-radius: 50%;
            /* Curved effect */
            transform: translateX(-50%);
            z-index: -1;
            /* Make sure shadow is behind the box */
            filter: blur(8px);
            /* Optional: soften the shadow */
        }


        .tab-pane {
            padding: 20px;
            border-top: none;
        }

        /* .steps-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: bold;
        }

        .step.completed .circle {
            background-color: #4caf50;

            /* Green for completed steps */


        .step.in-progress .circle {
            background-color: #673ab7;
            /* Purple for in-progress steps */
        }

        .label {
            margin-top: 5px;
            font-size: 12px;
            color: #555;
        }

        .line {
            flex-grow: 1;
            height: 2px;
            background-color: #ddd;
        }

        .line.completed {
            background-color: #4caf50;
            /* Green for completed lines */
        } */


    </style>

    <x-slot name="header">
        @livewire('current-branch')
    </x-slot>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, { delay: { "show": 1000, "hide": 100 } });
        });
        });
    </script>

    <div class = 'container'>
        @yield('content')
    </div>
    @yield('script')
</x-app-layout>
