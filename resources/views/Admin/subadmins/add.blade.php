@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Subadmins</h1>

<div class="card shadow-sm border-0 rounded-lg">
    <div class="card-body p-5">
        <form action="{{ route('admin.add_subadmins') }}" method="POST" enctype="multipart/form-data" id="addForm">
            @csrf

            <div class="row mb-4">
                {{-- BASIC INFO --}}
                <div class="col-md-12 mt-3">
                    <h5 class="text-primary fw-bold">Basic Information</h5>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control shadow-sm" required>
                    <small id="name_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control shadow-sm" required>
                    <small id="email_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Mobile No.(Personal)</label>
                    <input type="text" name="mobile_no" class="form-control shadow-sm" required>
                    <small id="mobile_no_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Mobile No. (Official)</label>
                    <input type="text" name="mobile_official" class="form-control shadow-sm" required>
                    <small id="mobile_official_error" class="text-danger"></small>
                </div>
                 <div class="col-md-6 mt-3">
                    <label class="form-label">Mobile No. (Emergency)</label>
                    <input type="text" name="mobile_emergency" class="form-control shadow-sm" required>
                    <small id="mobile_emergency_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Password</label>
                    <input type="text" name="password" class="form-control shadow-sm" required>
                    <small id="password_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label for="image" class="form-label">Profile Image</label>
                    <input type="file" name="image" class="form-control shadow-sm" id="image">
                    <small id="image_error" class="text-danger"></small>
                </div>

                {{-- CATEGORY --}}
                <div class="col-md-6 mt-3">
                    <label class="form-label">Employee Category</label>
                    <select name="category" class="form-control shadow-sm" required>
                        <option value="">Select Category</option>
                        <option value="IT">IT</option>
                        <option value="HR">HR</option>
                        <option value="Calling">Calling</option>
                        <option value="Field">Field</option>
                        <option value="Support">Support</option>
                        <!-- Add more if needed -->
                    </select>
                    <small id="category_error" class="text-danger"></small>
                </div>
                {{-- PERMISSIONS --}}
                <div class="col-md-12 mt-4">
                    <label class="form-label">Permissions</label>

                    <!--<div class="form-check">-->
                    <!--    <input class="form-check-input" type="checkbox" name="permissions[]" value="dashboard"-->
                    <!--        {{ in_array('dashboard', old('permissions', $admin->permissions ?? [])) ? 'checked' : '' }}>-->
                    <!--    <label class="form-check-label">Dashboard</label>-->
                    <!--</div>-->

                    <!-- User Management -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="users" {{
                            in_array('users', old('permissions', $admin->permissions ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label">Users</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="providers" {{
                            in_array('providers', old('permissions', $admin->permissions ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label">Service Providers</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="providers" {{
                            in_array('providers_action', old('permissions', $admin->permissions ?? [])) ? 'checked' : ''
                        }}>
                        <label class="form-check-label">Providers Plan/Security/Block</label>
                    </div>


                    <!-- Services & Media -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="categories" {{
                            in_array('categories', old('permissions', $admin->permissions ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label">Services</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="banners" {{
                            in_array('banners', old('permissions', $admin->permissions ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label">Banners</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="service_videos" {{
                            in_array('service_videos', old('permissions', $admin->permissions ?? [])) ? 'checked' : ''
                        }}>
                        <label class="form-check-label">Service Videos</label>
                    </div>

                    <!-- CMS Pages -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="about_us" {{
                            in_array('about_us', old('permissions', $admin->permissions ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label">About Us</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="privacy_policy" {{
                            in_array('privacy_policy', old('permissions', $admin->permissions ?? [])) ? 'checked' : ''
                        }}>
                        <label class="form-check-label">Privacy Policy</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="contact_us" {{
                            in_array('contact_us', old('permissions', $admin->permissions ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label">Contact Us</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="terms" {{
                            in_array('terms', old('permissions', $admin->permissions ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label">Terms & Conditions</label>
                    </div>

                    <!-- Operations -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="bookings" {{
                            in_array('bookings', old('permissions', $admin->permissions ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label">Bookings</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="complaints" {{
                            in_array('complaints', old('permissions', $admin->permissions ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label">Complaints</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="transactions" {{
                            in_array('transactions', old('permissions', $admin->permissions ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label">Transactions</label>
                    </div>

                    <!-- Location Settings -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="zones" {{
                            in_array('zones', old('permissions', $admin->permissions ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label">Zones</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="countries" {{
                            in_array('countries', old('permissions', $admin->permissions ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label">GeoZone</label>
                    </div>

                    <!-- Subadmins -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="subadmins" {{
                            in_array('subadmins', old('permissions', $admin->permissions ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label">Subadmins</label>
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
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">Aadhaar Card (Back)</label>
                    <input type="file" name="aadhaar_back" class="form-control shadow-sm">
                    <small id="aadhaar_back_error" class="text-danger"></small>
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">PAN Card</label>
                    <input type="file" name="pan_card" class="form-control shadow-sm">
                    <small id="pan_card_error" class="text-danger"></small>
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">10th Marksheet</label>
                    <input type="file" name="marksheet_10" class="form-control shadow-sm">
                    <small id="marksheet_10_error" class="text-danger"></small>
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">12th Marksheet</label>
                    <input type="file" name="marksheet_12" class="form-control shadow-sm">
                    <small id="marksheet_12_error" class="text-danger"></small>
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">Experience Letter</label>
                    <input type="file" name="experience_letter" class="form-control shadow-sm">
                    <small id="experience_letter_error" class="text-danger"></small>
                </div>

                <hr class="my-4 w-100">

                {{-- BANK DETAILS --}}
                <div class="col-md-12 mt-3">
                    <h5 class="text-primary fw-bold">Bank Details</h5>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Bank Name</label>
                    <input type="text" name="bank_name" class="form-control shadow-sm">
                    <small id="bank_name_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Account Holder Name</label>
                    <input type="text" name="account_holder" class="form-control shadow-sm">
                    <small id="account_holder_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Account Number</label>
                    <input type="text" name="account_number" class="form-control shadow-sm">
                    <small id="account_number_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">IFSC Code</label>
                    <input type="text" name="ifsc_code" class="form-control shadow-sm">
                    <small id="ifsc_code_error" class="text-danger"></small>
                </div>
                <div class="col-md-6 mt-3">
                    <label class="form-label">Branch Name</label>
                    <input type="text" name="branch_name" class="form-control shadow-sm">
                    <small id="branch_name_error" class="text-danger"></small>
                </div>


                {{-- SALARY DETAILS --}}
                <div class="col-md-12 mt-4">
                    <h5 class="text-primary fw-bold">Salary Details</h5>
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">Salary Amount (₹)</label>
                    <input type="number" name="salary_amount" class="form-control shadow-sm" required>
                    <small id="salary_amount_error" class="text-danger"></small>
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">Salary Type</label>
                    <select name="salary_type" class="form-control shadow-sm" required>
                        <option value="">Select Type</option>
                        <option value="Fixed">Fixed</option>
                        <option value="Variable">Variable</option>
                        <option value="Commission">Commission Based</option>
                    </select>
                    <small id="salary_type_error" class="text-danger"></small>
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">Date of Joining</label>
                    <input type="date" name="joining_date" class="form-control shadow-sm" required>
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
                                        <tr>
                                            <td>{{ $day }}</td>
                                            <td>
                                                <input type="time" name="timings[{{ $day }}][start]" class="form-control"
                                                    value="10:00" required>
                                            </td>
                                            <td>
                                                <input type="time" name="timings[{{ $day }}][end]" class="form-control"
                                                    value="18:30" required>
                                            </td>
                                            <td>
                                                <input type="time" name="timings[{{ $day }}][lunch_start]" class="form-control"
                                                    value="13:00">
                                            </td>
                                            <td>
                                                <input type="time" name="timings[{{ $day }}][lunch_end]" class="form-control"
                                                    value="13:30">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-md px-5">Add Subadmin</button>
            </div>
        </form>
    </div>
</div>
@endsection