@extends('layouts.app')

@section('title', 'Detail Transaksi - ' . ($transaction->invoice_number ?? ''))

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1" style="color: var(--accent-gold);">
                        <i class="fas fa-receipt me-2"></i>
                        Detail Transaksi
                    </h4>
                    <p class="text-muted mb-0">
                        <i class="fas fa-file-invoice me-1"></i>
                        {{ $transaction->invoice_number }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                    <a href="{{ route('receipt.print', $transaction) }}" class="btn btn-primary" target="_blank">
                        <i class="fas fa-print me-2"></i> Cetak Struk
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Detail Transaksi -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2" style="color: var(--accent-gold);"></i>
                        Informasi Transaksi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="35%" class="text-muted">Invoice</td>
                                    <td><strong>{{ $transaction->invoice_number }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Transaksi</td>
                                    <td>{{ $transaction->transaction_date ? $transaction->transaction_date->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kasir</td>
                                    <td>{{ $transaction->user->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status</td>
                                    <td>
                                        @if($transaction->status == 'completed')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i> Selesai
                                            </span>
                                        @elseif($transaction->status == 'cancelled')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i> Dibatalkan
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="35%" class="text-muted">Pelanggan</td>
                                    <td><strong>{{ $transaction->customer->name ?? 'Guest' }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nomor HP</td>
                                    <td>{{ $transaction->customer->phone ?? '-' }}</td>
                                </tr>
                                @if($transaction->customer && $transaction->customer->member_level != 'regular')
                                <tr>
                                    <td class="text-muted">Member Level</td>
                                    <td>
                                        <span class="badge" style="background: var(--accent-gold); color: var(--primary-black);">
                                            {{ ucfirst($transaction->customer->member_level) }}
                                        </span>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">Metode Pembayaran</td>
                                    <td>
                                        @if($transaction->payment_method == 'cash')
                                            <span class="badge bg-info">Tunai</span>
                                        @elseif($transaction->payment_method == 'qris')
                                            <span class="badge bg-primary">QRIS</span>
                                        @else
                                            <span class="badge bg-secondary">Debit</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Layanan -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cut me-2" style="color: var(--accent-gold);"></i>
                        Detail Layanan
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Layanan</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->details as $index => $detail)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $detail->service->name }}</td>
                                    <td class="text-end">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Subtotal</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">PPN 11%</td>
                                    <td class="text-end">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td>
                                </tr>
                                @if($transaction->discount > 0)
                                <tr>
                                    <td colspan="3" class="text-end text-success">Diskon</td>
                                    <td class="text-end text-success">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr class="border-top">
                                    <td colspan="3" class="text-end fw-bold fs-5">TOTAL</td>
                                    <td class="text-end fw-bold fs-5" style="color: var(--accent-gold);">
                                        Rp {{ number_format($transaction->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Catatan -->
            @if($transaction->notes)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-sticky-note me-2" style="color: var(--accent-gold);"></i>
                        Catatan
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $transaction->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar - Ringkasan Pembayaran -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2" style="color: var(--accent-gold);"></i>
                        Ringkasan Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Tagihan</span>
                            <strong>Rp {{ number_format($transaction->total, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Dibayar</span>
                            <strong class="text-success">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top">
                            <span class="text-muted">Kembalian</span>
                            <strong style="color: var(--accent-gold);">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    
                    @if($transaction->points_used > 0 || $transaction->points_earned > 0)
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Informasi Poin</h6>
                        @if($transaction->points_used > 0)
                        <div class="d-flex justify-content-between mb-1">
                            <span>Poin Digunakan</span>
                            <span>{{ number_format($transaction->points_used) }} poin</span>
                        </div>
                        @endif
                        @if($transaction->points_earned > 0)
                        <div class="d-flex justify-content-between">
                            <span>Poin Didapat</span>
                            <span class="text-success">+{{ number_format($transaction->points_earned) }} poin</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-grid gap-2">
                        @if($transaction->status == 'completed')
                        <button class="btn btn-danger cancel-transaction" data-id="{{ $transaction->id }}" data-invoice="{{ $transaction->invoice_number }}">
                            <i class="fas fa-times-circle me-2"></i> Batalkan Transaksi
                        </button>
                        @endif
                        <a href="{{ route('receipt.print', $transaction) }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-print me-2"></i> Cetak Ulang Struk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Batalkan -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2" style="color: var(--danger-red);"></i>
                    Konfirmasi Pembatalan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin membatalkan transaksi <strong id="cancelInvoice"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    Poin yang digunakan akan dikembalikan ke pelanggan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmCancel">
                    <i class="fas fa-check me-1"></i> Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let cancelTransactionId = null;

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
        cancelTransactionId = $(this).data('id');
        let invoice = $(this).data('invoice');
        $('#cancelInvoice').text(invoice);
        $('#cancelModal').modal('show');
    });
    
    $('#confirmCancel').click(function() {
        if (!cancelTransactionId) return;
        
        let button = $(this);
        button.html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');
        button.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("transactions.cancel", $transaction) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'POST'
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    setTimeout(() => {
                        window.location.href = '{{ route("transactions.index") }}';
                    }, 1500);
                } else {
                    showToast('error', response.message);
                    button.html('Ya, Batalkan');
                    button.prop('disabled', false);
                    $('#cancelModal').modal('hide');
                }
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || 'Gagal membatalkan transaksi';
                showToast('error', message);
                button.html('Ya, Batalkan');
                button.prop('disabled', false);
                $('#cancelModal').modal('hide');
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
    .table-borderless td {
        padding: 0.5rem 0;
        border: none;
    }
    
    .sticky-top {
        position: sticky;
        top: 20px;
    }
</style>
@endpush
@endsection