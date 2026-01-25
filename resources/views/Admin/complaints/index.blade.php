@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Complaints</h1>

    <div class="card shadow mb-4">

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr. no</th>
                            <th>User</th>
                            <th>Provider</th>
                            <th>Order Id</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complaints as $complaint)
                            <tr>
                                <td>{{ $complaint->id }}</td>
                                <td>
                                    {{ $complaint->user->name ?? 'N/A' }}
                                    ({{ $complaint->user->mobile_no ?? 'N/A' }})
                                </td>
                                <td>
                                    {{ $complaint->provider->name ?? 'N/A' }}
                                    ({{ $complaint->provider->mobile_no ?? 'N/A' }})
                                </td>
                                <td><a href="{{ route('admin.booking_detail', ['id' => $complaint->order_id]) }}"
                                        target="_blank"
                                        rel="noopener noreferrer">{{ $complaint->order->booking_id ?? 'N/A' }}</a></td>
                                <td>{{ $complaint->message }}</td>
                                <td>
                                    @if($complaint->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-success">Resolved</span>
                                    @endif
                                </td>
                                <td>{{ $complaint->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    @if($complaint->status == 'pending')
                                        <form action="{{ route('admin.complaints.resolve', $complaint->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-success d-flex align-items-center gap-1 px-3"
                                                style="border-radius: 25px; font-weight: 600;"
                                                onclick="return confirm('Are you sure to mark this complaint as resolved?')">
                                                <i class="fas fa-check-circle"></i> Resolve
                                            </button>
                                        </form>
                                    @else
                                        <span>---</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection