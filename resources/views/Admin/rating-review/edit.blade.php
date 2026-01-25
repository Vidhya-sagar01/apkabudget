@extends('Admin.layouts.app')

@section('content')
<style>
    .star-rating .star {
        font-size: 2rem;
        color: #ccc;
        cursor: pointer;
        transition: color 0.2s;
    }
    .star-rating .star.selected,
    .star-rating .star.hovered {
        color: gold;
    }
</style>

<h1 class="h3 mb-2 text-gray-800">Edit Rating Review</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <h4 class="m-3">Update Rating</h4>
        <form action="{{ route('admin.edit_rating_review', $ratingReview->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Name -->
                <div class="form-group col-6">
                    <label for="name">Name</label>
                    <input type="text" name="name" value="{{ old('name',$ratingReview->name) }}" class="form-control" required>
                </div>

                <!-- Profession -->
                <div class="form-group col-6">
                    <label for="profession">Profession</label>
                    <input type="text" name="profession" value="{{ old('profession',$ratingReview->profession) }}" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <!-- Profile Photo -->
                <div class="form-group col-6">
                    <label for="photo">Profile Photo</label><br>
                    @if($ratingReview->photo)
                        <img src="{{ asset($ratingReview->photo) }}" alt="Photo" width="80" class="mb-2"><br>
                    @endif
                    <input type="file" name="photo" class="form-control-file" accept="image/*">
                </div>

                <!-- Star Rating -->
                <div class="form-group col-6">
                    <label>Star Rating</label>
                    <div class="star-rating d-flex align-items-center">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="star {{ $i <= $ratingReview->rating ? 'selected' : '' }}" data-value="{{ $i }}">&#9733;</span>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating" value="{{ old('rating',$ratingReview->rating) }}" required>
                </div>
            </div>

            <!-- Content -->
            <div class="form-group col-6">
                <label for="content">Content</label>
                <textarea name="content" class="form-control" rows="4" required>{{ old('content',$ratingReview->content) }}</textarea>
            </div>

            <div class="form-group col-6 mt-3">
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="{{ route('admin.rating_review') }}" class="btn btn-secondary btn-sm">Cancel</a>
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

        // highlight already saved rating on load
        highlightStars(parseInt(ratingInput.value));
    });
</script>
@endsection
