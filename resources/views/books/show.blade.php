@extends('layouts.main')
@section('title', $book->title)

@php
$hasPhysical = $book_type_avail->where('type', 'physical')->where('availability', 'available')->first();
$hasEbook = $book_type_avail->where('type', 'e_book')->where('availability', 'available')->first();
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('css/student_book.css') }}">
@endpush

@section('content')
<div class="container mt-3">
    {{-- Success Feedback --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Error Feedback --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
</div>


<div class="book-container">

    <!-- HERO SECTION -->
    <div class="book-hero-section">
        <div class="hero-left">
            <div class="side-votes">
                @if($prevId)
                <a href="{{ route('books.show', $prevId) }}" title="Previous Book">
                    <img src="{{ asset('icons/arrow-up.svg') }}" class="icon" alt="Previous Book">
                </a>
                @else
                <img src="{{ asset('icons/arrow-up.svg') }}" class="icon disabled" alt="No Previous Book" style="opacity: 0.4;">
                @endif

                @if($nextId)
                <a href="{{ route('books.show', $nextId) }}" title="Next Book">
                    <img src="{{ asset('icons/arrow-down.svg') }}" class="icon" alt="Next Book">
                </a>
                @else
                <img src="{{ asset('icons/arrow-down.svg') }}" class="icon disabled" alt="No Next Book" style="opacity: 0.4;">
                @endif
            </div>

            <div class="book-hero-image">
                @if($book->image)
                <img src="{{ asset('books/' . $book->image) }}" alt="{{ $book->title }} Cover">
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

    <!-- INFO BOX -->
    <div class="big-info-box mb-5">

        <div class="book_type_avail-overlay d-flex justify-content-between align-items-center">

            <span class="book_type_avail-badge {{ $isAvailable ? 'available' : 'unavailable' }}" style="margin-left: 37rem;">
                Availability: <b>{{ $isAvailable ? 'Available' : 'Unavailable' }}</b>
            </span>

            <div class="d-flex align-items-center gap-3">
                {{-- Check if user is a Librarian / Admin --}}
                @if(Auth::guard('admin')->check())
                <span class="text-muted small">Admin View Mode</span>
                @else
                {{-- Check if user is a Student --}}
                @auth
                <button type="button"
                    class="btn btn-primary btn-sm px-4"
                    data-bs-toggle="modal"
                    data-bs-target="#borrowModal">
                    Borrow Book
                </button>

                <div class="bookmark-controls">
                    @auth
                    <form action="{{ route('books.bookmark', $book->book_id) }}" method="POST" id="bookmark-form">
                        @csrf
                        <button type="submit" style="border: none; background: none; padding: 0;" class="icon-bookmark">
                            @if($isBookmarked)
                            <i class="bi bi-bookmark-fill" title="Remove Bookmark"></i>
                            @else
                            <i class="bi bi-bookmark outline-icon" title="Add Bookmark"></i>
                            <i class="bi bi-bookmark-fill fill-icon" title="Add Bookmark"></i>
                            @endif
                        </button>
                    </form>
                    @else
                    <a href="{{ route('auth.showSignUp') }}" style="text-decoration: none; color: inherit;">
                        <i class="bi bi-bookmark outline-icon" title="Login to Bookmark"></i>
                    </a>
                    @endauth
                </div>
                @else
                {{-- Guests --}}
                <a href="{{ route('auth.showSignUp') }}" class="btn btn-primary btn-sm px-4">Borrow Book</a>
                <div class="bookmark-controls">
                    <a href="{{ route('auth.showSignUp') }}" class="icon-bookmark">
                        <i class="bi bi-bookmark outline-icon" title="Sign Up to Bookmark"></i>
                        <i class="bi bi-bookmark-fill fill-icon" title="Sign Up to Bookmark"></i>
                    </a>
                </div>
                @endauth
                @endif
            </div>
        </div>

            </div>

        </div>
        <hr class="info-divider">

        <div class="big-info-content">
            <div class="content-columns">
                <div class="left-column mt-5">
                    <strong>Description</strong>
                    <p>{{ $book->short_description }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>



<!-- Borrow Modal -->
<div class="modal fade pupshelf-modal" id="borrowModal" tabindex="-1" aria-labelledby="borrowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header pupshelf-modal-header">
                <div>
                    <h5 class="modal-title" id="borrowModalLabel">Borrow Form</h5>
                    <small class="pupshelf-subtitle">Confirm details and choose a format</small>
                </div>
                <button type="button" class="btn-close pupshelf-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('books.borrow', $book->book_id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="pupshelf-bookcard">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="pupshelf-label">Book Title</div>
                                <div class="pupshelf-value">{{ $book->title }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="pupshelf-label">Year</div>
                                <div class="pupshelf-value">{{ $book->year }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="pupshelf-label">Genre</div>
                                <div class="pupshelf-value">
                                    {{ $genres->pluck('name')->implode(', ') }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="pupshelf-label">Author</div>
                                <div class="pupshelf-value">
                                    {{ $authors->pluck('name')->implode(', ') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="book_type_id" class="form-label pupshelf-form-label">
                            Choose Format
                        </label>

                        <select name="book_type_id" id="book_type_id"
                            class="form-select pupshelf-select" required>
                            <option value="" disabled selected>Select format...</option>

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
                        <div class="pupshelf-alert mt-2">
                            No copies are currently available.
                        </div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer pupshelf-modal-footer">
                    <button type="button"
                        class="btn pupshelf-btn-outline"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                        class="pupshelf-btn-primary"
                        {{ !$isAvailable ? 'disabled' : '' }}>
                        Borrow
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection