@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Service Videos</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form action="{{ route('admin.edit_service_videos', ['id' => $ServiceVideo->id]) }}" method="POST"
                enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="video" class="form-label">Upload New Video</label>
                        <input type="file" name="video" class="form-control shadow-sm" id="video" accept="video/*">
                        <small id="video_error" class="text-danger"></small>
                        <div class="mt-3">
                            <p>Current Video:</p>
                            <video width="150" controls>
                                <source src="{{ asset($ServiceVideo->video_url) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <video id="videoPreview" controls style="display:none; width:150px; margin-top:10px;">
                            <source src="#" id="videoSource">
                        </video>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Edit Video</button>
                </div>
            </form>
        </div>
    </div>
@endsection
