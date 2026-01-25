@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Subadmins</h1>

<div class="card shadow-sm border-0 rounded-lg">
    <div class="card-body p-5">
        <form action="{{ route('admin.edit_subadmins', ['id' => $admin->id]) }}" method="POST"
            enctype="multipart/form-data" id="addForm">
            @csrf

            <div class="row mb-4">
                {{-- BASIC INFO --}}
                <div class="col-md-12 mt-3">
                    <h5 class="text-primary fw-bold">Basic Information</h5>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control shadow-sm" value="{{ $admin->name }}" required>
                    <small id="name_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control shadow-sm" value="{{ $admin->email }}"
                        required>
                    <small id="email_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Mobile No.(Personal)</label>
                    <input type="text" name="mobile_no" class="form-control shadow-sm" value="{{ $admin->mobile_no }}"
                        required>
                    <small id="mobile_no_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Mobile No. (Official)</label>
                    <input type="text" name="mobile_official" class="form-control shadow-sm"
                        value="{{ $admin->mobile_official }}" required>
                    <small id="mobile_official_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Mobile No. (Emergency)</label>
                    <input type="text" name="mobile_emergency" class="form-control shadow-sm"
                        value="{{ $admin->mobile_emergency }}" required>
                    <small id="mobile_emergency_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Password <small class="text-muted">(Leave blank to keep
                            current)</small></label>
                    <input type="text" name="password" class="form-control shadow-sm">
                </div>
                <div class="col-md-6 mt-3">
                    <label for="image" class="form-label">Profile Image</label>
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
                {{-- CATEGORY --}}
                <div class="col-md-6 mt-3">
                    <label class="form-label">Employee Category</label>
                    <select name="category" class="form-control shadow-sm" required>
                        <option value="">Select Category</option>
                        <option value="IT" {{ $admin->category == 'IT' ? 'selected' : '' }}>IT</option>
                        <option value="HR" {{ $admin->category == 'HR' ? 'selected' : '' }}>HR</option>
                        <option value="Calling" {{ $admin->category == 'Calling' ? 'selected' : '' }}>Calling</option>
                        <option value="Field" {{ $admin->category == 'Field' ? 'selected' : '' }}>Field</option>
                        <option value="Support" {{ $admin->category == 'Support' ? 'selected' : '' }}>Support</option>
                        <!-- Add more if needed -->
                    </select>
                    <small id="category_error" class="text-danger"></small>
                </div>
                {{-- PERMISSIONS --}}
                <div class="col-md-12 mt-4">
                    <label class="form-label">Permissions</label>

                    @php
                    $permissions = json_decode($admin->permissions ?? '[]', true);
                    @endphp

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="dashboard" {{
                            in_array('dashboard', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Dashboard</label>
                    </div>

                    <hr>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="users" {{
                            in_array('users', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Users</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="providers" {{
                            in_array('providers', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Service Providers</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="providers" {{
                            in_array('providers_action', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Providers Plan/Security/Block</label>
                    </div>

                    <hr>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="categories" {{
                            in_array('categories', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Services</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="banners" {{
                            in_array('banners', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Banners</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="service_videos" {{
                            in_array('service_videos', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Service Videos</label>
                    </div>

                    <hr>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="about_us" {{
                            in_array('about_us', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">About Us Page</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="privacy_policy" {{
                            in_array('privacy_policy', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Privacy Policy Page</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="contact_us" {{
                            in_array('contact_us', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Contact Us Page</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="terms" {{
                            in_array('terms', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Terms & Conditions Page</label>
                    </div>

                    <hr>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="all_bookings" {{
                            in_array('all_bookings', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Bookings</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="complaints" {{
                            in_array('complaints', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Complaints</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="transaction" {{
                            in_array('transaction', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Transactions</label>
                    </div>

                    <hr>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="zones" {{
                            in_array('zones', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Zones</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="countries" {{
                            in_array('countries', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">GeoZones</label>
                    </div>

                    <hr>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="subadmins" {{
                            in_array('subadmins', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Subadmins</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="rating_review" {{
                            in_array('rating_review', $permissions) ? 'checked' : '' }}>
                        <label class="form-check-label">Rating & Review</label>
                    </div>
                </div>

                <hr class="my-4 w-100">

                {{-- DOCUMENTS --}}
                <div class="col-md-12 mt-3">
                    <h5 class="text-primary fw-bold">Documents</h5>
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">Aadhaar Card (Front)</label>
                    <input type="file" name="aadhaar_front" class="form-control shadow-sm">
                    <small id="aadhaar_front_error" class="text-danger"></small>
                    @if ($admin->aadhaar_front)
                    <a href="{{ asset($admin->aadhaar_front) }}" target="_blank">View Old</a>
                    @endif

                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">Aadhaar Card (Back)</label>
                    <input type="file" name="aadhaar_back" class="form-control shadow-sm">
                    <small id="aadhaar_back_error" class="text-danger"></small>
                    @if ($admin->aadhaar_back)
                    <a href="{{ asset($admin->aadhaar_back) }}" target="_blank">View Old</a>
                    @endif
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">PAN Card</label>
                    <input type="file" name="pan_card" class="form-control shadow-sm">
                    <small id="pan_card_error" class="text-danger"></small>
                    @if ($admin->pan_card)
                    <a href="{{ asset($admin->pan_card) }}" target="_blank">View Old</a>
                    @endif
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">10th Marksheet</label>
                    <input type="file" name="marksheet_10" class="form-control shadow-sm">
                    <small id="marksheet_10_error" class="text-danger"></small>
                    @if ($admin->marksheet_10)
                    <a href="{{ asset($admin->marksheet_10) }}" target="_blank">View Old</a>
                    @endif
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">12th Marksheet</label>
                    <input type="file" name="marksheet_12" class="form-control shadow-sm">
                    <small id="marksheet_12_error" class="text-danger"></small>
                    @if ($admin->marksheet_12)
                    <a href="{{ asset($admin->marksheet_12) }}" target="_blank">View Old</a>
                    @endif
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">Experience Letter</label>
                    <input type="file" name="experience_letter" class="form-control shadow-sm">
                    <small id="experience_letter_error" class="text-danger"></small>
                    @if ($admin->experience_letter)
                    <a href="{{ asset($admin->experience_letter) }}" target="_blank">View Old</a>
                    @endif
                </div>

                <hr class="my-4 w-100">

                {{-- BANK DETAILS --}}
                <div class="col-md-12 mt-3">
                    <h5 class="text-primary fw-bold">Bank Details</h5>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Bank Name</label>
                    <input type="text" name="bank_name" class="form-control shadow-sm" value="{{ $admin->bank_name }}">
                    <small id="bank_name_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Account Holder Name</label>
                    <input type="text" name="account_holder" class="form-control shadow-sm"
                        value="{{ $admin->account_holder }}">
                    <small id="account_holder_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Account Number</label>
                    <input type="text" name="account_number" class="form-control shadow-sm"
                        value="{{ $admin->account_number }}">
                    <small id="account_number_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">IFSC Code</label>
                    <input type="text" name="ifsc_code" class="form-control shadow-sm" value="{{ $admin->ifsc_code }}">
                    <small id="ifsc_code_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Branch Name</label>
                    <input type="text" name="branch_name" class="form-control shadow-sm"
                        value="{{ $admin->branch_name }}">
                    <small id="branch_name_error" class="text-danger"></small>
                </div>


                {{-- SALARY DETAILS --}}
                <div class="col-md-12 mt-4">
                    <h5 class="text-primary fw-bold">Salary Details</h5>
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">Salary Amount (₹)</label>
                    <input type="number" name="salary_amount" class="form-control shadow-sm"
                        value="{{ $admin->salary_amount }}" required>
                    <small id="salary_amount_error" class="text-danger"></small>
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">Salary Type</label>
                    <select name="salary_type" class="form-control shadow-sm" required>
                        <option value="">Select Type</option>
                        <option value="Fixed" {{ $admin->salary_type == "Fixed" ? 'selected' : '' }}>Fixed</option>
                        <option value="Variable" {{ $admin->salary_type == "Variable" ? 'selected' : '' }}>Variable
                        </option>
                        <option value="Commission" {{ $admin->salary_type == "Commission" ? 'selected' : ''
                            }}>Commission
                            Based</option>
                    </select>
                    <small id="salary_type_error" class="text-danger"></small>
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">Date of Joining</label>
                    <input type="date" name="joining_date" class="form-control shadow-sm"
                        value="{{ $admin->joining_date }}" required>
                    <small id="joining_date_error" class="text-danger"></small>
                </div>
                <div class="col-md-12 mt-4">
                        <h5 class="text-primary fw-bold">Weekly Office Timing</h5>
                    </div>

                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Day</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Lunch Start</th>
                                        <th>Lunch End</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                        @php
                                            $timing = $timings[$day] ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $day }}</td>
                                            <td>
                                                <input type="time" name="timings[{{ $day }}][start]" class="form-control"
                                                    value="{{ $timing['start_time'] ?? '10:00' }}" required>
                                            </td>
                                            <td>
                                                <input type="time" name="timings[{{ $day }}][end]" class="form-control"
                                                    value="{{ $timing['end_time'] ?? '18:30' }}" required>
                                            </td>
                                            <td>
                                                <input type="time" name="timings[{{ $day }}][lunch_start]" class="form-control"
                                                    value="{{ $timing['lunch_start'] ?? '13:00' }}">
                                            </td>
                                            <td>
                                                <input type="time" name="timings[{{ $day }}][lunch_end]" class="form-control"
                                                    value="{{ $timing['lunch_end'] ?? '13:30' }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-md px-5">Update Subadmin</button>
            </div>
        </form>
    </div>
</div>
@endsection