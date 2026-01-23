@extends('layouts.main')

@section('title', 'Manage Books')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/manage-books.css') }}">
@endpush

@section('content')
<div class="container px-md-5 mt-3">
    <div class="page-header">
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
                <a class="nav-link {{ request()->routeIs('manageAvailability') ? 'active' : '' }}" href="{{ route('manageAvailability') }}">Availability</a>
            </li>
        </ul>
    </div>

    <!-- Controls: Search, Filter, Add -->
    <div class="d-flex flex-wrap gap-3">
        <div class="search-container position-relative me-2">
            <form action="{{ route('admin.manageBooks') }}" method="GET" class="d-flex align-items-center">
                <i class="bi bi-search position-absolute ms-3 text-muted"></i>
                <input name="search"
                    class="form-control ps-5 search-input"
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
                <span id="current-filter">{{ $selectedGenre ?? 'Filter' }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item filter-opt" href="{{ route('admin.manageBooks', request()->except('genre')) }}">All Genres</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                @foreach($genres as $genre)
                <li>
                    <a class="dropdown-item filter-opt {{ $selectedGenre == $genre->name ? 'active' : '' }}"
                        href="{{ route('admin.manageBooks', array_merge(request()->query(), ['genre' => $genre->name])) }}">
                        {{ $genre->name }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>

        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBookModal">
            <i class="bi bi-plus-lg me-1"></i> Add Book
        </button>
    </div>

    <!-- Books Table -->
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($books as $book)
                <tr>
                    <td class="fw-medium">{{ Str::limit($book->title, 40) }}</td>
                    <td>{{ $book->author }}</td>
                    <td>{{ $book->genre }}</td>
                    <td>{{ $book->types ?? 'Physical Book' }}</td>
                    <td>
                        <span class="badge-status {{ strtolower($book->status) }}">{{ $book->status }}</span>
                    </td>
                    <td>
                        <div class="action-icons">
                            <!-- Edit button that opens modal -->
                            <button type="button" class="edit-icon" title="Edit" data-bs-toggle="modal" data-bs-target="#editBookModal{{ $book->book_id }}">
                                <img src="{{ asset('icons/edit.svg') }}" alt="Edit" width="20" height="20">
                            </button>
                            <span class="action-divider">|</span>
                            <form action="{{ route('librarian.books.destroy', $book->book_id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-icon" title="Delete" onclick="return confirm('Are you sure you want to delete {{  $book->title  }}?')">
                                    <img src="{{ asset('icons/delete.svg') }}" alt="Delete" width="20" height="20">
                                </button>
                            </form>
                        </div>

                        <!-- EDIT BOOK MODAL -->
                        <div class="modal fade" id="editBookModal{{ $book->book_id }}" tabindex="-1"
                            aria-labelledby="editBookModalLabel{{ $book->book_id }}" aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content modal-form-section">
                                    <div class="modal-form">
                                        <h2>EDIT BOOK</h2>

                                        <form action="{{ route('librarian.books.update', $book->book_id) }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

                                            <!-- Title + Cover -->
                                            <div class="row mb-3">
                                                <div class="col-md-8">
                                                    <input type="text" name="title"
                                                        value="{{ $book->title }}"
                                                        class="form-control" placeholder="Title" required>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="d-flex gap-2">
                                                        <div class="flex-grow-1">
                                                            <input type="text"
                                                                class="form-control"
                                                                placeholder="Change Book Cover"
                                                                readonly>
                                                            <input type="file" name="cover_image"
                                                                class="d-none" accept="image/*">
                                                        </div>
                                                        <button type="button" class="btn btn-light"
                                                            onclick="this.previousElementSibling.querySelector('input[type=file]').click()">
                                                            Browse
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Description + Year -->
                                            <div class="row mb-3">
                                                <div class="col-md-8">
                                                    <textarea name="short_description"
                                                        class="form-control"
                                                        rows="2"
                                                        placeholder="Description">{{ $book->short_description }}</textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" name="year"
                                                        value="{{ $book->year }}"
                                                        class="form-control"
                                                        placeholder="Year"
                                                        min="1000" max="{{ date('Y') }}">
                                                </div>
                                            </div>

                                            <!-- Author + Add Author -->
                                            <div class="row mb-3">
                                                <div class="col-md-10">
                                                    <select name="author_id" class="form-select" required>
                                                        <option value="">Choose Author</option>
                                                        @foreach($authors as $author)
                                                        <option value="{{ $author->author_id }}"
                                                            {{ $book->author_id == $author->author_id ? 'selected' : '' }}>
                                                            {{ $author->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <button type="button" class="btn btn-add-action"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#addAuthorModal">
                                                        + Add Author
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Genre + Add Genre -->
                                            <div class="row mb-3">
                                                <div class="col-md-10">
                                                    <select name="genre_id" class="form-select" required>
                                                        <option value="">Choose Genre</option>
                                                        @foreach($genres as $genre)
                                                        <option value="{{ $genre->genre_id }}"
                                                            {{ $book->genre_id == $genre->genre_id ? 'selected' : '' }}>
                                                            {{ $genre->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <button type="button" class="btn btn-add-action"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#addGenreModal">
                                                        + Add Genre
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Type + Status -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <select name="type" class="form-select" required>
                                                        <option value="">Choose Type of Book</option>
                                                        <option value="physical" {{ $book->type == 'physical' ? 'selected' : '' }}>
                                                            Physical Book
                                                        </option>
                                                        <option value="e_book" {{ $book->type == 'e_book' ? 'selected' : '' }}>
                                                            E-Book
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <select name="status" class="form-select" required>
                                                        <option value="">Status</option>
                                                        <option value="available" {{ $book->availability == 'available' ? 'selected' : '' }}>
                                                            Available
                                                        </option>
                                                        <option value="unavailable" {{ $book->availability == 'unavailable' ? 'selected' : '' }}>
                                                            Unavailable
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Buttons -->
                                            <div class="d-flex justify-content-between mt-4">
                                                <a href="#" class="btn btn-cancel" data-bs-dismiss="modal">CANCEL</a>
                                                <button type="submit" class="btn btn-add-book">
                                                    UPDATE BOOK
                                                </button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No books found.</td>
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

    <div class="mt-4 flex justify-content-center">
        {{ $books->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@include('dashboard.librarian.modals.add_book_modal')
@include('dashboard.librarian.modals.add_author_modal')
@include('dashboard.librarian.modals.add_genre_modal')


@push('scripts')
<script>
    // File input preview for Add Book modal
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.querySelector('input[name="cover_image"]');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const fileName = e.target.files[0]?.name || 'Choose Book Cover';
                const textInput = fileInput.closest('.d-flex').querySelector('input[type="text"]');
                if (textInput) {
                    textInput.value = fileName;
                }
            });
        }
    });
</script>
@endpush