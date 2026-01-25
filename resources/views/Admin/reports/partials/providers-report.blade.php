<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th>Provider</th>
            <th>Total Leads</th>
            <th>Accepted</th>
            <th>Completed</th>
            <th>Skipped</th>
        </tr>
    </thead>
    <tbody>
        @forelse($providerReports as $report)
            <tr>
                <td>{{ $report['provider_name'] }}</td>
                <td>{{ $report['total_leads'] }}</td>
                <td>{{ $report['accepted_leads'] }}</td>
                <td>{{ $report['completed_leads'] }}</td>
                <td>{{ $report['skipped_leads'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">No data found</td>
            </tr>
        @endforelse
    </tbody>
</table>
