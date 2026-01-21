@extends('layouts.main')
@section('title', 'Monitor Users')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/books-grid.css') }}">
<link rel="stylesheet" href="{{ asset('css/user_management.css') }}">
@endpush

@section('content')
<div class="container px-md-5 mt-3">
  <div class="page-header">
    <h1 class="page-title">User Management</h1>
  </div>

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

  <form method="GET" action="{{ route('admin.users.index') }}" id="searchForm">
    <div class="search-box">
      <div class="row g-3">
        <div class="col-md-9">
          <div class="search-input-group">
            <input type="text"
              class="form-control"
              name="search"
              id="searchUsers"
              placeholder="Search by name, email, or ID..."
              value="{{ request('search') }}">
            <button type="submit" class="btn-search">
              <i class="bi bi-search"></i> Search
            </button>
          </div>
        </div>
        <div class="col-md-3">
          <input type="hidden" name="status" id="statusInput" value="{{ request('status', 'all') }}">
          <div class="dropdown">
            <button class="btn-filter dropdown-toggle w-100"
              type="button"
              id="statusFilterDropdown"
              data-bs-toggle="dropdown"
              aria-expanded="false">
              <i class="bi bi-funnel"></i>
              <span id="status-filter-label">
                @if(request('status') == 'active')
                Active
                @elseif(request('status') == 'suspended')
                Suspended
                @elseif(request('status') == 'pending')
                Pending
                @else
                All Status
                @endif
              </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end w-100" aria-labelledby="statusFilterDropdown">
              <li>
                <a class="dropdown-item filter-status {{ request('status', 'all') == 'all' ? 'active' : '' }}"
                  href="#"
                  data-value="all">All Status</a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item filter-status {{ request('status') == 'active' ? 'active' : '' }}"
                  href="#"
                  data-value="active">Active</a>
              </li>
              <li>
                <a class="dropdown-item filter-status {{ request('status') == 'suspended' ? 'active' : '' }}"
                  href="#"
                  data-value="suspended">Suspended</a>
              </li>
              <li>
                <a class="dropdown-item filter-status {{ request('status') == 'pending' ? 'active' : '' }}"
                  href="#"
                  data-value="pending">Pending</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </form>

  <div class="table-responsive user-table-container shadow-sm">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th scope="col">Student ID</th>
          <th scope="col">Name</th>
          <th scope="col">Email</th>
          <th scope="col">Books Borrowed</th>
          <th scope="col">Status</th>
          <th scope="col">Joined Date</th>
          <th scope="col">Last Active</th>
          <th scope="col" class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
        <tr class="user-row" data-status="{{ $user->status }}">
          <td class="fw-medium text-muted">#{{ $user->user_id }}</td>
          <td>
            <div class="d-flex align-items-center">
              @if($user->profile_image)
              <img src="{{ asset($user->profile_image) }}" alt="{{ $user->name }}" class="user-avatar">
              @else
              <span class="user-avatar">
                {{ strtoupper(substr($user->first_name, 0, 1)) . strtoupper(substr($user->last_name, 0, 1)) }}
              </span>
              @endif
              <span class="fw-medium">{{ $user->name }}</span>
            </div>
          </td>
          <td class="text-muted">{{ $user->email }}</td>
          <td class="text-center text-muted">{{ $user->borrowed_books_count ?? 0 }}</td>
          <td>
            <span class="badge-status {{ strtolower($user->status) }}">
              {{ ucfirst($user->status) }}
            </span>
          </td>
          <td class="text-muted">{{ \Carbon\Carbon::parse($user->date_joined)->format('d/m/Y') }}</td>
          <td class="text-muted">{{ $user->last_active ? \Carbon\Carbon::parse($user->last_active)->diffForHumans() : 'Never' }}</td>
          <td>
            <div class="d-flex justify-content-center gap-1">
              {{-- VIEW --}}
              <a href="{{ route('admin.users.index', ['view_user' => $user->user_id, 'open' => 1]) }}"
                class="btn-action view"
                title="View Details">
                <i class="bi bi-eye"></i>
              </a>

              {{-- EDIT --}}
              <a href="{{ route('admin.users.index', ['edit_user' => $user->user_id, 'open_edit' => 1]) }}"
                class="btn-action edit"
                title="Edit Student">
                <i class="bi bi-pencil"></i>
              </a>

              @if($user->status == 'active')
              <form action="{{ route('admin.users.suspend', $user->user_id) }}" method="POST" class="d-inline">
                @csrf
                <button type="button" class="btn-action danger"
                  data-bs-toggle="modal"
                  data-bs-target="#suspendConfirmModal"
                  data-user-id="{{ $user->user_id }}"
                  data-user-name="{{ $user->name }}"
                  title="Suspend Student">
                  <i class="bi bi-ban"></i>
                </button>
              </form>
              @else
              <form action="{{ route('admin.users.activate', $user->user_id) }}" method="POST" class="d-inline">
                @csrf
                <button type="button" class="btn-action success"
                  data-bs-toggle="modal"
                  data-bs-target="#activateConfirmModal"
                  data-user-id="{{ $user->user_id }}"
                  data-user-name="{{ $user->name }}"
                  title="Activate Student">
                  <i class="bi bi-check-circle"></i>
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- Auto-open View Modal --}}
@if(request('open') == 1 && $viewUser)
<button type="button"
  id="autoOpenViewModal"
  data-bs-toggle="modal"
  data-bs-target="#viewUserModal"
  hidden>
</button>
@endif

