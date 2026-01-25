@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Bookings</h1>

    <div class="card shadow mb-4">
       
        <div class="card-body">
            <div class="table-responsive">
                {{--<a href="{{ route('admin.bookings.export_csv') }}" class="btn btn-success mb-3" target="_blank">
                    Export CSV
                </a>--}}
                <form method="GET" action="{{ route('admin.all_bookings') }}" class="form-inline mb-3">
                    <div class="form-group mr-2">
                        <label for="category_filter" class="mr-2">Filter by Category:</label>
                        <select name="category_id" id="category_filter" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr. no</th>
                            <th>Zone</th>
                            <th>Category</th>
                            <th>By</th>
                            <th>User</th>
                            <th>Provider</th>
                            <th>Booking Date</th>
                            <th>Booking Id</th>
                            <th>Price</th>
                            <th>Lead Status</th>
                            <th>Slot Date</th>
                            <th>Slot Start Time</th>
                            <th>Slot End Time</th>
                            <th>Action</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $key => $val)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>
                                    {{ $val->zone->name ?? 'N/A' }}
                                    @if (!$val->zone)
                                        <!-- Edit icon to open modal -->
                                        <a href="#" data-toggle="modal" data-target="#zoneModal" data-id="{{ $val->id }}">
                                            <i class="fas fa-edit text-primary ml-2" style="cursor: pointer;"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $val->subcategory->category->category ?? 'N/A' }}</td>
                                <td>{{ ($val->is_admin_created ?? 0) == 1 ? 'Admin' : 'App' }}</td>
                                <td>{{ $val->user->mobile_no?? 'N/A' }}({{ $val->user->name??'N/A' }})</td>
                                <td>{{ $val->provider->mobile_no??'N/A' }}({{ $val->provider->name??'N/A' }})</td>
                                <td>{{ \Carbon\Carbon::parse($val->created_at)->format('d M, Y - h:i A') }}</td>
                                <td>
                                    <a href="{{ route('admin.booking_detail', ['id' => $val->id]) }}" target="_blank" rel="noopener noreferrer">
                                        {{ $val->booking_id }}
                                    </a>
                                </td>
                                <td>{{ $val->total_price }}</td>
                                <!--<td>{{ $val->status }}</td>-->
                                 <td>
                                    <select class="form-control change-status" data-id="{{ $val->id }}">
                                        <option value="pending"   {{ $val->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="placed"    {{ $val->status == 'placed' ? 'selected' : '' }}>Placed</option>
                                        <option value="accepted"  {{ $val->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                        <option value="completed" {{ $val->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value=" done" {{ $val->status == 'survey done' ? 'selected' : '' }}>Survey Done</option>

                                    </select>
                                </td>
                                <td>{{ $val->slot_date }}</td>
                                <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $val->slot_start_time)->format('g:i A') }}</td>
                                <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $val->slot_end_time)->format('g:i A') }}</td>
                                <td>
                                    {{--<a href="#" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>--}}
                                    @if($val->status == 'accepted')
                                    <button class="btn btn-sm btn-danger cancel-booking-btn" data-id="{{ $val->id }}">
                                        <i class="fas fa-times-circle"></i> Cancel
                                    </button>
                                    @endif
                                     @php
    $hasUnresolvedComplaint = $val->complaints->where('status', '!=', 'resolved')->isNotEmpty();
    $canComplain = false;

    if ($val->status === 'completed') {
        $baseDate = $val->completed_date ? \Carbon\Carbon::parse($val->completed_date) : \Carbon\Carbon::parse($val->created_at);
        $canComplain = now()->diffInMonths($baseDate) < 3;
    }
@endphp

@if($val->status == 'completed' && $canComplain)
    @if(!$hasUnresolvedComplaint)
        <button class="btn btn-sm btn-warning mt-1" data-toggle="modal"
            data-target="#sharedComplaintModal" data-user="{{ $val->user_id }}"
            data-provider="{{ $val->provider_id }}" data-order="{{ $val->id }}"
            data-booking="{{ $val->booking_id }}">
            <i class="fas fa-comment-dots"></i> Add Complaint
        </button>
    @else
        <button class="btn btn-sm btn-secondary mt-1" disabled>
            <i class="fas fa-check-circle"></i> Complaint Exists
        </button>
    @endif
@endif


                                </td>
                               <td>
    <div class="d-flex align-items-start" style="gap: 10px; flex-wrap: nowrap;">

        <div class="text-nowrap" style="width: 140px;">
            <label class="small mb-1">Total</label>
            <input type="number" class="form-control form-control-sm editable"
                data-id="{{ $val->id }}" data-field="xtotal_amount"
                value="{{ $val->xtotal_amount }}">
        </div>

        <div class="text-nowrap" style="width: 140px;">
            <label class="small mb-1">Commission</label>
            <input type="number" class="form-control form-control-sm editable"
                data-id="{{ $val->id }}" data-field="xcommission_amount"
                value="{{ $val->xcommission_amount }}">
        </div>

        <div class="text-nowrap" style="width: 180px;">
            <label class="small mb-1">Status</label>
            <select class="form-control form-control-sm editable"
                data-id="{{ $val->id }}" data-field="xstatus">
                @foreach([
                    'Pending', 'Ongoing', 'Rescheduled', 'Partner On The Way',
                    'Job Done', 'Job Not Done', 'Customer Denied',
                    'Partner Side Cancel', 'Cancelled', 'Unpaid', 'Commission Paid'
                ] as $status)
                    <option value="{{ $status }}" {{ $val->xstatus === $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="text-nowrap" style="width: 220px;">
            <label class="small mb-1">Notes</label>
            <textarea class="form-control form-control-sm editable"
                data-id="{{ $val->id }}" data-field="xdescription"
                rows="1" style="resize: none;">{{ $val->xdescription }}</textarea>
        </div>

    </div>
</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
     <!-- Zone Assign Modal -->
    <div class="modal fade" id="zoneModal" tabindex="-1" role="dialog" aria-labelledby="zoneModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="zoneAssignForm" method="POST" action="{{ route('admin.booking_assign_zone') }}">
                @csrf
                <input type="hidden" name="order_id" id="zone_order_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="zoneModalLabel">Assign Zone</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Select Zone</label>
                            <select name="zone_id" class="form-control" required>
                                <option value="">-- Select Zone --</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
     <!-- Shared Complaint Modal -->
    <div class="modal fade" id="sharedComplaintModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.complaints.store_direct') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="complaint_user_id">
                <input type="hidden" name="provider_id" id="complaint_provider_id">
                <input type="hidden" name="order_id" id="complaint_order_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="complaintModalTitle">Add Complaint</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <textarea name="message" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on('change', '.change-status', function () {
        let id = $(this).data('id');
        let status = $(this).val();

        $.ajax({
            url: "{{ route('admin.change-status') }}",
            type: "POST",
            data: { 
                    id: id,
                    status: status,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
            success: function (response) {
                if(response.status){
                    alert("Success: " + response.message);
                } else {
                    alert("Failed: " + response.message);
                }
            },
            error: function (xhr) {
                alert("Error: " + xhr.responseText);
            }
        });
    });
</script>

