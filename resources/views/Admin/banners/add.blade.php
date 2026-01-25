@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Banners</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form action="{{ route('admin.add_banners') }}" method="POST" enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label>Type</label>
                        <select id="type" name="type" class="form-control">
                            <option value="">-- Select Type --</option>
                            <option value="1">Upper</option>
                            <option value="2">Middle</option>
                            <option value="3">Last</option>
                        </select>
                        <small class="text-danger" id="type_error"></small>
                    </div>
                    <div class="col-md-6">
                        <label for="image" class="form-label">Banner Image</label>
                        <input type="file" name="image" class="form-control shadow-sm" id="image">
                        <small id="image_error" class="text-danger"></small>
                        <br>
                        <img id="imagePreview" src="#" alt="Image Preview"
                            style="display:none; width:150px; margin-top:10px;" />
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Add Banner</button>
                </div>
            </form>
        </div>
    </div>
@endsection