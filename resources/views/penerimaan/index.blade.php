@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Riwayat Pembelian Barang</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
                <li class="breadcrumb-item active">Pembelian</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('penerimaan.create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-truck-loading me-2"></i> Catat Penerimaan
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <form action="{{ route('penerimaan.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <div class="input-group search-bar">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari No. Penerimaan atau Supplier..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-light border">Filter</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No. Penerimaan</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Tunai</th>
                        <th class="text-end">Kredit</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penerimaans as $penerimaan)
                    <tr>
                        <td class="ps-4 fw-medium text-primary">{{ $penerimaan->nopenerimaan }}</td>
                        <td>{{ \Carbon\Carbon::parse($penerimaan->tglpenerimaan)->format('d/m/Y') }}</td>
                        <td>
                            <div class="fw-bold">{{ $penerimaan->namasupplier }}</div>
                            <small class="text-muted">ID: {{ $penerimaan->supplier }}</small>
                        </td>
                        <td class="text-end fw-bold">Rp {{ number_format($penerimaan->grandtotal, 0, ',', '.') }}</td>
                        <td class="text-end text-success">Rp {{ number_format($penerimaan->tunai, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">Rp {{ number_format($penerimaan->kredit, 0, ',', '.') }}</td>
                        <td class="text-center pe-4">
                            <a href="#" class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Tidak ada data penerimaan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $penerimaans->appends(request()->query())->links() }}
    </div>
</div>
@endsection
