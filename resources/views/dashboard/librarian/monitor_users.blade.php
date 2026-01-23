@extends('layouts.main')
@section('title', 'Monitor Users')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/books-grid.css') }}">
<link rel="stylesheet" href="{{ asset('css/user_management.css') }}">
@endpush

@section('content')
<div class="container py-5">
  <div class="page-header">
    <h1 class="page-title">User Management</h1>
  </div>

  {{-- Stats Cards --}}
  <div class="row mb-3">
    <div class="col-md-3">
      <div class="stats-card">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <p>Total Students</p>
            <h3>{{ $totalUsers ?? 0 }}</h3>
          </div>
          <div class="stats-icon" style="background: #e3f2fd;">
            <i class="bi bi-people" style="color: #1976d2;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stats-card">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <p>Active Students</p>
            <h3>{{ $activeUsers ?? 0 }}</h3>
          </div>
          <div class="stats-icon" style="background: #d4edda;">
            <i class="bi bi-check-circle" style="color: #155724;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stats-card">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <p>Suspended</p>
            <h3>{{ $suspendedUsers ?? 0 }}</h3>
          </div>
          <div class="stats-icon" style="background: #f8d7da;">
            <i class="bi bi-x-circle" style="color: #721c24;"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stats-card">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <p>New This Month</p>
            <h3>{{ $newUsers ?? 0 }}</h3>
          </div>
          <div class="stats-icon" style="background: #fff3cd;">
            <i class="bi bi-person-plus" style="color: #856404;"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Search & Filter --}}
  <form method="GET" action="{{ route('admin.users.index') }}" id="searchForm">
    <div class="search-box">
      <div class="row g-3">
        <div class="col-md-9">
          <div class="search-input-group">
            <input type="text" class="form-control" name="search" placeholder="Search by name, email, or ID..." value="{{ request('search') }}">
            <button type="submit" class="btn-search">
              <i class="bi bi-search"></i> Search
            </button>
          </div>
        </div>
        <div class="col-md-3">
          <input type="hidden" name="status" id="statusInput" value="{{ request('status', 'all') }}">
          <div class="dropdown">
            <button class="btn-filter dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
              <i class="bi bi-funnel"></i>
              <span id="statusLabel">
                @if(request('status') == 'active') Active
                @elseif(request('status') == 'suspended') Suspended
                @elseif(request('status') == 'pending') Pending
                @else All Status
                @endif
              </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end w-100">
              <li><a class="dropdown-item filter-status {{ request('status', 'all') == 'all' ? 'active' : '' }}" href="#" data-value="all">All Status</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item filter-status {{ request('status') == 'active' ? 'active' : '' }}" href="#" data-value="active">Active</a></li>
              <li><a class="dropdown-item filter-status {{ request('status') == 'suspended' ? 'active' : '' }}" href="#" data-value="suspended">Suspended</a></li>
              <li><a class="dropdown-item filter-status {{ request('status') == 'pending' ? 'active' : '' }}" href="#" data-value="pending">Pending</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </form>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mt-2 pr-2 pt-2" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  {{-- Users Table --}}
  <div class="table-responsive user-table-container shadow-sm">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Student ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Books Borrowed</th>
          <th>Status</th>
          <th>Joined Date</th>
          <th>Last Active</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
        <tr>
          <td class="fw-medium text-muted">#{{ $user->user_id }}</td>
          <td>
            <div class="d-flex align-items-center">
              @if($user->profile_image)
              <img src="{{ asset($user->profile_image) }}" alt="{{ $user->name }}" class="user-avatar">
              @else
              <span class="user-avatar">{{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}</span>
              @endif
              <span class="fw-medium">{{ $user->name }}</span>
            </div>
          </td>
          <td class="text-muted">{{ $user->email }}</td>
          <td class="text-center text-muted">{{ $user->borrowed_books_count ?? 0 }}</td>
          <td><span class="badge-status {{ strtolower($user->status) }}">{{ ucfirst($user->status) }}</span></td>
          <td class="text-muted">{{ \Carbon\Carbon::parse($user->date_joined)->format('d/m/Y') }}</td>
          <td class="text-muted">{{ $user->last_active ? \Carbon\Carbon::parse($user->last_active)->diffForHumans() : 'Never' }}</td>
          <td>
            <div class="d-flex justify-content-center gap-1">
              <a href="{{ route('admin.users.index', ['view_user' => $user->user_id, 'open' => 1]) }}" class="btn-action view" title="View Details">
                <i class="bi bi-eye"></i>
              </a>
              <a href="{{ route('admin.users.index', ['edit_user' => $user->user_id, 'open_edit' => 1]) }}" class="btn-action edit" title="Edit Student">
                <i class="bi bi-pencil"></i>
              </a>
              @if($user->status == 'active')
              <button type="button" class="btn-action danger" data-bs-toggle="modal" data-bs-target="#suspendModal" data-id="{{ $user->user_id }}" data-name="{{ $user->name }}" title="Suspend">
                <i class="bi bi-ban"></i>
              </button>
              @else
              <button type="button" class="btn-action success" data-bs-toggle="modal" data-bs-target="#activateModal" data-id="{{ $user->user_id }}" data-name="{{ $user->name }}" title="Activate">
                <i class="bi bi-check-circle"></i>
              </button>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- View Modal --}}
