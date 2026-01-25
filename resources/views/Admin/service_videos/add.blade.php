@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Service Videos</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form action="{{ route('admin.add_service_videos') }}" method="POST" enctype="multipart/form-data" id="addForm">
                @csrf
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="video" class="form-label">Video</label>
                        <input type="file" name="video" class="form-control shadow-sm" id="video" accept="video/*"
                            required>
                        <small id="video_error" class="text-danger"></small>
                        <br>
                        <video id="videoPreview" controls style="display:none; width:150px; margin-top:10px;">
                            <source src="#" id="videoSource">
                        </video>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Add Video</button>
                </div>
            </form>
        </div>
    </div>
@endsection
