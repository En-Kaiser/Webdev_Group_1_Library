@extends('layouts.main')
@section('title', 'Welcome to PUPShelf!')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
@endpush

@section('content')
<div class="welcome-bg d-flex align-items-center">

    <div class="container text-center">
        <h1 class="welcome-title" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
            WELCOME TO PUPShelf
        </h1>

        <p class="welcome-subtitle text-center">
            Your gateway to the PUP University Library Repository—empowering the Iskolar ng Bayan
            with access to thousands of books, resources, and academic materials anytime, anywhere.
        </p>

        <a class="get-started-btn" href="{{ route('dashboard.index') }}" class="btn btn-lg px-5 py-3 fw-bold">
            Get Started
        </a>

        <!-- DISPLAY CARDS -->
        <div class="row g-4 mt-5 justify-content-center">
            <!-- CARD 1 -->
            <div class="col-md-3 col-6">
                <div class="status-box">
                    <h3 class="fw-bold mb-0 text-dark">{{ $bookCount  }}</h3>
                    <p class="text-muted fw-bold text-uppercase">Books Available</p>
                </div>
            </div>

            <!-- CARD 2 -->
            <div class="col-md-3 col-6">
                <div class="status-box" style="border-bottom: 5px solid #800000;">
                    <h3 class="fw-bold mb-0 text-dark">{{ $userCount  }}</h3>
                    <small class="text-muted fw-bold text-uppercase">Students</small>
                </div>
            </div>

            <!-- CARD 3 -->
            <div class="col-md-3 col-6">
                <div class="status-box" style="border-bottom: 5px solid #800000;">
                    <h3 class="fw-bold mb-0 text-dark">24/7</h3>
                    <small class="text-muted fw-bold text-uppercase">Access</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection