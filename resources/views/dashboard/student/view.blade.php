@extends('layouts.main')
@section('title', 'All Books')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/books-grid.css') }}">
<style>
    .book-card-item {
        transition: opacity 0.3s ease;
    }
</style>
@endpush

@section('content')
<div class="container px-md-5 mt-3">
    <div class="page-header">

        <h1 class="page-title">All Books</h1>
        <!-- Added by Jarell: Added a search bar here, pafix nalang if may problem-->
        <div class="header-controls">
            <!-- SEACH BAR -->
            <div class="search-container position-relative me-2">
                <form action="{{ route('dashboard.search') }}" method="GET" class="d-flex align-items-center">

                    <i class="bi bi-search position-absolute ms-3 text-muted"></i>

                    <input name="search"
                        class="form-control ps-5"
                        type="search"
                        placeholder="Search books..."
                        value="{{ $searchTerm ?? '' }}"
                        aria-label="Search">

                    <button type="submit" class="d-none">Search</button>
                </form>
            </div>

            <!-- FILTER BUTTON -->
            <div class="dropdown">
                <button class="btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-funnel"></i>
                    <span id="current-filter">{{ request('genre') && request('genre') !== 'all' ? request('genre') : 'Filter' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item filter-opt" href="?genre=all">All Genres</a></li>
                    <li><hr class="dropdown-divider"></li>
                    @foreach($genres as $genre)
                    <li><a class="dropdown-item filter-opt" href="?genre={{ urlencode($genre->name) }}">{{ $genre->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            @auth
            <!-- BOOKMARK -->
            <a href="{{ route('student.bookmarked') }}" class="icon-bookmark">
                <i class="bi bi-bookmark outline-icon" title="View Bookmarks"></i>
                <i class="bi bi-bookmark-fill fill-icon" title="View Bookmarks"></i>
            </a>
            @endauth
        </div>
    </div>

    <!-- Mobile: 2 columns | Tablet: 3 columns | Desktop: 6 columns -->
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-4">
        @forelse($books as $book)
        <div class="col book-card-item" data-genre="{{ $book->genre }}">

            <x-book-card
                :id="$book->book_id"
                :title="$book->title"
                :author="$book->author"
                :genre="$book->genre"
                :year="$book->year"
                :cover="$book->image" />

        </div>
        @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted">No books found.</p>
        </div>
        @endforelse
    </div>
    <div class="m-5"></div>
</div>
@endsection