@if(request('open') == 1 && $viewUser)
<button type="button" id="autoOpenView" data-bs-toggle="modal" data-bs-target="#viewModal" hidden></button>
@endif

<div class="modal fade" id="viewModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Student Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        @if(isset($viewUser))
        <div class="row">
          <div class="col-md-4 text-center mb-3">
            @if($viewUser->profile_image)
            <img src="{{ asset($viewUser->profile_image) }}" class="rounded-circle mb-3" width="120" height="120" alt="{{ $viewUser->first_name }}" style="object-fit: cover;">
            @else
            <span class="user-avatar" style="width: 120px; height: 120px; font-size: 2.5rem; margin: 0 auto; display: inline-flex; align-items: center; justify-content: center;">
              {{ strtoupper(substr($viewUser->first_name, 0, 1) . substr($viewUser->last_name, 0, 1)) }}
            </span>
            @endif
            <h5 class="fw-bold mt-3">{{ $viewUser->first_name }} {{ $viewUser->last_name }}</h5>
            <p class="text-muted">{{ $viewUser->email }}</p>
          </div>
          <div class="col-md-8">
            <table class="table">
              <tbody>
                <tr>
                  <th width="40%">Student ID:</th>
                  <td>#{{ $viewUser->user_id }}</td>
                </tr>
                <tr>
                  <th>Status:</th>
                  <td><span class="badge-status {{ strtolower($viewUser->status) }}">{{ ucfirst($viewUser->status) }}</span></td>
                </tr>
                <tr>
                  <th>Course:</th>
                  <td>{{ $viewUser->course->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                  <th>Books Borrowed:</th>
                  <td>{{ $viewUser->borrowed_books_count ?? 0 }}</td>
                </tr>
                <tr>
                  <th>Date Joined:</th>
                  <td>{{ \Carbon\Carbon::parse($viewUser->date_joined)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                  <th>Last Active:</th>
                  <td>{{ $viewUser->last_active ? \Carbon\Carbon::parse($viewUser->last_active)->format('d/m/Y H:i') : 'Never' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

{{-- Edit Modal --}}
@if(request('open_edit') == 1 && $editUser)
<button type="button" id="autoOpenEdit" data-bs-toggle="modal" data-bs-target="#editModal" hidden></button>
@endif

<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Edit Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ $editUser ? route('admin.users.update', $editUser->user_id) : '' }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4 text-center mb-3">
              <span class="user-avatar" style="width: 120px; height: 120px; font-size: 2.5rem; margin: 0 auto; display: inline-flex; align-items: center; justify-content: center;">
                @if($editUser){{ strtoupper(substr($editUser->first_name, 0, 1) . substr($editUser->last_name, 0, 1)) }}@else--@endif
              </span>
            </div>
            <div class="col-md-8">
              <div class="mb-3">
                <label class="form-label fw-medium">Full Name</label>
                <input type="text" class="form-control" name="name" value="{{ $editUser ? $editUser->first_name . ' ' . $editUser->last_name : '' }}" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-medium">Email Address</label>
                <input type="email" class="form-control" name="email" value="{{ $editUser ? $editUser->email : '' }}" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-medium">Status</label>
                <select class="form-select" name="status" required>
                  <option value="active" {{ $editUser && $editUser->status === 'active' ? 'selected' : '' }}>Active</option>
                  <option value="suspended" {{ $editUser && $editUser->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                  <option value="pending" {{ $editUser && $editUser->status === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-medium">Password <small class="text-muted">(leave blank to keep current)</small></label>
                <input type="password" class="form-control" name="password" placeholder="Password (min. 6 chars)" >
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a>
          <button type="submit" class="btn-signup">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Suspend Modal --}}
<div class="modal fade" id="suspendModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('admin.users.suspend', 0) }}" method="POST" id="suspendForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header border-0">
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center px-4 pb-4">
          <div class="mb-3">
            <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 4rem;"></i>
          </div>
          <h5 class="fw-bold mb-2">Suspend Student Account?</h5>
          <p class="text-muted mb-4">Are you sure you want to suspend <strong id="suspendName"></strong>? This student will not be able to access the system until reactivated.</p>
          <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger px-4"><i class="bi bi-ban"></i> Suspend Account</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Activate Modal --}}
<div class="modal fade" id="activateModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('admin.users.activate', 0) }}" method="POST" id="activateForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header border-0">
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center px-4 pb-4">
          <div class="mb-3">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
          </div>
          <h5 class="fw-bold mb-2">Activate Student Account?</h5>
          <p class="text-muted mb-4">Are you sure you want to activate <strong id="activateName"></strong>? This student will regain access to the system.</p>
          <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-circle"></i> Activate Account</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Auto-open view modal
    const autoView = document.getElementById('autoOpenView');
    if (autoView) {
      autoView.click();
      document.getElementById('viewModal').addEventListener('hidden.bs.modal', function() {
        window.location.href = "{{ route('admin.users.index') }}";
      }, {
        once: true
      });
    }

    // Auto-open edit modal
    const autoEdit = document.getElementById('autoOpenEdit');
    if (autoEdit) {
      autoEdit.click();
      document.getElementById('editModal').addEventListener('hidden.bs.modal', function() {
        window.location.href = "{{ route('admin.users.index') }}";
      }, {
        once: true
      });
    }

    // Suspend modal
    document.getElementById('suspendModal').addEventListener('show.bs.modal', function(e) {
      const btn = e.relatedTarget;
      document.getElementById('suspendName').textContent = btn.dataset.name;
      document.getElementById('suspendForm').action = "{{ route('admin.users.suspend', ':id') }}".replace(':id', btn.dataset.id);
    });

    // Activate modal
    document.getElementById('activateModal').addEventListener('show.bs.modal', function(e) {
      const btn = e.relatedTarget;
      document.getElementById('activateName').textContent = btn.dataset.name;
      document.getElementById('activateForm').action = "{{ route('admin.users.activate', ':id') }}".replace(':id', btn.dataset.id);
    });

    // Status filter
    document.querySelectorAll('.filter-status').forEach(item => {
      item.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('statusInput').value = this.dataset.value;
        document.getElementById('statusLabel').textContent = this.textContent;
        document.getElementById('searchForm').submit();
      });
    });
  });
</script>
@endpush