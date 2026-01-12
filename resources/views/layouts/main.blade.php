<!DOCTYPE html>
<html lang="en">
<!-- unsure ako which one should be used here -->
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'PUP LMS')</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{ asset('css/global.css') }}">

  @vite(['resources/sass/app.scss', 'resources/js/app.js'])
  @stack('styles')
  @stack('scripts')
</head>

<body style="min-height: 100vh; display: flex; flex-direction: column;">
  @include('layouts.header')
  <main style="flex:1;">
    @yield('content')
  </main>

  @include('layouts.footer')
</body>

</html>
<!-- <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PUPShelf')</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('styles')
    @stack('styles')
</head>

<body>
    @include('layouts.header')
    @yield('content')
    @include('layouts.footer')
</body>

</html> -->
