@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Plans</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-end">
            <a href="{{ route('admin.add_plans', ['category_id' => $category_id]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Plan
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr.n</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Name</th>
                            <th>Price (₹)</th>
                            <th>Duration</th>
                            <th>Leads</th>
                            <th>Features</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($plans as $key => $val)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td><span
                                        class="badge {{ $val->plan_size == 1 ? 'badge-success' : ($val->plan_size == 2 ? 'badge-secondary' : 'badge-light') }}">
                                        {{ $val->plan_size == 1 ? 'Large' : ($val->plan_size == 2 ? 'Small' : 'N/A') }}
                                    </span></td>
                                <td>
                                    @if ($val->type == 1)
                                        <span class="badge badge-primary">Subscription Plan</span>
                                    @elseif ($val->type == 2)
                                        <span class="badge badge-warning">Security Plan</span>
                                    @else
                                        <span class="badge badge-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>{{ $val->name }}</td>
                                <td>₹{{ number_format($val->price, 2) }}</td>
                                <td>{{ $val->duration }} Days</td>
                                <td>{{ $val->leads ?? '-' }}</td>
                                <td>{{ $val->features }}</td>
                                <td>
                                    <a href="{{ route('admin.edit_plans', ['category_id' => $val->category_id, 'id' => $val->id]) }}"
                                        class="btn btn-info btn-sm my-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{--<button class="btn btn-sm btn-danger delete-btn"
                                        data-url="{{ route('admin.delete_subcategory', ['categoryId' => $val->category_id, 'id' => $val->id]) }}"
                                        title="Delete">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>--}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection