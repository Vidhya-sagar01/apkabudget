@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Subadmins</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form action="{{ route('admin.edit_subadmins', ['id' => $admin->id]) }}" method="POST"
                enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control shadow-sm" value="{{ $admin->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control shadow-sm" value="{{ $admin->email }}"
                            required>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Mobile No.</label>
                        <input type="text" name="mobile_no" class="form-control shadow-sm" value="{{ $admin->mobile_no }}"
                            required>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Password <small class="text-muted">(Leave blank to keep
                                current)</small></label>
                        <input type="text" name="password" class="form-control shadow-sm">
                    </div>
                    <div class="col-md-6">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" name="image" class="form-control shadow-sm" id="image">
                        <small id="image_error" class="text-danger"></small>
                        <div class="mt-3">
                            <p>Current Image:</p>
                            <img id="currentImage" src="{{ asset($admin->image) }}" alt="Subadmin Image"
                                style="width:150px; height:auto; border-radius:8px;">
                        </div>
                        <img id="imagePreview" src="#" alt="Image Preview"
                            style="display:none; width:150px; margin-top:10px;" />
                    </div>
                    <div class="col-md-12 mt-4">
        <label class="form-label">Permissions</label>

        @php
            $permissions = json_decode($admin->permissions ?? '[]', true);
        @endphp

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="permissions[]" value="users"
                {{ in_array('users', $permissions) ? 'checked' : '' }}>
            <label class="form-check-label">View Users</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="permissions[]" value="providers"
                {{ in_array('providers', $permissions) ? 'checked' : '' }}>
            <label class="form-check-label">View Providers</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="permissions[]" value="bookings"
                {{ in_array('bookings', $permissions) ? 'checked' : '' }}>
            <label class="form-check-label">View Bookings</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="permissions[]" value="service_videos"
                {{ in_array('service_videos', $permissions) ? 'checked' : '' }}>
            <label class="form-check-label">View Service Videos</label>
        </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Update Subadmin</button>
                </div>
            </form>
        </div>
    </div>
@endsection