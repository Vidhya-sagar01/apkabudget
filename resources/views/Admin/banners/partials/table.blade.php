<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr.n</th>
                            <th>Type</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($banners as $key => $val)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>
                                    @if($val->type == 1)
                                        Upper
                                    @elseif ($val->type == 2)
                                        Middle
                                    @elseif ($val->type == 3)
                                        Last
                                    @else
                                        Somthing Wrong
                                    @endif
                                </td>
                                <td><img src="{{ asset($val->image) }}" class="img-fluid w-25"></td>
                                <td>
                                    <a href="{{ route('admin.edit_banners', $val->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-btn"
                                        data-url="{{ route('admin.delete_banners', ['id' => $val->id]) }}" title="Delete">
                                        <i class="fa fa-trash" aria-hidden="true"></i> </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>