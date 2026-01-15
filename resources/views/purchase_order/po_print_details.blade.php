<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>&nbsp;</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #000;
            font-size: 12px;
            /* Smaller global font size */
        }


        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 4px;
            /* Reduced padding for a compact table */
            text-align: center;
            font-size: 10px;
            /* Smaller font size for table content */
        }

        /* Light grey for TOTAL row */
        .total-row {
            background-color: #d3d3d3;
            /* Light grey */
            font-weight: bold;
        }

        .header,
        .footer {
            text-align: center;
            margin-top: 20px;
        }

        .footer {
            margin-top: 40px;
        }

        .title {
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }

        /* Underline for labels */
        .label {
            text-decoration: underline;
            font-weight: bold;
            font-size: 12px;
            /* Slightly larger font size for labels */
        }

        /* Address styling */
        .address {
            display: block;
            margin-top: 5px;
            text-align: center;
        }

        /* Hide print button during printing */
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

    <!-- PRINT BUTTON -->
    <div class="no-print">
        <button onclick="history.back()" class="btn btn-secondary" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor"
                class="bi bi-reply-fill" viewBox="0 0 16 16">
                <path
                    d="M5.921 11.9 1.353 8.62a.72.72 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z" />
            </svg> Back
        </button>
        <button class="btn btn-primary" onclick="updateRequisitionStatus()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-printer" viewBox="0 0 16 16">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                <path
                    d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
            </svg> Print
        </button>
    </div>

    <!-- HEADER SECTION -->
    <div class="title mt-4">
        <div class="header">
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
                <img src="{{ asset('images/' . auth()->user()->branch->company->company_logo) }}" alt="Branch Logo" style="max-height: 50px;">
                <h5 style="margin: 0;">{{ $requestInfo->branches->branch_name }}</h5>
            </div>
        </div>
        <span class="address">{{ $requestInfo->branches->branch_address }}</span>
    </div>
    <div>SUPPLIER NAME: <span class="label">{{ $requestInfo->supplier->supp_name }}</span></div>
    <div>MERCHANDISE PO #: <span class="label">{{ $requestInfo->merchandise_po_number }}</span></div>
    <div>ORDER NO.: <span class="label">{{ $requestInfo->requisition_number }}</span></div>
    <div>ORDER DATE: <span class="label">{{ $requestInfo->trans_date }}</span></div>

    <!-- TABLE SECTION -->
    <table>
        <thead>
            <tr>
                <th>NO.</th>
                <th>PARTNUMBER</th>
                <th>DESCRIPTION</th>
                <th>TTL ORDER</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requestInfo->requisitionDetails as $reqdetail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $reqdetail->items->item_code }}</td>
                    <td>{{ $reqdetail->items->item_description }}</td>
                    <td>{{ $reqdetail->qty }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">TOTAL</td>
                <td>{{ $requestInfo->requisitionDetails->sum('qty') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- NOTICE SECTION -->
    <div class="title mt-4">
        THIS ORDER IS VALID ONLY FOR 45 DAYS. PLEASE CANCEL ANY UNSERVE ITEMS AFTER THE VALIDITY PERIOD.
    </div>

    <!-- FOOTER SECTION -->
    <div class="footer">
        <span style="text-decoration-line: underline">{{ $requestInfo->preparer->name }}
            {{ $requestInfo->preparer->last_name }}</span>&emsp;&emsp;&emsp;&emsp;
            @if ($requestInfo->reviewer)
                <span style="text-decoration-line: underline">{{ $requestInfo->reviewer->name }}
                {{ $requestInfo->reviewer->last_name }}</span>&emsp;&emsp;&emsp;&emsp;
            @endif
        <span style="text-decoration-line: underline">{{ $requestInfo->approver->name }} {{ $requestInfo->approver->middle_name }}
            {{ $requestInfo->approver->last_name }}</span>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateRequisitionStatus() {
            fetch('/po_printed/' + '{{ $requestInfo->id }}').then(response => {
                if (response.ok) {
                    window.print();
                } else {
                    alert('Failed to update requisition status.');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('An error occurred.');
            });
        }
    </script>
</body>

</html>
