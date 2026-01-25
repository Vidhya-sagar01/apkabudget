@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Zones</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <form action="{{ route('admin.add_zone') }}" method="POST" class="card p-4 shadow-sm" id="addForm">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Zone Name:</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <input type="hidden" name="boundary" id="boundary">
                    <input type="hidden" name="center_lat" id="center_lat">
                    <input type="hidden" name="center_lng" id="center_lng">
                    <input type="hidden" name="perimeter" id="perimeter">
                    <input type="hidden" name="area" id="area">
                    <input type="hidden" name="areas" id="areas">

                    <div class="mb-3">
                        <label for="place-search" class="form-label">Search Location:</label>
                        <input id="place-search" type="text" placeholder="Search a place...">
                    </div>

                    <div id="map" class="border rounded" style="width: 100%; height: 500px;"></div>
                    <div id="distance-info" class="mt-3 fw-bold"></div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">Save Zone</button>
                        <button type="button" id="reset-zone" class="btn btn-secondary ms-2">Reset Zone</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
