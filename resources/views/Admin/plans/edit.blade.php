@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Edit Plan</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form action="{{ route('admin.edit_plans', ['category_id' => $category_id, 'id' => $plan->id]) }}" method="POST"
                enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Plan Name</label>
                        <input type="text" name="name" class="form-control shadow-sm" placeholder="Enter Plan Name"
                            value="{{ $plan->name }}">
                        <small class="text-danger" id="name_error"></small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Price</label>
                        <input type="number" name="price" class="form-control shadow-sm" placeholder="Enter Price"
                            value="{{ $plan->price }}">
                        <small class="text-danger" id="price_error"></small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Duration (in days)</label>
                        <input type="number" name="duration" class="form-control shadow-sm" placeholder="Enter Duration"
                            value="{{ $plan->duration }}">
                        <small class="text-danger" id="duration_error"></small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Leads</label>
                        <input type="number" name="leads" class="form-control shadow-sm" placeholder="Enter Leads"
                            value="{{ $plan->leads }}">
                        <small class="text-danger" id="leads_error"></small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-control shadow-sm" id="type">
                            <option value="">Select Type</option>
                            <option value="1" {{ $plan->type == 1 ? 'selected' : '' }}>Subscription Plan</option>
                            <option value="2" {{ $plan->type == 2 ? 'selected' : '' }}>Security Plan</option>
                        </select>
                        <small class="text-danger" id="type_error"></small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Plan Size</label>
                        <select name="plan_size" class="form-control shadow-sm" id="plan_size"
                            {{ $plan->type == 2 ? 'disabled' : '' }}>
                            <option value="">Select Size</option>
                            <option value="1" {{ $plan->plan_size == 1 ? 'selected' : '' }}>Large</option>
                            <option value="2" {{ $plan->plan_size == 2 ? 'selected' : '' }}>Small</option>
                        </select>
                        <small class="text-danger" id="plan_size_error"></small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Features</label>
                    <textarea name="features" class="form-control shadow-sm" rows="4"
                        placeholder="Enter Features">{{ $plan->features }}</textarea>
                    <small class="text-danger" id="features_error"></small>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Update Plan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeField = document.querySelector('#type');
            const planSizeField = document.querySelector('#plan_size');

            function togglePlanSize() {
                if (typeField.value === '2') {
                    planSizeField.value = '0';
                    planSizeField.disabled = true;
                } else {
                    planSizeField.disabled = false;
                    if (planSizeField.value == 0) {
                        planSizeField.value = '';
                    }
                }
            }

            togglePlanSize(); // run on page load
            typeField.addEventListener('change', togglePlanSize);
        });
    </script>
@endsection
