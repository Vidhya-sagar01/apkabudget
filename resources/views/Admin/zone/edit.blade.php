@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Edit Zone</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.update_zone', $zone->id) }}" method="POST" class="card p-4 shadow-sm" id="editForm">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Zone Name:</label>
                    <input type="text" name="name" id="name" class="form-control" 
                           value="{{ $zone->name }}" required>
                </div>

                <input type="hidden" name="boundary" id="boundary" value="{{ $zone->boundary }}">
                <input type="hidden" name="center_lat" id="center_lat" value="{{ $zone->center_lat }}">
                <input type="hidden" name="center_lng" id="center_lng" value="{{ $zone->center_lng }}">
                <input type="hidden" name="perimeter" id="perimeter" value="{{ $zone->perimeter }}">
                <input type="hidden" name="area" id="area" value="{{ $zone->area }}">
                <input type="hidden" name="areas" id="areas" value="{{ $zone->areas }}">

                <div class="mb-3">
                    <label for="place-search" class="form-label">Search Location:</label>
                    <input id="place-search" type="text" placeholder="Search a place...">
                </div>

                <div id="map" class="border rounded" style="width: 100%; height: 500px;"></div>
                <div id="distance-info" class="mt-3 fw-bold"></div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Update Zone</button>
                    <a href="{{ route('admin.zones') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
