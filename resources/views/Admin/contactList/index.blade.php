@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Contact List</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <form method="GET" action="{{ route('admin.partners_contactlist') }}" class="d-flex">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search..." class="form-control mr-2" />
                <button class="btn btn-primary btn-sm" type="submit">Search</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="partnersTable">
                @include('Admin.contactList.partials.table', ['contactList' => $contactList])
            </div>
        </div>
    </div>
@endsection