@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Price List</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form
                action="{{ route('admin.edit_price_list', ['categoryId' => $categoryId, 'partId' => $price_list->part_id, 'id' => $price_list->id]) }}"
                method="POST" enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="detail" class="form-label">Detail</label>
                        <input type="text" name="detail" class="form-control shadow-sm" id="detail"
                            placeholder="Enter Detail" value="{{ $price_list->detail }}">
                        <small id="detail_error" class="text-danger"></small>
                    </div>
                    <div class="col-md-3">
                        <label for="charge" class="form-label">Charge</label>
                        <input type="number" name="charge" class="form-control shadow-sm" id="charge"
                            placeholder="Enter Charge" value="{{ $price_list->charge }}">
                        <small id="charge_error" class="text-danger"></small>
                    </div>
                    <div class="col-md-3">
                        <label for="labour_charge" class="form-label">Labour Charge</label>
                        <input type="number" name="labour_charge" class="form-control shadow-sm" id="labour_charge"
                            placeholder="Enter Labour Charge" value="{{ $price_list->labour_charge }}">
                        <small id="labour_charge_error" class="text-danger"></small>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Edit Price</button>
                </div>
            </form>
        </div>
    </div>
@endsection