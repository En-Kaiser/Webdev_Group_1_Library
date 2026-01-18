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

            <div class="dropdown">
                <button class="btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-funnel"></i>
                    <span id="current-filter">Filter</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item filter-opt" data-value="all">All Genres</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    @foreach($genres as $genre)
                        <li><a class="dropdown-item filter-opt" data-value="{{ $genre->name }}">{{ $genre->name }}</a></li>
                    @endforeach
    
                </ul>
            </div>
            <a href="{{ route('student.bookmarked') }}" class="icon-bookmark">
                <i class="bi bi-bookmark" title="View Bookmarks"></i>
            </a>
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
            />
            <!-- :cover="$book->cover_image" -->
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterOptions = document.querySelectorAll('.filter-opt');
        const bookItems = document.querySelectorAll('.book-card-item');
        const filterLabel = document.getElementById('current-filter');

        filterOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();

                const selectedGenre = this.getAttribute('data-value');
                const genreName = this.innerText;

                if (filterLabel) {
                    filterLabel.innerText = selectedGenre === 'all' ? 'Filter' : genreName;
                }

                bookItems.forEach(item => {
                    const itemGenre = item.getAttribute('data-genre');

                    if (selectedGenre === 'all' || itemGenre === selectedGenre) {
                        // SHOW: Remove the hidden class
                        item.classList.remove('d-none');
                    } else {
                        item.classList.add('d-none');
                    }
                });
            });
        });
    });
</script>
@endpush