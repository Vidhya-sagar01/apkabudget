<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Invoice - {{ $booking->booking_id ?? 'N/A' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            /*padding: 30px;*/
            /*background: #f3f6f9;*/
            color: #2c3e50;
            font-size: 15px;
        }

        .invoice-container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 40px;
            border: 1px solid #dce3e8;
            border-radius: 8px;
            box-sizing: border-box;
        }


        .header-table,
        .info-table,
        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .header-table td {
            vertical-align: top;
        }

        .info-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .info-table td {
            vertical-align: top;
            padding-top: 8px;
            line-height: 1.6;
        }

        .services-table th,
        .services-table td {
            border: 1px solid #ccd6dd;
            padding: 10px;
            text-align: left;
        }

        .services-table th {
            background-color: #ecf4f7;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }

        .services-table tbody tr {
            page-break-inside: avoid;
            /* Prevent row splitting across pages */
        }

        .services-table tbody tr:nth-child(even) {
            background-color: #f9fcff;
        }

        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 12px;
            border-top: 2px solid #d1dce3;
            color: #2c3e50;
            page-break-inside: avoid;
        }

        .footer {
            text-align: center;
            font-size: 13px;
            color: #7f8c8d;
            margin-top: 50px;
            page-break-inside: avoid;
        }

        a {
            color: #3498db;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <table class="header-table">
            <tr>
                <td style="width: 50%;">
                    <img src="data:image/png;base64,{{ $logo }}" height="60" alt="Logo">
                </td>
                <td style="width: 50%; text-align: right; color: #555;">
                    <strong>Invoice : {{ $booking->booking_id ?? 'N/A' }}</strong><br>
                    Date: {{ \Carbon\Carbon::parse($booking->slot_date ?? now())->format('d M Y') }}
                </td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td style="width: 50%;">
                    <div class="info-title">Customer Details</div>
                    Name: {{ optional($booking->user)->name ?? 'N/A' }}<br>
                    Phone: {{ optional($booking->user)->mobile_no ?? 'N/A' }}<br>
                    Address: {{ optional($booking->address)->flat_no }}, {{ optional($booking->address)->address }}<br>
                    Landmark: {{ optional($booking->address)->landmark ?? 'N/A' }}
                </td>
                <td style="width: 50%;">
                    <div class="info-title">Booking Info</div>
                    Service: {{ optional($booking->subCategory)->name ?? 'N/A' }}<br>
                    Date: {{ \Carbon\Carbon::parse($booking->slot_date ?? now())->format('d-m-Y') }}<br>
                    @php
                        $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $booking->slot_start_time)->format('g:i A');
                        $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $booking->slot_end_time)->format('g:i A');
                    @endphp
                    Time: {{ $startTime }} - {{ $endTime }}<br>
                    Payment Method: {{ strtoupper($booking->payment_method ?? 'N/A') }}
                </td>
            </tr>
        </table>

        <table class="services-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Service</th>
                    <th>Qty</th>
                    <th>Unit Price (₹)</th>
                    <th>Total (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->orderItems ?? [] as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ $item->extra_service ? optional($item->priceList)->detail : optional($item->service)->service_name }}
                            <br>
                            <small><em>3 month warranty</em></small>
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit_price }}</td>
                        <td>{{ $item->total_price }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">Total Amount: ₹ {{ $total ?? '0' }}</div>

        <div class="footer" style="border-top: 1px solid #d1dce3; padding-top: 20px; margin-top: 50px;">
            <p style="margin: 0; font-size: 14px; color: #2c3e50;">
                Thank you for choosing us!
            </p>
            <p style="margin: 5px 0; font-size: 13px; color: #7f8c8d;">
                For queries or support, feel free to call or message us at:<br>
                <a href="tel:+919625979696" style="color: #3498db; font-weight: 500;">+91 96259 79696</a>
            </p>
            <p style="margin: 0; font-size: 12px; color: #95a5a6;">
                We're here to help – 7 days a week, 9AM to 9PM.
            </p>
        </div>

    </div>
</body>

</html>