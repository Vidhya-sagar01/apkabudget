<table class="table table-bordered">
    <thead>
        <tr>
            <th>Sr.n</th>
            <th>Pro Name</th>
            <th>Contact Name</th>
            <th>Phone Number</th>
        </tr>
    </thead>
    <tbody>
        @foreach($contactList as $key => $val)
            <tr>
                <td>{{ $contactList->firstItem() + $key }}</td>
                <td>{{ $val->user->name ?? 'N/A' }}</td>
                <td>{{ $val->name }}</td>
                <td>{{ $val->phone }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Laravel pagination links --}}
<div class="d-flex justify-content-end">
    {{ $contactList->appends(request()->query())->links() }}
</div>
