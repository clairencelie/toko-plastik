@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-0">Dashboard Analytics</h2>
    <p class="text-muted">Welcome back to Stephany Plastik management system.</p>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm overflow-hidden h-100" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-white bg-opacity-20 rounded-3 p-2">
                        <i class="fas fa-cube text-white fa-lg"></i>
                    </div>
                    <span class="text-white text-opacity-75 small fw-bold">ITEMS</span>
                </div>
                <h3 class="text-white fw-bold mb-0">{{ number_format($totalBarang) }}</h3>
                <p class="text-white text-opacity-75 small mb-0 mt-2">Total Produk Terdaftar</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm overflow-hidden h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-white bg-opacity-20 rounded-3 p-2">
                        <i class="fas fa-users text-white fa-lg"></i>
                    </div>
                    <span class="text-white text-opacity-75 small fw-bold">CUSTOMERS</span>
                </div>
                <h3 class="text-white fw-bold mb-0">{{ number_format($totalPelanggan) }}</h3>
                <p class="text-white text-opacity-75 small mb-0 mt-2">Total Pelanggan Aktif</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm overflow-hidden h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-white bg-opacity-20 rounded-3 p-2">
                        <i class="fas fa-cart-shopping text-white fa-lg"></i>
                    </div>
                    <span class="text-white text-opacity-75 small fw-bold">SALES</span>
                </div>
                <h3 class="text-white fw-bold mb-0">{{ number_format($totalPenjualan) }}</h3>
                <p class="text-white text-opacity-75 small mb-0 mt-2">Total Transaksi Selesai</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm overflow-hidden h-100" style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="bg-white bg-opacity-20 rounded-3 p-2">
                        <i class="fas fa-wallet text-white fa-lg"></i>
                    </div>
                    <span class="text-white text-opacity-75 small fw-bold">REVENUE</span>
                </div>
                <h3 class="text-white fw-bold mb-0">Rp {{ number_format($revenue, 0, ',', '.') }}</h3>
                <p class="text-white text-opacity-75 small mb-0 mt-2">Total Omzet Penjualan</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Recent Transactions</h5>
                <a href="{{ route('penjualan.index') }}" class="btn btn-light btn-sm px-3">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">No. Penjualan</th>
                                <th>Pelanggan</th>
                                <th>Waktu</th>
                                <th class="text-end pe-4">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                            <tr>
                                <td class="ps-4"><span class="fw-bold">{{ $sale->nopenjualan }}</span></td>
                                <td>{{ $sale->namapelanggan }}</td>
                                <td><small class="text-muted">{{ $sale->waktu ? \Carbon\Carbon::parse($sale->waktu)->format('d M Y, H:i') : '-' }}</small></td>
                                <td class="text-end pe-4 fw-bold text-primary">Rp {{ number_format($sale->grandtotal, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Belum ada transaksi terbaru</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white p-4 border-0">
                <h5 class="fw-bold mb-0">Our Store</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="rounded-4 overflow-hidden mb-3 shadow-sm" style="height: 200px;">
                    <img src="/images/toko.jpg" alt="Stephany Plastik Store" class="w-100 h-100" style="object-fit: cover;">
                </div>
                <h6 class="fw-bold mb-1">Stephany Plastik Karawang</h6>
                <p class="text-muted small mb-3"><i class="fas fa-location-dot me-1"></i> Karawang, West Java, Indonesia</p>
                <div class="bg-light rounded-3 p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted">Status:</span>
                        <span class="badge bg-success small">Open Now</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small text-muted">Business Hours:</span>
                        <span class="small fw-bold">08:00 - 17:00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
