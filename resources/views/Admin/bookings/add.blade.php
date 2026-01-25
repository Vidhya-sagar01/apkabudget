@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Create Booking</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.create_booking', ['userId' => $userId]) }}" method="POST" id="addForm">
                @csrf
                <input type="hidden" name="userId" value="{{ $userId }}">

                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Categories</label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="category_id_error"></small>
                    </div>

                    <!-- SubCategory Dropdown -->
                    <div class="form-group col-md-4">
                        <label>SubCategories</label>
                        <select id="subcategory_id" name="subcategory_id" class="form-control" required>
                            <option value="">-- Select SubCategory --</option>
                        </select>
                        <small class="text-danger" id="subcategory_id_error"></small>
                    </div>

                    <!-- Sub SubCategory Dropdown -->
                    <div class="form-group col-md-4">
                        <label>Sub SubCategories</label>
                        <select id="sub_subcategory_id" name="sub_subcategory_id" class="form-control" required>
                            <option value="">-- Select Sub SubCategory --</option>
                        </select>
                        <small class="text-danger" id="sub_subcategory_id_error"></small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Select Address</label>
                    <select id="address_id" name="address_id" class="form-control" required>
                        <option value="">-- Select Address --</option>
                        @foreach ($addresses as $address)
                            <option value="{{ $address->id }}">{{ $address->flat_no }}, {{ $address->address }}
                                ({{ $address->type == 1 ? 'Home' : 'Other' }})</option>
                        @endforeach
                    </select>
                    <small class="text-danger" id="address_id_error"></small>
                </div>
                <!-- Service Selection Section -->
                <div class="form-group">
                    <label>Services</label>
                    <div id="servicesContainer" class="row">
                        <p class="text-muted col-12">Select a category to view available services.</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <!-- Slot Selection Section -->
                    <div class="form-group col-md-3">
                        <label>Select Date</label>
                        <input type="date" id="slot_date" name="slot_date" class="form-control" required>
                        <small class="text-danger" id="date_error"></small>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Select Slot</label>
                        <select id="slot_time" name="slot_time" class="form-control" required>
                            <option value="">-- Select Slot --</option>
                        </select>
                        <small class="text-danger" id="slot_time_error"></small>
                    </div>
                   <div class="form-group col-md-3">
                        <label>Select Zone</label>
                        <select id="zone_id" name="zone_id" class="form-control" required>
                            <option value="">-- Select Zone --</option>
                            @foreach ($zones as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="zone_id_error"></small>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Select Provider</label>
                        <select id="provider_id" name="provider_id" class="form-control">
                            <option value="">-- Select Provider --</option>
                        </select>
                        <small class="text-danger" id="provider_id_error"></small>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Add Booking</button>
            </form>
        </div>
    </div>
@endsection
