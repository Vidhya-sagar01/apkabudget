@extends('Admin.layouts.app')

@section('content')
<style>
    .star-rating .star {
        font-size: 2rem;
        color: #ccc;
        cursor: pointer;
        transition: color 0.2s;
    }
    .star-rating .star.selected, .star-rating .star.hovered {
        color: gold;
    }
</style>

<h1 class="h3 mb-2 text-gray-800">Rating Review</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <h4 class="m-3">Add Rating</h4>
        <form action="{{ route('admin.add_rating_review') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                
                <!-- Name -->
                <div class="form-group col-6">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter name" required>
                    <span class="text-danger" id="name_error"></span>
                </div>

                <!-- Profession -->
                <div class="form-group col-6">
                    <label for="profession">Profession</label>
                    <input type="text" name="profession" id="profession" class="form-control" placeholder="Enter profession" required>
                    <span class="text-danger" id="profession_error"></span>
                </div>
            </div>
            <div class="row">

                <!-- Profile Photo -->
                <div class="form-group col-6">
                    <label for="photo">Profile Photo</label>
                    <input type="file" name="photo" id="photo" class="form-control-file" accept="image/*" required>
                    <span class="text-danger" id="photo_error"></span>
                </div>

                <!-- Star Rating -->
                <div class="form-group col-6">
                    <label for="rating">Star Rating</label>
                    <div class="star-rating d-flex align-items-center ">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="star" data-value="{{ $i }}">&#9733;</span>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating" required>
                    <span class="text-danger" id="rating_error"></span>
                </div>
            </div>

            <!-- Content / Review -->
            <div class="form-group col-6">
                <label for="content">Content</label>
                <textarea name="content" id="content" class="form-control" rows="4" placeholder="Enter review..." required></textarea>
                <span class="text-danger" id="content_error"></span>
            </div>

            <!-- Submit Button -->
            <div class="form-group col-6 mt-3">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stars = document.querySelectorAll('.star-rating .star');
        const ratingInput = document.getElementById('rating');

        stars.forEach((star) => {
            star.addEventListener('mouseenter', function () {
                const value = parseInt(this.getAttribute('data-value'));
                highlightStars(value);
            });

            star.addEventListener('mouseleave', function () {
                const selected = parseInt(ratingInput.value);
                highlightStars(selected);
            });

            star.addEventListener('click', function () {
                const value = parseInt(this.getAttribute('data-value'));
                ratingInput.value = value;
                highlightStars(value);
            });
        });

        function highlightStars(value) {
            stars.forEach((star) => {
                const starValue = parseInt(star.getAttribute('data-value'));
                star.classList.toggle('selected', starValue <= value);
            });
        }
    });
</script>

@endsection
