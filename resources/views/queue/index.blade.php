@extends('layouts.app')

@section('title', 'Manajemen Antrian')

@push('styles')
<style>
    /* Stat Cards Styling */
    .stat-card {
        background: linear-gradient(135deg, var(--secondary-dark) 0%, rgba(212, 175, 55, 0.03) 100%);
        border: 1px solid rgba(212, 175, 55, 0.1);
        border-radius: 20px;
        padding: 1.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--accent-gold), transparent);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        border-color: rgba(212, 175, 55, 0.3);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
    }

    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: rgba(212, 175, 55, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.05);
        background: rgba(212, 175, 55, 0.2);
    }

    .stat-icon i {
        font-size: 1.4rem;
        color: var(--accent-gold);
    }

    /* Progress bar styling */
    .progress {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
    }

    .progress-bar {
        border-radius: 10px;
        transition: width 1s ease-in-out;
    }

    /* Badge styling */
    .stat-card .badge {
        font-size: 0.7rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 500;
    }

    /* Hover effect untuk angka */
    .stat-card h2 {
        transition: all 0.3s;
    }

    .stat-card:hover h2 {
        transform: scale(1.02);
    }
    /* Modal styling untuk dark theme */
    .modal-content {
        background: var(--secondary-dark) !important;
        border: 1px solid rgba(212, 175, 55, 0.3) !important;
        border-radius: 16px !important;
    }
    
    .modal-header {
        border-bottom: 1px solid rgba(212, 175, 55, 0.2) !important;
        padding: 1rem 1.5rem !important;
    }
    
    .modal-header .modal-title {
        color: var(--accent-gold) !important;
        font-weight: 600 !important;
    }
    
    .modal-header .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
        opacity: 0.8;
    }
    
    .modal-header .btn-close:hover {
        opacity: 1;
    }
    
    .modal-body {
        color: var(--text-light) !important;
        padding: 1.5rem !important;
    }
    
    .modal-body p {
        color: var(--text-light) !important;
        margin-bottom: 1rem;
    }
    
    .modal-body .alert {
        background: rgba(212, 175, 55, 0.1) !important;
        border: 1px solid rgba(212, 175, 55, 0.2) !important;
        color: var(--text-light) !important;
        border-radius: 12px;
    }
    
    .modal-body .alert-info {
        border-left: 3px solid var(--accent-gold) !important;
    }
    
    .modal-body .alert-info i {
        color: var(--accent-gold) !important;
    }
    
    .modal-body .alert strong {
        color: var(--accent-gold) !important;
    }
    
    .modal-footer {
        border-top: 1px solid rgba(212, 175, 55, 0.2) !important;
        padding: 1rem 1.5rem !important;
    }
    
    .modal-footer .btn-secondary {
        background: rgba(255, 255, 255, 0.1) !important;
        border: none !important;
        color: var(--text-light) !important;
        transition: all 0.3s;
    }
    
    .modal-footer .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.2) !important;
        transform: translateY(-1px);
    }
    
    .modal-footer .btn-primary {
        background: linear-gradient(135deg, var(--accent-gold) 0%, var(--accent-gold-dark) 100%) !important;
        border: none !important;
        color: var(--primary-black) !important;
        font-weight: 600;
    }
    
    .modal-footer .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
    }
    
    .modal-footer .btn-danger {
        background: var(--danger-red) !important;
        border: none !important;
        color: white !important;
    }
    
    .modal-footer .btn-danger:hover {
        background: #b91c1c !important;
        transform: translateY(-1px);
    }
    
    /* Form control dalam modal */
    .modal-body .form-control,
    .modal-body .form-select {
        background: rgba(0, 0, 0, 0.5) !important;
        border: 1px solid rgba(212, 175, 55, 0.2) !important;
        color: var(--text-light) !important;
        border-radius: 10px;
    }
    
    .modal-body .form-control:focus,
    .modal-body .form-select:focus {
        background: rgba(0, 0, 0, 0.7) !important;
        border-color: var(--accent-gold) !important;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25) !important;
        color: var(--text-light) !important;
    }
    
    .modal-body .form-label {
        color: var(--accent-gold) !important;
        font-weight: 500 !important;
        margin-bottom: 0.5rem;
    }
    
    /* Table dalam modal */
    .modal-body .table {
        color: var(--text-light) !important;
        margin-bottom: 0;
    }
    
    .modal-body .table th {
        color: var(--accent-gold) !important;
        border-bottom-color: rgba(212, 175, 55, 0.2);
    }
    
    .modal-body .table td {
        color: var(--text-light) !important;
        border-bottom-color: rgba(255, 255, 255, 0.05);
    }
    
    /* Animasi modal */
    .modal.fade .modal-dialog {
        transform: scale(0.9);
        transition: transform 0.2s ease-out;
    }
    
    .modal.show .modal-dialog {
        transform: scale(1);
    }
    
    /* Disabled button styling */
    .start-service:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    /* Badge styling */
    .badge.bg-warning {
        background: #f59e0b !important;
        color: #000 !important;
    }
    
    .badge.bg-success {
        background: #10b981 !important;
    }
    
    /* Table hover effect */
    .table tbody tr:hover {
        background: rgba(212, 175, 55, 0.05) !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1" style="color: var(--accent-gold);">
                        <i class="fas fa-people-arrows me-2"></i>
                        Manajemen Antrian
                    </h4>
                    <p class="text-muted mb-0">Kelola antrian pelanggan barbershop</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('queue.display') }}" class="btn btn-outline-primary" target="_blank">
                        <i class="fas fa-tv me-2"></i> Display Antrian
                    </a>
                    <button class="btn btn-outline-success" onclick="exportQueues()">
                        <i class="fas fa-file-excel me-2"></i> Export
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQueueModal">
                        <i class="fas fa-plus me-2"></i> Tambah Antrian
                    </button>
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
                        <i class="fas fa-calendar-alt me-1"></i> HARI INI
                    </span>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div>
                <h6 class="text-muted mb-2">Total Antrian</h6>
                <h2 class="text-white mb-0" id="statToday">{{ $todayQueues }}</h2>
                <small class="text-muted">
                    <i class="fas fa-chart-line me-1"></i>
                    Antrian masuk hari ini
                </small>
            </div>
            <div class="mt-3">
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: {{ min(($todayQueues / 50) * 100, 100) }}%; background: var(--accent-gold);">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b;">
                        <i class="fas fa-clock me-1"></i> MENUNGGU
                    </span>
                </div>
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1);">
                    <i class="fas fa-hourglass-half" style="color: #f59e0b;"></i>
                </div>
            </div>
            <div>
                <h6 class="text-muted mb-2">Dalam Antrian</h6>
                <h2 class="mb-0" id="statWaiting" style="color: #f59e0b;">{{ $waitingQueues }}</h2>
                <small class="text-muted">
                    <i class="fas fa-user-clock me-1"></i>
                    Belum dilayani
                </small>
            </div>
            <div class="mt-3">
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: {{ min(($waitingQueues / 20) * 100, 100) }}%; background: #f59e0b;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #10b981;">
                        <i class="fas fa-check-circle me-1"></i> SELESAI
                    </span>
                </div>
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1);">
                    <i class="fas fa-check-double" style="color: #10b981;"></i>
                </div>
            </div>
            <div>
                <h6 class="text-muted mb-2">Layanan Selesai</h6>
                <h2 class="mb-0" id="statCompleted" style="color: #10b981;">{{ $completedToday }}</h2>
                <small class="text-muted">
                    <i class="fas fa-smile me-1"></i>
                    Pelanggan selesai
                </small>
            </div>
            <div class="mt-3">
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: {{ min(($completedToday / 50) * 100, 100) }}%; background: #10b981;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="badge" style="background: rgba(212, 175, 55, 0.2); color: var(--accent-gold);">
                        <i class="fas fa-chart-line me-1"></i> PERFORMANCE
                    </span>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
            <div>
                <h6 class="text-muted mb-2">Rata-rata Waktu Tunggu</h6>
                <h2 class="text-white mb-0">
                    {{ round($avgWaitingTime) }} 
                    <small class="fs-6 text-muted">menit</small>
                </h2>
                <small class="text-muted">
                    <i class="fas fa-tachometer-alt me-1"></i>
                    Dari antri ke panggil
                </small>
            </div>
            <div class="mt-3">
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: {{ min(($avgWaitingTime / 30) * 100, 100) }}%; background: var(--accent-gold);">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
    <!-- Antrian Aktif -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list-ol me-2"></i>
                Antrian Aktif
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No. Antrian</th>
                            <th>Waktu</th>
                            <th>Nama Pelanggan</th>
                            <th>Layanan</th>
                            <th>Estimasi</th>
                            <th>Status</th>
                            <th>Barber</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="activeQueuesTable">
                        @forelse($queues as $queue)
                        <tr data-queue-id="{{ $queue->id }}" data-status="{{ $queue->status }}">
                            <td>
                                <strong class="text-secondary" style="font-size: 1.1rem;">{{ $queue->queue_number }}</strong>
                            </td>
                            <td>
                                <small class="text-muted">{{ $queue->queue_time->setTimezone('Asia/Jakarta')->format('H:i') }}</small>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $queue->customer_name }}</strong>
                                    @if($queue->customer_phone)
                                        <br><small class="text-muted">{{ $queue->customer_phone }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $queue->service->name }}</td>
                            <td>
                                <small>
                                    <i class="far fa-clock me-1"></i>
                                    {{ $queue->estimated_duration }} menit<br>
                                    <i class="fas fa-tags me-1"></i>
                                    Rp {{ number_format($queue->estimated_price, 0, ',', '.') }}
                                </small>
                            </td>
                            <td>{!! $queue->status_badge !!}</td>
                            <td class="barber-cell">
                                @if($queue->status == 'in_service')
                                    <span class="text-success">
                                        <i class="fas fa-user-check me-1"></i>
                                        {{ $queue->barber->name ?? '-' }}
                                    </span>
                                @elseif($queue->status == 'calling')
                                    <select class="form-select form-select-sm barber-select" data-id="{{ $queue->id }}" data-status="{{ $queue->status }}">
                                        <option value="">-- Pilih Barber --</option>
                                        @foreach($barbers as $barber)
                                            @php
                                                $isBusy = $barber->is_busy ?? false;
                                            @endphp
                                            <option value="{{ $barber->id }}" 
                                                data-name="{{ $barber->name }}"
                                                data-busy="{{ $isBusy ? '1' : '0' }}"
                                                {{ $queue->barber_id == $barber->id ? 'selected' : '' }}
                                                {{ $isBusy ? 'disabled' : '' }}>
                                                {{ $barber->name }}
                                                @if($isBusy)
                                                    (Sedang sibuk)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($queue->status == 'waiting')
                                        <button class="btn btn-primary call-queue" data-id="{{ $queue->id }}" data-name="{{ $queue->customer_name }}" title="Panggil">
                                            <i class="fas fa-bell"></i>
                                        </button>
                                        <button class="btn btn-danger cancel-queue" data-id="{{ $queue->id }}" title="Batalkan">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        
                                    @elseif($queue->status == 'calling')
                                        <button class="btn btn-success start-service" data-id="{{ $queue->id }}" data-name="{{ $queue->customer_name }}" title="Mulai Layanan" disabled>
                                            <i class="fas fa-play"></i> Mulai
                                        </button>
                                        <button class="btn btn-danger cancel-queue" data-id="{{ $queue->id }}" title="Batalkan">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        
                                    @elseif($queue->status == 'in_service')
                                        <button class="btn btn-warning complete-service" data-id="{{ $queue->id }}" title="Selesai Layanan">
                                            <i class="fas fa-check-circle"></i> Selesai
                                        </button>
                                        
                                    @elseif($queue->status == 'completed')
                                        @if($queue->transaction_id)
                                            <a href="{{ route('receipt.print', $queue->transaction_id) }}" class="btn btn-info" target="_blank" title="Cetak Struk">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('pos.from-queue', $queue->id) }}" class="btn btn-primary" title="Proses Pembayaran">
                                                <i class="fas fa-cash-register"></i> Bayar
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Tidak ada antrian aktif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Antrian Selesai Hari Ini -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-history me-2"></i>
            Antrian Selesai (Hari Ini)
        </h5>
        <span class="badge bg-secondary">Total: {{ $completedQueues->count() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">No. Antrian</th>
                        <th>Nama Pelanggan</th>
                        <th>Layanan</th>
                        <th class="text-center">Waktu Antri</th>
                        <th class="text-center">Waktu Selesai</th>
                        <th class="text-center">Status Pembayaran</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedQueues as $queue)
                    <tr>
                        <td class="text-center align-middle">
                            <strong class="text-secondary">{{ $queue->queue_number }}</strong>
                        </td>
                        <td class="align-middle">
                            <strong>{{ $queue->customer_name }}</strong>
                            @if($queue->customer_phone)
                                <div><small class="text-muted">{{ $queue->customer_phone }}</small></div>
                            @endif
                        </td>
                        <td class="align-middle">
                            {{ $queue->service->name }}
                            <div><small class="text-muted">Rp {{ number_format($queue->estimated_price, 0, ',', '.') }}</small></div>
                        </td>
                        <td class="text-center align-middle">
                            {{ $queue->queue_time ? $queue->queue_time->setTimezone('Asia/Jakarta')->format('H:i') : '-' }}
                        </td>
                        <td class="text-center align-middle">
                            {{ $queue->end_time ? $queue->end_time->setTimezone('Asia/Jakarta')->format('H:i') : '-' }}
                        </td>
                        <td class="text-center align-middle">
                            @if($queue->transaction_id)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i> Lunas
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i> Belum Dibayar
                                </span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if($queue->transaction_id)
                                <a href="{{ route('receipt.print', $queue->transaction_id) }}" 
                                   class="btn btn-sm btn-info" 
                                   target="_blank" 
                                   title="Cetak Struk">
                                    <i class="fas fa-print"></i>
                                </a>
                            @else
                                <a href="{{ route('pos.from-queue', $queue->id) }}" 
                                   class="btn btn-sm btn-primary" 
                                   title="Proses Pembayaran">
                                    <i class="fas fa-cash-register"></i> Bayar
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Belum ada antrian selesai
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<!-- Modal Tambah Antrian -->
<div class="modal fade" id="addQueueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>
                    Tambah Antrian Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addQueueForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Pelanggan *</label>
                        <input type="text" name="customer_name" class="form-control" required placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor HP</label>
                        <input type="text" name="customer_phone" class="form-control" placeholder="0812-3456-7890">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Layanan *</label>
                        <select name="service_id" class="form-select" required id="serviceSelect">
                            <option value="">-- Pilih Layanan --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" data-price="{{ $service->price }}" data-duration="{{ $service->duration }}">
                                    {{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }} ({{ $service->duration }} menit)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="waitingEstimate" class="mb-3" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-clock me-2"></i>
                            Estimasi waktu tunggu: <strong id="estimateTime">0</strong> menit
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Catatan khusus untuk barber..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Antrian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Panggil Antrian -->
<div class="modal fade" id="callQueueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-bell me-2"></i>
                    Konfirmasi Panggil Antrian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin memanggil pelanggan ini?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong id="callCustomerName"></strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmCall">
                    <i class="fas fa-bell me-1"></i> Ya, Panggil
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Mulai Layanan -->
<div class="modal fade" id="startServiceModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-play me-2"></i>
                    Konfirmasi Mulai Layanan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Mulai layanan untuk pelanggan ini?</p>
                <div class="alert alert-info">
                    <i class="fas fa-user me-2"></i>
                    <strong id="startCustomerName"></strong><br>
                    <i class="fas fa-cut me-2"></i>
                    <span id="startBarberName"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmStart">
                    <i class="fas fa-play me-1"></i> Ya, Mulai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Selesai Layanan -->
<div class="modal fade" id="completeServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>
                    Konfirmasi Selesai Layanan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah layanan ini sudah selesai?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Setelah diselesaikan, pelanggan akan diarahkan ke halaman pembayaran.
                </div>
                <div id="completeQueueInfo"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmComplete">
                    <i class="fas fa-check me-1"></i> Ya, Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Batalkan Antrian -->
<div class="modal fade" id="cancelQueueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle me-2" style="color: var(--danger-red);"></i>
                    Konfirmasi Batalkan Antrian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin membatalkan antrian ini?</p>
                <div class="mb-3">
                    <label class="form-label">Alasan Pembatalan</label>
                    <textarea id="cancelReason" class="form-control" rows="2" placeholder="Masukkan alasan pembatalan..."></textarea>
                </div>
                <div id="cancelQueueInfo"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="confirmCancel">
                    <i class="fas fa-times me-1"></i> Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentQueueId = null;
let currentQueueName = null;
let currentBarberId = null;
let currentBarberName = null;
let notificationSound = null;
let busyBarbers = {};
let barberStatusInterval = null;

// Initialize sound
function initSound() {
    try {
        notificationSound = new Audio('/sounds/notification.mp3');
        notificationSound.load();
    } catch(e) {
        console.log('Sound not supported');
    }
}

function playNotificationSound() {
    if (notificationSound) {
        notificationSound.currentTime = 0;
        notificationSound.play().catch(e => console.log('Audio play failed'));
    }
}

function showToast(type, message) {
    const toastHtml = `
        <div class="alert alert-${type} alert-dismissible fade show mb-2" role="alert" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideInRight 0.3s ease;">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('body').append(toastHtml);
    setTimeout(() => $('.alert').first().fadeOut('slow', function() { $(this).remove(); }), 3000);
}

function updateWaitingEstimate() {
    let waitingCount = parseInt($('#statWaiting').text()) || 0;
    let selectedOption = $('#serviceSelect').find(':selected');
    let duration = parseInt(selectedOption.data('duration')) || 30;
    let estimatedWait = waitingCount * duration;
    
    if (waitingCount > 0) {
        $('#waitingEstimate').show();
        $('#estimateTime').text(estimatedWait);
    } else {
        $('#waitingEstimate').hide();
    }
}

function exportQueues() {
    window.location.href = '{{ route("queue.export") }}';
}

function updateBarberAvailability() {
    $.ajax({
        url: '{{ route("queue.barber-status") }}',
        method: 'GET',
        success: function(barbers) {
            busyBarbers = {};
            barbers.forEach(barber => {
                if (!barber.is_available) {
                    busyBarbers[barber.id] = barber.current_queue;
                }
            });
            
            $('.barber-select').each(function() {
                let $select = $(this);
                let currentVal = $select.val();
                let $row = $select.closest('tr');
                let $startBtn = $row.find('.start-service');
                let hasValidSelection = false;
                
                $select.find('option').each(function() {
                    let $option = $(this);
                    let barberId = $option.val();
                    
                    if (barberId) {
                        if (busyBarbers[barberId]) {
                            $option.prop('disabled', true);
                            $option.text($option.text().replace(/\s*\(Sedang sibuk\)/g, ''));
                            $option.text($option.text() + ' (Sedang sibuk)');
                            
                            if (currentVal == barberId) {
                                $select.val('');
                                if ($startBtn.length) {
                                    $startBtn.prop('disabled', true);
                                }
                            }
                        } else {
                            $option.prop('disabled', false);
                            $option.text($option.text().replace(/\s*\(Sedang sibuk\)/g, ''));
                            if (currentVal == barberId) {
                                hasValidSelection = true;
                            }
                        }
                    }
                });
                
                if ($startBtn.length) {
                    $startBtn.prop('disabled', !hasValidSelection);
                }
            });
        }
    });
}

function checkBarberAvailability(barberId, callback) {
    $.ajax({
        url: '{{ url("queue") }}/check-barber/' + barberId,
        method: 'GET',
        success: function(response) {
            callback(response.available, response.name);
        },
        error: function() {
            callback(false, null);
        }
    });
}

function updateStartButtonState($select) {
    let $row = $select.closest('tr');
    let $startBtn = $row.find('.start-service');
    let selectedBarberId = $select.val();
    let selectedOption = $select.find('option:selected');
    let isDisabled = selectedOption.prop('disabled');
    
    if ($startBtn.length) {
        $startBtn.prop('disabled', !selectedBarberId || isDisabled);
    }
}

$(document).ready(function() {
    initSound();
    updateBarberAvailability();
    
    if (barberStatusInterval) {
        clearInterval(barberStatusInterval);
    }
    barberStatusInterval = setInterval(updateBarberAvailability, 10000);
    
    $('#serviceSelect').change(updateWaitingEstimate);
    
    $('#addQueueForm').on('submit', function(e) {
        e.preventDefault();
        let button = $(this).find('button[type="submit"]');
        let originalText = button.html();
        button.html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');
        button.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("queue.store") }}',
            method: 'POST',
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    $('#addQueueModal').modal('hide');
                    $('#addQueueForm')[0].reset();
                    $('#waitingEstimate').hide();
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || 'Gagal menambah antrian';
                showToast('error', message);
            },
            complete: function() {
                button.html(originalText);
                button.prop('disabled', false);
            }
        });
    });
    
    $('.call-queue').click(function() {
        currentQueueId = $(this).data('id');
        currentQueueName = $(this).data('name');
        $('#callCustomerName').text(currentQueueName);
        $('#callQueueModal').modal('show');
    });
    
    $('#confirmCall').click(function() {
        let button = $('#confirmCall');
        button.html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');
        button.prop('disabled', true);
        
        $.ajax({
            url: '{{ url("queue") }}/' + currentQueueId + '/call',
            method: 'PUT',
            data: {_token: '{{ csrf_token() }}'},
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    playNotificationSound();
                    $('#callQueueModal').modal('hide');
                    location.reload();
                }
            },
            error: function() {
                showToast('error', 'Gagal memanggil antrian');
                button.html('<i class="fas fa-bell me-1"></i> Ya, Panggil');
                button.prop('disabled', false);
            }
        });
    });
    
    $('.start-service').click(function() {
        currentQueueId = $(this).data('id');
        currentQueueName = $(this).data('name');
        let row = $(this).closest('tr');
        let barberSelect = row.find('.barber-select');
        currentBarberId = barberSelect.val();
        currentBarberName = barberSelect.find('option:selected').text();
        
        if (!currentBarberId) {
            showToast('error', 'Silakan pilih barber terlebih dahulu!');
            barberSelect.addClass('is-invalid');
            setTimeout(() => barberSelect.removeClass('is-invalid'), 3000);
            return;
        }
        
        checkBarberAvailability(currentBarberId, function(available, name) {
            if (!available) {
                showToast('error', `Barber ${currentBarberName} sedang sibuk melayani pelanggan lain!`);
                updateBarberAvailability();
                barberSelect.val('');
                return;
            }
            
            $('#startCustomerName').text(currentQueueName);
            $('#startBarberName').text(currentBarberName);
            $('#startServiceModal').modal('show');
        });
    });
    
    $('#confirmStart').click(function() {
        let button = $('#confirmStart');
        button.html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');
        button.prop('disabled', true);
        
        $.ajax({
            url: '{{ url("queue") }}/' + currentQueueId + '/start',
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                barber_id: currentBarberId
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    $('#startServiceModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || 'Gagal memulai layanan';
                showToast('error', message);
                button.html('<i class="fas fa-play me-1"></i> Ya, Mulai');
                button.prop('disabled', false);
            }
        });
    });
    
    $('.complete-service').click(function() {
        currentQueueId = $(this).data('id');
        let queueNumber = $(this).closest('tr').find('td:first').text();
        
        $('#completeQueueInfo').html(`
            <table class="table table-sm">
                <tr><th>No. Antrian</th><td><strong class="text-secondary">${queueNumber}</strong></td></tr>
            </table>
        `);
        $('#completeServiceModal').modal('show');
    });
    
    $('#confirmComplete').click(function() {
        let button = $('#confirmComplete');
        button.html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');
        button.prop('disabled', true);
        
        $.ajax({
            url: '{{ url("queue") }}/' + currentQueueId + '/complete',
            method: 'PUT',
            data: {_token: '{{ csrf_token() }}'},
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    $('#completeServiceModal').modal('hide');
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        location.reload();
                    }
                }
            },
            error: function() {
                showToast('error', 'Gagal menyelesaikan layanan');
                button.html('Ya, Selesai');
                button.prop('disabled', false);
            }
        });
    });
    
    $('.cancel-queue').click(function() {
        currentQueueId = $(this).data('id');
        let queueNumber = $(this).closest('tr').find('td:first').text();
        
        $('#cancelQueueInfo').html(`
            <table class="table table-sm">
                <tr><th>No. Antrian</th><td><strong class="text-secondary">${queueNumber}</strong></td></tr>
            </table>
        `);
        $('#cancelReason').val('');
        $('#cancelQueueModal').modal('show');
    });
    
    $('#confirmCancel').click(function() {
        let reason = $('#cancelReason').val();
        let button = $('#confirmCancel');
        button.html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');
        button.prop('disabled', true);
        
        $.ajax({
            url: '{{ url("queue") }}/' + currentQueueId + '/cancel',
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                reason: reason || 'Dibatalkan oleh operator'
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    $('#cancelQueueModal').modal('hide');
                    location.reload();
                }
            },
            error: function() {
                showToast('error', 'Gagal membatalkan antrian');
                button.html('Ya, Batalkan');
                button.prop('disabled', false);
            }
        });
    });
    
    $('.barber-select').change(function() {
        let id = $(this).data('id');
        let barberId = $(this).val();
        let barberName = $(this).find('option:selected').text();
        let $select = $(this);
        
        if (!barberId) {
            updateStartButtonState($select);
            return;
        }
        
        checkBarberAvailability(barberId, function(available, name) {
            if (!available) {
                showToast('error', `Barber ${barberName} sedang sibuk melayani pelanggan lain!`);
                $select.val('');
                updateBarberAvailability();
                updateStartButtonState($select);
                return;
            }
            
            $.ajax({
                url: '{{ url("queue") }}/' + id + '/assign-barber',
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    barber_id: barberId
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', `Barber ${barberName} ditugaskan untuk antrian ini`);
                        updateStartButtonState($select);
                    }
                },
                error: function(xhr) {
                    let message = xhr.responseJSON?.message || 'Gagal menugaskan barber';
                    showToast('error', message);
                    $select.val('');
                    updateStartButtonState($select);
                }
            });
        }.bind(this));
    });
    
    $('.modal').on('hidden.bs.modal', function() {
        $('#confirmCall, #confirmStart, #confirmComplete, #confirmCancel').each(function() {
            let originalText = $(this).data('original-text');
            if (originalText) {
                $(this).html(originalText);
            }
            $(this).prop('disabled', false);
        });
    });
    
    let autoRefresh = setInterval(function() {
        if (!document.hidden) {
            $.ajax({
                url: window.location.href,
                method: 'GET',
                success: function(response) {
                    let newWaiting = $(response).find('#statWaiting').text();
                    let currentWaiting = $('#statWaiting').text();
                    if (newWaiting !== currentWaiting) {
                        location.reload();
                    }
                }
            });
        }
    }, 30000);
    
    $(window).on('beforeunload', function() {
        if (barberStatusInterval) {
            clearInterval(barberStatusInterval);
        }
        if (autoRefresh) {
            clearInterval(autoRefresh);
        }
    });
});
// Auto refresh every 5 seconds to update payment status
let autoRefreshInterval = setInterval(function() {
    if (!document.hidden) {
        $.ajax({
            url: window.location.href,
            method: 'GET',
            success: function(response) {
                let newContent = $(response);
                // Check if transaction_id status changed
                let currentStatus = $('.badge.bg-success, .badge.bg-warning').first().text();
                let newStatus = newContent.find('.badge.bg-success, .badge.bg-warning').first().text();
                if (currentStatus !== newStatus) {
                    location.reload();
                }
            }
        });
    }
}, 5000);
$(document).keydown(function(e) {
    if (e.key === 'F2') {
        e.preventDefault();
        $('#addQueueModal').modal('show');
    }
    if (e.key === 'F5') {
        e.preventDefault();
        location.reload();
    }
    if (e.key === 'F12') {
        e.preventDefault();
        updateBarberAvailability();
        showToast('info', 'Data barber diperbarui');
    }
});
</script>
@endpush
@endsection