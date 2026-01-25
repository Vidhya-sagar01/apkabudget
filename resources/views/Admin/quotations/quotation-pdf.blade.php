<!DOCTYPE html>
<html>
<head>
    <title>Quotation - {{ $quotation->quotation_no }}</title>
    <style>
        .service-column {
            width: 60% !important;
            word-wrap: break-word;
            white-space: normal;
        }
        .cmp_name{
            font-size: 11px;
        }
        .header {
            background: #00a9ce;
            color: #fff;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .qutotation {
            font-size: 22px;
        }

        .badge {
            background-color: #f6c23e;
            padding: 5px 10px;
            border-radius: 7px;
            color: #000;
            font-size: 15px;
        }

        .header-left {
            width: 35% !important;
        }
        .qt_1{
            line-height:5px;
        }
        .qt_2{
            line-height:25px;
        }
        .qt_3{
            line-height:45px;
        }

        .header-right p {
            margin-top: -110px;
            
            font-size: 13px;
            text-align: right;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .container {
            padding: 20px;
        }

        .section {
            margin: 20px 0;
        }
        .customer, .company {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            font-size: 13px;
        }
        th {
            background: #00a9ce;
            color: #fff;
            text-align: left;
        }
        .total-row td {
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
        }
        .footer p {
            margin: 3px 0;
        }
        .quotation-details {
            margin-top: 30px;
            text-align: right;
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <!-- LEFT SIDE -->
        <div class="header-left">
            <h3 class="mb-0">
                <strong class="qutotation">Quotation</strong>  
                <span class="badge">Created</span>
            </h3>
            <!--<img src="data:image/png;base64,{{ base64_encode(public_path('assets/img/logo/apweblogo.png')) }}" style="height:63px; width:auto; margin-top:10px;border-radius:10px;">-->

            <img src="data:image/png;base64,{{ base64_encode(file_get_contents('https://api.apkabudget.com/assets/img/logo/apweblogo.png')) }}" 
                style="height:63px; width:auto; margin-top:10px; border-radius:10px;">
        </div>

        <!-- RIGHT SIDE -->
        <div class="header-right">
            <p class="qt_1">Quotation No: {{ $quotation->quotation_no }}</p>
            <p class="qt_2">Quotation Date: {{ $quotation->quotation_date }}</p>
            <p class="qt_3">Booking ID: {{ $quotation->order->booking_id }}</p>
        </div>
    </div>

    <!-- COMPANY & CUSTOMER INFO -->
    <div class="section">
        <div class="company">
            <p><strong>Quotation From:</strong><br>
                <b class="cmp_name">RBSR APKA BUDGET HOME SERVICES PVT. LTD.</b><br>
                <strong>Address: </strong> {{ $quotation->contact->address }}<br>
                <strong>Phone: </strong> {{ $quotation->contact->phone_number }}, {{ $quotation->contact->whatsapp_number }}<br>
                <strong>Email: </strong>{{ $quotation->contact->email }}
            </p>
        </div>
        <div class="customer">
            <p><strong>Quotation For:</strong><br>
            <b>{{ $quotation->user->name ?? 'NA' }}</b><br>
            <strong>Address: </strong>{{ $quotation->custom_address ?? $quotation->order->address->address }}<br>
            <strong>Phone: </strong>{{ $quotation->user->mobile_no }}<br>
            <strong>Email: </strong>{{ $quotation->user->email ?? 'NA' }}
            </p>
        </div>
    </div>

    <!-- ITEMS TABLE -->
    <table>
        <thead>
            <tr>
                <th>Sr.No</th>
                <th class="service-column">Service</th>
                <th>Unit</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
        @foreach($quotation->items as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td class="service-column">
                    <strong>{{ ucfirst($item->service_name) }}</strong></br></br>
                    <p>{{ $item->description }}</p>
                    
                </td>
                <td>{{ $item->quantity }} {{ $item->unit }}</td>
                <td>₹{{ number_format($item->rate, 2) }}</td>
                <td>₹{{ number_format($item->amount, 2) }}</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2">Total</td>
            <td>{{ $quotation->items->sum('quantity') }} unit</td>
            <td></td>
            <td>₹ {{ $grandTotal = $quotation->items->sum('amount') }}.00</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align:right;"><strong>Reductions</strong></td>
            <td>
                @if($quotation->discount_type === '%')
                    {{ $quotation->discount_value }} %
                @else
                    ₹{{ number_format($quotation->discount_value, 2) }}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align:right;"><strong>Total (INR)</strong></td>
            <td>₹{{ number_format($quotation->total_amount, 2) }}</td>
        </tr>
        </tbody>
    </table>

    <!-- ITEM DESCRIPTION -->
    <!--<p>{{ strip_tags($quotation->add_notes) }}</p>-->
    {!! $quotation->add_notes !!}

    <!-- BANK DETAILS -->
    <div class="footer">
        <p><strong>Company Bank Account Details:</strong></p>
        <p><b>NAME:</b> RBSR APKA BUDGET HOME SERVICES PVT. LTD.</p>
        <p><b>BANK:</b> YES BANK</p>
        <p><b>A/C No:</b> 001461900008102</p>
        <p><b>IFSC CODE:</b> YESB0000014</p>
        <p>For any enquiry, reach out via <a href="mailto:{{ $quotation->contact->email }}">{{ $quotation->contact->email }}</a>
            or call on <a style="color:#00a9ce;" href="tel:+91{{ $quotation->contact->phone_number }}">
                        +91 {{ $quotation->contact->phone_number }}
                    </a></p>
    </div>

</div>
</body>
</html>
