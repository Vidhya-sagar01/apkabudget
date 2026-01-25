@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Partners</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            {{-- Sample Info Box --}}
            <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap mb-4">
                <div class="mb-2 mb-md-0">
                    <strong>Note:</strong> Please make sure your Excel/CSV file follows the correct format:
                    <br>
                    <code>pro_name</code>, <code>phone_number</code>, <code>city</code>, <code>amount_paid</code>,
                    <code>category</code>, <code>pending_amount</code>, <code>payment_gateway</code>,
                    <code>hub</code>, <code>tshirt_cap</code>, <code>source</code>, <code>status</code>
                </div>
                <a href="{{ asset('sample/partners_template.xlsx') }}" class="btn btn-sm btn-info" download>
                    <i class="fas fa-file-download"></i> Download Sample Format
                </a>
            </div>
            <form action="{{ route('admin.add_partners_data') }}" method="POST" enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Category:</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category }}</option>
                            @endforeach
                        </select>
                        <small id="category_id_error" class="text-danger"></small>
                    </div>

                    <div class="col-md-6">
                        <label for="file" class="form-label">Excel/CSV Choose File:</label>
                        <input type="file" name="file" class="form-control" required>
                        <small id="file_error" class="text-danger"></small>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Import</button>
                </div>
            </form>
        </div>
    </div>
@endsection