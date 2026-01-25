@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Price List</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-end">
            <a href="{{ route('admin.add_price_list', ['categoryId' => $categoryId, 'partId' => $partId]) }}"
                class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Price
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr.n</th>
                            <th>Detail</th>
                            <th>Charge</th>
                            <th>Labour Charge</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($price_lists as $key => $val)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $val->detail }}</td>
                                <td>{{ $val->charge }}</td>
                                <td>{{ $val->labour_charge }}</td>
                                <td>
                                    <a href="{{ route('admin.edit_price_list', ['categoryId' => $categoryId, 'partId' => $val->part_id, 'id' => $val->id]) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button class="btn btn-sm btn-danger delete-btn"
                                        data-url="{{ route('admin.delete_price_list', ['categoryId' => $categoryId, 'partId' => $val->part_id, 'id' => $val->id]) }}"
                                        title="Delete">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
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