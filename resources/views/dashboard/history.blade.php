@extends('layouts.main')
@section('title', 'History')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/books-grid.css') }}">
@endpush

@section('content')
<div class="container px-md-5 mt-3">
    <div class="page-header">

        <h1 class="page-title">History</h1>

        <div class="header-controls">
            <!-- FILTER BUTTON -->
            <div class="dropdown">
                <button class="btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-funnel"></i>
                    <span id="current-filter">Filter</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item filter-opt" data-value="all">All Status</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item filter-opt" data-value="Borrowed">Borrowed</a></li>
                    <li><a class="dropdown-item filter-opt" data-value="Returned">Returned</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-responsive history-table-container shadow sm">
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
                <tr class="history-row" data-status="{{ $item->status }}">
                    <td class="fw-medium text-muted">{{ $item->title }}</td>
                    <td class="text-muted">{{ $item->type }}</td>
                    <td class="text-muted">{{ $item->date_borrowed }}</td>
                    <td class="text-muted">{{ $item->date_returned ?? '-' }}</td>
                    <td>
                        <span class="badge-status {{ strtolower($item->status) }}">
                            {{ $item->status }}
                        </span>
                    </td>
                </tr>
                @empty
                @for($i = 0; $i < 8; $i++)
                    <tr>
                    <td class="text-muted">-</td>
                    <td class="text-muted">-</td>
                    <td class="text-muted">-</td>
                    <td class="text-muted">-</td>
                    <td>
                        <span class="badge-status">
                            -
                        </span>
                    </td>
                    </tr>
                    @endfor
                    @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterOptions = document.querySelectorAll('.filter-opt');
        const historyRows = document.querySelectorAll('.history-row');
        const filterLabel = document.getElementById('current-filter');

        filterOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();

                const selectedStatus = this.getAttribute('data-value');

                // Update Label
                if (filterLabel) {
                    filterLabel.innerText = selectedStatus === 'all' ? 'Filter' : selectedStatus;
                }

                // Filter Rows
                historyRows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');

                    if (selectedStatus === 'all' || rowStatus === selectedStatus) {
                        row.classList.remove('d-none');
                    } else {
                        row.classList.add('d-none');
                    }
                });
            });
        });
    });
</script>
@endpush