@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">How It Work</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form
                action="{{ route('admin.edit_how_it_works', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId, 'subsubcategoryId' => $subsubcategoryId, 'service_id' => $serviceId, 'id' => $how_it_work->id]) }}"
                method="POST" enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" class="form-control shadow-sm" id="title" placeholder="Enter Title"
                            value="{{ $how_it_work->title }}">
                        <small id="title_error" class="text-danger"></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" name="image" class="form-control shadow-sm" id="image">
                        <small id="image_error" class="text-danger"></small>
                        @if($how_it_work->image)
                            <div class="mt-3">
                                <p>Current Image:</p>
                                <img id="currentImage" src="{{ asset($how_it_work->image) }}" alt="Image"
                                    style="width:150px; height:auto; border-radius:8px;">
                            </div>
                        @endif
                        <img id="imagePreview" src="#" alt="Image Preview"
                            style="display:none; width:150px; margin-top:10px;" />
                    </div>
                    <div class="col-md-12 mt-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control shadow-sm"
                            placeholder="Enter Description...">{{$how_it_work->description}}</textarea>
                        <small id="description_error" class="text-danger"></small>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection