<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
</head>

<body>
    <h2>Log In</h2><br><br>
    <form method="post" action="{{ route('auth.login') }}">
        @csrf
        <input type="email" placeholder="Email" name="email"><br><br>
        <input type="password" placeholder="Password" name="password"><br><br>
        <button type="submit">Log In</button>
    </form>
</body>

</html> -->

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
            <form method="POST" action="{{ route('auth.signup') }}" class="auth-form">
                @csrf
                <h2>Welcome Back!</h2>

                <input type="email" placeholder="Email" name="email" required>
                <input type="password" placeholder="Password" name="password" required>

                <div class="divider"></div>

                <select name="course" required>
                    <option value="" disabled selected>Choose Course</option>
                    <option value="BSIT">BSIT</option>
                    <option value="BSCS">BSCS</option>
                </select>

                <button type="submit" style="margin-top: 8rem;">Log In</button>

                <div class="auth-footer">
                    Dont have an account? <a href="{{ route('auth.signup') }}">Sign up</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection