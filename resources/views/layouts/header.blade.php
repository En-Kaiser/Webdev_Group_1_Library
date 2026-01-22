<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-puplms">
  <div class="container-fluid">
    <!-- Logo/Brand -->
    <a class="navbar-brand" href="{{ route('welcome') }}">
      <img src="{{ asset('images/pup_logo.png') }}" alt="PUPLMS Logo" height="35">
      <span>PUPShelf</span>
    </a>

    <!-- Mobile Toggle Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Content -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <!-- Right Side - Browse, Sign In & Sign Up -->
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Browse
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('dashboard.index') }}">Dashboard</a></li>

            @auth
            @if(Auth::user()->role == 'librarian')
            {{-- Librarian Pages --}}
            <li><a class="dropdown-item" href="{{ route('librarian.viewAll') }}">Manage Books</a></li>
            <li><a class="dropdown-item" href="{{ route('librarian.monitorUsers') }}">Monitor Users</a></li>
            <li><a class="dropdown-item" href="{{ route('librarian.transactions') }}">Transactions</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="{{ route('librarian.create') }}">Add New Book</a></li>
            @else
            {{-- Student Pages --}}
            <li><a class="dropdown-item" href="{{ route('student.viewAll') }}">All Books</a></li>
            <li><a class="dropdown-item" href="{{ route('student.bookmarked') }}">Bookmarked</a></li>
            <li><a class="dropdown-item" href="{{ route('student.history') }}">History</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="#">About Us</a></li>
            @endif
            @endauth

            @guest
            {{-- Guest Pages --}}
            <li><a class="dropdown-item" href="{{ route('student.viewAll') }}">All Books</a></li>
            @endguest
          </ul>
        </li>


        <li class="nav-item d-flex align-items-center">
          <span style="height: 24px; width: 1px; background-color: rgba(255, 255, 255, 0.3); margin: 0 0.5rem;"></span>
        </li>

        @guest
        <li class="nav-item ">
          <a href="{{route('auth.showLogIn')}}" class="nav-link">Sign in</a>
        </li>
        <li class="nav-item">
          <a href="{{route('auth.showSignUp')}}" class="btn btn-signup">Sign up</a>
        </li>
        @endguest

        @auth
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle"></i>
            {{ auth()->user()->first_name }}
          </a>

          <ul class="dropdown-menu dropdown-menu-end">
            <li class="px-3 py-2">
              <div class="fw-semibold">
                {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
              </div>
              <small class="text-muted text-capitalize">
                {{ auth()->user()->role }}
              </small>
            </li>

            <li><hr class="dropdown-divider"></li>

            <li>
              <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button type="submit" class="dropdown-item text-danger">
                  Log out
                </button>
              </form>
            </li>
          </ul>
        </li>
        @endauth

      </ul>
    </div>
  </div>

  <style>
    /* Navbar Styles */
    .navbar-puplms {
      background: #800000;
    }

    .navbar-puplms .navbar-brand {
      color: #fff !important;
      font-weight: bold;
      font-size: 1.7rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      letter-spacing: 2px;
    }

    .puplms-logo {
      width: 40px;
      height: 40px;
      object-fit: contain;
    }

    .navbar-puplms .nav-link {
      color: #fff !important;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .navbar-puplms .nav-link:hover {
      color: #ffd700 !important;
    }

    .navbar-puplms .dropdown-menu {
      border: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .navbar-puplms .dropdown-item:hover {
      background-color: #f8e5e5;
      color: #7c1d1d;
    }

    .btn-signup {
      margin-left: 0.5rem;
      background-color: #ffd700;
      color: #7c1d1d;
      font-weight: bold;
      border: none;
      border-radius: 25px;
      padding: 0.5rem 1.5rem;
      transition: all 0.3s ease;
    }

    .btn-signup:hover {
      background-color: #ffed4e;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .navbar-puplms .navbar-toggler {
      border-color: rgba(255, 215, 0, 0.5);
    }

    .navbar-puplms .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 215, 0, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
  </style>
</nav>