@extends('layouts.main')
@section('title', 'All Books')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/books-grid.css') }}">
@endpush

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <h2>All Books</h2>

  </div>
</div>
@endsection