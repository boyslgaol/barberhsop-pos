@extends('layouts.app')

@section('title', 'Detail Layanan - ' . $service->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1" style="color: var(--accent-gold);">
                        <i class="fas fa-cut me-2"></i>
                        Detail Layanan
                    </h4>
                    <p class="text-muted mb-0">
                        <i class="fas fa-code me-1"></i>
                        Kode: {{ $service->code }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                    <a href="{{ route('services.edit', $service) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i> Edit
                    </a>
                    @if($service->canDelete())
                    <button class="btn btn-danger delete-service" data-id="{{ $service->id }}" data-name="{{ $service->name }}">
                        <i class="fas fa-trash me-2"></i> Hapus
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Informasi Layanan -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2" style="color: var(--accent-gold);"></i>
                        Informasi Layanan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="35%" class="text-muted">Kode Layanan</td>
                                    <td><strong>{{ $service->code }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nama Layanan</td>
                                    <td><strong>{{ $service->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kategori</td>
                                    <td>
                                        <span class="badge" style="background: rgba(212, 175, 55, 0.2); color: var(--accent-gold);">
                                            {{ $service->category->name }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status</td>
                                    <td>
                                        @if($service->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="35%" class="text-muted">Harga</td>
                                    <td>
                                        <strong class="price-tag" style="color: var(--accent-gold); font-size: 1.2rem;">
                                            {{ $service->formatted_price }}
                                        </strong>
                                    </td>
                                </tr>
                                @if($service->cost > 0)
                                <tr>
                                    <td class="text-muted">Modal</td>
                                    <td>{{ $service->formatted_cost }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">Durasi</td>
                                    <td>
                                        <i class="far fa-clock me-1"></i>
                                        {{ $service->duration }} menit
                                        ({{ floor($service->duration / 60) }} jam {{ $service->duration % 60 }} menit)
                                    </td>
                                </tr>
                                @if($service->cost > 0)
                                <tr>
                                    <td class="text-muted">Keuntungan</td>
                                    <td class="text-success">
                                        {{ $service->formatted_profit ?? 'Rp 0' }}
                                        ({{ $service->profit_margin }}%)
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    @if($service->description)
                    <div class="mt-3">
                        <h6 class="text-muted mb-2">Deskripsi</h6>
                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.05);">
                            {{ $service->description }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Statistik Penggunaan -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2" style="color: var(--accent-gold);"></i>
                        Statistik Penggunaan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="text-center p-3 rounded" style="background: rgba(212, 175, 55, 0.05);">
                                <h6 class="text-muted">Total Transaksi</h6>
                                <h3 class="mb-0" style="color: var(--accent-gold);">
                                    {{ $service->transactionDetails->count() }}
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center p-3 rounded" style="background: rgba(212, 175, 55, 0.05);">
                                <h6 class="text-muted">Total Pendapatan</h6>
                                <h3 class="mb-0" style="color: var(--accent-gold);">
                                    Rp {{ number_format($service->transactionDetails->sum('price'), 0, ',', '.') }}
                                </h3>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center p-3 rounded" style="background: rgba(212, 175, 55, 0.05);">
                                <h6 class="text-muted">Rata-rata per Transaksi</h6>
                                <h3 class="mb-0" style="color: var(--accent-gold);">
                                    Rp {{ number_format($service->transactionDetails->avg('price') ?? 0, 0, ',', '.') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2" style="color: var(--accent-gold);"></i>
                        Aksi Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('services.edit', $service) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i> Edit Layanan
                        </a>
                        <button class="btn btn-outline-primary toggle-status" data-id="{{ $service->id }}" data-status="{{ $service->is_active }}">
                            <i class="fas fa-{{ $service->is_active ? 'ban' : 'check-circle' }} me-2"></i>
                            {{ $service->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Layanan
                        </button>
                        @if($service->canDelete())
                        <button class="btn btn-danger delete-service" data-id="{{ $service->id }}" data-name="{{ $service->name }}">
                            <i class="fas fa-trash me-2"></i> Hapus Layanan
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Informasi Tambahan -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2" style="color: var(--accent-gold);"></i>
                        Informasi Lainnya
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td class="text-muted">Dibuat pada</td>
                            <td>{{ $service->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Terakhir diupdate</td>
                            <td>{{ $service->updated_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
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

@push('styles')
<style>
    .table-borderless td {
        padding: 0.75rem 0;
        border: none;
    }
    
    .price-tag {
        font-size: 1.2rem;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
let deleteServiceId = null;

function showToast(type, message) {
    const toastHtml = `
        <div class="alert alert-${type} alert-dismissible fade show mb-2" role="alert" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideInRight 0.3s ease;">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('body').append(toastHtml);
    setTimeout(() => $('.alert').fadeOut('slow', function() { $(this).remove(); }), 3000);
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
                    setTimeout(() => {
                        window.location.href = '{{ route("services.index") }}';
                    }, 1500);
                } else {
                    showToast('error', response.message);
                }
                $('#deleteModal').modal('hide');
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
    
    // Toggle status
    $('.toggle-status').click(function() {
        let serviceId = $(this).data('id');
        let currentStatus = $(this).data('status');
        let newStatus = currentStatus ? 'nonaktifkan' : 'aktifkan';
        
        if (confirm(`Yakin ingin ${newStatus} layanan ini?`)) {
            let button = $(this);
            button.html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');
            button.prop('disabled', true);
            
            $.ajax({
                url: '/services/' + serviceId + '/toggle-status',
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
                },
                error: function() {
                    showToast('error', 'Gagal mengubah status layanan');
                },
                complete: function() {
                    button.html('<i class="fas fa-spinner fa-spin me-1"></i>');
                }
            });
        }
    });
});
</script>
@endpush
@endsection