@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Quotation</h1>

    <div class="card shadow mb-4">
        @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        <div class="card-header d-flex justify-content-end flex-wrap">
            
            <a href="{{ route('admin.add-quotations') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Quotation
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="bannersTable">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                   <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr.No</th>
                            <th>Quotation No</th>
                            <th>Booking Id</th>
                            <th>Quoted To</th>
                            <th>Amount</th>
                            <th>Quotation Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; @endphp
                        @foreach($quotations as $quotation)
                        <tr>
                           <td>{{ $i++ }}</td>
                            <td><a target="_blank" href="{{ route('admin.view-quotations', $quotation->id ) }}">{{ $quotation->quotation_no ?? 'NA' }}</a></td>
                            <td>{{ $quotation->booking_id ?? 'NA' }}</td>
                            <td>{{ $quotation->name ?? 'NA' }}</td>
                            <td>{{ $quotation->total_amount ?? 'NA' }}</td>
                            <td>{{ $quotation->quotation_date ?? 'NA' }}</td>
                            <td>
                                <a href="{{ route('admin.download-quotations', $quotation->id ) }}">Download</a> |
                                <a href="{{ route('admin.edit-quotations', $quotation->id ) }}">Edit</a> |
                               <form action="{{ route('admin.delete-quotations', $quotation->id) }}" method="POST"  style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this quotation?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">Delete</button>
                                </form>
                                
                            </td>
                    </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection