@extends('Admin.layouts.app')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    @php
        $admin = Auth::guard('admin')->user();
    @endphp
    @if($admin->role == 1)
        <!-- Content Row -->
        <div class="row">


            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Users</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Providers</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProviders }}</div>

                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Earning
                                </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">₹
                                            {{ number_format($totalEarning, 2) }}
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-wallet fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif
    @if($admin->role != 1)
        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <div class="text-center mt-4">
                    @if(!$todayAttendance)
                        {{-- Punch In --}}
                        <button id="punchInBtn" class="btn btn-primary btn-lg shadow-sm">
                            <i class="fas fa-sign-in-alt mr-2"></i> Punch In
                        </button>
                    @elseif(!$todayAttendance->check_out)
                        {{-- Punch Out --}}
                        <button id="punchOutBtn" class="btn btn-danger btn-lg shadow-sm">
                            <i class="fas fa-sign-out-alt mr-2"></i> Punch Out
                        </button>
                    @else
                        {{-- Already Done --}}
                        <button class="btn btn-secondary btn-lg shadow-sm" disabled>
                            <i class="fas fa-check-circle mr-2"></i> You have completed today’s attendance
                        </button>
                    @endif

                    <div id="punchStatus" class="mt-3 text-success font-weight-bold"></div>
                </div>

            </div>
        </div>


        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-clock mr-2"></i> Your Office Timings</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 text-center table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Day</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Lunch Start</th>
                                        <th>Lunch End</th>
                                    </tr>
                                </thead>
                                <tbody>
    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
        @php
            $dayTiming = $timings->get($day);

            $isOff = $dayTiming
                && $dayTiming->start_time === '00:00:00'
                && $dayTiming->end_time === '00:00:00'
                && $dayTiming->lunch_start === '00:00:00'
                && $dayTiming->lunch_end === '00:00:00';
        @endphp
        <tr class="{{ $isOff ? 'table-danger text-muted font-italic' : '' }}">
            <td><strong>{{ $day }}</strong></td>
            @if($isOff)
                <td colspan="4">OFF</td>
            @else
                <td>{{ \Carbon\Carbon::parse($dayTiming->start_time)->format('h:i A') ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($dayTiming->end_time)->format('h:i A') ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($dayTiming->lunch_start)->format('h:i A') ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($dayTiming->lunch_end)->format('h:i A') ?? '-' }}</td>
            @endif
        </tr>
    @endforeach
</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Content Row -->

    {{--<div class="row">

        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Dropdown Header:</div>
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Sources</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Dropdown Header:</div>
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Direct
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Social
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Referral
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>--}}

@endsection