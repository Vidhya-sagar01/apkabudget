@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Partners</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="{{ route('admin.add_partners_data') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Import Partners
            </a>

            <div class="d-flex gap-2">
                <select id="categoryFilter" class="form-control form-control-sm">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->category }}</option>
                    @endforeach
                </select>

                <select id="statusFilter" class="form-control form-control-sm">
                    <option value="">All Status</option>
                    @foreach (['New', 'Approved', 'Call Not Connected', 'Coming', 'Denied', 'Recharge Pending', 'Selected', 'Rejected', 'Reached', 'Schedule'] as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive" id="partnersTable">
                @include('Admin.partners.partials.table', ['partners' => $partners])
            </div>
        </div>
    </div>
@endsection