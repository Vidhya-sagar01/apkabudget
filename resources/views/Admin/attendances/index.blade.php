@extends('Admin.layouts.app')
@section('content')

    <h1 class="h3 mb-2 text-gray-800">Attendances</h1>

    <div class="card shadow mb-4">
        {{-- <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <a href="{{ route('admin.add_banners') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Banner
            </a>
        </div> --}}
        <div class="card-body">
            <div class="table-responsive" id="bannersTable">
                @include('Admin.attendances.partials.table', ['attendances' => $attendances])
            </div>
        </div>

    </div>

@endsection