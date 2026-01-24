@extends('layouts.main')
@section('title', 'User Transactions')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/transactions.css') }}">
@endpush

@section('content')
<div class="container py-5">

    <!-- TRANSACTION REQUESTS -->
    <div class="page-header">
        <h1 class="page-title">Transaction Requests</h1>
    </div>

    @foreach($pendingRequests as $request)
    <div class="request-wrapper">
        <div class="request-row">
            <div class="request-info">
                <span>{{ $request->user->first_name }} {{ $request->user->last_name }}</span>
                <span>{{ $request->book->title }}</span>
                <span>{{ $request->type }}</span>
            </div>
            <div class="status-pill">
                <span>{{ $request->status }}</span>
            </div>
        </div>

        <div class="action-btns">
            <!-- REJECT BUTTON -->
            <form action="{{ route('librarian.transactions.reject', $request->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn-circle btn-reject">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </form>
            <!-- APPROVE BUTTON -->
            <form action="{{ route('librarian.transactions.approve', $request->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn-circle btn-approve">
                    <i class="bi bi-check-circle-fill"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach

    <!-- TRANSACTION STATUS -->
    <div class="page-header">
        <h1 class="page-title">Transaction Status</h1>
    </div>

    <div class="table-responsive table-container shadow-sm">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Book Title</th>
                    <th>Type</th>
                    <th>Date Borrowed</th>
                    <th>Date Returned</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($completedTransactions as $trans)
                <tr>
                    <td>{{ $trans->user_name }}</td>
                    <td>{{ $trans->book_title }}</td>
                    <td>{{ $trans->type }}</td>
                    <td>{{ $trans->borrow_date }}</td>
                    <td>{{ $trans->return_date ?? '-' }}</td>
                    <td>
                        <span class="badge-status {{ strtolower($trans->status) }}">
                            {{ $trans->status }}
                        </span>
                    </td>
                </tr>

                @empty
                @for($i = 0; $i < 3; $i++)
                    <tr>
                    <td class="text-muted">-</td>
                    <td class="text-muted">-</td>
                    <td class="text-muted">-</td>
                    <td class="text-muted">-</td>
                    <td class="text-muted">-</td>
                    <td class="text-muted">-</td>
                    <td class="text-muted">-</td>
                    </tr>
                    @endfor
                    @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection