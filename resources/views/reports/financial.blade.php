@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Laporan Keuangan</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Financial Report</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="text-muted mb-2 text-uppercase small fw-bold">Hutang Dagang (AP)</h6>
                <h3 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalHutang, 0, ',', '.') }}</h3>
                <div class="mt-3 small">
                    <span class="text-muted">Total kewajiban supplier</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="text-muted mb-2 text-uppercase small fw-bold">Piutang Dagang (AR)</h6>
                <h3 class="fw-bold mb-0 text-success">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h3>
                <div class="mt-3 small">
                    <span class="text-muted">Tagihan ke pelanggan</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="text-muted mb-2 text-uppercase small fw-bold">Total Pendapatan</h6>
                <h3 class="fw-bold mb-0 text-primary">Rp {{ number_format($salesProfit->revenue, 0, ',', '.') }}</h3>
                <div class="mt-3 small text-primary">
                    <i class="fas fa-arrow-up me-1"></i> Gross Revenue
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-4">
                <h6 class="text-white text-opacity-75 mb-2 text-uppercase small fw-bold">Laba Kotor</h6>
                <h3 class="fw-bold mb-0">Rp {{ number_format($salesProfit->revenue - $salesProfit->total_hpp, 0, ',', '.') }}</h3>
                <div class="mt-3 small">
                    <i class="fas fa-chart-line me-1"></i> Margin Profit
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0">Ringkasan Laba Rugi (Kotor)</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span class="text-muted">Total Penjualan</span>
                    <span class="fw-bold">Rp {{ number_format($salesProfit->revenue, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span class="text-muted">Harga Pokok Penjualan (HPP FIFO)</span>
                    <span class="text-danger">-(Rp {{ number_format($salesProfit->total_hpp, 0, ',', '.') }})</span>
                </div>
                <div class="d-flex justify-content-between pt-2">
                    <h5 class="fw-bold">Estimasi Laba Kotor</h5>
                    <h5 class="fw-bold text-primary">Rp {{ number_format($salesProfit->revenue - $salesProfit->total_hpp, 0, ',', '.') }}</h5>
                </div>
                
                <div class="mt-5 bg-light p-4 rounded-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-circle-info text-primary me-3 fa-lg"></i>
                        <h6 class="fw-bold mb-0">Catatan Keuangan</h6>
                    </div>
                    <p class="small text-muted mb-0">
                        Laba kotor dihitung berdasarkan selisih antara harga jual dan Harga Pokok Penjualan (HPP) yang tercatat menggunakan metode FIFO. Angka ini belum termasuk biaya operasional lainnya (listrik, gaji, sewa, dll).
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm bg-dark text-white h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Arus Kas (Estimate)</h5>
                <div class="mb-4">
                    <small class="text-muted text-uppercase fw-bold d-block mb-2">Net Balance (AR - AP)</small>
                    <h3 class="fw-bold {{ $totalPiutang - $totalHutang >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($totalPiutang - $totalHutang, 0, ',', '.') }}
                    </h3>
                </div>
                <hr class="opacity-25">
                <p class="small text-muted">
                    Informasi ini menunjukkan selisih antara tagihan yang akan diterima dan kewajiban yang harus dibayarkan.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
