@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Category</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form action="{{ route('admin.edit_categories', ['id' => $category->id]) }}" method="POST"
                enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category Name</label>
                        <input type="text" name="category" class="form-control shadow-sm" id="category"
                            placeholder="Enter Category Name" value="{{$category->category}}">
                        <small id="category_error" class="text-danger"></small>
                    </div>
                    <div class="col-md-3">
                        <label for="max_price" class="form-label">Max Price</label>
                        <input type="number" name="max_price" class="form-control shadow-sm" id="max_price"
                            placeholder="Enter Max Price" value="{{$category->max_price}}">
                        <small id="max_price_error" class="text-danger"></small>
                    </div>

                    <div class="col-md-6">
                        <label for="image" class="form-label">Category Image</label>
                        <input type="file" name="image" class="form-control shadow-sm" id="image">
                        <small id="image_error" class="text-danger"></small>
                        <div class="mt-3">
                            <p>Current Image:</p>
                            <img id="currentImage" src="{{ asset($category->image) }}" alt="Category Image" style="width:150px; height:auto; border-radius:8px;">
                        </div>
                        <img id="imagePreview" src="#" alt="Image Preview"
                            style="display:none; width:150px; margin-top:10px;" />
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Edit Category</button>
                </div>
            </form>
        </div>
    </div>
@endsection
