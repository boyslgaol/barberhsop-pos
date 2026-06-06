@extends('layouts.app')

@section('title', 'Laporan Harian')

@push('styles')
<style>
    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    
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
        transform: translateY(-4px);
        border-color: rgba(212, 175, 55, 0.3);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
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
        font-size: 1.5rem;
        color: var(--accent-gold);
    }
    
    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0;
        line-height: 1.2;
    }
    
    /* Report Cards */
    .report-card {
        background: var(--secondary-dark);
        border: 1px solid rgba(212, 175, 55, 0.1);
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s;
        height: 100%;
    }
    
    .report-card:hover {
        border-color: rgba(212, 175, 55, 0.2);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    }
    
    .report-header {
        background: rgba(0, 0, 0, 0.3);
        border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        padding: 1rem 1.25rem;
        font-weight: 600;
        color: var(--accent-gold);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .report-header i {
        font-size: 1.1rem;
    }
    
    /* Profit Cards */
    .profit-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    
    .profit-card {
        background: linear-gradient(135deg, var(--secondary-dark) 0%, rgba(212, 175, 55, 0.05) 100%);
        border-radius: 20px;
        padding: 1.25rem;
        text-align: center;
        transition: all 0.3s;
        border: 1px solid rgba(212, 175, 55, 0.1);
    }
    
    .profit-card:hover {
        transform: translateY(-3px);
        border-color: rgba(212, 175, 55, 0.3);
    }
    
    /* Payment Method Item */
    .payment-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .payment-item:last-child {
        border-bottom: none;
    }
    
    .payment-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .payment-badge {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: rgba(212, 175, 55, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Table Styling */
    .table-custom {
        margin-bottom: 0;
    }
    
    .table-custom th {
        background: #1a1a1a;
        border-bottom: 2px solid var(--accent-gold);
        padding: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table-custom td {
        padding: 12px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .table-custom tr:hover {
        background: rgba(212, 175, 55, 0.05);
    }
    
    /* Alert Styling */
    .alert-custom {
        background: rgba(212, 175, 55, 0.1);
        border: 1px solid rgba(212, 175, 55, 0.2);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
    }
    
    /* Chart Container */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
        padding: 1rem;
    }
    
    /* Top Services Table */
    .service-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.3s;
    }
    
    .service-item:hover {
        background: rgba(212, 175, 55, 0.05);
        transform: translateX(5px);
    }
    
    .service-rank {
        width: 30px;
        font-weight: 700;
        color: var(--accent-gold);
    }
    
    .service-name {
        flex: 1;
        font-weight: 500;
    }
    
    .service-stats {
        text-align: right;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .profit-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-value {
            font-size: 1.25rem;
        }
        
        .chart-container {
            height: 250px;
        }
    }
    
    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1" style="color: var(--accent-gold);">
                <i class="fas fa-chart-line me-2"></i>
                Laporan Harian
            </h4>
            <p class="text-muted mb-0">Ringkasan transaksi dan performa bisnis harian</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form method="GET" action="{{ route('reports.daily') }}" class="d-flex gap-2">
                <div class="input-group" style="width: auto;">
                    <span class="input-group-text bg-transparent border-secondary">
                        <i class="fas fa-calendar-alt" style="color: var(--accent-gold);"></i>
                    </span>
                    <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i> Tampilkan
                </button>
                <a href="{{ route('reports.export-excel') }}?date={{ $date->format('Y-m-d') }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i> Export
                </a>
            </form>
        </div>
    </div>
    
    <!-- Alert jika tidak ada data -->
    @if($transactions->isEmpty())
    <div class="alert-custom text-center">
        <i class="fas fa-info-circle fa-2x mb-2 d-block" style="color: var(--accent-gold);"></i>
        <strong class="d-block mb-1">Belum ada transaksi pada tanggal {{ $date->format('d/m/Y') }}</strong>
        <p class="text-muted mb-0">Silakan pilih tanggal lain atau lakukan transaksi terlebih dahulu.</p>
    </div>
    @endif
    
    <!-- Stats Cards Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <span class="badge bg-success">Today</span>
            </div>
            <h6 class="text-muted mb-1">Total Pendapatan</h6>
            <div class="stat-value text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <small class="text-muted">Dari {{ $totalTransactions }} transaksi</small>
        </div>
        
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <span class="badge bg-info">Transactions</span>
            </div>
            <h6 class="text-muted mb-1">Total Transaksi</h6>
            <div class="stat-value">{{ $totalTransactions }}</div>
            <small class="text-muted">Rata-rata Rp {{ number_format($averageTransaction, 0, ',', '.') }}</small>
        </div>
        
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <span class="badge bg-warning">Customers</span>
            </div>
            <h6 class="text-muted mb-1">Pelanggan</h6>
            <div class="stat-value">{{ $totalCustomers }}</div>
            <small class="text-muted">Pelanggan unik hari ini</small>
        </div>
        
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span class="badge bg-primary">Average</span>
            </div>
            <h6 class="text-muted mb-1">Rata-rata Transaksi</h6>
            <div class="stat-value">Rp {{ number_format($averageTransaction, 0, ',', '.') }}</div>
            <small class="text-muted">Per transaksi</small>
        </div>
    </div>
    
    <!-- Profit & Expenses Grid -->
    <div class="profit-grid">
        <div class="profit-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">Total Pendapatan</small>
                    <h4 class="mb-0 text-success mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                </div>
                <i class="fas fa-arrow-up fa-2x text-success opacity-50"></i>
            </div>
        </div>
        <div class="profit-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">Pengeluaran</small>
                    <h4 class="mb-0 text-danger mt-1">Rp {{ number_format($expenses, 0, ',', '.') }}</h4>
                </div>
                <i class="fas fa-arrow-down fa-2x text-danger opacity-50"></i>
            </div>
        </div>
        <div class="profit-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">Laba Bersih</small>
                    <h4 class="mb-0 mt-1" style="color: var(--accent-gold);">Rp {{ number_format($netProfit, 0, ',', '.') }}</h4>
                </div>
                <i class="fas fa-chart-line fa-2x" style="color: var(--accent-gold); opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Payment Methods Card -->
        <div class="col-lg-4">
            <div class="report-card">
                <div class="report-header">
                    <i class="fas fa-credit-card"></i>
                    Metode Pembayaran
                </div>
                <div class="p-3">
                    <div class="payment-item">
                        <div class="payment-label">
                            <div class="payment-badge">
                                <i class="fas fa-money-bill-wave" style="color: var(--accent-gold);"></i>
                            </div>
                            <span>Tunai</span>
                        </div>
                        <div class="text-end">
                            <strong>Rp {{ number_format($paymentMethods['cash'], 0, ',', '.') }}</strong>
                            <br>
                            <small class="text-muted">{{ $totalRevenue > 0 ? round(($paymentMethods['cash'] / $totalRevenue) * 100) : 0 }}%</small>
                        </div>
                    </div>
                    <div class="payment-item">
                        <div class="payment-label">
                            <div class="payment-badge">
                                <i class="fas fa-qrcode" style="color: var(--accent-gold);"></i>
                            </div>
                            <span>QRIS</span>
                        </div>
                        <div class="text-end">
                            <strong>Rp {{ number_format($paymentMethods['qris'], 0, ',', '.') }}</strong>
                            <br>
                            <small class="text-muted">{{ $totalRevenue > 0 ? round(($paymentMethods['qris'] / $totalRevenue) * 100) : 0 }}%</small>
                        </div>
                    </div>
                    <div class="payment-item">
                        <div class="payment-label">
                            <div class="payment-badge">
                                <i class="fas fa-credit-card" style="color: var(--accent-gold);"></i>
                            </div>
                            <span>Debit</span>
                        </div>
                        <div class="text-end">
                            <strong>Rp {{ number_format($paymentMethods['debit'], 0, ',', '.') }}</strong>
                            <br>
                            <small class="text-muted">{{ $totalRevenue > 0 ? round(($paymentMethods['debit'] / $totalRevenue) * 100) : 0 }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Services Card -->
        <div class="col-lg-8">
            <div class="report-card">
                <div class="report-header">
                    <i class="fas fa-trophy"></i>
                    Layanan Terlaris
                </div>
                <div class="p-0">
                    @forelse($topServices as $index => $service)
                    <div class="service-item">
                        <div class="service-rank">
                            @if($index == 0)
                                <i class="fas fa-crown" style="color: #ffd700;"></i>
                            @elseif($index == 1)
                                <i class="fas fa-medal" style="color: #c0c0c0;"></i>
                            @elseif($index == 2)
                                <i class="fas fa-medal" style="color: #cd7f32;"></i>
                            @else
                                <span class="text-muted">#{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <div class="service-name">
                            {{ $service->name }}
                        </div>
                        <div class="service-stats">
                            <span class="badge bg-primary me-2">{{ $service->total }}x</span>
                            <small class="text-muted">Rp {{ number_format($service->revenue, 0, ',', '.') }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-simple fa-2x mb-2 d-block opacity-50"></i>
                        Belum ada data layanan
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hourly Chart -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="report-card">
                <div class="report-header">
                    <i class="fas fa-chart-bar"></i>
                    Jam Sibuk (Peak Hours Analysis)
                </div>
                <div class="chart-container">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transactions List -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="report-card">
                <div class="report-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-list"></i>
                        Daftar Transaksi
                    </div>
                    <span class="badge bg-secondary">{{ $transactions->count() }} transaksi</span>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Waktu</th>
                                    <th>Pelanggan</th>
                                    <th>Kasir</th>
                                    <th class="text-end">Total</th>
                                    <th>Metode</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $transaction->invoice_number }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $transaction->transaction_date->setTimezone('Asia/Jakarta')->format('H:i:s') }}</small>
                                    </td>
                                    <td>{{ $transaction->customer->name ?? 'Guest' }}</td>
                                    <td>{{ $transaction->user->name ?? '-' }}</td>
                                    <td class="text-end">
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
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                        <p class="mb-0">Belum ada transaksi</p>
                                        <small>Silakan lakukan transaksi terlebih dahulu</small>
                                    </td>
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
// Hourly chart
const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
const hourlyData = @json(array_values($hourlyBreakdown));
const maxRevenue = Math.max(...hourlyData);
const maxHour = hourlyData.indexOf(maxRevenue);

// Gradient for chart
const gradient = hourlyCtx.createLinearGradient(0, 0, 0, 300);
gradient.addColorStop(0, 'rgba(212, 175, 55, 0.3)');
gradient.addColorStop(1, 'rgba(212, 175, 55, 0.0)');

new Chart(hourlyCtx, {
    type: 'line',
    data: {
        labels: ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'],
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: hourlyData,
            backgroundColor: gradient,
            borderColor: '#d4af37',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: function(context) {
                const index = context.dataIndex;
                return index === maxHour ? '#d4af37' : '#d4af37';
            },
            pointBorderColor: '#000',
            pointBorderWidth: 1,
            pointRadius: function(context) {
                return context.dataIndex === maxHour ? 6 : 3;
            },
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                labels: {
                    color: '#a0a0a0',
                    font: { size: 11 }
                },
                position: 'top'
            },
            tooltip: {
                backgroundColor: '#1a1a1a',
                titleColor: '#d4af37',
                bodyColor: '#fff',
                borderColor: '#d4af37',
                borderWidth: 1,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) label += ': ';
                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        if (context.dataIndex === maxHour) {
                            label += ' (Peak Hour)';
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
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                    },
                    stepSize: maxRevenue > 0 ? Math.ceil(maxRevenue / 5) : 100000
                }
            },
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    color: '#a0a0a0',
                    stepSize: 2
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});

// Animate numbers on load
document.addEventListener('DOMContentLoaded', function() {
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(el => {
        const finalValue = el.innerText;
        el.style.opacity = '0';
        el.style.transform = 'translateY(10px)';
        setTimeout(() => {
            el.style.transition = 'all 0.5s ease';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 100);
    });
});
</script>
@endpush
@endsection