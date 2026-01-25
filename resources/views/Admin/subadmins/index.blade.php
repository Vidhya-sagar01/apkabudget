@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Subadmins</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <a href="{{ route('admin.add_subadmins') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Subadmin
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive" id="bannersTable">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr.n</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile No.</th>
                            <th>Image</th>
                            <th>Password</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subadmins as $key => $val)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $val->name }}</td>
                                <td>{{ $val->email }}</td>
                                <td>{{ $val->mobile_no }}</td>
                                <td><img src="{{ asset($val->image) }}" class="img-fluid w-25"></td>
                                <td>{{ $val->temp_password }}</td>
                                <td>
                                    @if($val->status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.edit_subadmins', $val->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-btn"
                                        data-url="{{ route('admin.delete_subadmins', ['id' => $val->id]) }}" title="Delete">
                                        <i class="fa fa-trash" aria-hidden="true"></i> </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection