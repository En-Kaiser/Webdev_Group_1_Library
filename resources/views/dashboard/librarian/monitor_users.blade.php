@extends('layouts.main')
@section('title', 'Monitor Users')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/books-grid.css') }}">
<link rel="stylesheet" href="{{ asset('css/user_management.css') }}">
@endpush

@section('content')
@php // remove niyo lang to if itrtry niyo na sa actual db
use Carbon\Carbon;

$users = [
(object)[
'id' => 1001,
'name' => 'Juan Dela Cruz',
'email' => 'juan.delacruz@student.edu',
'status' => 'Active',
'borrowed_books_count' => 3,
'profile_image' => null,
'created_at' => Carbon::now()->subMonths(3),
'last_active' => Carbon::now()->subHours(2),
],
(object)[
'id' => 1002,
'name' => 'Maria Santos',
'email' => 'maria.santos@student.edu',
'status' => 'Suspended',
'borrowed_books_count' => 1,
'profile_image' => null,
'created_at' => Carbon::now()->subMonths(5),
'last_active' => Carbon::now()->subDays(12),
],
(object)[
'id' => 1003,
'name' => 'Carlos Reyes',
'email' => 'carlos.reyes@student.edu',
'status' => 'Pending',
'borrowed_books_count' => 0,
'profile_image' => null,
'created_at' => Carbon::now()->subDays(10),
'last_active' => null,
],
(object)[
'id' => 1004,
'name' => 'Anne Villanueva',
'email' => 'anne.villanueva@student.edu',
'status' => 'Active',
'borrowed_books_count' => 5,
'profile_image' => null,
'created_at' => Carbon::now()->subYear(),
'last_active' => Carbon::now()->subMinutes(30),
],
];

