@extends('layouts.main')

@section('title', 'Records')

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

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-2 pr-2 pt-2" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- PHYSICAL BOOKS BORROWING TABLE -->
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Book Title</th>
                    <th>Borrowed By</th>
                    <th>Type</th>
                    <th>Date Borrowed</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($books as $book)
                <tr>
                    <td>{{ $book->title }}</td>
                    <td>{{ $book->first_name }} {{ $book->last_name }}</td>
                    <td>Physical Book</td>
                    <td>{{ \Carbon\Carbon::parse($book->date_borrowed)->format('M d, Y') }}</td>
                    <td>
                        <span class="badge-status {{ strtolower($book->status) }}">
                            {{ strtoupper($book->status) }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('librarian.updateStatus', $book->history_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')

                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="borrowed" {{ $book->status === 'borrowed' ? 'selected' : '' }}>
                                    Borrowed
                                </option>
                                <option value="returned" {{ $book->status === 'returned' ? 'selected' : '' }}>
                                    Returned
                                </option>
                            </select>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No physical books currently borrowed.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection