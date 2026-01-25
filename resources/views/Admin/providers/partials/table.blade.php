<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr. no</th>
                            <th>Name</th>
                            <th>Email Id</th>
                            <th>Mobile No</th>
                            <th>Total Leads</th>
                            <th>Accepted Leads</th>
                            <th>Completed Leads</th>
                            <th>Skipped Leads</th>
                            @if(hasPermission('providers_action'))
                            <th>Security</th>
                            <th>Plan</th>
                            <th>Status</th>
                            @endif
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($providers as $key => $val)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td><a href="{{ route('admin.user_details',['id'=>$val->id]) }}" target="_blank" rel="noopener noreferrer"> {{ $val->name }}</a></td>
                                <td>{{ $val->email }}</td>
                                <td>{{ $val->mobile_no }}</td>
                               
                                    @php
                                        $stats = $providerStats[$val->id] ?? ['total' => 0, 'accepted' => 0, 'completed' => 0, 'skipped' => 0];
                                    @endphp
                                    <td>
                                    {{ $stats['total'] }}
                                    </td>
                                    <td>
                                    {{ $stats['accepted'] }}
                                    </td>
                                    <td>
                                    {{ $stats['completed'] }}
                                    </td>
                                    <td>
                                    {{ $stats['skipped'] }}
                                </td>
                                @if(hasPermission('providers_action'))
                                <td>
                                    @php
                                        $securityStatus = \App\Models\Subscription::hasActiveSubscription($val->id, 2);
                                        
                                    @endphp
                                    <span
                                        class="badge security-status {{ $securityStatus ? 'badge-success' : 'badge-danger' }}"
                                        data-id="{{ $val->id }}" data-type="2" style="cursor: pointer;">
                                        {{ $securityStatus ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $planStatus = \App\Models\Subscription::hasActiveSubscription($val->id, 1);

                                    @endphp
                                    <span
                                        class="badge security-status {{ $planStatus ? 'badge-success' : 'badge-danger' }}"
                                        data-id="{{ $val->id }}" data-type="1" style="cursor: pointer;">
                                        {{ $planStatus ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <span id="blockStatus-{{ $val->id }}" class="badge {{ $val->is_blocked ? 'badge-danger' : 'badge-success' }}" style="cursor: pointer;" data-provider-id="{{ $val->id }}">
                                        {{ $val->is_blocked ? 'Blocked' : 'Unblocked' }}
                                    </span>
                                </td>
                                @endif
                                <td>
                                    <button class="btn btn-sm btn-warning assign-zone-btn"
                                        data-provider-id="{{ $val->id }}" data-provider-name="{{ $val->name }}"
                                        data-toggle="modal" data-target="#assignZoneModal">
                                        <i class="fas fa-map-marker-alt"></i> Assign Zone
                                    </button>
                                    <a href="{{ route('admin.edit_providers', ['id' => $val->id]) }}"
                                        class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                     <button class="btn btn-sm btn-danger delete-btn" data-url="{{ route('admin.delete_providers', ['id' => $val->id]) }}" title="Delete"> <i class="fa fa-trash" aria-hidden="true"></i> </button> 
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>