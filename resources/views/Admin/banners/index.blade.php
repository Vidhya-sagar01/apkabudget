@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Banners</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <label for="bannerType" class="form-label mb-0 me-2">Filter by Type:</label>
                <select id="bannerType" class="form-select form-select-sm w-auto" style="min-width: 120px;">
                    <option value="">All</option>
                    <option value="1">Upper</option>
                    <option value="2">Middle</option>
                    <option value="3">Last</option>
                </select>
            </div>
            <a href="{{ route('admin.add_banners') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Banner
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive" id="bannersTable">
                @include('Admin.banners.partials.table', ['banners' => $banners])
            </div>
        </div>
    </div>
@endsection