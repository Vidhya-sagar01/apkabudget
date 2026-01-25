@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">SubCategory</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form action="{{ route('admin.add_subcategory', ['categoryId' => $categoryId]) }}" method="POST"
                enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label">SubCategory Name</label>
                        <input type="text" name="name" class="form-control shadow-sm" id="name"
                            placeholder="Enter SubCategory Name">
                        <small id="name_error" class="text-danger"></small>
                    </div>

                    <div class="col-md-6">
                        <label for="image" class="form-label">Category Image</label>
                        <input type="file" name="image" class="form-control shadow-sm" id="image">
                        <small id="image_error" class="text-danger"></small>
                        <br>
                        <img id="imagePreview" src="#" alt="Image Preview"
                            style="display:none; width:150px; margin-top:10px;" />
                    </div>
                     <div class="col-md-6">
                        <label for="details" class="form-label">Detail</label>
                        <input type="text" name="details" class="form-control shadow-sm" id="details"
                            placeholder="Enter Detail">
                        <small id="details_error" class="text-danger"></small>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Add SubCategory</button>
                </div>
            </form>
        </div>
    </div>
@endsection
