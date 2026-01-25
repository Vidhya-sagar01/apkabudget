@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Rating Review</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-end flex-wrap">
            <a href="{{ route('admin.add_rating_review') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Rating
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive" id="bannersTable">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr.n</th>
                            <th>Name</th>
                            <th>Profession</th>
                            <th>Rating</th>
                            <th>Profile</th>
                            <th>Content</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; @endphp
                        @foreach($ratingReviews as $ratingReview)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $ratingReview->name }}</td>
                            <td>{{ $ratingReview->profession }}</td>
                            <td>
                                @for ($i = 1; $i <= 5; $i++)
                                    @if($i <= $ratingReview->rating)
                                        ⭐
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </td>
                            <td>
                                <img src="{{ asset($ratingReview->photo) }}" alt="" style="width:60px;          height:60px">
                            </td>
                            <td>
                                @php
                                    $words = explode(' ', $ratingReview->content);
                                    $shortContent = implode(' ', array_slice($words, 0, 4));
                                @endphp

                                {{ $shortContent }}...

                                <!-- More button -->
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $ratingReview->id }}">
                                    More
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="reviewModal{{ $ratingReview->id }}" tabindex="-1" aria-labelledby="reviewModalLabel{{ $ratingReview->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="reviewModalLabel{{ $ratingReview->id }}">Review by {{ $ratingReview->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                {{ $ratingReview->content }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.edit_rating_review', ['id' => $ratingReview->id]) }}"
                                    class="btn btn-info btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.delete_rating_review', $ratingReview->id) }}"      method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this review?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
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