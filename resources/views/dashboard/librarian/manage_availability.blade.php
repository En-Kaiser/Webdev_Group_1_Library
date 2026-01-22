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

    <!-- AVAILABILITY TABLE -->
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover align-middle mb-0">
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
                    <td>{{ $book->type ?? 'Physical Book' }}</td>
                    <td>
                        <span class="badge-status  {{ strtolower($book->status) }}">
                            {{ strtoupper($book->status) }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('librarian.updateStatus', $book->book_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')

                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="available" {{ $book->status === 'available' ? 'selected' : '' }}>
                                    Available
                                </option>
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
</div>
@endsection