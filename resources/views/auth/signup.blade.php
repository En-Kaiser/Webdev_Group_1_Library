@extends('layouts.auth')
@section('title', 'Sign Up')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="auth-container">

        <div class="auth-image signup-bg">
            <h1>Empowering Your Academic Journey Through Digital Discovery</h1>
            <p>We invite you to join our community of lifelong learners.
                Create your PUPLMS account today and gain instant
                access to thousands of digital resources, journals, and
                repository materials — anytime, anywhere..</p>
        </div>

        <!-- FORM -->
        <div class="auth-form-section">
            <form method="POST" action="{{ route('auth.signup') }}" class="auth-form">
                @csrf
                <h2>Welcome to PUPShelf</h2>
                @error('first_name')
                <div style="color: Yellow;">{{ $message }}</div>
                @enderror
                <input type="text" placeholder="First Name" name="first_name" required>
                @error('last_name')
                <div style="color: Yellow;">{{ $message }}</div>
                @enderror
                <input type="text" placeholder="Last Name" name="last_name" required>

                @error('email')
                <div style="color: Yellow;">{{ $message }}</div>
                @enderror
                <input type="email" placeholder="Email" name="email" required>

                @error('password')
                <div style="color: Yellow;">{{ $message }}</div>
                @enderror

                <div class="password-wrapper">
                    <input type="password" id="password" placeholder="Password" name="password" required>
                    <button type="button" id="togglePassword" class="toggle-btn">
                        SHOW
                    </button>
                </div>

                @error('course')
                <div style="color: Yellow;">{{ $message }}</div>
                @enderror
                <select class='form-select' name="course" required>
                    <option disabled selected>Choose Course</option>
                    @foreach($courses as $course)
                    <option value="{{ $course->course_id }}">{{$course->name}}</option>
                    @endforeach
                </select>

                <button type="submit" style="margin-top: 1.8rem;">Create Account</button>

                <div class="auth-footer">
                    Already have an account? <a href="{{ route('auth.login') }}">Sign in</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Password Toggle -->
<script>
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');

    togglePassword.addEventListener('click', function() {
        // Toggle the type attribute
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

        // Toggle the button text (SHOW / HIDE)
        this.textContent = isPassword ? 'HIDE' : 'SHOW';
    });
</script>
@endsection