{{-- View User Modal --}}
<div class="modal fade" id="viewUserModal" tabindex="-1">
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
            <img src="{{ asset($viewUser->profile_image) }}"
              class="rounded-circle mb-3"
              width="120"
              height="120"
              alt="{{ $viewUser->first_name }}"
              style="object-fit: cover;">
            @else
            <span class="user-avatar"
              style="width: 120px; height: 120px; font-size: 2.5rem; margin: 0 auto; display: inline-flex; align-items: center; justify-content: center;">
              {{ strtoupper(substr($viewUser->first_name, 0, 1)) . strtoupper(substr($viewUser->last_name, 0, 1)) }}
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
                  <td>
                    <span class="badge-status {{ strtolower($viewUser->status) }}">
                      {{ ucfirst($viewUser->status) }}
                    </span>
                  </td>
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
        @else
        <div class="alert alert-info text-center">
          <i class="bi bi-info-circle me-2"></i>
          Click on a student's view button to see their details.
        </div>
        @endif
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

{{-- Auto-open Edit Modal --}}
@if(request('open_edit') == 1 && $editUser)
<button type="button"
  id="autoOpenEditModal"
  data-bs-toggle="modal"
  data-bs-target="#editUserModal"
  hidden>
</button>
@endif

{{-- Edit User Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Edit Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ $editUser ? route('admin.users.update', $editUser->user_id) : '' }}" method="POST" id="editUserForm">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4 text-center mb-3">
              <span class="user-avatar" id="editUserAvatar"
                style="width: 120px; height: 120px; font-size: 2.5rem; margin: 0 auto; display: inline-flex; align-items: center; justify-content: center;">
                @if($editUser)
                {{ strtoupper(substr($editUser->first_name, 0, 1) . substr($editUser->last_name, 0, 1)) }}
                @else
                --
                @endif
              </span>
            </div>
            <div class="col-md-8">
              <div class="mb-3">
                <label class="form-label fw-medium">Full Name</label>
                <input type="text" class="form-control" id="editUserName" name="name" value="{{ $editUser ? $editUser->first_name . ' ' . $editUser->last_name : '' }}" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-medium">Email Address</label>
                <input type="email" class="form-control" id="editUserEmail" name="email" value="{{ $editUser ? $editUser->email : '' }}" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-medium">Status</label>
                <select class="form-select" id="editUserStatus" name="status" required>
                  <option value="active" {{ $editUser && $editUser->status === 'active' ? 'selected' : '' }}>Active</option>
                  <option value="suspended" {{ $editUser && $editUser->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                  <option value="pending" {{ $editUser && $editUser->status === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label fw-medium">Password <small class="text-muted">(leave blank to keep current)</small></label>
                <input type="password" class="form-control" id="editUserPassword" name="password">
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

{{-- Suspend Confirmation Modal --}}
<div class="modal fade" id="suspendConfirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center px-4 pb-4">
        <div class="mb-3">
          <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 4rem;"></i>
        </div>
        <h5 class="fw-bold mb-2">Suspend Student Account?</h5>
        <p class="text-muted mb-4">
          Are you sure you want to suspend <strong id="suspendUserName"></strong>?
          This student will not be able to access the system until reactivated.
        </p>
        <form action="" method="POST" id="suspendForm">
          @csrf
          <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger px-4">
              <i class="bi bi-ban"></i> Suspend Account
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Activate Confirmation Modal --}}
<div class="modal fade" id="activateConfirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center px-4 pb-4">
        <div class="mb-3">
          <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
        </div>
        <h5 class="fw-bold mb-2">Activate Student Account?</h5>
        <p class="text-muted mb-4">
          Are you sure you want to activate <strong id="activateUserName"></strong>?
          This student will regain access to the system.
        </p>
        <form action="" method="POST" id="activateForm">
          @csrf
          <div class="d-flex gap-2 justify-content-center">
            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success px-4">
              <i class="bi bi-check-circle"></i> Activate Account
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {

    // Auto-open view modal
    const autoBtn = document.getElementById('autoOpenViewModal');
    if (autoBtn) {
      autoBtn.click();
      const viewModalEl = document.getElementById('viewUserModal');
      if (viewModalEl) {
        viewModalEl.addEventListener('hidden.bs.modal', function() {
          window.location.href = "{{ route('admin.users.index') }}";
        }, {
          once: true
        });
      }
    }

    // Auto-open edit modal
    const autoEditBtn = document.getElementById('autoOpenEditModal');
    if (autoEditBtn) {
      autoEditBtn.click();
      const editModalEl = document.getElementById('editUserModal');
      if (editModalEl) {
        editModalEl.addEventListener('hidden.bs.modal', function() {
          window.location.href = "{{ route('admin.users.index') }}";
        }, {
          once: true
        });
      }
    }

    // Suspend Modal
    document.getElementById('suspendConfirmModal')
      .addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        document.getElementById('suspendUserName').textContent =
          button.dataset.userName;
        document.getElementById('suspendForm').action =
          `/admin/users/${button.dataset.userId}/suspend`;
      });

    // Activate Modal
    document.getElementById('activateConfirmModal')
      .addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        document.getElementById('activateUserName').textContent =
          button.dataset.userName;
        document.getElementById('activateForm').action =
          `/admin/users/${button.dataset.userId}/activate`;
      });

    // Status filter
    document.querySelectorAll('.filter-status').forEach(item => {
      item.addEventListener('click', function(e) {
        e.preventDefault();
        const statusValue = this.dataset.value;
        document.getElementById('statusInput').value = statusValue;
        document.getElementById('status-filter-label').textContent = this.textContent;
        document.getElementById('searchForm').submit();
      });
    });

  });
</script>
@endpush