@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Edit Service</h1>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-5">
            <form
                action="{{ route('admin.edit_service', ['categoryId' => $service->category_id, 'subcategoryId' => $service->subcategory_id, 'subsubcategoryId' => $service->sub_subcategory_id, 'id' => $service->id]) }}"
                method="POST" enctype="multipart/form-data" id="addForm">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="service_name" class="form-label">Service Name</label>
                        <input type="text" name="service_name" class="form-control shadow-sm" id="service_name"
                            placeholder="Enter Service Name" value="{{ $service->service_name }}">
                        <small id="service_name_error" class="text-danger"></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" name="price" class="form-control shadow-sm" id="price"
                            placeholder="Enter Price" value="{{ $service->price }}">
                        <small id="price_error" class="text-danger"></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="time" class="form-label">Time (HH:MM)</label>
                        <input type="time" name="time" class="form-control shadow-sm" id="time"
                            value="{{ $service->time }}">
                        <small id="time_error" class="text-danger"></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" name="image" class="form-control shadow-sm" id="image">
                        <small id="image_error" class="text-danger"></small>

                        <div class="mt-3">
                            <p>Current Image:</p>
                            <img id="currentImage" src="{{ asset($service->image) }}" alt="Service Image"
                                style="width:150px; height:auto; border-radius:8px;">
                        </div>
                        <img id="imagePreview" src="#" alt="Image Preview"
                            style="display:none; width:150px; margin-top:10px;" />
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Service Details</h5>
                <div id="detailsContainer">
                    @php
                        $details = json_decode($service->details) ?? [];
                    @endphp

                    @foreach($details as $detail)
                        <div class="row detail-item mb-3">
                            <div class="col-md-10">
                                <input type="text" name="details[]" class="form-control shadow-sm"
                                    placeholder="Enter Service Detail" value="{{ $detail }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm remove-detail w-100">❌ </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="addMoreDetails">➕ Add More</button>
                </div>

                {{-- <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_trending[]" value="1"
                            {{ $service->is_trending ? 'checked' : '' }}>
                        <label class="form-check-label">Trending</label>
                    </div>
                </div> --}}

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-md px-5">Update Service</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const detailsContainer = document.getElementById('detailsContainer');
        const addMoreBtn = document.getElementById('addMoreDetails');

        addMoreBtn.addEventListener('click', function () {
            const newBlock = document.createElement('div');
            newBlock.classList.add('row', 'detail-item', 'mb-3');

            newBlock.innerHTML = `
                <div class="col-md-10">
                    <input type="text" name="details[]" class="form-control shadow-sm" placeholder="Enter Service Detail">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-detail w-100">❌ </button>
                </div>
            `;

            detailsContainer.appendChild(newBlock);
        });

        detailsContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-detail')) {
                e.target.closest('.detail-item').remove();
            }
        });

    </script>
@endsection
