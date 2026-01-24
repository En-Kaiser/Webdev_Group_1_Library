@extends('layouts.auth')
@section('title', 'Log In')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="auth-container">

        <!-- IMAGE -->
        <div class="auth-image login-bg">
        </div>

        <!-- LOG-IN FORM -->
        <div class="auth-form-section">
            <form method="POST" action="{{ route('auth.login') }}" class="auth-form">

                @csrf

                <h2>Welcome Back!</h2>
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

                <div class="divider"></div>

                @error('course')
                <div style="color: Yellow;">{{ $message }}</div>
                @enderror
                <select class='form-select' name="course" required>
                    <option disabled selected>Choose Course</option>
                    @foreach($courses as $course)
                    <option value="{{$course->course_id}}">{{$course->name}}</option>
                    @endforeach
                </select>

                <button type="submit" style="margin-top: 6rem;">Log In</button>

                <div class="auth-footer">
                    Dont have an account? <a href="{{ route('auth.signup') }}">Sign up</a>
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