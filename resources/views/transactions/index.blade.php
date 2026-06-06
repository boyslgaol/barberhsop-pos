@extends('layouts.app')

@section('title', 'Manajemen Transaksi')

@push('styles')
<style>
    .filter-card {
        background: var(--secondary-dark);
        border: 1px solid rgba(212, 175, 55, 0.1);
        border-radius: 16px;
        margin-bottom: 1.5rem;
    }
    
    .transaction-card {
        transition: all 0.3s;
    }
    
    .transaction-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    
    .invoice-number {
        font-family: monospace;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--accent-gold);
    }
    
    .summary-card {
        background: linear-gradient(135deg, var(--secondary-dark) 0%, rgba(212, 175, 55, 0.05) 100%);
        border: 1px solid rgba(212, 175, 55, 0.1);
        border-radius: 16px;
        padding: 1rem;
        text-align: center;
    }
    
    .summary-card h3 {
        color: var(--accent-gold);
        margin-bottom: 0;
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
                        <i class="fas fa-receipt me-2"></i>
                        Manajemen Transaksi
                    </h4>
                    <p class="text-muted mb-0">Kelola dan lihat semua transaksi</p>
                </div>
                <div>
                    <button class="btn btn-outline-success" onclick="window.location.href='{{ route('reports.export-excel') }}'">
                        <i class="fas fa-file-excel me-2"></i> Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter Section -->
    <div class="filter-card p-3">
        <form method="GET" action="{{ route('transactions.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small text-muted">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i> Filter
                </button>
            </div>
        </form>
    </div>
    
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <small class="text-muted">Total Transaksi</small>
                <h3>{{ $transactions->total() }}</h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <small class="text-muted">Total Pendapatan</small>
                <h3>Rp {{ number_format($transactions->sum('total'), 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <small class="text-muted">Rata-rata Transaksi</small>
                <h3>Rp {{ number_format($transactions->avg('total') ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="summary-card">
                <small class="text-muted">Hari Ini</small>
                <h3>{{ $transactions->where('transaction_date', '>=', now('Asia/Jakarta')->startOfDay())->count() }}</h3>
            </div>
        </div>
    </div>
    
    <!-- Transactions Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Daftar Transaksi
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Invoice</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Kasir</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>
                                <span class="invoice-number">{{ $transaction->invoice_number }}</span>
                            </td>
                            <td>
                                <small>{{ $transaction->transaction_date->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                {{ $transaction->customer->name ?? 'Guest' }}
                                @if($transaction->customer && $transaction->customer->phone)
                                    <br><small class="text-muted">{{ $transaction->customer->phone }}</small>
                                @endif
                            </td>
                            <td>{{ $transaction->user->name ?? '-' }}</td>
                            <td>
                                <strong class="text-success">Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                @if($transaction->payment_method == 'cash')
                                    <span class="badge bg-info">Tunai</span>
                                @elseif($transaction->payment_method == 'qris')
                                    <span class="badge bg-primary">QRIS</span>
                                @else
                                    <span class="badge bg-secondary">Debit</span>
                                @endif
                            </td>
                            <td>
                                @if($transaction->status == 'completed')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($transaction->status == 'cancelled')
                                    <span class="badge bg-danger">Dibatalkan</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('receipt.print', $transaction) }}" class="btn btn-primary" target="_blank" title="Cetak Struk">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @if($transaction->status == 'completed')
                                        <button class="btn btn-danger cancel-transaction" data-id="{{ $transaction->id }}" data-invoice="{{ $transaction->invoice_number }}" title="Batalkan">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada transaksi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-receipt me-2" style="color: var(--accent-gold);"></i>
                    Detail Transaksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="transactionDetailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="printFromModal">
                    <i class="fas fa-print me-1"></i> Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showTransactionDetail(id) {
    $('#transactionModal').modal('show');
    $('#transactionDetailContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-warning" role="status"></div>
            <p class="mt-2">Memuat data...</p>
        </div>
    `);
    
    $.ajax({
        url: '/transactions/' + id,
        method: 'GET',
        success: function(response) {
            $('#transactionDetailContent').html(response);
        },
        error: function() {
            $('#transactionDetailContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Gagal memuat data transaksi
                </div>
            `);
        }
    });
}

function cancelTransaction(id, invoice) {
    if (confirm(`Yakin ingin membatalkan transaksi ${invoice}?`)) {
        $.ajax({
            url: '/transactions/' + id + '/cancel',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'POST'
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
                showToast('error', 'Gagal membatalkan transaksi');
            }
        });
    }
}

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
    $('.cancel-transaction').click(function() {
        let id = $(this).data('id');
        let invoice = $(this).data('invoice');
        cancelTransaction(id, invoice);
    });
    
    $('#printFromModal').click(function() {
        let printUrl = $('#transactionDetailContent').data('print-url');
        if (printUrl) {
            window.open(printUrl, '_blank');
        }
    });
});
</script>
@endpush
@endsection