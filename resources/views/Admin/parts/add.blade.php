@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Parts</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form action="{{ route('admin.add_parts', ['categoryId' => $categoryId]) }}" method="POST"
                enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="part" class="form-label">Part</label>
                        <input type="text" name="part" class="form-control shadow-sm" id="part"
                            placeholder="Enter Part Name">
                        <small id="part_error" class="text-danger"></small>
                    </div>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary btn-md px-5">Add Part</button>
                </div>
            </form>
        </div>
    </div>
@endsection
