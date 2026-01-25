@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Services</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-end">
            <a href="{{ route('admin.add_service', ['categoryId' => $categoryId, 'subcategoryId' => $subcategoryId, 'subsubcategoryId' => $subsubcategoryId]) }}"
                class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Service
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr.n</th>
                            <th>Service Name</th>
                            <th>Image</th>
                            <th>Price</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $key => $service)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $service->service_name }}</td>
                                <td>
                                    @if ($service->image)
                                        <img src="{{ asset($service->image) }}" class="img-fluid rounded shadow-sm"
                                            style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        No Image
                                    @endif
                                </td>
                                <td>{{ $service->price }}</td>
                                <td>{{ $service->time }}</td>
                                <td>
                                    <a href="{{ route('admin.edit_service', ['categoryId' => $service->category_id, 'subcategoryId' => $service->subcategory_id, 'subsubcategoryId' => $service->sub_subcategory_id, 'id' => $service->id]) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm delete-btn"
                                        data-url="{{ route('admin.delete_service', ['categoryId' => $service->category_id, 'subcategoryId' => $service->subcategory_id, 'subsubcategoryId' => $service->sub_subcategory_id, 'id' => $service->id]) }}"
                                        title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <a href="{{ route('admin.how_it_works', ['categoryId' => $service->category_id, 'subcategoryId' => $service->subcategory_id, 'subsubcategoryId' => $service->sub_subcategory_id, 'service_id' => $service->id]) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>How It Work
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
