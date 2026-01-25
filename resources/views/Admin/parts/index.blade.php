@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Parts</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-end">
            <a href="{{ route('admin.add_parts', ['categoryId' => $categoryId]) }}" class=" btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Parts
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr.n</th>
                            <th>Part</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parts as $key => $val)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $val->part }}</td>
                                <td>
                                    <a href="{{ route('admin.edit_parts', ['categoryId' => $val->category_id, 'id' => $val->id]) }}"
                                        class=" btn btn-info btn-sm my-1">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button class="btn btn-sm btn-danger delete-btn"
                                        data-url="{{ route('admin.delete_parts', ['categoryId' => $val->category_id, 'id' => $val->id]) }}"
                                        title="Delete">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>

                                    <a href="{{ route('admin.price_list', ['categoryId' => $val->category_id, 'partId' => $val->id]) }}"
                                        class=" btn btn-warning btn-sm">
                                        <i class="fas fa-eye"></i>Price List
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection