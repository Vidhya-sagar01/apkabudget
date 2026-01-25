@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">How It Work</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-end">
            <a href="{{ route('admin.add_how_it_works', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId, 'subsubcategoryId' => $subsubcategoryId, 'service_id' => $serviceId]) }}"
                class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr.n</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($how_it_works as $key => $val)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $val->title }}</td>
                                <td>{{ $val->description }}</td>
                                <td>
                                    @if ($val->image)
                                        <img src="{{ asset($val->image) }}" class="img-fluid rounded shadow-sm"
                                            style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        No Image
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.edit_how_it_works', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId, 'subsubcategoryId' => $subsubcategoryId, 'service_id' => $serviceId, 'id' => $val->id]) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm delete-btn"
                                        data-url="{{ route('admin.delete_how_it_works', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId, 'subsubcategoryId' => $subsubcategoryId, 'service_id' => $serviceId, 'id' => $val->id]) }}"
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