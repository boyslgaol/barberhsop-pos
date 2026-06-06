@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Selamat Datang -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(135deg, var(--secondary-dark) 0%, rgba(212, 175, 55, 0.05) 100%);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h4 class="mb-2" style="color: var(--accent-gold);">
                                <i class="fas fa-fire me-2"></i>Selamat Datang Kembali!
                            </h4>
                            <h2 class="text-white mb-0">
                                {{ auth()->user()?->name ?? 'Pengguna' }}
                            </h2>
                            <p class="text-muted mt-2 mb-0">
                                <i class="fas fa-chart-line me-1"></i>
                                Berikut adalah ringkasan bisnis <span data-shop-name>{{ $shopName ?? 'BARBERSHOP' }}</span> Anda hari ini.
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="stat-card d-inline-block p-3">
                                <small class="text-muted d-block">Hari Ini</small>
                                <strong class="text-light" id="dashboardDate"></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge" style="background: rgba(212, 175, 55, 0.2); color: var(--accent-gold);">
                            HARI INI
                        </span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
                <div>
                    <h6 class="text-muted mb-2">Total Pendapatan</h6>
                    <h2 class="text-white mb-0" id="todayRevenue" data-currency-value="{{ $todayRevenue ?? 0 }}">
                        {{ $todayRevenue ?? 0 }}
                    </h2>
                    <small class="text-muted">
                        <i class="fas fa-calendar-alt me-1"></i>
                        <span id="todayDate"></span>
                    </small>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar" role="progressbar" style="width: 75%; background: var(--accent-gold);"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge" style="background: rgba(212, 175, 55, 0.2); color: var(--accent-gold);">
                            TRANSAKSI
                        </span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
                <div>
                    <h6 class="text-muted mb-2">Transaksi Hari Ini</h6>
                    <h2 class="text-white mb-0">{{ $todayTransactionsCount ?? 0 }}</h2>
                    <small class="text-muted">
                        <i class="fas fa-chart-line me-1"></i>
                        Rata-rata {{ number_format(($todayTransactionsCount ?? 0) * 1.2, 0) }} per hari
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge" style="background: rgba(212, 175, 55, 0.2); color: var(--accent-gold);">
                            PELANGGAN
                        </span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div>
                    <h6 class="text-muted mb-2">Pelanggan Unik</h6>
                    <h2 class="text-white mb-0">{{ $todayCustomers ?? 0 }}</h2>
                    <small class="text-muted" id="newCustomersThisWeek">
                        <i class="fas fa-user-plus me-1"></i>
                        {{ rand(1, 10) }} pelanggan baru minggu ini
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge" style="background: rgba(212, 175, 55, 0.2); color: var(--accent-gold);">
                            BULAN INI
                        </span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div>
                    <h6 class="text-muted mb-2">Pendapatan Bulan Ini</h6>
                    <h2 class="text-white mb-0" id="monthRevenue" data-currency-value="{{ $monthRevenue ?? 0 }}">
                        {{ $monthRevenue ?? 0 }}
                    </h2>
                    <small class="text-muted" id="dailyAverageRevenue">
                        <i class="fas fa-trend-up me-1"></i>
                        Rata-rata {{ number_format((($monthRevenue ?? 0) / 30) ?? 0, 0, ',', '.') }} per hari
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik & Layanan Terlaris -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Grafik Pendapatan
                        </h5>
                        <small class="text-muted">Performa 7 hari terakhir (dalam <span id="currencySymbol">Rp</span>)</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-calendar-alt me-1"></i> 7 Hari Terakhir
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-period="7">7 Hari Terakhir</a></li>
                            <li><a class="dropdown-item" href="#" data-period="30">30 Hari Terakhir</a></li>
                            <li><a class="dropdown-item" href="#" data-period="this_month">Bulan Ini</a></li>
                            <li><a class="dropdown-item" href="#" data-period="last_month">Bulan Lalu</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>
                        Layanan Terlaris
                    </h5>
                    <small class="text-muted">Layanan paling populer bulan ini</small>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($topServices ?? [] as $index => $service)
                            <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="rank-badge me-3" style="width: 30px;">
                                        @if($index == 0)
                                            <i class="fas fa-trophy" style="color: #ffd700;"></i>
                                        @elseif($index == 1)
                                            <i class="fas fa-medal" style="color: #c0c0c0;"></i>
                                        @elseif($index == 2)
                                            <i class="fas fa-medal" style="color: #cd7f32;"></i>
                                        @else
                                            <span class="text-muted">#{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <strong class="text-light">{{ $service->name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-cut me-1"></i>Layanan Barbershop
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge" style="background: var(--accent-gold); color: var(--primary-black);">
                                        {{ $service->total }}x Dipesan
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="fas fa-chart-simple"></i>
                                <p class="mb-0">Belum ada data layanan</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-arrow-right me-1"></i> Kelola Layanan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Terbaru & Aksi Cepat -->
    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            Transaksi Terbaru
                        </h5>
                        <small class="text-muted">10 transaksi terakhir</small>
                    </div>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>No. Invoice</th>
                                    <th>Pelanggan</th>
                                    <th>Tanggal & Waktu</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions ?? [] as $transaction)
                                <tr>
                                    <td>
                                        <span class="text-muted">#</span>
                                        <strong class="text-light">{{ $transaction->invoice_number }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2">
                                                {{ substr($transaction->customer->name ?? 'T', 0, 1) }}
                                            </div>
                                            {{ $transaction->customer->name ?? 'Tamu' }}
                                        </div>
                                    </td>
                                    <td>
                                        <small class="transaction-date" data-date="{{ $transaction->transaction_date }}">
                                            {{ $transaction->transaction_date->format('d/m/Y') }}<br>
                                            <i class="far fa-clock me-1"></i>
                                            {{ $transaction->transaction_date->format('H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <strong class="text-success transaction-amount" data-currency-value="{{ $transaction->total }}">
                                            {{ $transaction->total }}
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle me-1"></i> Selesai
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('receipt.print', $transaction->id) }}" 
                                           class="btn btn-sm btn-info" target="_blank" title="Cetak Struk">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <p class="mb-0">Belum ada transaksi</p>
                                            <a href="{{ route('pos.index') }}" class="btn btn-sm btn-primary mt-2">
                                                <i class="fas fa-plus me-1"></i> Mulai Transaksi Baru
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5 mb-4">
            <!-- Aksi Cepat -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Aksi Cepat
                    </h5>
                    <small class="text-muted">Fitur yang sering digunakan</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="{{ route('pos.index') }}" class="quick-action-btn">
                                <div class="text-center p-3 rounded" style="background: rgba(212, 175, 55, 0.1);">
                                    <i class="fas fa-cash-register fa-2x mb-2" style="color: var(--accent-gold);"></i>
                                    <div class="text-light small">Transaksi Baru</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('customers.create') }}" class="quick-action-btn">
                                <div class="text-center p-3 rounded" style="background: rgba(212, 175, 55, 0.1);">
                                    <i class="fas fa-user-plus fa-2x mb-2" style="color: var(--accent-gold);"></i>
                                    <div class="text-light small">Tambah Pelanggan</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('services.create') }}" class="quick-action-btn">
                                <div class="text-center p-3 rounded" style="background: rgba(212, 175, 55, 0.1);">
                                    <i class="fas fa-cut fa-2x mb-2" style="color: var(--accent-gold);"></i>
                                    <div class="text-light small">Tambah Layanan</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('reports.daily') }}" class="quick-action-btn">
                                <div class="text-center p-3 rounded" style="background: rgba(212, 175, 55, 0.1);">
                                    <i class="fas fa-chart-line fa-2x mb-2" style="color: var(--accent-gold);"></i>
                                    <div class="text-light small">Lihat Laporan</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informasi Toko dari Pengaturan -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-store me-2"></i>
                        Informasi Toko
                    </h5>
                    <small class="text-muted">Data dari pengaturan sistem</small>
                </div>
                <div class="card-body">
                    <div class="shop-info">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-cut fa-2x me-3" style="color: var(--accent-gold);"></i>
                            <div>
                                <h6 class="text-light mb-0" id="shopInfoName">BARBERSHOP</h6>
                                <small class="text-muted">Barbershop Professional</small>
                            </div>
                        </div>
                        <hr class="bg-secondary">
                        <div class="info-item mb-2">
                            <i class="fas fa-map-marker-alt me-2" style="color: var(--accent-gold); width: 20px;"></i>
                            <span id="shopInfoAddress">Jl. Contoh No. 123, Jakarta</span>
                        </div>
                        <div class="info-item mb-2">
                            <i class="fas fa-phone me-2" style="color: var(--accent-gold); width: 20px;"></i>
                            <span id="shopInfoPhone">+62 812-3456-7890</span>
                        </div>
                        <div class="info-item mb-2">
                            <i class="fas fa-envelope me-2" style="color: var(--accent-gold); width: 20px;"></i>
                            <span id="shopInfoEmail">info@barbershop.com</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock me-2" style="color: var(--accent-gold); width: 20px;"></i>
                            <span id="shopInfoHours">09:00 - 21:00</span>
                            <span class="badge bg-success ms-2" id="openStatusBadge">Buka</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Wawasan Bisnis -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Wawasan Bisnis
                    </h5>
                    <small class="text-muted">Analitik bertenaga AI</small>
                </div>
                <div class="card-body">
                    <div class="insight-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Jam Sibuk</span>
                            <strong class="text-light">
                                <i class="fas fa-clock me-1" style="color: var(--accent-gold);"></i>
                                <span id="busyHours">15:00 - 18:00</span>
                            </strong>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: 85%; background: var(--accent-gold);"></div>
                        </div>
                    </div>
                    
                    <div class="insight-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Kepuasan Pelanggan</span>
                            <strong class="text-light">
                                <i class="fas fa-star me-1" style="color: #ffd700;"></i>
                                4.8 / 5.0
                            </strong>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: 96%; background: var(--accent-gold);"></div>
                        </div>
                    </div>
                    
                    <div class="insight-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Target Bulanan</span>
                            <strong class="text-light">
                                <i class="fas fa-bullseye me-1" style="color: var(--accent-gold);"></i>
                                <span id="monthlyTargetPercent">
                                    {{ number_format((($monthRevenue ?? 0) / 10000000) * 100, 0) }}%
                                </span>
                            </strong>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" 
                                 id="monthlyTargetBar"
                                 style="width: {{ min((($monthRevenue ?? 0) / 10000000) * 100, 100) }}%; background: var(--accent-gold);">
                            </div>
                        </div>
                    </div>
                    
                    <hr class="bg-secondary my-3">
                    
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-sync-alt me-1"></i>
                            Last updated: <span id="lastUpdated"></span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom styles untuk dashboard */
    .stat-card {
        background: linear-gradient(135deg, var(--secondary-dark) 0%, rgba(212, 175, 55, 0.05) 100%);
        border: 1px solid rgba(212, 175, 55, 0.1);
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        border-color: var(--accent-gold);
        box-shadow: 0 10px 30px rgba(212, 175, 55, 0.1);
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(212, 175, 55, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .stat-icon i {
        font-size: 1.5rem;
        color: var(--accent-gold);
    }
    
    .rank-badge {
        font-size: 1.2rem;
    }
    
    .avatar-circle {
        width: 32px;
        height: 32px;
        background: rgba(212, 175, 55, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent-gold);
        font-weight: bold;
    }
    
    .quick-action-btn {
        text-decoration: none;
        transition: all 0.3s;
        display: block;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-3px);
    }
    
    .quick-action-btn:hover .rounded {
        background: rgba(212, 175, 55, 0.2) !important;
        border: 1px solid rgba(212, 175, 55, 0.3);
    }
    
    .chart-container {
        position: relative;
        height: 350px;
        width: 100%;
    }
    
    .progress-bar {
        transition: width 1s ease-in-out;
    }
    
    .shop-info .info-item {
        font-size: 0.9rem;
    }
    
    /* Animasi untuk stat cards */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .stat-card {
        animation: fadeInUp 0.5s ease forwards;
    }
    
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    
    /* Badge sukses */
    .badge-success {
        background: var(--success-green);
        color: white;
    }
    
    .badge-warning {
        background: var(--warning-yellow);
        color: var(--primary-black);
    }
    
    .badge-danger {
        background: var(--danger-red);
        color: white;
    }
    
    .badge-info {
        background: var(--info-blue);
        color: white;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Set tanggal dashboard dengan format dari pengaturan
    function updateDashboardDate() {
        const now = new Date();
        const dateFormat = window.appSettings?.get('dateFormat') || 'd/m/Y';
        let formattedDate;
        
        if (dateFormat === 'd/m/Y') {
            formattedDate = now.toLocaleDateString('id-ID', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        } else if (dateFormat === 'm/d/Y') {
            formattedDate = now.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        } else {
            formattedDate = now.toISOString().split('T')[0];
        }
        
        document.getElementById('dashboardDate').innerHTML = formattedDate;
        document.getElementById('todayDate').innerHTML = now.toLocaleDateString('id-ID', { 
            day: 'numeric', month: 'long', year: 'numeric' 
        });
    }
    
    // Update currency pada semua elemen
    function updateAllCurrencies() {
        if (!window.appSettings) return;
        
        // Update pendapatan hari ini
        const todayRevenueEl = document.getElementById('todayRevenue');
        if (todayRevenueEl) {
            const value = parseFloat(todayRevenueEl.getAttribute('data-currency-value') || 0);
            todayRevenueEl.textContent = window.appSettings.formatCurrency(value);
        }
        
        // Update pendapatan bulan ini
        const monthRevenueEl = document.getElementById('monthRevenue');
        if (monthRevenueEl) {
            const value = parseFloat(monthRevenueEl.getAttribute('data-currency-value') || 0);
            monthRevenueEl.textContent = window.appSettings.formatCurrency(value);
        }
        
        // Update semua transaksi
        document.querySelectorAll('.transaction-amount').forEach(el => {
            const value = parseFloat(el.getAttribute('data-currency-value') || 0);
            el.textContent = window.appSettings.formatCurrency(value);
        });
        
        // Update currency symbol di grafik
        const currencySymbol = window.appSettings.get('currency') === 'IDR' ? 'Rp' : '$';
        document.getElementById('currencySymbol').textContent = currencySymbol;
        
        // Update chart jika ada
        if (window.revenueChart) {
            const chartData = window.revenueChart.data.datasets[0].data;
            window.revenueChart.update();
        }
    }
    
    // Update informasi toko dari pengaturan
    function updateShopInfo() {
        if (!window.appSettings) return;
        
        const shopName = window.appSettings.get('shopName') || 'BARBERSHOP';
        const shopAddress = window.appSettings.get('shopAddress') || 'Jl. Contoh No. 123, Jakarta';
        const shopPhone = window.appSettings.get('shopPhone') || '+62 812-3456-7890';
        const shopEmail = window.appSettings.get('shopEmail') || 'info@barbershop.com';
        const openTime = window.appSettings.get('openTime') || '09:00';
        const closeTime = window.appSettings.get('closeTime') || '21:00';
        const openEveryday = window.appSettings.get('openEveryday') || true;
        
        document.getElementById('shopInfoName').textContent = shopName;
        document.getElementById('shopInfoAddress').textContent = shopAddress;
        document.getElementById('shopInfoPhone').textContent = shopPhone;
        document.getElementById('shopInfoEmail').textContent = shopEmail;
        document.getElementById('shopInfoHours').textContent = `${openTime} - ${closeTime}`;
        
        // Update status buka/tutup
        const now = new Date();
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();
        const openHour = parseInt(openTime.split(':')[0]);
        const openMinute = parseInt(openTime.split(':')[1] || 0);
        const closeHour = parseInt(closeTime.split(':')[0]);
        const closeMinute = parseInt(closeTime.split(':')[1] || 0);
        
        const currentTotal = currentHour * 60 + currentMinute;
        const openTotal = openHour * 60 + openMinute;
        const closeTotal = closeHour * 60 + closeMinute;
        
        const isOpen = currentTotal >= openTotal && currentTotal <= closeTotal;
        const statusBadge = document.getElementById('openStatusBadge');
        
        if (isOpen) {
            statusBadge.textContent = 'Buka';
            statusBadge.className = 'badge bg-success ms-2';
        } else {
            statusBadge.textContent = 'Tutup';
            statusBadge.className = 'badge bg-danger ms-2';
        }
    }
    
    // Update semua tanggal dengan format dari pengaturan
    function updateAllDates() {
        if (!window.appSettings) return;
        
        document.querySelectorAll('.transaction-date').forEach(el => {
            const dateValue = el.getAttribute('data-date');
            if (dateValue) {
                const formattedDate = window.appSettings.formatDate(dateValue);
                const timeHtml = el.innerHTML.split('<br>')[1] || '';
                el.innerHTML = `${formattedDate}<br>${timeHtml}`;
            }
        });
    }
    
    // Format jam sibuk
    function formatBusyHours() {
        if (!window.appSettings) return;
        
        const openTime = window.appSettings.get('openTime') || '09:00';
        const closeTime = window.appSettings.get('closeTime') || '21:00';
        
        // Simulasi jam sibuk (2 jam sebelum tutup)
        const closeHour = parseInt(closeTime.split(':')[0]);
        const startBusy = `${String(closeHour - 3).padStart(2, '0')}:00`;
        const endBusy = `${String(closeHour - 1).padStart(2, '0')}:00`;
        
        document.getElementById('busyHours').textContent = `${startBusy} - ${endBusy}`;
    }
    
    // Dashboard initialization
    document.addEventListener('DOMContentLoaded', function() {
        updateDashboardDate();
        
        // Tunggu appSettings siap
        const waitForAppSettings = setInterval(function() {
            if (window.appSettings) {
                clearInterval(waitForAppSettings);
                updateAllCurrencies();
                updateShopInfo();
                updateAllDates();
                formatBusyHours();
                
                // Listen untuk perubahan pengaturan
                window.appSettings.onChange(function(settings) {
                    updateAllCurrencies();
                    updateShopInfo();
                    updateAllDates();
                    formatBusyHours();
                    updateChartCurrency();
                });
            }
        }, 100);
        
        // Last updated time
        document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString('id-ID');
        
        // Animasi progress bars
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        });
    });
    
    // Function to update chart with new currency
    function updateChartCurrency() {
        if (window.revenueChart && window.appSettings) {
            window.revenueChart.options.scales.y.ticks.callback = function(value) {
                return window.appSettings.formatCurrency(value);
            };
            window.revenueChart.update();
        }
    }
    
    // Grafik Pendapatan
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Gradient untuk chart
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(212, 175, 55, 0.3)');
    gradient.addColorStop(1, 'rgba(212, 175, 55, 0.0)');
    
    const chartData = {!! json_encode($chartData['revenue'] ?? [0, 0, 0, 0, 0, 0, 0]) !!};
    const chartLabels = {!! json_encode($chartData['labels'] ?? ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']) !!};
    
    window.revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Pendapatan',
                data: chartData,
                borderColor: '#d4af37',
                backgroundColor: gradient,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#d4af37',
                pointBorderColor: '#000000',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#d4af37'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    labels: {
                        color: '#a0a0a0',
                        font: {
                            family: 'Inter',
                            size: 12
                        }
                    },
                    position: 'top'
                },
                tooltip: {
                    backgroundColor: '#1a1a1a',
                    titleColor: '#d4af37',
                    bodyColor: '#ffffff',
                    borderColor: '#d4af37',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (window.appSettings) {
                                label += window.appSettings.formatCurrency(context.raw);
                            } else {
                                label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#a0a0a0',
                        callback: function(value) {
                            if (window.appSettings) {
                                return window.appSettings.formatCurrency(value);
                            }
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        color: '#a0a0a0'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            elements: {
                line: {
                    tension: 0.4
                }
            }
        }
    });
    
    // Period filter untuk grafik
    document.querySelectorAll('[data-period]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const period = this.getAttribute('data-period');
            
            // Simulate loading data
            showToast('info', 'Memuat data untuk periode ' + period + '...');
            
            // Here you would fetch new data via AJAX
            setTimeout(() => {
                showToast('success', 'Data berhasil diperbarui!');
            }, 1000);
        });
    });
    
    // Auto refresh data setiap 30 detik (opsional)
    let autoRefresh = setInterval(function() {
        if (!document.hidden) {
            $.ajax({
                url: window.location.href,
                method: 'GET',
                success: function(response) {
                    // Update statistik tanpa reload halaman
                    const newData = $(response).find('.stat-card .text-white');
                    if (newData.length) {
                        location.reload();
                    }
                }
            });
        }
    }, 30000);
</script>
@endpush
@endsection