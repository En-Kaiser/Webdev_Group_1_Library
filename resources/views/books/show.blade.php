@extends('layouts.main')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/student_book.css') }}">
@endsection

@section('content')
<div class="book-container">

    <div class="book-hero-section">
        <!-- Arrow Icons AND Book Cover -->
        <div class="hero-left">
            <div class="side-votes">
                <img src="{{ asset('icons/arrow-up.svg') }}" class="icon" alt="Arrow Up">
                <img src="{{ asset('icons/arrow-down.svg') }}" class="icon" alt="Arrow Down">
            </div>

            <!-- Book Images -->
            <div class="book-hero-image">
                <img
                    src="{{ asset('images/' . $book->cover_image) }}"
                    alt="{{ $book->title }} Cover">
            </div>
        </div>

        <!-- Title, Author, Genre -->
        <div class="book-meta">
            <h1>{{ $book->title }}</h1>
            <p class="author">{{ $book->author_name }}</p>
            <p class="year">{{ $book->year }}</p>
            <p class="genre">{{ $genres->pluck('name')->implode(', ') }}</p>
        </div>
    </div>

    <!-- BIG GREY BOX -->
    <div class="big-info-box">

        <!-- Available/Borrowed AND Bookmark/Bookmarked -->
        <div class="availability-overlay">
            <span class="availability-badge {{ $isAvailable ? 'available' : 'borrowed' }}">
                {{ $isAvailable ? 'Available' : 'Borrowed' }}</span>
            <div class="bookmark-controls">
                <img src="{{ asset('icons/bookmark.svg') }}" class="bookmark-icon"
                    alt="Bookmark"
                    onclick="toggleBookmark(this)"
                    style="width:22px; height:22px; cursor:pointer;">
            </div>
        </div>

        <script>
            function toggleBookmark(el) {
                const bookmark = "{{ asset('icons/bookmark.svg') }}";
                const bookmarked = "{{ asset('icons/bookmarked.svg') }}";
                el.src = el.src.includes('bookmarked.svg') ? bookmark : bookmarked;
            }
        </script>


        <!-- Description, Language, ISBN -->
        <div class="big-info-content">
            <hr class="info-divider">

            <div class="content-columns">
                <div class="left-column">
                    <strong>Description</strong>
                    <p>{{ $book->description }}</p>
                </div>
                <div class="right-column">
                    <strong>Language</strong>
                    <p>{{ $book->language }}</p>
                    <strong style="margin-top: 2rem">ISBN</strong>
                    <p>{{ $book->isbn }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection