@extends('layouts.main')

@section('title', 'About Us | PUPSHELF')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/aboutus.css') }}">
@endpush

@section('content')

<!-- FULL-WIDTH HERO -->
<section class="about-hero">
    <div class="hero-inner">
        <h1>About PUPSHELF</h1>
        <p>
            PUPSHELF is an online library system designed to make book discovery 
            and borrowing simple, fast, and accessible for students. 
        </p>
        <p>
            It helps learners easily find, track, and borrow books 
            while providing a user-friendly platform that supports their academic needs.
        </p>

        <div class="chips">
            <span>Online Library</span>
            <span>Book Borrowing</span>
            <span>Availability Tracking</span>
            <span>Student-Friendly UI</span>
        </div>
    </div>
</section>

<!-- PAGE CONTENT -->
<div class="aboutus-wrap">

    <section class="team-section">
        <h2>Meet the Team</h2>
        <p>The people behind the development of PUPSHELF.</p>

        <div class="team-grid">
            @php
            $team = [
                ['name'=>'Sy, Josiah Zachary','role'=>'Frontend Developer','img'=>'memberOne.jpg'],
                ['name'=>'Estalilla, Johanna Angela','role'=>'Frontend Developer','img'=>'memberTwo.jpg'],
                ['name'=>'Magpantay, Reina Chloe','role'=>'Backend Developer','img'=>'memberThree.jpg'],
                ['name'=>'Fababier, Jarell Vincent','role'=>'Backend Developer','img'=>'memberFour.jpg'],
                ['name'=>'Calata, Bianca Weizenthal','role'=>'Documentation','img'=>'memberFive.jpg'],
                ['name'=>'Camorongan, Christine Lei','role'=>'Documentation','img'=>'memberSix.jpg'],
            ];
            @endphp

            @foreach($team as $member)
                <div class="team-item">
                    <div class="team-card">
                        <img src="{{ asset('images/'.$member['img']) }}" alt="{{ $member['name'] }}">
                    </div>
                    <h3 class="team-name">{{ $member['name'] }}</h3>
                    <p class="team-role">{{ $member['role'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

</div>

@endsection