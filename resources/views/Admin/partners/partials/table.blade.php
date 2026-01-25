<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead class="bg-primary text-white">
        <tr>
            <th>Sr.n</th>
            <th>Category</th>
            <th>Pro Name</th>
            <th>Phone Number</th>
            <th>Source</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($partners as $key => $val)
            <tr>
                <td>{{ ++$key }}</td>
                <td>{{ $val->categoryRelation->category ?? 'N/A' }}</td>
                <td>{{ $val->pro_name }}</td>
                <td>{{ $val->phone_number }}</td>
                <td>{{ $val->source }}</td>
                <td>
                    <select class="form-control form-control-sm status-dropdown" data-id="{{ $val->id }}">
                        @php
                            $statuses = ['New','Approved', 'Call Not Connected','Coming', 'Denied', 'Recharge Pending', 'Selected', 'Rejected', 'Reached', 'Schedule'];
                        @endphp
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ $val->status === $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>