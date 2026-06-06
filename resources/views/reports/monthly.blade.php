@extends('layouts.app')

@section('title', 'Laporan Bulanan')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1" style="color: var(--accent-gold);">
                        <i class="fas fa-chart-bar me-2"></i>
                        Laporan Bulanan
                    </h4>
                    <p class="text-muted mb-0">Ringkasan transaksi per bulan</p>
                </div>
                <div>
                    <form method="GET" action="{{ route('reports.monthly') }}" class="d-flex gap-2">
                        <input type="month" name="month" class="form-control" value="{{ $month->format('Y-m') }}" style="width: 200px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-alt me-2"></i> Tampilkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Total Pendapatan</small>
                        <h3 class="mb-0 text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    </div>
                    <i class="fas fa-money-bill-wave fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Total Transaksi</small>
                        <h3 class="mb-0">{{ $totalTransactions }}</h3>
                    </div>
                    <i class="fas fa-receipt fa-2x text-info opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Pelanggan Unik</small>
                        <h3 class="mb-0">{{ $totalCustomers }}</h3>
                    </div>
                    <i class="fas fa-users fa-2x text-warning opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Rata-rata Transaksi</small>
                        <h3 class="mb-0">Rp {{ number_format($averageTransaction, 0, ',', '.') }}</h3>
                    </div>
                    <i class="fas fa-chart-line fa-2x text-primary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Period Info -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}
    </div>
    
    <!-- Daily Breakdown Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="report-card">
                <div class="report-header">
                    <i class="fas fa-chart-line me-2"></i>
                    Grafik Pendapatan Harian
                </div>
                <div class="p-3">
                    <canvas id="dailyChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Payment Methods -->
        <div class="col-md-4 mb-4">
            <div class="report-card">
                <div class="report-header">
                    <i class="fas fa-credit-card me-2"></i>
                    Metode Pembayaran
                </div>
                <div class="p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tunai</span>
                        <strong>Rp {{ number_format($paymentMethods['cash'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>QRIS</span>
                        <strong>Rp {{ number_format($paymentMethods['qris'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Debit</span>
                        <strong>Rp {{ number_format($paymentMethods['debit'], 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Services -->
        <div class="col-md-8 mb-4">
            <div class="report-card">
                <div class="report-header">
                    <i class="fas fa-trophy me-2"></i>
                    Layanan Terlaris
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Layanan</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topServices as $service)
                                <tr>
                                    <td>{{ $service->name }}</td>
                                    <td class="text-center">{{ $service->total }}x</td>
                                    <td class="text-end">Rp {{ number_format($service->revenue, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Daily chart
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
const dailyData = @json(array_values($dailyBreakdown));
const dailyLabels = @json(array_keys($dailyBreakdown));

new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: dailyData,
            borderColor: '#d4af37',
            backgroundColor: 'rgba(212, 175, 55, 0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#d4af37',
            pointBorderColor: '#000',
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            }
        }
    }
});
</script>
@endpush
@endsection