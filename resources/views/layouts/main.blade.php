<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'PUPShelf')</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{ asset('css/global.css') }}">
  <link rel="stylesheet" href="{{ asset('css/student_book.css') }}">

  @vite(['resources/sass/app.scss', 'resources/js/app.js'])
  @stack('styles')
</head>

<body style="min-height: 100vh; display: flex; flex-direction: column;">
  @include('layouts.header')
  <main style="flex:1;">
    @yield('content')
  </main>

  @include('layouts.footer')
  @stack('scripts')
</body>

</html>