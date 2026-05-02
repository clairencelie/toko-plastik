@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Laporan Stok Barang</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Stok Opname</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <button onclick="window.print()" class="btn btn-light border shadow-sm">
            <i class="fas fa-print me-2"></i> Cetak Laporan
        </button>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white text-opacity-75 mb-2">Total Item</h6>
                        <h3 class="fw-bold mb-0">{{ $totalItems }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-25 p-3 rounded-circle">
                        <i class="fas fa-boxes-stacked fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Stok Menipis</h6>
                        <h3 class="fw-bold mb-0 text-warning">
                            {{ $lowStockCount }}
                        </h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                        <i class="fas fa-triangle-exclamation fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Kehabisan Stok</h6>
                        <h3 class="fw-bold mb-0 text-danger">
                            {{ $outOfStockCount }}
                        </h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger">
                        <i class="fas fa-circle-xmark fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form action="{{ route('report.stock') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-bold text-muted text-uppercase">Pencarian Barang</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nama atau kode barang..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted text-uppercase">Filter Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Stok Menipis</option>
                    <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('report.stock') }}" class="btn btn-light border">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nama Barang</th>
                        <th class="text-center">Saldo Awal</th>
                        <th class="text-center">Masuk (Beli)</th>
                        <th class="text-center">Keluar (Jual)</th>
                        <th class="text-center">Adjustment</th>
                        <th class="text-center">Saldo Akhir</th>
                        <th class="text-center pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                    @php
                        $status = 'normal';
                        $min = $stock->barang->stokminimal ?? 10;
                        if ($stock->saldoakhir <= 0) $status = 'out';
                        elseif ($stock->saldoakhir <= $min) $status = 'low';
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $stock->barang->namabarang ?? 'Unknown' }}</div>
                            <small class="text-muted">{{ $stock->kodebarang }}</small>
                        </td>
                        <td class="text-center">{{ number_format($stock->saldoawal, 0) }}</td>
                        <td class="text-center text-success">+{{ number_format($stock->beli, 0) }}</td>
                        <td class="text-center text-danger">-{{ number_format($stock->jual, 0) }}</td>
                        <td class="text-center">{{ number_format($stock->adjustmen, 0) }}</td>
                        <td class="text-center">
                            <span class="fw-bold fs-5 {{ $status == 'out' ? 'text-danger' : ($status == 'low' ? 'text-warning' : 'text-primary') }}">
                                {{ number_format($stock->saldoakhir, 0) }}
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            @if($status == 'out')
                                <span class="badge bg-danger">Habis</span>
                            @elseif($status == 'low')
                                <span class="badge bg-warning text-dark">Menipis</span>
                            @else
                                <span class="badge bg-success">Tersedia</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">Tidak ada data stok ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-4 border-0">
        {{ $stocks->appends(request()->query())->links() }}
    </div>
</div>
@endsection
