<!-- Footer -->
<footer class="text-white" style="background-color: #550000; display: flex; flex-direction: column;">

  <div class="container py-5">
    <div class="row">

      <div class="col-md-5 col-lg-5 col-xl-5 mx-auto">
        <h5 class="text-uppercase mb-4 font-weight-bold">
          <a href="/about" class="footer-link title-link">About Us</a>
        </h5>
        <p style="font-size: 0.9rem; line-height: 1.6;">
          The PUP Library Management System is a centralized digital platform designed to manage, organize, and provide access to library collections and online resources. It supports students and faculty by enabling efficient searching, borrowing, and utilization of academic materials to enhance learning and research.
        </p>
      </div>

      <div class="col-md-2 col-lg-2 col-xl-2 mx-auto">
        <h5 class="text-uppercase mb-4 font-weight-bold" style="color: white;">Features</h5>
        <p><a href="{{ route('dashboard.viewAll') }}" class="footer-link">All Books</a></p>
        <p><a href="{{ route('dashboard.bookmarked') }}" class="footer-link">Bookmarked</a></p>
        <p><a href="{{ route('dashboard.history') }}" class="footer-link">History</a></p>
      </div>

      <div class="col-md-4 col-lg-3 col-xl-3 mx-auto">
        <h5 class="text-uppercase mb-4 font-weight-bold">Contacts</h5>
        <p class="mb-1">2/F NALLRC Bldg. A Mabini Campus, Anonas Street, Sta. Mesa Manila, Philippines 1016</p>
        <p class="mb-1">puplibrary@pup.edu.ph</p>
        <p class="mb-0">(02) 5335 1787</p>
      </div>

    </div>
  </div>

  <div class="text-center p-3" style="background-color: rgba(0,0,0,0.2);">
    © 2025 Polytechnic University of the Philippines. All rights reserved.
  </div>

  <style>
    .footer-link {
      color: white !important;
      text-decoration: none;
      transition: all 0.3s ease-in-out;
      display: inline-block;
    }

    .footer-link:hover {
      color: #ffc107 !important;
      text-decoration: underline !important;
      transform: translateX(5px);
    }

    .title-link {
      font-weight: bold;
    }

    h5.font-weight-bold:not(a) {
      cursor: default;
    }
  </style>
</footer>