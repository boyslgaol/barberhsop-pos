@extends('layouts.app')

@section('title', 'Manajemen Pelanggan')

@push('styles')
<style>
    .stat-card {
        background: linear-gradient(135deg, var(--secondary-dark) 0%, rgba(212, 175, 55, 0.03) 100%);
        border: 1px solid rgba(212, 175, 55, 0.1);
        border-radius: 20px;
        padding: 1.25rem;
        transition: all 0.3s;
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
    
    .member-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-block;
    }
    
    .member-regular { background: #6c757d; color: white; }
    .member-silver { background: #c0c0c0; color: #333; }
    .member-gold { background: #ffd700; color: #333; }
    .member-platinum { background: #e5e4e2; color: #333; }
    
    .avatar-circle {
        width: 40px;
        height: 40px;
        background: rgba(212, 175, 55, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1rem;
        color: var(--accent-gold);
        margin: 0 auto;
    }
    
    /* Table styling */
    .table-custom {
        margin-bottom: 0;
    }
    
    .table-custom th {
        background: #1a1a1a;
        border-bottom: 2px solid var(--accent-gold);
        padding: 12px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .table-custom td {
        padding: 12px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .table-custom tr:hover {
        background: rgba(212, 175, 55, 0.05);
    }
    
    /* Badge styling */
    .badge-points {
        background: rgba(212, 175, 55, 0.2);
        color: var(--accent-gold);
        padding: 4px 8px;
        border-radius: 20px;
        font-weight: 600;
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
                        <i class="fas fa-users me-2"></i>
                        Manajemen Pelanggan
                    </h4>
                    <p class="text-muted mb-0">Kelola semua data pelanggan barbershop</p>
                </div>
                <div>
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Tambah Pelanggan
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <small class="text-muted">TOTAL PELANGGAN</small>
                    <i class="fas fa-users fa-2x" style="color: var(--accent-gold); opacity: 0.5;"></i>
                </div>
                <h2 class="mb-0">{{ number_format($totalCustomers) }}</h2>
                <small class="text-muted">Terdaftar</small>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <small class="text-muted">TOTAL BELANJA</small>
                    <i class="fas fa-chart-line fa-2x" style="color: var(--accent-gold); opacity: 0.5;"></i>
                </div>
                <h2 class="mb-0">Rp {{ number_format($totalSpent, 0, ',', '.') }}</h2>
                <small class="text-muted">Akumulasi</small>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <small class="text-muted">TOTAL POIN</small>
                    <i class="fas fa-gem fa-2x" style="color: var(--accent-gold); opacity: 0.5;"></i>
                </div>
                <h2 class="mb-0">{{ number_format($totalPoints) }}</h2>
                <small class="text-muted">Tersedia</small>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <small class="text-muted">RATA-RATA POIN</small>
                    <i class="fas fa-chart-bar fa-2x" style="color: var(--accent-gold); opacity: 0.5;"></i>
                </div>
                <h2 class="mb-0">{{ $totalCustomers > 0 ? number_format($totalPoints / $totalCustomers) : 0 }}</h2>
                <small class="text-muted">Per pelanggan</small>
            </div>
        </div>
    </div>
    
    <!-- Member Level Distribution -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2" style="color: var(--accent-gold);"></i>
                        Distribusi Member
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <span class="member-badge member-regular">Regular</span>
                            <h4 class="mt-2 mb-0">{{ $memberCounts['regular'] }}</h4>
                            <small class="text-muted">pelanggan</small>
                        </div>
                        <div class="col-3">
                            <span class="member-badge member-silver">Silver</span>
                            <h4 class="mt-2 mb-0">{{ $memberCounts['silver'] }}</h4>
                            <small class="text-muted">pelanggan</small>
                        </div>
                        <div class="col-3">
                            <span class="member-badge member-gold">Gold</span>
                            <h4 class="mt-2 mb-0">{{ $memberCounts['gold'] }}</h4>
                            <small class="text-muted">pelanggan</small>
                        </div>
                        <div class="col-3">
                            <span class="member-badge member-platinum">Platinum</span>
                            <h4 class="mt-2 mb-0">{{ $memberCounts['platinum'] }}</h4>
                            <small class="text-muted">pelanggan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('customers.index') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small text-muted">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="Nama, No HP, Email, atau Kode Member" value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Level Member</label>
                    <select name="member_level" class="form-select">
                        <option value="">Semua Level</option>
                        <option value="regular" {{ request('member_level') == 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="silver" {{ request('member_level') == 'silver' ? 'selected' : '' }}>Silver</option>
                        <option value="gold" {{ request('member_level') == 'gold' ? 'selected' : '' }}>Gold</option>
                        <option value="platinum" {{ request('member_level') == 'platinum' ? 'selected' : '' }}>Platinum</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Customers Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Daftar Pelanggan
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 10%">Member</th>
                            <th style="width: 25%">Nama</th>
                            <th style="width: 20%">Kontak</th>
                            <th class="text-center" style="width: 12%">Level</th>
                            <th class="text-center" style="width: 10%">Poin</th>
                            <th class="text-end" style="width: 15%">Total Belanja</th>
                            <th class="text-center" style="width: 8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td class="text-center">
                                <div class="avatar-circle">
                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                </div>
                                <small class="text-muted d-block mt-1">{{ $customer->member_code }}</small>
                            </td>
                            <td>
                                <strong>{{ $customer->name }}</strong>
                                @if($customer->birthdate)
                                    <br><small class="text-muted">
                                        <i class="fas fa-birthday-cake me-1"></i>
                                        {{ \Carbon\Carbon::parse($customer->birthdate)->age }} tahun
                                    </small>
                                @endif
                            </td>
                            <td>
                                <i class="fas fa-phone me-1"></i> {{ $customer->phone }}<br>
                                @if($customer->email)
                                    <small class="text-muted">{{ $customer->email }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="member-badge member-{{ $customer->member_level }}">
                                    {{ ucfirst($customer->member_level) }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    Diskon {{ $customer->member_discount }}%
                                </small>
                            </td>
                            <td class="text-center">
                                <span class="badge-points">
                                    {{ number_format($customer->points) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <strong>Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</strong>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ $customer->visit_count }}x kunjungan
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($customer->transactions()->count() == 0)
                                    <button class="btn btn-danger delete-customer" data-id="{{ $customer->id }}" data-name="{{ $customer->name }}" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada pelanggan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $customers->links() }}
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
                <p>Yakin ingin menghapus pelanggan <strong id="deleteCustomerName"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    Pelanggan yang sudah memiliki transaksi tidak dapat dihapus.
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

@push('scripts')
<script>
let deleteCustomerId = null;

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
    $('.delete-customer').click(function() {
        deleteCustomerId = $(this).data('id');
        let customerName = $(this).data('name');
        $('#deleteCustomerName').text(customerName);
        $('#deleteModal').modal('show');
    });
    
    $('#confirmDelete').click(function() {
        if (!deleteCustomerId) return;
        
        let button = $(this);
        button.html('<i class="fas fa-spinner fa-spin me-1"></i> Menghapus...');
        button.prop('disabled', true);
        
        $.ajax({
            url: '/customers/' + deleteCustomerId,
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
                let message = xhr.responseJSON?.message || 'Gagal menghapus pelanggan';
                showToast('error', message);
                $('#deleteModal').modal('hide');
            },
            complete: function() {
                button.html('Hapus');
                button.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
@endsection