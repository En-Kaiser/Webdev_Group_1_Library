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
                <input type="password" placeholder="Password" name="password" required>

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
@endsection