@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Contact Us</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.update_contact_us') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone_number" class="form-control"
                value="{{ old('phone_number', $contact->phone_number ?? '') }}">
            @error('phone_number') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>WhatsApp Number</label>
            <input type="text" name="whatsapp_number" class="form-control"
                value="{{ old('whatsapp_number', $contact->whatsapp_number ?? '') }}">
            @error('whatsapp_number') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $contact->email ?? '') }}">
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-control">{{ old('address', $contact->address ?? '') }}</textarea>
            @error('address') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@endsection