@extends('layouts.main')

@section('title', 'Availability')

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
                <a class="nav-link {{ request()->routeIs('manageAvailability') ? 'active' : '' }}" href="{{ route('manageAvailability') }}">Availability</a>
            </li>
        </ul>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-2 pr-2 pt-2" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- AVAILABILITY TABLE -->
    <div class="table-responsive table-container shadow-sm">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Book Title</th>
                    <th>Type</th>
                    <th>Current Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                @forelse($books as $book)
                <tr>
                    <td>{{ $book->title }}</td>
                    <td>Physical Copy</td>
                    <td>
                        <span class="badge-status {{ strtolower($book->current_status) }}">
                            {{ strtoupper($book->current_status) }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('librarian.updateStatus', $book->book_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="available" {{ $book->current_status === 'available' ? 'selected' : '' }}>
                                    Available
                                </option>
                                <!-- <option value="borrowed" {{ $book->current_status === 'borrowed' ? 'selected' : '' }}>
                                    Borrowed -->
                                <option value="unavailable" {{ $book->status === 'unavailable' ? 'selected' : '' }}>
                                    Unavailable
                                </option>
                            </select>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No books found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $books->appends(request()->query())->links() }}
    </div>
</div>
@endsection