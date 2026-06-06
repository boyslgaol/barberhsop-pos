@extends('layouts.app')

@section('title', 'Detail Pelanggan - ' . $customer->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1" style="color: var(--accent-gold);">
                        <i class="fas fa-user-circle me-2"></i>
                        Detail Pelanggan
                    </h4>
                    <p class="text-muted mb-0">
                        <i class="fas fa-id-card me-1"></i>
                        Member: {{ $customer->member_code }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                    </div>
                    <h4>{{ $customer->name }}</h4>
                    <div class="member-badge member-{{ $customer->member_level }} d-inline-block">
                        {{ ucfirst($customer->member_level) }} Member
                    </div>
                    <div class="mt-3">
                        <p class="mb-1">
                            <i class="fas fa-phone me-2"></i> {{ $customer->phone }}
                        </p>
                        @if($customer->email)
                        <p class="mb-1">
                            <i class="fas fa-envelope me-2"></i> {{ $customer->email }}
                        </p>
                        @endif
                        @if($customer->address)
                        <p class="mb-1">
                            <i class="fas fa-map-marker-alt me-2"></i> {{ $customer->address }}
                        </p>
                        @endif
                        @if($customer->birthdate)
                        <p class="mb-1">
                            <i class="fas fa-birthday-cake me-2"></i> 
                            {{ \Carbon\Carbon::parse($customer->birthdate)->format('d/m/Y') }}
                            ({{ \Carbon\Carbon::parse($customer->birthdate)->age }} tahun)
                        </p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Stats Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Statistik</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Belanja</span>
                        <strong>Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Poin</span>
                        <strong>{{ number_format($customer->points) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Kunjungan</span>
                        <strong>{{ $customer->visit_count }}x</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Terakhir Kunjung</span>
                        <strong>{{ $customer->last_visit ? $customer->last_visit->setTimezone('Asia/Jakarta')->format('d/m/Y') : '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <!-- Transactions History -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Riwayat Transaksi
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Metode</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->invoice_number }}</td>
                                    <td>{{ $transaction->transaction_date->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                                    <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                    <td>
                                        @if($transaction->payment_method == 'cash')
                                            Tunai
                                        @elseif($transaction->payment_method == 'qris')
                                            QRIS
                                        @else
                                            Debit
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('receipt.print', $transaction) }}" class="btn btn-sm btn-info" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
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
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar-circle {
        width: 80px;
        height: 80px;
        background: rgba(212, 175, 55, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 2rem;
        color: var(--accent-gold);
    }
    .member-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .member-regular { background: #6c757d; color: white; }
    .member-silver { background: #c0c0c0; color: #333; }
    .member-gold { background: #ffd700; color: #333; }
    .member-platinum { background: #e5e4e2; color: #333; }
</style>
@endpush
@endsection