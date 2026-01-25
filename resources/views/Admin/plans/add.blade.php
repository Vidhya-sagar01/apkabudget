@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Plans</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form action="{{ route('admin.add_plans', ['category_id' => $category_id]) }}" method="POST"
                enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Plan Name</label>
                        <input type="text" name="name" class="form-control shadow-sm" placeholder="Enter Plan Name">
                        <small class="text-danger" id="name_error"></small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Price</label>
                        <input type="number" name="price" class="form-control shadow-sm" placeholder="Enter Price">
                        <small class="text-danger" id="price_error"></small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Duration (in days)</label>
                        <input type="number" name="duration" class="form-control shadow-sm" placeholder="Enter Duration">
                        <small class="text-danger" id="duration_error"></small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Leads</label>
                        <input type="number" name="leads" class="form-control shadow-sm" placeholder="Enter Leads">
                        <small class="text-danger" id="leads_error"></small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-control shadow-sm">
                            <option value="">Select Type</option>
                            <option value="1">Subscription Plan</option>
                            <option value="2">Security Plan</option>
                        </select>
                        <small class="text-danger" id="type_error"></small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Plan Size</label>
                        <select name="plan_size" class="form-control shadow-sm">
                            <option value="">Select Size</option>
                            <option value="1">Large</option>
                            <option value="2">Small</option>
                        </select>
                        <small class="text-danger" id="plan_size_error"></small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Features</label>
                    <textarea name="features" class="form-control shadow-sm" rows="4"
                        placeholder="Enter Features"></textarea>
                    <small class="text-danger" id="features_error"></small>
                </div>


                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Add Plan</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeField = document.querySelector('select[name="type"]');
        const planSizeField = document.querySelector('select[name="plan_size"]');

        typeField.addEventListener('change', function () {
            if (this.value === '2') { // Security Plan
                planSizeField.value = '0'; // set hidden value
                planSizeField.disabled = true;
            } else {
                planSizeField.disabled = false;
                planSizeField.value = ''; // reset if Subscription Plan
            }
        });
    });
</script>

@endsection