$totalUsers = count($users);
$activeUsers = collect($users)->where('status', 'Active')->count();
$suspendedUsers = collect($users)->where('status', 'Suspended')->count();
$newUsers = collect($users)->filter(fn($u) => $u->created_at->isCurrentMonth())->count();
@endphp // hanggang here yung ireremove
<div class="container px-md-5 mt-3">
  <div class="page-header">
    <h1 class="page-title">User Management</h1>
    <div class="header-controls">
      <button class="btn-signup" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-plus-circle"></i>
        Add Student
      </button>
    </div>
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

  <div class="search-box">
    <div class="row g-3">
      <div class="col-md-9">
        <div class="search-input-group">
          <input type="text" class="form-control" id="searchUsers"
            placeholder="Search by name, email, or ID..."
            value="{{ request('search') }}">
          <button class="btn-search" onclick="filterUsers()">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
      </div>
      <div class="col-md-3">
        <div class="dropdown">
          <button class="btn-filter dropdown-toggle w-100"
            type="button"
            id="statusFilterDropdown"
            data-bs-toggle="dropdown"
            aria-expanded="false">
            <i class="bi bi-funnel"></i>
            <span id="status-filter-label">All Status</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end w-100" aria-labelledby="statusFilterDropdown">
            <li><a class="dropdown-item filter-status" href="#" data-value="all">All Status</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item filter-status" href="#" data-value="Active">Active</a></li>
            <li><a class="dropdown-item filter-status" href="#" data-value="Suspended">Suspended</a></li>
            <li><a class="dropdown-item filter-status" href="#" data-value="Pending">Pending</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

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
        @forelse($users ?? [] as $user)
        <tr class="user-row" data-status="{{ $user->status }}">
          <td class="fw-medium text-muted">#{{ $user->id }}</td>
          <td>
            <div class="d-flex align-items-center">
              @if($user->profile_image)
              <img src="{{ asset($user->profile_image) }}" alt="{{ $user->name }}"
                class="user-avatar">
              @else
              <span class="user-avatar">
                {{ strtoupper(substr($user->name, 0, 2)) }}
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
          <td class="text-muted">{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</td>
          <td class="text-muted">{{ $user->last_active ? \Carbon\Carbon::parse($user->last_active)->diffForHumans() : 'Never' }}</td>
          <td>
            <div class="d-flex justify-content-center gap-1">
              <button class="btn-action view"
                data-bs-toggle="modal"
                data-bs-target="#viewUserModal"
                data-user-id="{{ $user->id }}"
                title="View Details">
                <i class="bi bi-eye"></i>
              </button>
              <button class="btn-action edit"
                data-bs-toggle="modal"
                data-bs-target="#editUserModal"
                data-user-id="{{ $user->id }}"
                title="Edit Student">
                <i class="bi bi-pencil"></i>
              </button>
              @if($user->status == 'Active')
              <button class="btn-action danger"
                data-bs-toggle="modal"
                data-bs-target="#suspendConfirmModal"
                data-user-id="{{ $user->id }}"
                data-user-name="{{ $user->name }}"
                title="Suspend Student">
                <i class="bi bi-ban"></i>
              </button>
              @else
              <button class="btn-action success"
                data-bs-toggle="modal"
                data-bs-target="#activateConfirmModal"
                data-user-id="{{ $user->id }}"
                data-user-name="{{ $user->name }}"
                title="Activate Student">
                <i class="bi bi-check-circle"></i>
              </button>
              @endif
            </div>
          </td>
        </tr>
        @empty
        @for($i = 0; $i < 8; $i++)
          <tr>
          <td class="text-muted">-</td>
          <td class="text-muted">-</td>
          <td class="text-muted">-</td>
          <td class="text-muted text-center">-</td>
          <td><span class="badge-status">-</span></td>
          <td class="text-muted">-</td>
          <td class="text-muted">-</td>
          <td class="text-center">-</td>
          </tr>
          @endfor
          @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="viewUserModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Student Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="userDetailsContent">
        <div class="text-center py-5">
          <div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Edit Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editUserForm">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4 text-center mb-3">
              <span class="user-avatar" id="editUserAvatar" style="width: 120px; height: 120px; font-size: 2.5rem; margin: 0 auto;">--</span>
              <input type="hidden" id="editUserId" name="user_id">
            </div>
            <div class="col-md-8">
              <div class="mb-3">
                <label class="form-label fw-medium">Full Name</label>
                <input type="text" class="form-control" id="editUserName" name="name" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-medium">Email Address</label>
                <input type="email" class="form-control" id="editUserEmail" name="email" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-medium">Status</label>
                <select class="form-select" id="editUserStatus" name="status" required>
                  <option value="Active">Active</option>
                  <option value="Suspended">Suspended</option>
                  <option value="Pending">Pending</option>
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
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-signup">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Add New Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <input type="hidden" name="role" value="student">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-medium">Full Name</label>
            <input type="text" class="form-control" name="name" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-medium">Email Address</label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-medium">Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-signup">Add Student</button>
        </div>
      </form>
    </div>
  </div>
</div>

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
        <div class="d-flex gap-2 justify-content-center">
          <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger px-4" id="confirmSuspendBtn">
            <i class="bi bi-ban"></i> Suspend Account
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

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
        <div class="d-flex gap-2 justify-content-center">
          <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success px-4" id="confirmActivateBtn">
            <i class="bi bi-check-circle"></i> Activate Account
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    let currentStatusFilter = 'all';

    // Status Filter
    const statusFilters = document.querySelectorAll('.filter-status');
    const statusLabel = document.getElementById('status-filter-label');

    statusFilters.forEach(option => {
      option.addEventListener('click', function(e) {
        e.preventDefault();
        currentStatusFilter = this.getAttribute('data-value');
        statusLabel.innerText = currentStatusFilter === 'all' ? 'All Status' : currentStatusFilter;
        filterUsers();
      });
    });

    // Search on Enter key
    const searchInput = document.getElementById('searchUsers');
    searchInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        filterUsers();
      }
    });

    // Filter Users Function
    window.filterUsers = function() {
      const searchTerm = searchInput.value.toLowerCase();
      const userRows = document.querySelectorAll('.user-row');

      userRows.forEach(row => {
        const status = row.getAttribute('data-status');
        const rowText = row.textContent.toLowerCase();

        const matchesStatus = currentStatusFilter === 'all' || status === currentStatusFilter;
        const matchesSearch = searchTerm === '' || rowText.includes(searchTerm);

        if (matchesStatus && matchesSearch) {
          row.classList.remove('d-none');
        } else {
          row.classList.add('d-none');
        }
      });
    }

    // View User Modal Handler
    const viewUserModal = document.getElementById('viewUserModal');
    viewUserModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const userId = button.getAttribute('data-user-id');
      loadUserDetails(userId);
    });

    // Edit Button Handler - Open modal with user data
    document.querySelectorAll('.btn-action.edit').forEach(button => {
      button.addEventListener('click', function() {
        const userId = this.getAttribute('data-user-id');
        loadUserForEdit(userId);
      });
    });

    // Edit Form Submit Handler
    const editUserForm = document.getElementById('editUserForm');
    editUserForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const userId = document.getElementById('editUserId').value;
      const formData = new FormData(this);

      fetch(`/admin/users/${userId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Failed to update student');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred');
        });
    });

    // -------------------------------------------------------------------
    // UPDATED SUSPEND/ACTIVATE HANDLERS (Fix for missing Bootstrap global)
    // -------------------------------------------------------------------

    // Suspend Modal Handler
    const suspendModalEl = document.getElementById('suspendConfirmModal');
    suspendModalEl.addEventListener('show.bs.modal', function(event) {
      // Button that triggered the modal
      const button = event.relatedTarget;

      // Extract info from data-* attributes
      const userId = button.getAttribute('data-user-id');
      const userName = button.getAttribute('data-user-name');

      // Update the modal's content
      document.getElementById('suspendUserName').textContent = userName;

      // Handle the confirm click
      const confirmBtn = document.getElementById('confirmSuspendBtn');

      // Clone the button to remove old event listeners (prevents multiple clicks)
      const newBtn = confirmBtn.cloneNode(true);
      confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);

      newBtn.addEventListener('click', function() {
        // Disable button to prevent double submit
        this.disabled = true;
        suspendUser(userId);
      });
    });

    // Activate Modal Handler
    const activateModalEl = document.getElementById('activateConfirmModal');
    activateModalEl.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const userId = button.getAttribute('data-user-id');
      const userName = button.getAttribute('data-user-name');

      document.getElementById('activateUserName').textContent = userName;

      const confirmBtn = document.getElementById('confirmActivateBtn');
      const newBtn = confirmBtn.cloneNode(true);
      confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);

      newBtn.addEventListener('click', function() {
        this.disabled = true;
        activateUser(userId);
      });
    });

    // Load User Details
    function loadUserDetails(userId) {
      fetch(`/admin/users/${userId}`)
        .then(response => response.json())
        .then(data => {
          const avatarHtml = data.profile_image ?
            `<img src="${data.profile_image}" class="rounded-circle mb-3" width="120" height="120">` :
            `<span class="user-avatar" style="width: 120px; height: 120px; font-size: 2.5rem; margin: 0 auto;">${data.name.substring(0, 2).toUpperCase()}</span>`;

          document.getElementById('userDetailsContent').innerHTML = `
                        <div class="row">
                            <div class="col-md-4 text-center mb-3">
                                ${avatarHtml}
                                <h5 class="fw-bold mt-3">${data.name}</h5>
                                <p class="text-muted">${data.email}</p>
                            </div>
                            <div class="col-md-8">
                                <table class="table">
                                    <tr><th>Student ID:</th><td>#${data.id}</td></tr>
                                    <tr><th>Status:</th><td><span class="badge-status ${data.status.toLowerCase()}">${data.status}</span></td></tr>
                                    <tr><th>Books Borrowed:</th><td>${data.borrowed_books_count || 0}</td></tr>
                                    <tr><th>Joined:</th><td>${data.created_at}</td></tr>
                                    <tr><th>Last Active:</th><td>${data.last_active || 'Never'}</td></tr>
                                </table>
                            </div>
                        </div>
                    `;
        })
        .catch(error => {
          console.error('Error loading student details:', error);
          document.getElementById('userDetailsContent').innerHTML = `
                        <div class="alert alert-danger">Failed to load student details.</div>
                    `;
        });
    }

    // Load User For Edit
    function loadUserForEdit(userId) {
      fetch(`/admin/users/${userId}`)
        .then(response => response.json())
        .then(data => {
          // Set form values
          document.getElementById('editUserId').value = data.id;
          document.getElementById('editUserName').value = data.name;
          document.getElementById('editUserEmail').value = data.email;
          document.getElementById('editUserStatus').value = data.status;
          document.getElementById('editUserPassword').value = '';

          // Set avatar
          const avatar = document.getElementById('editUserAvatar');
          avatar.textContent = data.name.substring(0, 2).toUpperCase();
        })
        .catch(error => {
          console.error('Error loading student for edit:', error);
          alert('Failed to load student data');
        });
    }

    // Suspend User
    function suspendUser(userId) {
      fetch(`/admin/users/${userId}/suspend`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Failed to suspend student');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred');
        });
    }

    // Activate User
    function activateUser(userId) {
      fetch(`/admin/users/${userId}/activate`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Failed to activate student');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred');
        });
    }
  });
</script>
@endpush