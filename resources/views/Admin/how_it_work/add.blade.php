@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">How It Work</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form
                action="{{ route('admin.add_how_it_works', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId, 'subsubcategoryId' => $subsubcategoryId, 'service_id' => $serviceId]) }}"
                method="POST" enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" class="form-control shadow-sm" id="title" placeholder="Enter Title">
                        <small id="title_error" class="text-danger"></small>
                    </div>
                    <div class="col-md-6">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" name="image" class="form-control shadow-sm" id="image">
                        <small id="image_error" class="text-danger"></small>
                        <br>
                        <img id="imagePreview" src="#" alt="Image Preview"
                            style="display:none; width:150px; margin-top:10px;" />
                    </div>
                    <div class="col-md-12 mt-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control shadow-sm"
                            placeholder="Enter Description..."></textarea>
                        <small id="description_error" class="text-danger"></small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Add</button>
                </div>
            </form>
        </div>
    </div>
@endsection