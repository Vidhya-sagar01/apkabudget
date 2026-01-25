@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Terms & Conditions (Per Category)</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.terms.update') }}" method="POST">
                @csrf

                {{-- Tabs Nav --}}
                <ul class="nav nav-tabs mb-3" id="termsTabs" role="tablist">
                    @foreach ($categories as $index => $category)
                        <li class="nav-item">
                            <a class="nav-link {{ $index === 0 ? 'active' : '' }}" id="tab-{{ $category->id }}"
                                data-toggle="tab" href="#cat-{{ $category->id }}" role="tab"
                                aria-controls="cat-{{ $category->id }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                {{ $category->category }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- Tabs Content --}}
                <div class="tab-content" id="termsTabContent">
                    @foreach ($categories as $index => $category)
                        @php
                            $term = $terms[$category->id] ?? null;
                        @endphp
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="cat-{{ $category->id }}"
                            role="tabpanel" aria-labelledby="tab-{{ $category->id }}">

                            <h5 class="mb-3">{{ $category->name }} Terms & Conditions</h5>

                            <div class="form-group">
                                <label>Content (English)</label>
                                <textarea name="content_english[{{ $category->id }}]" class="form-control editor"
                                    rows="6">{{ $term->content_english ?? '' }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Content (Hindi)</label>
                                <textarea name="content_hindi[{{ $category->id }}]" class="form-control editor"
                                    rows="6">{{ $term->content_hindi ?? '' }}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary mt-3">Save All</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.ckeditor.com/4.20.2/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replaceAll(function (textarea, config) {
            config.removePlugins = 'notification';
        });
    </script>
@endsection