@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                         class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;"
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=d4af37&color=000&size=120'">
                    
                    <h4 class="text-light mb-1">{{ $user->name }}</h4>
                    <span class="badge bg-{{ $user->getRoleBadgeClass() }} mb-3">
                        {{ $user->role_name }}
                    </span>
                    
                    <div class="mt-3">
                        @if($user->is_active)
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i> Aktif
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="fas fa-times-circle me-1"></i> Nonaktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Info Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2" style="color: var(--accent-gold);"></i>
                        Informasi Detail
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th style="width: 150px;">Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        @if($user->phone)
                        <tr><th>Nomor Telepon</th>
                            <td>{{ $user->phone }}</td>
                        </tr>
                        @endif
                        @if($user->address)
                        <tr><th>Alamat</th>
                            <td>{{ $user->address }}</td>
                        </tr>
                        @endif
                        <tr><th>Role</th>
                            <td>{{ $user->role_name }}</td>
                        </tr>
                        <tr><th>Status</th>
                            <td>{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        </tr>
                        @if($user->last_login_at)
                        <tr><th>Terakhir Login</th>
                            <td>
                                {{ $user->last_login_at->format('d F Y H:i:s') }}
                                @if($user->last_login_ip)
                                    <br><small class="text-muted">IP: {{ $user->last_login_ip }}</small>
                                @endif
                            </td>
                        </tr>
                        @endif
                        <tr><th>Bergabung Sejak</th>
                            <td>{{ $user->created_at->format('d F Y') }}</td>
                        </tr>
                        @if($user->updated_at != $user->created_at)
                        <tr><th>Terakhir Diupdate</th>
                            <td>{{ $user->updated_at->format('d F Y H:i:s') }}</td>
                        </tr>
                        @endif
                    </table>
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i> Edit User
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection