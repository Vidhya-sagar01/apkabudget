@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Providers</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-end">
            <a href="{{ route('admin.add_providers') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add providers
            </a>
        </div>
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <label for="categoryType" class="form-label mb-0 me-2">Filter by Category:</label>
                <select id="categoryType" class="form-select form-select-sm w-auto form-control" style="min-width: 120px;">
                    <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->category }}
                                </option>
                            @endforeach
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="providersTableBody">
                @include('Admin.providers.partials.table', ['providers' => $providers])
            </div>
            <!-- Popup Modal -->
            <div class="modal fade" id="planModal" tabindex="-1" role="dialog" aria-labelledby="planModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="planModalLabel">Select a Plan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="providerId">
                            <input type="hidden" id="planType">

                            <label>Select a Plan:</label>
                            <select class="form-control" id="planDropdown">
                                <!-- Options dynamically fill होंगे -->
                            </select>
                            <label class="mt-3">Payment Screenshot (required):</label>
                            <input type="file" class="form-control" id="paymentScreenshot" accept="image/*">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" id="activatePlanBtn">Activate</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assign Zone Modal -->
            <div class="modal" id="assignZoneModal" tabindex="-1" aria-labelledby="assignZoneModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <form id="assignZoneForm">
                        @csrf
                        <input type="hidden" name="provider_id" id="modalProviderId">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="assignZoneModalLabel">Assign Zones to <span
                                        id="providerName"></span></h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="zoneList">
                                Loading zones...
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Assign Zones</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <script>
        document.querySelectorAll('.badge[data-provider-id]').forEach(function(button) {
            button.addEventListener('click', function() {
                const providerId = this.getAttribute('data-provider-id');
                const blockStatusElement = document.getElementById('blockStatus-' + providerId);
                const currentStatus = blockStatusElement.textContent.trim();
                const newStatus = currentStatus === 'Blocked' ? 'Unblock' : 'Block';
                
                fetch(`/admin/user-block/${providerId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'blocked') {
                        blockStatusElement.textContent = 'Blocked';
                        blockStatusElement.classList.remove('badge-success');
                        blockStatusElement.classList.add('badge-danger');
                    } else {
                        blockStatusElement.textContent = 'Unblocked';
                        blockStatusElement.classList.remove('badge-danger');
                        blockStatusElement.classList.add('badge-success');
                    }
                })
                .catch(error => console.error('Error toggling block status:', error));
            });
        });
    </script>
    <script>
        document.querySelectorAll('.assign-zone-btn').forEach(button => {
            button.addEventListener('click', function() {
                const providerId = this.getAttribute('data-provider-id');
                const providerName = this.getAttribute('data-provider-name');
                document.getElementById('modalProviderId').value = providerId;
                document.getElementById('providerName').textContent = providerName;

                fetch(`/admin/get-zones/${providerId}`)
                    .then(response => response.json())
                    .then(data => {
                        const zoneList = document.getElementById('zoneList');
                        zoneList.innerHTML = '';
                        data.zones.forEach(zone => {
                            const checked = data.assignedZones.includes(zone.id) ? 'checked' :
                                '';
                            zoneList.innerHTML += `
                                <div class="form-check">
                                    <input type="checkbox" name="zones[]" value="${zone.id}" class="form-check-input" ${checked}>
                                    <label class="form-check-label">${zone.name}</label>
                                </div>
                            `;
                        });
                    })
                    .catch(error => console.error('Error fetching zones:', error));
            });
        });

        document.getElementById('assignZoneForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('{{ route('admin.assign_zones') }}', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    $('#assignZoneModal').modal('hide');
                    location.reload();
                })
                .catch(error => console.error('Error assigning zones:', error));
        });
    </script>
@endsection
