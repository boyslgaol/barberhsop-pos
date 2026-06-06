@extends('layouts.app')

@section('title', 'Manajemen Layanan')

@push('styles')
<style>
    .service-card {
        transition: all 0.3s;
        border: 1px solid rgba(212, 175, 55, 0.1);
        border-radius: 16px;
    }
    
    .service-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    
    .price-tag {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--accent-gold);
    }
    
    .duration-badge {
        background: rgba(212, 175, 55, 0.1);
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 0.75rem;
    }
    
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
    
    /* Animation for stats cards */
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
    
    /* Toggle switch styling */
    .toggle-status {
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .toggle-status:hover {
        transform: scale(1.05);
    }
    
    /* Badge pointer */
    .badge-status {
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .badge-status:hover {
        opacity: 0.8;
        transform: scale(1.02);
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
                        <i class="fas fa-cut me-2"></i>
                        Manajemen Layanan
                    </h4>
                    <p class="text-muted mb-0">Kelola semua layanan barbershop</p>
                </div>
                <div>
                    <a href="{{ route('services.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Tambah Layanan
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge" style="background: rgba(212, 175, 55, 0.2); color: var(--accent-gold);">
                            <i class="fas fa-chart-line me-1"></i> TOTAL
                        </span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-cut"></i>
                    </div>
                </div>
                <div>
                    <h6 class="text-muted mb-2">Total Layanan</h6>
                    <h2 class="text-white mb-0" id="totalServices">{{ $services->total() }}</h2>
                    <small class="text-muted">
                        <i class="fas fa-store me-1"></i>
                        Semua layanan tersedia
                    </small>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar" role="progressbar" style="width: 100%; background: var(--accent-gold);"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #10b981;">
                            <i class="fas fa-check-circle me-1"></i> AKTIF
                        </span>
                    </div>
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1);">
                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                    </div>
                </div>
                <div>
                    <h6 class="text-muted mb-2">Layanan Aktif</h6>
                    <h2 class="mb-0" id="activeServices" style="color: #10b981;">{{ $services->where('is_active', true)->count() }}</h2>
                    <small class="text-muted">
                        <i class="fas fa-percent me-1"></i>
                        <span id="activePercent">{{ $services->total() > 0 ? round(($services->where('is_active', true)->count() / $services->total()) * 100) : 0 }}</span>% dari total
                    </small>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $services->total() > 0 ? ($services->where('is_active', true)->count() / $services->total()) * 100 : 0 }}%; background: #10b981;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: #ef4444;">
                            <i class="fas fa-ban me-1"></i> NONAKTIF
                        </span>
                    </div>
                    <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1);">
                        <i class="fas fa-ban" style="color: #ef4444;"></i>
                    </div>
                </div>
                <div>
                    <h6 class="text-muted mb-2">Layanan Nonaktif</h6>
                    <h2 class="mb-0" id="inactiveServices" style="color: #ef4444;">{{ $services->where('is_active', false)->count() }}</h2>
                    <small class="text-muted">
                        <i class="fas fa-eye-slash me-1"></i>
                        Tidak tersedia di POS
                    </small>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $services->total() > 0 ? ($services->where('is_active', false)->count() / $services->total()) * 100 : 0 }}%; background: #ef4444;">
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
                            <i class="fas fa-tags me-1"></i> KATEGORI
                        </span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                </div>
                <div>
                    <h6 class="text-muted mb-2">Total Kategori</h6>
                    <h2 class="text-white mb-0">{{ $categories->count() }}</h2>
                    <small class="text-muted">
                        <i class="fas fa-folder-open me-1"></i>
                        Kelompok layanan
                    </small>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar" role="progressbar" style="width: 100%; background: var(--accent-gold);"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Services Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Daftar Layanan
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Layanan</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                        <tr data-service-id="{{ $service->id }}">
                            <td>
                                <code>{{ $service->code }}</code>
                            </td>
                            <td>
                                <strong>{{ $service->name }}</strong>
                                @if($service->description)
                                    <br><small class="text-muted">{{ Str::limit($service->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background: rgba(212, 175, 55, 0.2); color: var(--accent-gold);">
                                    {{ $service->category->name }}
                                </span>
                            </td>
                            <td>
                                <span class="price-tag">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="duration-badge">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $service->duration }} menit
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-status {{ $service->is_active ? 'bg-success' : 'bg-danger' }} toggle-status-btn" 
                                      data-id="{{ $service->id }}" 
                                      data-name="{{ $service->name }}"
                                      data-active="{{ $service->is_active ? '1' : '0' }}"
                                      style="cursor: pointer;">
                                    <i class="fas fa-{{ $service->is_active ? 'check-circle' : 'times-circle' }} me-1"></i>
                                    {{ $service->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('services.show', $service) }}" class="btn btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('services.edit', $service) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($service->canDelete())
                                    <button class="btn btn-danger delete-service" data-id="{{ $service->id }}" data-name="{{ $service->name }}" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @else
                                    <button class="btn btn-secondary" disabled title="Tidak dapat dihapus karena sudah digunakan">
                                        <i class="fas fa-lock"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada layanan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $services->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2" style="color: var(--danger-red);"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menghapus layanan <strong id="deleteServiceName"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    Layanan yang sudah digunakan dalam transaksi tidak dapat dihapus.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toggle Status Confirmation Modal -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt me-2" style="color: var(--accent-gold);"></i>
                    Konfirmasi Ubah Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin <strong id="toggleActionText"></strong> layanan <strong id="toggleServiceName"></strong>?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Layanan yang tidak aktif tidak akan muncul di halaman POS.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmToggle">
                    <i class="fas fa-check me-1"></i> Ya, Ubah
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let deleteServiceId = null;
let toggleServiceId = null;
let toggleServiceName = null;
let toggleNewStatus = null;

function showToast(type, message) {
    const toastHtml = `
        <div class="alert alert-${type} alert-dismissible fade show mb-2" role="alert" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideInRight 0.3s ease;">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('body').append(toastHtml);
    setTimeout(() => $('.alert').fadeOut('slow', function() { $(this).remove(); }), 3000);
}

function updateStatistics() {
    // Reload page to update statistics
    location.reload();
}

$(document).ready(function() {
    // Delete service
    $('.delete-service').click(function() {
        deleteServiceId = $(this).data('id');
        let serviceName = $(this).data('name');
        $('#deleteServiceName').text(serviceName);
        $('#deleteModal').modal('show');
    });
    
    $('#confirmDelete').click(function() {
        if (!deleteServiceId) return;
        
        let button = $(this);
        button.html('<i class="fas fa-spinner fa-spin me-1"></i> Menghapus...');
        button.prop('disabled', true);
        
        $.ajax({
            url: '/services/' + deleteServiceId,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('error', response.message);
                    $('#deleteModal').modal('hide');
                }
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || 'Gagal menghapus layanan';
                showToast('error', message);
                $('#deleteModal').modal('hide');
            },
            complete: function() {
                button.html('Hapus');
                button.prop('disabled', false);
            }
        });
    });
    
    // Toggle status - menggunakan modal
    $('.toggle-status-btn').click(function() {
        toggleServiceId = $(this).data('id');
        toggleServiceName = $(this).data('name');
        let isActive = $(this).data('active') == '1';
        toggleNewStatus = !isActive;
        
        let actionText = toggleNewStatus ? 'mengaktifkan' : 'menonaktifkan';
        $('#toggleActionText').text(actionText);
        $('#toggleServiceName').text(toggleServiceName);
        $('#toggleStatusModal').modal('show');
    });
    
    $('#confirmToggle').click(function() {
        if (!toggleServiceId) return;
        
        let button = $(this);
        button.html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');
        button.prop('disabled', true);
        
        $.ajax({
            url: '/services/' + toggleServiceId + '/toggle-status',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('error', response.message);
                }
                $('#toggleStatusModal').modal('hide');
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || 'Gagal mengubah status layanan';
                showToast('error', message);
                $('#toggleStatusModal').modal('hide');
            },
            complete: function() {
                button.html('Ya, Ubah');
                button.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
@endsection