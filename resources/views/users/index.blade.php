@extends('layouts.app')

@section('title', 'Manajemen User')

@push('styles')
<style>
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .stat-card-user {
        background: linear-gradient(135deg, var(--secondary-dark) 0%, rgba(212, 175, 55, 0.05) 100%);
        border: 1px solid rgba(212, 175, 55, 0.1);
        border-radius: 16px;
        padding: 1rem;
        transition: all 0.3s;
    }
    
    .stat-card-user:hover {
        transform: translateY(-3px);
        border-color: var(--accent-gold);
    }
    
    .filter-section {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .badge-role {
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 20px;
    }
    
    .badge-status {
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 20px;
    }
    
    .table-user tbody tr {
        transition: all 0.2s;
    }
    
    .table-user tbody tr:hover {
        background: rgba(212, 175, 55, 0.05);
    }
    
    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        margin: 0 2px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-users me-2" style="color: var(--accent-gold);"></i>
                        Manajemen User
                    </h4>
                    <p class="text-muted mb-0">
                        Kelola semua pengguna sistem barbershop Anda
                    </p>
                </div>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Tambah User Baru
                </a>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-user text-center">
                <div class="mb-2">
                    <i class="fas fa-users fa-2x" style="color: var(--accent-gold);"></i>
                </div>
                <h3 class="text-light mb-0">{{ $statistics['total'] }}</h3>
                <small class="text-muted">Total User</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-user text-center">
                <div class="mb-2">
                    <i class="fas fa-check-circle fa-2x" style="color: #10b981;"></i>
                </div>
                <h3 class="text-light mb-0">{{ $statistics['active'] }}</h3>
                <small class="text-muted">Aktif</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-user text-center">
                <div class="mb-2">
                    <i class="fas fa-times-circle fa-2x" style="color: #ef4444;"></i>
                </div>
                <h3 class="text-light mb-0">{{ $statistics['inactive'] }}</h3>
                <small class="text-muted">Nonaktif</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card-user text-center">
                <div class="mb-2">
                    <i class="fas fa-user-shield fa-2x" style="color: var(--accent-gold);"></i>
                </div>
                <h3 class="text-light mb-0">{{ $statistics['admin'] + $statistics['owner'] }}</h3>
                <small class="text-muted">Admin + Owner</small>
            </div>
        </div>
    </div>
    
    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted">
                    <i class="fas fa-search me-1"></i> Cari User
                </label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Nama, Email, atau No HP..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">
                    <i class="fas fa-tag me-1"></i> Role
                </label>
                <select name="role" class="form-select">
                    <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>Semua Role</option>
                    @foreach($roles as $key => $label)
                        <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">
                    <i class="fas fa-circle me-1"></i> Status
                </label>
                <select name="status" class="form-select">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i> Filter
                </button>
            </div>
        </form>
    </div>
    
    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2" style="color: var(--accent-gold);"></i>
                        Daftar User
                    </h5>
                    <small class="text-muted">
                        Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} user
                    </small>
                </div>
                @if(request('trashed') == 'true')
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> Lihat User Aktif
                    </a>
                @else
                    <a href="{{ route('users.index', ['trashed' => 'true']) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-trash-restore me-1"></i> Lihat Sampah
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-user mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Avatar</th>
                            <th>Nama</th>
                            <th>Email / No HP</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Terakhir Login</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                     class="user-avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=d4af37&color=000'">
                            </td>
                            <td>
                                <div>
                                    <strong class="text-light">{{ $user->name }}</strong>
                                    @if($user->id === auth()->id())
                                        <span class="badge bg-info ms-1">Anda</span>
                                    @endif
                                    @if($user->trashed())
                                        <span class="badge bg-secondary ms-1">Terhapus</span>
                                    @endif
                                </div>
                                @if($user->address)
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ Str::limit($user->address, 30) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <i class="fas fa-envelope me-1 text-muted"></i>
                                    {{ $user->email }}
                                </div>
                                @if($user->phone)
                                    <div class="mt-1">
                                        <i class="fas fa-phone me-1 text-muted"></i>
                                        {{ $user->phone }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-role bg-{{ $user->getRoleBadgeClass() }}">
                                    <i class="fas {{ $user->role == 'admin' ? 'fa-user-shield' : ($user->role == 'owner' ? 'fa-crown' : 'fa-user') }} me-1"></i>
                                    {{ $user->role_name }}
                                </span>
                            </td>
                            <td>
                                @if(!$user->trashed())
                                    @if($user->is_active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i> Nonaktif
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-trash me-1"></i> Deleted
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($user->last_login_at)
                                    <div>
                                        <small>
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ $user->last_login_at->format('d/m/Y') }}
                                        </small>
                                    </div>
                                    <div>
                                        <small>
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $user->last_login_at->format('H:i') }}
                                        </small>
                                    </div>
                                @else
                                    <small class="text-muted">Belum pernah login</small>
                                @endif
                            </td>
                            <td class="action-buttons">
                                @if(!$user->trashed())
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#resetPasswordModal"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            title="Reset Password">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm {{ $user->is_active ? 'btn-danger' : 'btn-success' }}"
                                            onclick="toggleStatus({{ $user->id }}, '{{ $user->name }}', {{ $user->is_active ? 'false' : 'true' }})"
                                            title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button type="button" class="btn btn-sm btn-danger"
                                                onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                @else
                                    <button type="button" class="btn btn-sm btn-success"
                                            onclick="restoreUser({{ $user->id }}, '{{ $user->name }}')"
                                            title="Pulihkan">
                                        <i class="fas fa-trash-restore"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="forceDeleteUser({{ $user->id }}, '{{ $user->name }}')"
                                            title="Hapus Permanen">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">Belum ada data user</p>
                                <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary mt-3">
                                    <i class="fas fa-plus me-1"></i> Tambah User Pertama
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="resetPasswordForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-key me-2" style="color: var(--accent-gold);"></i>
                        Reset Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Reset password untuk user: <strong id="resetUserName"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Reset Password Modal Handler
    $('#resetPasswordModal').on('show.bs.modal', function(event) {
        let button = $(event.relatedTarget);
        let userId = button.data('user-id');
        let userName = button.data('user-name');
        
        let modal = $(this);
        modal.find('#resetUserName').text(userName);
        modal.find('#resetPasswordForm').attr('action', `/users/${userId}/reset-password`);
    });
    
    // Toggle Status
    function toggleStatus(userId, userName, deactivate) {
        let action = deactivate ? 'mengaktifkan' : 'menonaktifkan';
        let confirmMsg = `Apakah Anda yakin ingin ${action} user "${userName}"?`;
        
        if (confirm(confirmMsg)) {
            $.ajax({
                url: `/users/${userId}/toggle-status`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'POST'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            });
        }
    }
    
    // Soft Delete User
    function deleteUser(userId, userName) {
        if (confirm(`Apakah Anda yakin ingin menghapus user "${userName}"?`)) {
            $.ajax({
                url: `/users/${userId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            });
        }
    }
    
    // Restore User
    function restoreUser(userId, userName) {
        if (confirm(`Apakah Anda yakin ingin memulihkan user "${userName}"?`)) {
            $.ajax({
                url: `/users/${userId}/restore`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            });
        }
    }
    
    // Force Delete User
    function forceDeleteUser(userId, userName) {
        if (confirm(`Apakah Anda yakin ingin menghapus PERMANEN user "${userName}"? Tindakan ini tidak dapat dibatalkan!`)) {
            $.ajax({
                url: `/users/${userId}/force-delete`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            });
        }
    }
</script>
@endpush
@endsection