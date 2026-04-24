<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>&nbsp;</title>
     @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <style>
        .signaroty-name {
            font-weight: bold;
            text-decoration: underline;
        }
         
        .head {
            text-align: center;
            color: #ffffff;
            background-color: #272727;
            padding: 10px;
        }
            .thead {
                background-color: #b4faae;
                font-weight: bold;
                text-align: center;
            }
        .t-sm {
            font-size: 0.90rem;
        }
        .address {
            display: block;
            margin-top: 5px;
            text-align: center;
        }
         @media print {
            .no-print {
                display: none !important;
                visibility: hidden !important;
            }

            * {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                margin: 0;
                size: auto;
            }

            html, body {
                margin-left: 8pt;
                margin-right: 8pt;
                padding: 0;
            }
        }
    </style>
    <!-- PRINT BUTTON -->
    <div class="no-print d-flex justify-content-start gap-3 p-3">
        <button onclick="history.back()" class="btn btn-secondary" type="button">
            <i class="bi bi-reply-fill"></i> Back
        </button>
        <button class="btn btn-primary" onclick="executePrinting()">
            <i class="bi bi-printer"></i> Print
        </button>
        <button class="btn btn-success" wire:click="exportToPdf()">
            <i class="bi bi-file-pdf"></i> Export to PDF
        </button>
    </div>
    @livewire('banquet.printing.print-b-e-o')
    @livewireScripts
</body>

<script>
        function executePrinting() {
            // Suppress browser default headers and footers
            if (window.print) {
                setTimeout(() => {
                    window.print();
                }, 100);
            }
        }
    </script>
</html>