@extends('layouts.main')
@section('title', 'History')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/books-grid.css') }}">
@endpush

@section('content')
<div class="container">
    
    
@if(session('download_triggered'))
    <div class="alert alert-success mt-3">
        <h4 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Borrow Successful!</h4>
        <p>Your download should start automatically.</p>
        <hr>
        <p class="mb-0">
            If the download didn't start, 
            {{-- MANUAL FALLBACK LINK --}}
            <a href="{{ route('books.download.itds') }}" class="btn btn-primary btn-sm fw-bold">
                Click here to download PDF
            </a>
        </p>
    </div>
@endif
<div class="container px-md-5 mt-3">
    <div class="page-header">

        <h1 class="page-title">History</h1>

        <div class="header-controls">
            <!-- FILTER BUTTON -->
            <div class="dropdown">
                <button class="btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-funnel"></i>
                    <span id="current-filter">{{ request('status') ? (request('status')) : 'Filter' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item filter-opt" href="{{ route('student.history') }}">All Status</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item filter-opt" href="{{ route('student.history', ['status' => 'borrowed']) }}">Borrowed</a></li>
                    <li><a class="dropdown-item filter-opt" href="{{ route('student.history', ['status' => 'returned']) }}">Returned</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-responsive table-container shadow-sm">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Type</th>
                    <th scope="col">Date Borrowed</th>
                    <th scope="col">Date Returned</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $item)
                <tr class="history-row">
                    <td class="fw-medium text-muted">{{ $item->title }}</td>
                    <td class="text-muted">{{ ($item->type) }}</td>
                    <td class="text-muted">{{ $item->date_borrowed }}</td>
                    <td class="text-muted">{{ $item->date_return ?? '-' }}</td>
                    <td>
                        <span class="badge-status {{ strtolower($item->status) }}">
                            {{ ($item->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <td colspan="5" class="text-center py-4 text-muted">No transactions found.</td>
                    @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
    @if(session('download_triggered'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Wait 1 second for the page to settle, then trigger download
                setTimeout(function() {
                    window.location.href = "{{ route('books.download.itds') }}";
                }, 1000);
            });
        </script>
    @endif
@endsection