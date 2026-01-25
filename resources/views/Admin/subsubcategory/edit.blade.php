@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Sub SubCategories</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form
                action="{{ route('admin.edit_subsubcategory', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId, 'id' => $Subsubcategory->id]) }}"
                method="POST" enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="sub_subcategory_name" class="form-label">Sub SubCategory Name</label>
                        <input type="text" name="sub_subcategory_name" class="form-control shadow-sm"
                            id="sub_subcategory_name" placeholder="Enter Sub SubCategory Name"
                            value="{{ $Subsubcategory->sub_subcategory_name }}">
                        <small id="sub_subcategory_name_error" class="text-danger"></small>
                    </div>

                    <div class="col-md-6">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" name="image" class="form-control shadow-sm" id="image">
                        <small id="image_error" class="text-danger"></small>
                        <div class="mt-3">
                            <p>Current Image:</p>
                            <img id="currentImage" src="{{ asset($Subsubcategory->image) }}" alt="Category Image"
                                style="width:150px; height:auto; border-radius:8px;">
                        </div>
                        <img id="imagePreview" src="#" alt="Image Preview"
                            style="display:none; width:150px; margin-top:10px;" />
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Edit Sub SubCategory</button>
                </div>
            </form>
        </div>
    </div>
@endsection
