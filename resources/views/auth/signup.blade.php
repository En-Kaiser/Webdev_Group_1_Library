<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>

<body>
    <h2>Sign Up</h2>
    <form method="post" action="{{  route('auth.signup') }}">
        @csrf
        <input type="text" placeholder="First Name" name="first_name"><br><br>
        <input type="text" placeholder="Last Name" name="last_name"><br><br>
        <select name="course" id="course">
            <option value="BSIT">BSIT</option>
            <option value="BSCS">BSCS</option>
        </select><br><br>
        <input type="email" placeholder="Email" name="email"><br><br>
        <input type="password" placeholder="Password" name="password"><br><br>
        <button type="submit">
            Create Account
        </button>
    </form>
</body>

</html> -->

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
                <input type="password" placeholder="Password" name="password" required>
                @error('course')
                    <div style="color: Yellow;">{{ $message }}</div>
                @enderror
                <select class='form-select' name="course" required>
                    <option  disabled selected>Choose Course</option>
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
@endsection