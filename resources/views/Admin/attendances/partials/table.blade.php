<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead class="bg-primary text-white">
        <tr>
            <th>Sr.no</th>
            <th>Employee Name</th>
            <th>Date</th>
            <th>Check-In</th>
            <th>Check-Out</th>
            <th>Working Minutes</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($attendances as $key => $attendance)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $attendance->admin->name ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d-m-Y') }}</td>
                <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '-' }}</td>
                <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '-' }}</td>
                <td>{{ $attendance->working_minutes ?? '-' }}</td>
                <td>
                    @if($attendance->status == 'Present')
                        <span class="badge badge-success">Present</span>
                    @else
                        <span class="badge badge-secondary">{{ $attendance->status }}</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">No attendance records found.</td>
            </tr>
        @endforelse
    </tbody>
</table>