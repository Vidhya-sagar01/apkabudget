@extends('Admin.layouts.app')

@section('content')
<style>
    .container{
        color: #000000 !important;
    }
    .table{
        color: #000000 !important;
    }
    thead {
        background-color: #0cc0df70 !important;
        color: #000000 !important;
    }
    .service-column {
        width: 60%;
        word-wrap: break-word;
        white-space: normal;
    }
</style>
<div class="container my-4">
    <!-- Header Buttons -->
    <div class="d-flex justify-content-end mb-3">
        <!-- <button class="btn btn-sm btn-secondary mr-2">Change Lead Status</button>  -->
        <!-- <button class="btn btn-sm btn-info mr-2" onclick="window.print()">Print</button> -->
        <a href="{{ route('admin.download-quotations', $quotation->id) }}" class="btn btn-sm btn-warning mr-2">Download</a>
        <!-- <button class="btn btn-sm btn-success mr-2">Email / WhatsApp</button>-->
    </div>

    <!-- Quotation Summary -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-start p-3">
                <!-- Left Side -->
                <div>
                    <h3 class="mb-0">
                       <strong>Quotation</strong>  <span class="badge badge-warning">Created</span>
                    </h3>
                    <img src="{{ asset('assets/img/logo/apweblogo.png') }}" alt="logo" style="height:70px; width:auto; margin-top:30px;border-radius:10px;">
                </div>

                <!-- Right Side -->
                <div class="text-right">
                    <small> <strong>Quotation No: {{ $quotation->quotation_no }}</strong></small>
                    <br>
                    <small><strong>Quotation Date: {{ $quotation->quotation_date }}</strong></small>
                    <br>
                    <small><strong>Booking Id: {{ $quotation->order->booking_id }}</strong></small>
                </div>
            </div>

        </div>
        <div class="card-body bg-white">
            <div class="row mb-3">
                <div class="col-md-7 d-flex align-items-center">
                    <div>
                        <strong>Quotation From: </strong><br>
                        <strong>RBSR APKA BUDGET HOME SERVICES PVT. LTD.</strong><br>
                        <strong>Address: </strong>{{ $quotation->contact->address }}<br>
                        <strong>Phone: </strong>{{ $quotation->contact->phone_number }}, {{ $quotation->contact->whatsapp_number }}<br>
                        <strong>Email: </strong>{{ $quotation->contact->email }}<br>
                    </div>
                </div>
                <div class="col-md-5 text-right">
                    <strong>Quotation For: {{ $quotation->user->name ?? 'NA' }}</strong><br>
                    <strong>Address: </strong>{{ $quotation->custom_address ?? $quotation->order->address->address }}<br>
                    <strong>Phone: </strong>{{ $quotation->user->mobile_no ?? 'NA' }}<br>
                    <strong>Email: </strong>{{ $quotation->user->email ?? 'NA' }}<br>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th class="service-column">Service</th>
                            <th>Unit</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $grandTotal = 0; 
                            $total_quantity = 0;
                         
                        @endphp

                        @foreach($quotation->items as $index => $item)
                         {{ $item->discount_value }}
                            {{ $item->discount_type }}
                            @php
                                $grandTotal += $item->amount;
                                $total_quantity += $item->quantity;

                            @endphp
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td class="service-column"><strong>{{ ucfirst($item->service_name) }}</strong>
                                    </br></br>
                                    <p>{{ $item->description }}</p>
                                </td>

                               
                                <td>{{ $item->quantity }} {{ $item->unit }}</td>
                                <td>₹{{ number_format($item->rate, 2) }}</td>
                                <td>₹{{ number_format($item->amount, 2) }}  </td>
                            </tr>
                        @endforeach

                        
                        <tr>
                            <td colspan="2"><strong>Total</strong></td>
                            <td><strong>{{ $total_quantity }} {{ $item->unit }}</strong></td>
                            <td></td>
                            <td><strong>₹ {{ $grandTotal = $quotation->items->sum('amount') }}.00</strong></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-right"><strong>Reductions</strong></td>
                            <td>
                                @if($quotation->discount_type === '%')
                                    {{ $quotation->discount_value }} %
                                @else
                                    ₹{{ number_format($quotation->discount_value, 2) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-right"><strong>Total (INR)</strong></td>
                            <td>₹{{ number_format($quotation->total_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Terms and Conditions -->
            <div class="mt-4">
                <!--<p>{{ strip_tags($quotation->add_notes) }}</p>-->
                {!! $quotation->add_notes !!}
                </br>
                <p><strong>Company Bank Account Details:</strong></p>
                <p>
                    <strong>NAME: </strong> RBSR APKA BUDGET HOME SERVICES PVT. LTD.<br>
                    <strong>BANK: </strong>YES BANK<br>
                    <strong>A/C No: </strong>001461900008102<br>
                    <strong>IFSC CODE:</strong> YESB0000014
                </p>
                <small>For any enquiry, reach out via 
                    <a href="mailto:{{ $quotation->contact->email }}">
                        {{ $quotation->contact->email }}
                    </a> 
                    or call on 
                    <a href="tel:+91{{ $quotation->contact->phone_number }}">
                        +91 {{ $quotation->contact->phone_number }}
                    </a>
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
