@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Booking Details</h1>

<div class="row">
    {{-- Booking Info --}}
    <div class="col-md-6">
        <x-admin.card title="Booking Information">
            <strong>Booking ID:</strong> {{ $booking->booking_id }}<br>
            <strong>Status:</strong> 
            <span class="badge bg-{{ $booking->status === 'completed' ? 'success' : 'warning text-dark' }}">
                {{ ucfirst($booking->status) }}
            </span><br>
            <strong>Created At:</strong> {{ \Carbon\Carbon::parse($booking->created_at)->format('d M, Y h:i A') }}
        </x-admin.card>
    </div>

    {{-- Customer Info --}}
    <div class="col-md-6">
        <x-admin.card title="Customer Information">
            <strong>Name:</strong> {{ $booking->user->name ?? 'N/A' }}<br>
            <strong>Mobile:</strong> {{ $booking->user->mobile_no ?? 'N/A' }}
        </x-admin.card>
    </div>

    {{-- Address --}}
    <div class="col-md-6">
        <x-admin.card title="Service Location">
            <strong>Address:</strong> {{ $booking->address->address ?? 'N/A' }}<br>
            <strong>Landmark:</strong> {{ $booking->address->landmark ?? 'N/A' }}<br>
            <strong>Flat No:</strong> {{ $booking->address->flat_no ?? 'N/A' }}
        </x-admin.card>
    </div>

    {{-- Slot Details --}}
   <div class="col-md-6">
    <x-admin.card title="Booking Slot">
        <strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->slot_date)->format('d-m-Y') }}<br>
        <strong>Time:</strong> 
        {{ \Carbon\Carbon::parse($booking->slot_start_time)->format('g:i A') }} - 
        {{ \Carbon\Carbon::parse($booking->slot_end_time)->format('g:i A') }}
    </x-admin.card>
</div>

    {{-- Payment --}}
    <div class="col-md-6">
        <x-admin.card title="Payment Details">
            <strong>Method:</strong> {{ ucfirst($booking->payment_method) }}<br>
            <strong>Total Amount:</strong> ₹{{ $booking->total_price }}
        </x-admin.card>
    </div>

    {{-- Services --}}
    <div class="col-12">
        <x-admin.card title="Ordered Services">
            @foreach ($booking->orderItems as $item)
                <div class="border rounded p-3 mb-3 d-flex align-items-center justify-content-between bg-white">
                    <div>
                        <strong>Service:</strong> {{ $item->service->service_name }}<br>
                        <strong>Quantity:</strong> {{ $item->quantity }}<br>
                        <strong>Unit Price:</strong> ₹{{ $item->unit_price }}<br>
                        <strong>Total Price:</strong> ₹{{ $item->total_price }}
                    </div>
                    @if ($item->service->image)
                        <img src="{{ asset($item->service->image) }}" class="img-thumbnail" style="max-width: 100px;">
                    @endif
                </div>
            @endforeach
        </x-admin.card>
    </div>
</div>
@endsection
