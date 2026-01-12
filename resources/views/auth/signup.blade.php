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

                <input type="text" placeholder="First Name" name="first_name" required>
                <input type="text" placeholder="Last Name" name="last_name" required>

                <input type="email" placeholder="Email" name="email" required>
                <input type="password" placeholder="Password" name="password" required>

                <select name="course" required>
                    <option value="" disabled selected>Choose Course</option>
                    <option value="BSIT">BSIT</option>
                    <option value="BSCS">BSCS</option>
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