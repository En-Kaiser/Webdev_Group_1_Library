@extends('layouts.main')
@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-hero">
    <div class="container px-md-5">
        <div class="row align-items-center">

            <div class="col-lg-7">
                <h1 class="dashboard-title">
                    Mula sa'yo,<br>para sa bayan
                </h1>

                <p class="dashboard-subtitle">
                    Read any book you want from our University Library Repository!
                </p>

                <!-- SEARCH BAR -->
                <div class="search-container position-relative">
                    <form method="GET" action="{{ route('dashboard.search') }}" class="d-flex align-items-center">
                        <i class="bi bi-search position-absolute ms-4"></i>
                        <input name="search"
                            class="form-control"
                            type="search"
                            placeholder="What book are you looking for?"
                            value="{{ $searchTerm ?? '' }}" />
                        <button type="submit" style="display: none;">Search</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-block text-center">
                <img src="{{ asset('images/DASH_Illustration.png') }}"
                    alt="PUP Library Illustration"
                    class="img-fluid hero-side-img">
            </div>

        </div>
    </div>
</div>

<div class="card-container container">
    <div class="row g-4 px-md-5">

        @auth
        {{-- If the user is logged in, check their specific role --}}
        @if(Auth::user()->role === 'librarian')
        <!-- ALL BOOKS CARD -->
        <div class="col-md-4">
            <a href="{{ route('librarian.viewAll') }}" class="action-card ">
                <i class="bi bi-collection"></i>
                <h3>Manage Books</h3>
            </a>
        </div>

        <!-- MONITOR USERS CARD -->
        <div class="col-md-4">
            <a href="{{ route('librarian.monitorUsers') }}" class="action-card">
                <i class="bi bi-people"></i>
                <h3>Monitor Users</h3>
            </a>
        </div>

        <!-- TRANSACTIONS CARD -->
        <div class="col-md-4">
            <a href="{{ route('librarian.transactions') }}" class="action-card">
                <i class="bi bi-archive"></i>
                <h3>Transactions</h3>
            </a>
        </div>

        @else
        <!-- ALL BOOKS CARD -->
        <div class="col-md-4">
            <a href="{{ route('student.viewAll') }}" class="action-card ">
                <i class="bi bi-collection"></i>
                <h3>All Books</h3>
            </a>
        </div>

        <!-- BOOKMARKED CARD -->
        <div class="col-md-4">
            <a href="{{ route('student.bookmarked') }}" class="action-card">
                <i class="bi bi-bookmarks"></i>
                <h3>Bookmarks</h3>
            </a>
        </div>

        <!-- HISTORY CARD -->
        <div class="col-md-4">
            <a href="{{ route('student.history') }}" class="action-card">
                <i class="bi bi-clock-history"></i>
                <h3>History</h3>
            </a>
        </div>
        @endif
        @endauth

        @guest
        <div class="col-md-12">
            <a href="{{ route('student.viewAll') }}" class="action-card text-center">
                <i class="bi bi-collection"></i>
                <h3>All Books</h3><br>
            </a>
        </div>
        @endguest

    </div>
</div>



<!-- <form method="GET" action="{{ route('dashboard.search') }}" class="mb-4">
        <div class="input-group" style="max-width: 400px;">
            <input type="text" name="search" placeholder="Search" value="{{ $searchTerm ?? '' }}">
            <button type="submit">Search</button>
        </div>
    </form> -->

@endsection