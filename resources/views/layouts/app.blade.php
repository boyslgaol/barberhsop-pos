<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Barbershop POS - @yield('title')</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-dark: #0a0a0a;
            --primary-black: #000000;
            --secondary-dark: #1a1a1a;
            --accent-gold: #d4af37;
            --accent-gold-dark: #b8960c;
            --text-light: #ffffff;
            --text-gray: #a0a0a0;
            --danger-red: #dc2626;
            --success-green: #10b981;
            --warning-yellow: #f59e0b;
            --info-blue: #3b82f6;
            --body-bg: #000000;
            --card-bg: #1a1a1a;
            --border-color: rgba(212, 175, 55, 0.1);
        }
        
        body.light-mode {
            --primary-dark: #f5f5f5;
            --primary-black: #ffffff;
            --secondary-dark: #e8e8e8;
            --text-light: #1a1a1a;
            --text-gray: #666666;
            --body-bg: #f5f5f5;
            --card-bg: #ffffff;
            --border-color: rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--body-bg);
            color: var(--text-light);
            overflow: hidden;
            height: 100vh;
            transition: background 0.3s ease, color 0.3s ease;
        }
        
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--secondary-dark);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--accent-gold);
            border-radius: 4px;
        }
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 280px;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary-black) 100%);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .sidebar-header {
            flex-shrink: 0;
            padding: 1.5rem 1rem;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
        }
        
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.5rem 0;
            min-height: 0;
        }
        
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar-nav::-webkit-scrollbar-track {
            background: var(--secondary-dark);
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: var(--accent-gold);
            border-radius: 4px;
        }
        
        .sidebar-footer {
            flex-shrink: 0;
            padding: 1rem;
            border-top: 1px solid var(--border-color);
            text-align: center;
        }
        
        .sidebar .nav-link {
            color: var(--text-gray);
            transition: all 0.3s;
            padding: 12px 20px;
            margin: 2px 12px;
            border-radius: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            text-decoration: none;
            cursor: pointer;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: var(--accent-gold);
            background: rgba(212, 175, 55, 0.08);
        }
        
        .sidebar .nav-link i {
            width: 28px;
            margin-right: 12px;
            text-align: center;
        }
        
        .nav-divider {
            height: 1px;
            background: var(--border-color);
            margin: 8px 20px;
        }
        
        .sidebar.collapsed {
            width: 80px !important;
        }
        
        .sidebar.collapsed .sidebar-header h4,
        .sidebar.collapsed .sidebar-header .text-muted,
        .sidebar.collapsed .sidebar-header hr,
        .sidebar.collapsed .sidebar-header .mt-2 small,
        .sidebar.collapsed .nav-link span,
        .sidebar.collapsed .sidebar-footer span {
            display: none;
        }
        
        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px;
        }
        
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }
        
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: var(--body-bg);
            transition: all 0.3s;
            overflow-y: auto;
            height: 100vh;
        }
        
        .main-content.expanded {
            margin-left: 80px;
        }
        
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            transition: all 0.3s;
        }
        
        .card-header {
            background: rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--accent-gold);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-gold) 0%, var(--accent-gold-dark) 100%);
            border: none;
            color: var(--primary-black);
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.3);
        }
        
        .btn-outline-primary {
            border-color: var(--accent-gold);
            color: var(--accent-gold);
        }
        
        .btn-outline-primary:hover {
            background: var(--accent-gold);
            color: var(--primary-black);
        }
        
        .form-control, .form-select {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid var(--border-color);
            color: var(--text-light);
            border-radius: 10px;
        }
        
        body.light-mode .form-control,
        body.light-mode .form-select {
            background: #ffffff;
            color: #1a1a1a;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(0, 0, 0, 0.7);
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
            color: var(--text-light);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--accent-gold);
            font-size: 0.85rem;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--accent-gold);
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .table {
            color: var(--text-light);
        }
        
        .table thead th {
            background: rgba(0, 0, 0, 0.3);
            border-bottom: 2px solid var(--accent-gold);
            color: var(--accent-gold);
        }
        
        .table tbody tr:hover {
            background: rgba(212, 175, 55, 0.05);
        }
        
        .alert {
            border-radius: 12px;
            background: var(--secondary-dark);
            color: var(--text-light);
        }
        
        .alert-success {
            border-left: 4px solid var(--success-green);
        }
        
        .alert-danger {
            border-left: 4px solid var(--danger-red);
        }
        
        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        
        .loading.show {
            display: flex;
        }
        
        .spinner {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(212, 175, 55, 0.3);
            border-top: 3px solid var(--accent-gold);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 80px !important;
            }
            .sidebar .nav-link span {
                display: none;
            }
            .main-content {
                margin-left: 80px;
            }
        }
        
        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9998;
            min-width: 300px;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .settings-section {
            margin-bottom: 2rem;
        }
        
        .settings-section-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--accent-gold);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .setting-info h6 {
            margin-bottom: 0.25rem;
        }
        
        .setting-info small {
            color: var(--text-gray);
        }
        
        .modal-content {
            background: var(--card-bg);
            color: var(--text-light);
        }
        
        .modal-header {
            border-bottom-color: var(--border-color);
        }
        
        .modal-footer {
            border-top-color: var(--border-color);
        }
        
        .btn-close {
            filter: invert(1);
        }
        
        body.light-mode .btn-close {
            filter: invert(0);
        }
        
        .nav-tabs .nav-link {
            color: var(--text-gray);
        }
        
        .nav-tabs .nav-link.active {
            background: var(--card-bg);
            color: var(--accent-gold);
            border-color: var(--border-color);
        }
        
        .cursor-pointer {
            cursor: pointer;
        }
        
        .text-gold {
            color: var(--accent-gold);
        }
        
        .bg-gold {
            background: var(--accent-gold);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="loading" id="loading">
        <div class="spinner"></div>
    </div>
    
    <div class="toast-notification" id="toastContainer"></div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="mb-3">
                <i class="fas fa-cut fa-3x" style="color: var(--accent-gold);"></i>
            </div>
            <h4 class="mb-1" style="font-weight: 800; color: var(--text-light);" id="shopNameDisplay">BARBERSHOP</h4>
            <small class="text-muted" id="shopTaglineDisplay">Professional POS System</small>
            <hr class="my-3" style="background: var(--border-color);">
            <div class="mt-2">
                <i class="fas fa-user-circle me-1" style="color: var(--accent-gold);"></i>
                <small style="color: var(--text-light);">
                    {{ auth()->user()?->name ?? 'Guest User' }}
                    <span class="badge ms-1" style="background: var(--accent-gold); color: var(--primary-black);">
                        {{ ucfirst(auth()->user()?->role ?? 'Guest') }}
                    </span>
                </small>
            </div>
        </div>
        
        <div class="sidebar-nav">
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                   href="{{ route('dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> 
                    <span>Dashboard</span>
                </a>
                
                @if(auth()->user() && in_array(auth()->user()->role, ['admin', 'cashier']))
                <a class="nav-link {{ request()->routeIs('pos*') ? 'active' : '' }}" 
                   href="{{ route('pos.index') }}">
                    <i class="fas fa-cash-register"></i> 
                    <span>Point of Sale</span>
                </a>
                @endif
                
                @if(auth()->user() && in_array(auth()->user()->role, ['admin', 'cashier']))
                <a class="nav-link {{ request()->routeIs('transactions*') ? 'active' : '' }}" 
                   href="{{ route('transactions.index') }}">
                    <i class="fas fa-receipt"></i> 
                    <span>Transaksi</span>
                </a>
                @endif
                
                @if(auth()->user() && in_array(auth()->user()->role, ['admin', 'barber']))
                <a class="nav-link {{ request()->routeIs('services*') ? 'active' : '' }}" 
                   href="{{ route('services.index') }}">
                    <i class="fas fa-cut"></i> 
                    <span>Layanan</span>
                </a>
                @endif
                
                @if(auth()->user() && in_array(auth()->user()->role, ['admin', 'cashier']))
                <a class="nav-link {{ request()->routeIs('customers*') ? 'active' : '' }}" 
                   href="{{ route('customers.index') }}">
                    <i class="fas fa-users"></i> 
                    <span>Pelanggan</span>
                </a>
                @endif
                
                <div class="nav-divider"></div>
                
                @if(auth()->user() && auth()->user()->role === 'admin')
                <a class="nav-link {{ request()->routeIs('reports.daily') ? 'active' : '' }}" 
                   href="{{ route('reports.daily') }}">
                    <i class="fas fa-chart-line"></i> 
                    <span>Laporan Harian</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('reports.monthly') ? 'active' : '' }}" 
                   href="{{ route('reports.monthly') }}">
                    <i class="fas fa-chart-bar"></i> 
                    <span>Laporan Bulanan</span>
                </a>
                @endif
                
                @if(auth()->user() && auth()->user()->role === 'admin')
                <div class="nav-divider"></div>
                
                <a class="nav-link {{ request()->routeIs('users*') ? 'active' : '' }}" 
                   href="{{ route('users.index') }}">
                    <i class="fas fa-user-shield"></i> 
                    <span>Manajemen User</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('expenses*') ? 'active' : '' }}" 
                   href="{{ route('expenses.index') }}">
                    <i class="fas fa-money-bill-wave"></i> 
                    <span>Pengeluaran</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('queue*') ? 'active' : '' }}" 
                   href="{{ route('queue.index') }}">
                    <i class="fas fa-people-arrows"></i> 
                    <span>Manajemen Antrian</span>
                </a>
                
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#settingsModal">
                    <i class="fas fa-cog"></i> 
                    <span>Pengaturan</span>
                </a>
                
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#backupModal">
                    <i class="fas fa-database"></i> 
                    <span>Backup Database</span>
                </a>
                @endif
                
                @if(auth()->user() && in_array(auth()->user()->role, ['admin', 'cashier', 'barber']))
                <div class="nav-divider"></div>
                
                <a class="nav-link {{ request()->routeIs('queue*') ? 'active' : '' }}" 
                   href="{{ route('queue.index') }}">
                    <i class="fas fa-people-arrows"></i> 
                    <span>Manajemen Antrian</span>
                </a>
                @endif
                
                <div class="nav-divider"></div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link text-danger" 
                            style="background: none; border: none; width: 100%; text-align: left; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i> 
                        <span>Logout</span>
                    </button>
                </form>
            </nav>
        </div>
        
        <div class="sidebar-footer">
            <small class="text-muted">
                <i class="fas fa-code-branch"></i> v2.0.0<br>
                <i class="fas fa-copyright"></i> 2026
            </small>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <nav class="navbar navbar-dark sticky-top" style="background: var(--primary-dark); border-bottom: 1px solid var(--border-color); padding: 1rem 1.5rem;">
            <div class="container-fluid">
                <button class="btn btn-link text-white" id="sidebarCollapseBtn">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                
                <div class="ms-auto d-flex align-items-center gap-3">
                    <div class="text-end">
                        <small class="text-muted d-block" id="currentDate"></small>
                        <small class="text-light" id="currentTime"></small>
                    </div>
                    
                    <div class="dropdown">
                        <button class="btn btn-link text-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fa-lg"></i>
                            <span>{{ auth()->user()?->name ?? 'User' }}</span>
                            <span class="badge ms-1" style="background: var(--accent-gold); color: var(--primary-black);">
                                {{ ucfirst(auth()->user()?->role ?? 'Guest') }}
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                                <i class="fas fa-user me-2"></i> Profile
                            </a></li>
                            @if(auth()->user() && auth()->user()->role === 'admin')
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#settingsModal">
                                <i class="fas fa-cog me-2"></i> Pengaturan
                            </a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        
        <div class="p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {!! session('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {!! session('error') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    
    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-circle me-2" style="color: var(--accent-gold);"></i>
                        Profile Pengguna
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-circle fa-4x" style="color: var(--accent-gold);"></i>
                    </div>
                    <table class="table">
                        <tr><th style="width: 40%;">Nama</th><td>{{ auth()->user()?->name }}</span></td></tr>
                        <tr><th>Email</th><td>{{ auth()->user()?->email }}</span></td></tr>
                        <tr><th>Role</th><td>{{ ucfirst(auth()->user()?->role) }}</span></span></td></tr>
                        <tr><th>Bergabung Sejak</th><td>{{ auth()->user()?->created_at?->format('d F Y') }}</span></span></td></tr>
                    </table>
                    <hr>
                    <div class="text-center">
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-key me-1"></i> Ganti Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="changePasswordForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-key me-2" style="color: var(--accent-gold);"></i>
                            Ganti Password
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Settings Modal -->
    @if(auth()->user() && auth()->user()->role === 'admin')
    <div class="modal fade" id="settingsModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-cog me-2" style="color: var(--accent-gold);"></i>
                        Pengaturan Sistem
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-4" style="border-bottom-color: var(--border-color);">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#generalTab">
                                <i class="fas fa-globe me-1"></i> Umum
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#businessTab">
                                <i class="fas fa-store me-1"></i> Bisnis
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#paymentTab">
                                <i class="fas fa-credit-card me-1"></i> Pembayaran
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#notificationTab">
                                <i class="fas fa-bell me-1"></i> Notifikasi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#printTab">
                                <i class="fas fa-print me-1"></i> Cetak
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <!-- General Tab -->
                        <div class="tab-pane fade show active" id="generalTab">
                            <div class="settings-section">
                                <div class="settings-section-title">
                                    <i class="fas fa-store me-2"></i> Informasi Toko
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Barbershop</label>
                                        <input type="text" class="form-control" id="shopName" value="{{ session('settings.shop_name', 'BARBERSHOP') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nomor Telepon</label>
                                        <input type="text" class="form-control" id="shopPhone" value="{{ session('settings.shop_phone', '+62 812-3456-7890') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="shopEmail" value="{{ session('settings.shop_email', 'info@barbershop.com') }}">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Alamat</label>
                                        <textarea class="form-control" id="shopAddress" rows="2">{{ session('settings.shop_address', 'Jl. Contoh No. 123, Jakarta') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="settings-section">
                                <div class="settings-section-title">
                                    <i class="fas fa-clock me-2"></i> Jam Operasional
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jam Buka</label>
                                        <input type="time" class="form-control" id="openTime" value="{{ session('settings.open_time', '09:00') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jam Tutup</label>
                                        <input type="time" class="form-control" id="closeTime" value="{{ session('settings.close_time', '21:00') }}">
                                    </div>
                                </div>
                                <div class="setting-item">
                                    <div class="setting-info">
                                        <h6>Buka Setiap Hari</h6>
                                        <small>Termasuk hari Minggu dan libur nasional</small>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" id="openEveryday" {{ session('settings.open_everyday', true) ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="settings-section">
                                <div class="settings-section-title">
                                    <i class="fas fa-percent me-2"></i> Pajak & Biaya
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">PPN (Pajak)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="taxRate" value="{{ session('settings.tax_rate', 11) }}" step="0.5">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Service Charge</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="serviceCharge" value="{{ session('settings.service_charge', 0) }}" step="1000">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="settings-section">
                                <div class="settings-section-title">
                                    <i class="fas fa-language me-2"></i> Bahasa & Regional
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Bahasa</label>
                                        <select class="form-control" id="language">
                                            <option value="id" {{ session('settings.language', 'id') == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                            <option value="en" {{ session('settings.language', 'id') == 'en' ? 'selected' : '' }}>English</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Format Tanggal</label>
                                        <select class="form-control" id="dateFormat">
                                            <option value="d/m/Y" {{ session('settings.date_format', 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                            <option value="m/d/Y" {{ session('settings.date_format', 'd/m/Y') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                            <option value="Y-m-d" {{ session('settings.date_format', 'd/m/Y') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mata Uang</label>
                                        <select class="form-control" id="currency">
                                            <option value="IDR" {{ session('settings.currency', 'IDR') == 'IDR' ? 'selected' : '' }}>IDR (Rp)</option>
                                            <option value="USD" {{ session('settings.currency', 'IDR') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="settings-section">
                                <div class="settings-section-title">
                                    <i class="fas fa-palette me-2"></i> Tampilan
                                </div>
                                <div class="setting-item">
                                    <div class="setting-info">
                                        <h6>Mode Gelap</h6>
                                        <small>Tampilan dark/light mode</small>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" id="darkMode" {{ session('settings.dark_mode', true) ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                                <div class="setting-item">
                                    <div class="setting-info">
                                        <h6>Sidebar Collapse Default</h6>
                                        <small>Sidebar menyempit saat pertama kali buka</small>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" id="defaultCollapse" {{ session('settings.default_collapse', false) ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Business Tab -->
                        <div class="tab-pane fade" id="businessTab">
                            <div class="settings-section">
                                <div class="settings-section-title">
                                    <i class="fas fa-chart-line me-2"></i> Target & Laporan
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Target Harian (Rp)</label>
                                        <input type="number" class="form-control" id="dailyTarget" value="{{ session('settings.daily_target', 1000000) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Target Bulanan (Rp)</label>
                                        <input type="number" class="form-control" id="monthlyTarget" value="{{ session('settings.monthly_target', 30000000) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Tab -->
                        <div class="tab-pane fade" id="paymentTab">
                            <div class="settings-section">
                                <div class="settings-section-title">
                                    <i class="fas fa-gem me-2"></i> Program Member
                                </div>
                                <div class="setting-item">
                                    <div class="setting-info">
                                        <h6>Aktifkan Poin Member</h6>
                                        <small>Berikan poin untuk setiap transaksi</small>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" id="enablePoints" {{ session('settings.enable_points', true) ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Konversi Poin</label>
                                        <div class="input-group">
                                            <span>1 Poin =</span>
                                            <input type="number" class="form-control" id="pointValue" value="{{ session('settings.point_value', 100) }}">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Minimal Tukar Poin</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="minPointsRedeem" value="{{ session('settings.min_points_redeem', 100) }}">
                                            <span class="input-group-text">Poin</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="settings-section">
                                <div class="settings-section-title">
                                    <i class="fas fa-tags me-2"></i> Diskon Member
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Diskon Silver</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="silverDiscount" value="{{ session('settings.silver_discount', 5) }}">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Diskon Gold</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="goldDiscount" value="{{ session('settings.gold_discount', 10) }}">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Diskon Platinum</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="platinumDiscount" value="{{ session('settings.platinum_discount', 15) }}">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notification Tab -->
                        <div class="tab-pane fade" id="notificationTab">
                            <div class="setting-item">
                                <div class="setting-info">
                                    <h6>Notifikasi Transaksi</h6>
                                    <small>Tampilkan notifikasi saat transaksi berhasil</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="transactionNotification" {{ session('settings.transaction_notification', true) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="setting-item">
                                <div class="setting-info">
                                    <h6>Notifikasi Stok Habis</h6>
                                    <small>Peringatan saat stok layanan/produk habis</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="stockNotification" {{ session('settings.stock_notification', true) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="setting-item">
                                <div class="setting-info">
                                    <h6>Notifikasi Member Baru</h6>
                                    <small>Info saat ada pendaftaran member baru</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="memberNotification" {{ session('settings.member_notification', false) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Print Tab -->
                        <div class="tab-pane fade" id="printTab">
                            <div class="setting-item">
                                <div class="setting-info">
                                    <h6>Cetak Otomatis</h6>
                                    <small>Cetak struk otomatis setelah transaksi</small>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="autoPrint" {{ session('settings.auto_print', true) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="setting-item">
                                <div class="setting-info">
                                    <h6>Ukuran Kertas</h6>
                                    <small>Ukuran kertas untuk struk</small>
                                </div>
                                <select class="form-select w-auto" id="paperSize" style="width: 120px;">
                                    <option value="58" {{ session('settings.paper_size', '80') == '58' ? 'selected' : '' }}>58 mm</option>
                                    <option value="80" {{ session('settings.paper_size', '80') == '80' ? 'selected' : '' }}>80 mm</option>
                                </select>
                            </div>
                            <div class="setting-item">
                                <div class="setting-info">
                                    <h6>Copy Struk</h6>
                                    <small>Jumlah copy struk yang dicetak</small>
                                </div>
                                <select class="form-select w-auto" id="printCopies" style="width: 80px;">
                                    <option value="1" {{ session('settings.print_copies', 1) == 1 ? 'selected' : '' }}>1</option>
                                    <option value="2" {{ session('settings.print_copies', 1) == 2 ? 'selected' : '' }}>2</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveAllSettings">
                        <i class="fas fa-save me-2"></i> Simpan Semua Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Backup Modal -->
    <div class="modal fade" id="backupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-database me-2" style="color: var(--accent-gold);"></i>
                        Backup Database
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda ingin melakukan backup database?</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Backup akan menyimpan semua data transaksi, pelanggan, layanan, dan pengaturan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="backupDatabase()">
                        <i class="fas fa-download me-2"></i> Backup Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // AppSettings Class
        class AppSettings {
            constructor() {
                this.settings = this.loadSettings();
                this.listeners = [];
                this.init();
            }
            
            loadSettings() {
                const defaultSettings = {
                    language: 'id',
                    dateFormat: 'd/m/Y',
                    currency: 'IDR',
                    darkMode: true,
                    defaultCollapse: false,
                    shopName: 'BARBERSHOP',
                    shopAddress: 'Jl. Contoh No. 123, Jakarta',
                    shopPhone: '+62 812-3456-7890',
                    shopEmail: 'info@barbershop.com',
                    openTime: '09:00',
                    closeTime: '21:00',
                    openEveryday: true,
                    taxRate: 11,
                    serviceCharge: 0,
                    enablePoints: true,
                    pointValue: 100,
                    minPointsRedeem: 100,
                    silverDiscount: 5,
                    goldDiscount: 10,
                    platinumDiscount: 15,
                    transactionNotification: true,
                    stockNotification: true,
                    memberNotification: false,
                    autoPrint: true,
                    paperSize: '80',
                    printCopies: 1
                };
                
                const saved = localStorage.getItem('appSettings');
                if (saved) {
                    try {
                        return { ...defaultSettings, ...JSON.parse(saved) };
                    } catch(e) {
                        return defaultSettings;
                    }
                }
                return defaultSettings;
            }
            
            saveSettings(newSettings) {
                this.settings = { ...this.settings, ...newSettings };
                localStorage.setItem('appSettings', JSON.stringify(this.settings));
                this.applyAllSettings();
                this.triggerChange();
                return this.settings;
            }
            
            get(key) {
                return this.settings[key];
            }
            
            getAll() {
                return { ...this.settings };
            }
            
            init() {
                this.applyAllSettings();
                this.setupEventListeners();
            }
            
            applyAllSettings() {
                this.applyDarkMode();
                this.applyShopName();
                this.applySidebarCollapse();
                this.applyCurrencyToAllElements();
                this.applyDateFormatToAllElements();
            }
            
            applyDarkMode() {
                if (this.settings.darkMode) {
                    document.body.classList.remove('light-mode');
                } else {
                    document.body.classList.add('light-mode');
                }
            }
            
            applyShopName() {
                const elements = document.querySelectorAll('[data-shop-name], #shopNameDisplay');
                elements.forEach(el => {
                    el.textContent = this.settings.shopName;
                });
            }
            
            applySidebarCollapse() {
                const sidebar = document.getElementById('sidebar');
                const mainContent = document.getElementById('mainContent');
                if (!sidebar) return;
                
                const savedCollapse = localStorage.getItem('sidebarCollapsed');
                if (savedCollapse !== null) {
                    if (savedCollapse === 'true') {
                        sidebar.classList.add('collapsed');
                        if (mainContent) mainContent.classList.add('expanded');
                    }
                } else if (this.settings.defaultCollapse) {
                    sidebar.classList.add('collapsed');
                    if (mainContent) mainContent.classList.add('expanded');
                }
            }
            
            formatCurrency(amount) {
                if (isNaN(amount)) amount = 0;
                const currency = this.settings.currency;
                if (currency === 'IDR') {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
                } else {
                    return '$ ' + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount);
                }
            }
            
            formatDate(date, format = null) {
                const fmt = format || this.settings.dateFormat;
                const d = new Date(date);
                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const year = d.getFullYear();
                
                return fmt
                    .replace('d', day)
                    .replace('m', month)
                    .replace('Y', year);
            }
            
            applyCurrencyToAllElements() {
                document.querySelectorAll('[data-currency], .price-amount, .currency-value').forEach(el => {
                    let value = el.getAttribute('data-currency-value') || el.getAttribute('data-price') || el.textContent;
                    value = parseFloat(value.toString().replace(/[^0-9.-]/g, ''));
                    if (!isNaN(value)) {
                        el.textContent = this.formatCurrency(value);
                    }
                });
            }
            
            applyDateFormatToAllElements() {
                document.querySelectorAll('[data-date]').forEach(el => {
                    const dateValue = el.getAttribute('data-date');
                    if (dateValue) {
                        el.textContent = this.formatDate(dateValue);
                    }
                });
            }
            
            calculateTax(amount) {
                return amount * (this.settings.taxRate / 100);
            }
            
            calculateTotal(amount) {
                const tax = this.calculateTax(amount);
                const service = this.settings.serviceCharge;
                return amount + tax + service;
            }
            
            getMemberDiscount(level) {
                const discounts = {
                    silver: this.settings.silverDiscount,
                    gold: this.settings.goldDiscount,
                    platinum: this.settings.platinumDiscount
                };
                return discounts[level?.toLowerCase()] || 0;
            }
            
            calculatePoints(amount) {
                if (!this.settings.enablePoints) return 0;
                return Math.floor(amount / this.settings.pointValue);
            }
            
            onChange(callback) {
                this.listeners.push(callback);
            }
            
            triggerChange() {
                this.listeners.forEach(callback => callback(this.settings));
            }
            
            setupEventListeners() {
                const darkModeToggle = document.getElementById('darkMode');
                if (darkModeToggle) {
                    darkModeToggle.addEventListener('change', (e) => {
                        this.saveSettings({ darkMode: e.target.checked });
                    });
                }
            }
        }
        
        window.appSettings = new AppSettings();
        
        // Utility Functions
        function updateDateTime() {
            const now = new Date();
            const formattedDate = now.toLocaleDateString('id-ID', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            document.getElementById('currentDate').innerHTML = formattedDate;
            document.getElementById('currentTime').innerHTML = now.toLocaleTimeString('id-ID');
        }
        
        function updateSidebarShopName() {
            const shopName = window.appSettings?.get('shopName') || 'BARBERSHOP';
            const displayElement = document.getElementById('shopNameDisplay');
            if (displayElement) {
                displayElement.innerText = shopName;
            }
        }
        
        function showToast(type, message) {
            const toastContainer = document.getElementById('toastContainer');
            const icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-circle' : 'info-circle');
            const toastHtml = `
                <div class="alert alert-${type} alert-dismissible fade show mb-2" role="alert" style="animation: slideInRight 0.3s ease;">
                    <i class="fas fa-${icon} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            setTimeout(() => {
                const alerts = toastContainer.querySelectorAll('.alert');
                if (alerts.length > 0) alerts[0].remove();
            }, 3000);
        }
        
        function backupDatabase() {
            showToast('info', 'Proses backup dimulai...');
            setTimeout(() => {
                const data = {
                    settings: window.appSettings?.getAll(),
                    timestamp: new Date().toISOString(),
                    version: '2.0.0'
                };
                const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `barbershop_backup_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.json`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                showToast('success', 'Backup database berhasil! File telah disimpan.');
                $('#backupModal').modal('hide');
            }, 2000);
        }
        
        window.formatCurrency = (amount) => window.appSettings?.formatCurrency(amount) || `Rp ${amount}`;
        window.calculateTax = (amount) => window.appSettings?.calculateTax(amount) || 0;
        window.calculateTotal = (amount) => window.appSettings?.calculateTotal(amount) || amount;
        window.getMemberDiscount = (level) => window.appSettings?.getMemberDiscount(level) || 0;
        window.calculatePoints = (amount) => window.appSettings?.calculatePoints(amount) || 0;
        
        $(document).ready(function() {
            updateSidebarShopName();
            
            $('#sidebarCollapseBtn').click(function() {
                $('#sidebar').toggleClass('collapsed');
                $('#mainContent').toggleClass('expanded');
                localStorage.setItem('sidebarCollapsed', $('#sidebar').hasClass('collapsed'));
            });
            
            const savedState = localStorage.getItem('sidebarCollapsed');
            if (savedState === 'true') {
                $('#sidebar').addClass('collapsed');
                $('#mainContent').addClass('expanded');
            }
            
            $('#changePasswordForm').on('submit', function(e) {
                e.preventDefault();
                const newPass = $('input[name="new_password"]').val();
                const confirmPass = $('input[name="new_password_confirmation"]').val();
                
                if (newPass !== confirmPass) {
                    showToast('error', 'Password baru tidak cocok!');
                    return;
                }
                
                showToast('success', 'Password berhasil diubah!');
                $('#changePasswordModal').modal('hide');
                $('#changePasswordForm')[0].reset();
            });
            
            $('#saveAllSettings').click(function() {
                const newSettings = {
                    language: $('#language').val(),
                    dateFormat: $('#dateFormat').val(),
                    currency: $('#currency').val(),
                    darkMode: $('#darkMode').is(':checked'),
                    defaultCollapse: $('#defaultCollapse').is(':checked'),
                    shopName: $('#shopName').val(),
                    shopAddress: $('#shopAddress').val(),
                    shopPhone: $('#shopPhone').val(),
                    shopEmail: $('#shopEmail').val(),
                    openTime: $('#openTime').val(),
                    closeTime: $('#closeTime').val(),
                    openEveryday: $('#openEveryday').is(':checked'),
                    taxRate: parseFloat($('#taxRate').val()),
                    serviceCharge: parseFloat($('#serviceCharge').val()),
                    enablePoints: $('#enablePoints').is(':checked'),
                    pointValue: parseFloat($('#pointValue').val()),
                    minPointsRedeem: parseFloat($('#minPointsRedeem').val()),
                    silverDiscount: parseFloat($('#silverDiscount').val()),
                    goldDiscount: parseFloat($('#goldDiscount').val()),
                    platinumDiscount: parseFloat($('#platinumDiscount').val()),
                    transactionNotification: $('#transactionNotification').is(':checked'),
                    stockNotification: $('#stockNotification').is(':checked'),
                    memberNotification: $('#memberNotification').is(':checked'),
                    autoPrint: $('#autoPrint').is(':checked'),
                    paperSize: $('#paperSize').val(),
                    printCopies: parseInt($('#printCopies').val())
                };
                
                window.appSettings.saveSettings(newSettings);
                updateSidebarShopName();
                
                showToast('success', 'Semua pengaturan berhasil disimpan!');
                $('#settingsModal').modal('hide');
            });
            
            const currentSettings = window.appSettings?.getAll();
            if (currentSettings) {
                $('#language').val(currentSettings.language);
                $('#dateFormat').val(currentSettings.dateFormat);
                $('#currency').val(currentSettings.currency);
                $('#darkMode').prop('checked', currentSettings.darkMode);
                $('#defaultCollapse').prop('checked', currentSettings.defaultCollapse);
                $('#shopName').val(currentSettings.shopName);
                $('#shopAddress').val(currentSettings.shopAddress);
                $('#shopPhone').val(currentSettings.shopPhone);
                $('#shopEmail').val(currentSettings.shopEmail);
                $('#openTime').val(currentSettings.openTime);
                $('#closeTime').val(currentSettings.closeTime);
                $('#openEveryday').prop('checked', currentSettings.openEveryday);
                $('#taxRate').val(currentSettings.taxRate);
                $('#serviceCharge').val(currentSettings.serviceCharge);
                $('#enablePoints').prop('checked', currentSettings.enablePoints);
                $('#pointValue').val(currentSettings.pointValue);
                $('#minPointsRedeem').val(currentSettings.minPointsRedeem);
                $('#silverDiscount').val(currentSettings.silverDiscount);
                $('#goldDiscount').val(currentSettings.goldDiscount);
                $('#platinumDiscount').val(currentSettings.platinumDiscount);
                $('#transactionNotification').prop('checked', currentSettings.transactionNotification);
                $('#stockNotification').prop('checked', currentSettings.stockNotification);
                $('#memberNotification').prop('checked', currentSettings.memberNotification);
                $('#autoPrint').prop('checked', currentSettings.autoPrint);
                $('#paperSize').val(currentSettings.paperSize);
                $('#printCopies').val(currentSettings.printCopies);
            }
            
            updateDateTime();
            setInterval(updateDateTime, 1000);
            
            window.appSettings.applyCurrencyToAllElements();
        });
    </script>
    
    @stack('scripts')
</body>
</html>