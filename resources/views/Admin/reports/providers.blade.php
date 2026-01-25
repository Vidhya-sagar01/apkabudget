@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Provider Report</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <!-- Filters -->
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <!-- Category -->
                <div class="d-flex align-items-center">
                    <label for="category_id" class="mb-0 me-2">Category:</label>
                    <select name="category_id" id="category_id" class="form-select form-select-sm form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->category }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- From Date -->
                <div class="d-flex align-items-center">
                    <label for="from_date" class="mb-0 me-2">From:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control form-control-sm">
                </div>

                <!-- To Date -->
                <div class="d-flex align-items-center">
                    <label for="to_date" class="mb-0 me-2">To:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control form-control-sm">
                </div>

                <button type="button" class="btn btn-sm btn-primary" id="filterBtn">Filter</button>
                <button type="button" class="btn btn-sm btn-secondary" id="resetBtn">Reset</button>
            </div>
        </div>

        <div class="card-body">
            <div id="reportTable" class="table-responsive">
                @include('Admin.reports.partials.providers-report')
            </div>
        </div>
    </div>
@endsection


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

    // Filter button
    $('#filterBtn').on('click', function () {
        loadSummary();
    });

    // Reset button
    $('#resetBtn').on('click', function () {
        $('#category_id').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        loadSummary();
    });

    // Handle pagination click (AJAX)
    $(document).on('click', '#reportTable .pagination a', function (e) {
        e.preventDefault();
        let url = $(this).attr('href');
        loadSummary(url);
    });

    // Load data with filters + pagination
    function loadSummary(url = "{{ route('admin.provider-report') }}") {
        let category   = $('#category_id').val();
        let start_date = $('#start_date').val();
        let end_date   = $('#end_date').val();

        $.ajax({
            url: url,
            type: "GET",
            data: { category_id: category, start_date: start_date, end_date: end_date },
            beforeSend: function () {
                $('#reportTable').html('<p class="text-center">Loading...</p>');
            },
            success: function (response) {
                $('#reportTable').html(response);
            },
            error: function () {
                $('#reportTable').html('<p class="text-danger text-center">Error loading data</p>');
            }
        });
    }
});
</script>

