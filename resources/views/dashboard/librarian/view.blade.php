@extends('layouts.main')
@section('title', 'All Books')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/books-grid.css') }}">
@endpush

@section('content')
<div class="container py-5">
  <div class="page-header">
    <h1 class="page-title">Manage Books</h1>
  </div>
</div>
@endsection