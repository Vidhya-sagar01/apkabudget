@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Banners</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form action="{{ route('admin.edit_banners', ['id' => $banner->id]) }}" method="POST"
                enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label>Type</label>
                        <select id="type" name="type" class="form-control">
                            <option value="">-- Select Type --</option>
                            <option value="1" {{ $banner->type == 1 ? 'selected' : '' }}>Upper</option>
                            <option value="2" {{ $banner->type == 2 ? 'selected' : '' }}>Middle</option>
                            <option value="3" {{ $banner->type == 3 ? 'selected' : '' }}>Last</option>
                        </select>
                        <small class="text-danger" id="type_error"></small>
                    </div>
                    <div class="col-md-6">
                        <label for="image" class="form-label">Banner Image</label>
                        <input type="file" name="image" class="form-control shadow-sm" id="image">
                        <small id="image_error" class="text-danger"></small>
                        <div class="mt-3">
                            <p>Current Image:</p>
                            <img id="currentImage" src="{{ asset($banner->image) }}" alt="Category Image"
                                style="width:150px; height:auto; border-radius:8px;">
                        </div>
                        <img id="imagePreview" src="#" alt="Image Preview"
                            style="display:none; width:150px; margin-top:10px;" />
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Edit Banner</button>
                </div>
            </form>
        </div>
    </div>
@endsection