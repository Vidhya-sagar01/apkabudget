@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">User Details</h1>

<div class="row">
    {{-- Subscription Info --}}
    <div class="col-md-6">
        <x-admin.card title="Security Plan">
            @if($securityPlan)
                <strong>Name:</strong> {{ $securityPlan->plan->name ?? 'N/A' }}<br>
                <strong>Price:</strong> ₹{{ $securityPlan->plan->price ?? 'N/A' }}<br>
                <strong>Start:</strong> {{ \Carbon\Carbon::parse($securityPlan->start_date)->format('d-m-Y') }}<br>
                <strong>End:</strong> {{ \Carbon\Carbon::parse($securityPlan->end_date)->format('d-m-Y') }}<br>
                <span class="badge badge-success">Active</span>
            @else
                <span class="badge badge-danger">Inactive</span>
            @endif
        </x-admin.card>
    </div>

    <div class="col-md-6">
        <x-admin.card title="Main Plan">
            @if($mainPlan)
                <strong>Name:</strong> {{ $mainPlan->plan->name ?? 'N/A' }}<br>
                <strong>Price:</strong> ₹{{ $mainPlan->plan->price ?? 'N/A' }}<br>
                <strong>Start:</strong> {{ \Carbon\Carbon::parse($mainPlan->start_date)->format('d-m-Y') }}<br>
                <strong>End:</strong> {{ \Carbon\Carbon::parse($mainPlan->end_date)->format('d-m-Y') }}<br>
                <span class="badge badge-success">Active</span>
            @else
                <span class="badge badge-danger">Inactive</span>
            @endif
        </x-admin.card>
    </div>

    {{-- Basic Info --}}
    <div class="col-md-6">
        <x-admin.card title="Basic Information">
            <strong>Name:</strong> {{ $details->name }}<br>
            <strong>Email:</strong> {{ $details->email }}<br>
            <strong>Mobile:</strong> {{ $details->mobile_no }}<br>
            <strong>Created At:</strong> {{ \Carbon\Carbon::parse($details->created_at)->format('d M, Y h:i A') }}<br>
            <strong>Password:</strong> {{ $details->temp_password }}
        </x-admin.card>
    </div>

    {{-- Location Info --}}
    <div class="col-md-6">
        <x-admin.card title="Location">
            <strong>Country:</strong> {{ $details->country->name ?? 'N/A' }}<br>
            <strong>State:</strong> {{ $details->state->name ?? 'N/A' }}<br>
            <strong>City:</strong> {{ $details->city->name ?? 'N/A' }}<br>
            <strong>Pincode:</strong> {{ $details->pincode }}<br>
            <strong>Address:</strong> {{ $details->address }}
        </x-admin.card>
    </div>

    {{-- Device Info --}}
    <div class="col-md-6">
        <x-admin.card title="Device Information">
            <strong>Type:</strong> {{ $details->device_type }}<br>
            <strong>Model:</strong> {{ $details->device_model }}
        </x-admin.card>
    </div>

    {{-- Login Info --}}
    <div class="col-md-6">
        <x-admin.card title="Login Details">
            <strong>Login At:</strong> {{ $details->login_at }}<br>
            <strong>Logout At:</strong> {{ $details->logout_at }}
        </x-admin.card>
    </div>

    {{-- Professional Info --}}
    <div class="col-md-6">
        <x-admin.card title="Professional Information">
            <strong>Category:</strong> {{ $details->category->category ?? 'N/A' }}<br>
            <strong>Experience:</strong> {{ $details->experience }} Years<br>
            <strong>Assigned Zones:</strong><br>
            @if ($details->zones->count())
                <ul>
                    @foreach ($details->zones as $zone)
                        <li>{{ $zone->name }}</li>
                    @endforeach
                </ul>
            @else
                No Zones Assigned
            @endif
        </x-admin.card>
    </div>

    <div class="col-md-6">
        <x-admin.card title="Identity">
            <strong>Type:</strong> {{ $details->identityType->identity ?? 'N/A' }}<br>
            <strong>Number:</strong> {{ $details->identity_number }}<br>
            @if ($details->identity_image)
                <img src="{{ asset($details->identity_image) }}" class="img-thumbnail mt-2" style="max-width: 150px;">
            @else
                No Image
            @endif
            @if ($details->identity_image_back)
                <img src="{{ asset($details->identity_image_back) }}" class="img-thumbnail mt-2" style="max-width: 150px;">
            @else
                No Image
            @endif
        </x-admin.card>
    </div>
    
</div>
@endsection
