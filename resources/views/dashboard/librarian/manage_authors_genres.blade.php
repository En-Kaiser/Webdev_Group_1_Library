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
                <a class="nav-link {{ request()->routeIs('admin.manageBooks') ? 'active' : '' }}" href="{{ route('admin.manageBooks') }}">Books</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manageAuthorsGenres') ? 'active' : '' }}" href="{{ route('manageAuthorsGenres') }}">Authors & Genres</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manageRecords') ? 'active' : '' }}" href="{{ route('manageRecords') }}">Records</a>
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
            <div class="table-responsive table-container shadow-sm">
                <table class="table table-hover mb-0">
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
                                        <button type="submit" class="delete-icon" title="Delete" onclick="return confirm('Are you sure you want to remove {{  $author->name  }}?')">
                                            <img src="{{ asset('icons/delete.svg') }}" alt="Delete" width="20" height="20">
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
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-2 pr-2 pt-2" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>

        <!-- Genres Table -->
        <div class="col-md-6">
            <div class="table-responsive table-container shadow-sm">
                <table class="table table-hover mb-0">
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
                                        <button type="submit" class="delete-icon" title="Delete" onclick="return confirm('Are you sure you want to remove {{  $genre->name  }}?')">
                                            <img src="{{ asset('icons/delete.svg') }}" alt="Delete" width="20" height="20">
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