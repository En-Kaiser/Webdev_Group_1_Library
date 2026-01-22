@extends('layouts.main')

@php
$hasPhysical = $book_type_avail->where('type', 'physical')->where('availability', 'available')->first();
$hasEbook = $book_type_avail->where('type', 'e_book')->where('availability', 'available')->first();
@endphp

@section('styles')
<link rel="stylesheet" href="{{ asset('css/student_book.css') }}">
@endsection

@section('content')
<div class="book-container">

    <div class="book-hero-section">
        <div class="hero-left">
            <div class="side-votes">
                <img src="{{ asset('icons/arrow-up.svg') }}" class="icon" alt="Arrow Up">
                <img src="{{ asset('icons/arrow-down.svg') }}" class="icon" alt="Arrow Down">
            </div>

            <div class="book-hero-image">
                @if($book->image)
                <img src="{{ asset('images/' . $book->image) }}" alt="{{ $book->title }} Cover">
                @else
                <div class="d-flex align-items-center justify-content-center" style="width:220px; height:300px; background:#f8f9fa;">
                    <i class="bi bi-book text-secondary" style="font-size: 3rem;"></i>
                </div>
                @endif
            </div>
        </div>

        <div class="book-meta">
            <h1>{{ $book->title }}</h1>
            @foreach($authors as $author)
            <span class="author-tag">Author: <b>{{ $author->name }}</b></span>{{ !$loop->last ? ',' : '' }}
            @endforeach
            <p class="year">Year: <b>{{ $book->year }}</b></p>
            <p class="genre">
                @foreach($genres as $genre)
                <span class="genre-tag">Genre: <b>{{ $genre->name }}</b></span>{{ !$loop->last ? ',' : '' }}
                @endforeach
            </p>
        </div>
    </div>

    <div class="big-info-box mb-5">

        <div class="book_type_avail-overlay d-flex justify-content-between align-items-center">

            <span class="book_type_avail-badge {{ $isAvailable ? 'available' : 'unavailable' }}">
                Availability: <b>{{ $isAvailable ? 'Available' : 'Unavailable' }}</b>
            </span>

            <div class="d-flex align-items-center gap-3">

                @auth
                <button type="button"
                    class="btn btn-primary btn-sm px-4"
                    data-bs-toggle="modal"
                    data-bs-target="#borrowModal">
                    Borrow Book
                </button>

                @else
                <a href="{{ route('auth.showSignUp') }}" class="btn btn-primary btn-sm px-4">Borrow Book</a>
                @endauth
                <div class="bookmark-controls">
                    <form action="{{ route('books.bookmark', $book->book_id) }}" method="POST" id="bookmark-form">
                        @csrf

                        <button type="submit" style="border: none; background: none; padding: 0;">
                            <img src="{{ $isBookmarked ? asset('icons/bookmarked.svg') : asset('icons/bookmark.svg') }}"
                                class="bookmark-icon"
                                alt="Bookmark"
                                title="{{ $isBookmarked ? 'Remove Bookmark' : 'Add Bookmark' }}"
                                style="width:22px; height:22px; cursor:pointer;">
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function toggleBookmark(el) {
                const bookmark = "{{ asset('icons/bookmark.svg') }}";
                const bookmarked = "{{ asset('icons/bookmarked.svg') }}";
                el.src = el.src.includes('bookmarked.svg') ? bookmark : bookmarked;
            }
        </script>

        <hr class="info-divider">
        
        <div class="big-info-content">
            <div class="content-columns">
                <div class="left-column">
                    <strong>Description</strong>
                    <p>{{ $book->short_description }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="borrowModal" tabindex="-1" aria-labelledby="borrowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="borrowModalLabel">Borrow Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('books.borrow', $book->book_id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3 p-3 bg-light rounded">
                        <p class="mb-1"><strong>Book Title:</strong> {{ $book->title }}</p>
                        <p class="mb-1"><strong>Genre:</strong> {{ $genres->pluck('name')->implode(', ') }}</p>
                        <p class="mb-1"><strong>Author:</strong> {{ $authors->pluck('name')->implode(', ') }}</p>
                        <p class="mb-0"><strong>Year:</strong> {{ $book->year }}</p>
                    </div>

                    <div class="mb-3">
                        <label for="book_type_id" class="form-label">Choose Format</label>

                        <select name="book_type_id" id="book_type_id" class="form-select" required>
                            <option value="" selected disabled>Select format...</option>

                            <option value="{{ $hasPhysical ? $hasPhysical->book_type_id : '' }}"
                                {{ !$hasPhysical ? 'disabled' : '' }}>
                                Physical {{ !$hasPhysical ? '(Not Available)' : '' }}
                            </option>

                            <option value="{{ $hasEbook ? $hasEbook->book_type_id : '' }}"
                                {{ !$hasEbook ? 'disabled' : '' }}>
                                E-Book {{ !$hasEbook ? '(Not Available)' : '' }}
                            </option>
                        </select>

                        @if(!$hasPhysical && !$hasEbook)
                        <div class="form-text text-danger">No copies are currently available.</div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" {{ !$isAvailable ? 'disabled' : '' }}>Borrow</button>
                    </div>
            </form>
        </div>
    </div>
</div>
@endsection