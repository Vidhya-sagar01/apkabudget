@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">About Us</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="aboutTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="user-tab" data-toggle="tab" href="#user" role="tab" aria-controls="user"
                        aria-selected="true">User</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="partner-tab" data-toggle="tab" href="#partner" role="tab"
                        aria-controls="partner" aria-selected="false">Partner</a>
                </li>
            </ul>

            <div class="tab-content" id="aboutTabContent">
                <div class="tab-pane fade show active" id="user" role="tabpanel" aria-labelledby="user-tab">
                    <form action="{{ route('admin.update_about_us') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="1">
                        <textarea name="content" class="form-control editor"
                            rows="10">{{ $userContent->content ?? '' }}</textarea>
                        <button type="submit" class="btn btn-primary mt-2">Update User About</button>
                    </form>
                </div>

                <div class="tab-pane fade" id="partner" role="tabpanel" aria-labelledby="partner-tab">
                    <form action="{{ route('admin.update_about_us') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="2">
                        <textarea name="content" class="form-control editor"
                            rows="10">{{ $partnerContent->content ?? '' }}</textarea>
                        <button type="submit" class="btn btn-primary mt-2">Update Partner About</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include CKEditor CDN -->
    <script src="https://cdn.ckeditor.com/4.20.2/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replaceAll('editor');
    </script>
@endsection