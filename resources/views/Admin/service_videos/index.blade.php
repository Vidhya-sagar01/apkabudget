@extends('Admin.layouts.app')
@section('content')
    <h1 class="h3 mb-2 text-gray-800">Service Videos</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-end">
            <a href="{{ route('admin.add_service_videos') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Service Videos
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr.n</th>
                            <th>Video</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($service_videos as $key => $val)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>
                                    <video width="320" height="240" controls>
                                        <source src="{{ asset($val->video_url) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </td>
                                <td>
                                    <a href="{{ route('admin.edit_service_videos', $val->id) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm delete-btn"
                                        data-url="{{ route('admin.delete_service_videos', ['id' => $val->id]) }}"
                                        title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
