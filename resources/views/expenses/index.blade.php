@extends('layouts.app')

@section('title', 'Manajemen Pengeluaran')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1" style="color: var(--accent-gold);">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        Manajemen Pengeluaran
                    </h4>
                    <p class="text-muted mb-0">Kelola semua pengeluaran bisnis barbershop</p>
                </div>
                <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Tambah Pengeluaran
                </a>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Total Pengeluaran</small>
                        <h3 class="mb-0">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h3>
                    </div>
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Pengeluaran Hari Ini</small>
                        <h3 class="mb-0">Rp {{ number_format($todayExpenses, 0, ',', '.') }}</h3>
                    </div>
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Pengeluaran Bulan Ini</small>
                        <h3 class="mb-0">Rp {{ number_format($monthExpenses, 0, ',', '.') }}</h3>
                    </div>
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter & Table -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Daftar Pengeluaran</h5>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="row g-2">
                        <div class="col-md-4">
                            <input type="date" name="date_from" class="form-control" 
                                   value="{{ request('date_from') }}" placeholder="Dari">
                        </div>
                        <div class="col-md-4">
                            <input type="date" name="date_to" class="form-control" 
                                   value="{{ request('date_to') }}" placeholder="Sampai">
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Petugas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                            <td>{{ $expense->description }}</td>
                            <td>
                                <span class="badge" style="background: rgba(212, 175, 55, 0.2); color: var(--accent-gold);">
                                    {{ $expense->category }}
                                </span>
                            </td>
                            <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                            <td>{{ $expense->user->name }}</td>
                            <td>
                                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada data pengeluaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
</div>
@endsection