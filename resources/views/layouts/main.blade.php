<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'PUPShelf')</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{ asset('css/global.css') }}">
  <link rel="icon" type="image/png" href="{{ asset('pup_icon.png') }}">

  @vite(['resources/sass/app.scss', 'resources/js/app.js'])
  @stack('styles')
</head>

<body style="min-height: 100vh; display: flex; flex-direction: column;">
  @include('layouts.header')
  <main style="flex:1;">
    @yield('content')
  </main>

  @include('layouts.footer')
  <div id="global-loader">
    <div class="spinner"></div>
  </div>

  <div id="processing-overlay">
    <div class="spinner mb-3"></div>
    <h4>Processing Request...</h4>
    <p>Please do not close this page.</p>
  </div>

  
  <script>
    window.addEventListener("load", function() {
      const loader = document.getElementById("global-loader");
      loader.style.opacity = "0";
      setTimeout(function() {
        loader.style.display = "none";
      }, 500);
    });

    document.addEventListener("DOMContentLoaded", function() {
      const forms = document.querySelectorAll('form');
      const overlay = document.getElementById('processing-overlay');

      forms.forEach(form => {
        form.addEventListener('submit', function(e) {
          if (form.checkValidity()) {
            overlay.style.display = 'flex';
          }
        });
      });
    });
  </script>

  @stack('scripts')
</body>

</html>