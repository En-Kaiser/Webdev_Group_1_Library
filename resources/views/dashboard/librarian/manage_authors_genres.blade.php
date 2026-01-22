@extends('layouts.main')

@section('title', 'Authors & Genres')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/manage-books.css') }}">
@endpush

@section('content')
<div class="container px-md-5 mt-3 mb-3">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Library Management</h1>

        <!-- TABS -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manageBooks') ? 'active' : '' }}" href="{{ route('manageBooks') }}">Books</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manageAuthorsGenres') ? 'active' : '' }}" href="{{ route('manageAuthorsGenres') }}">Authors & Genres</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manageAvailability') ? 'active' : '' }}" href="{{ route('manageAvailability') }}">Availability</a>
            </li>
        </ul>
    </div>

    <!-- ADD BUTTONS -->
    <div class="d-flex justify-content-between mb-2">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAuthorModal">
            + Add Author
        </button>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addGenreModal">
            + Add Genre
        </button>
    </div>

    <!-- AUTHORS & GENRES TABLES -->
    <div class="row">
        <!-- Authors Table -->
        <div class="col-md-6">
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Author</th>
                            <th>No. of Books</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($authors as $author)
                        <tr>
                            <td>{{ $author->name }}</td>
                            <td>{{ $author->books_count }}</td>
                            <td>
                                <div class="action-icons">
                                   
                                    <form action="{{ route('librarian.authors.destroy', $author->author_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-icon" style="margin-right: 1rem;" title="Delete" onclick="return confirm('Are you sure?')">
                                            <img src="{{ asset('icons/delete.svg') }}" alt="Delete" style="margin-right: 3rem;" width="20" height="20">
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No authors found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Genres Table -->
        <div class="col-md-6">
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Genre</th>
                            <th>No. of Books</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($genres as $genre)
                        <tr>
                            <td>{{ $genre->name }}</td>
                            <td>{{ $genre->books_count }}</td>
                            <td>
                                <div class="action-icons">
                                    <form action="{{ route('librarian.genres.destroy', $genre->genre_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-icon" style="margin-left: -5rem;" title="Delete" onclick="return confirm('Are you sure?')">
                                            <img src="{{ asset('icons/delete.svg') }}" style="margin-right: -5rem;" alt="Delete" width="20" height="20">
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">No genres found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@include('dashboard.librarian.modals.add_author_modal')
@include('dashboard.librarian.modals.add_genre_modal')