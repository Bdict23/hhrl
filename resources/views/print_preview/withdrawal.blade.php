<!DOCTYPE html>
<html>
<head>
    <title>.</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
       @livewireStyles
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        h2 {
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        .flex-container {
            display: flex;
            justify-content: space-between;
        }
        .box-table {
            width: 48%;
            border-collapse: collapse;
        }
        .box-table th, .box-table td {
            padding: 8px;
            vertical-align: top;
        }
        .box-table th {
            text-align: left;
        }
        .section-title {
            font-weight: bold;
            background-color: #ccc;
            padding: 6px;
            margin-top: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
           
            padding: 8px;
            vertical-align: top;
        }
        
        .signature {
            margin-top: 30px;
        }
        .signature td {
            height: 50px;
            vertical-align: bottom;
            border: none;
        }
        .small-note {
            font-size: 11px;
            margin-top: 20px;
            text-align: justify;
        }

        @media print {
            .no-print {
                display: none;
            }

            * {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        
    </style>
</head>
<body>

    @livewire('print-preview.withdrawal')
    
    @livewireScripts
</body>
</html>

