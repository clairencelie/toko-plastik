@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Detail Adjustment</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('adjustmen.index') }}">Adjustment</a></li>
                <li class="breadcrumb-item active">{{ $adjustmen->noadjustmen }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('adjustmen.index') }}" class="btn btn-light border shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Ringkasan</h5>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">NOMOR</label>
                    <div class="fw-bold">{{ $adjustmen->noadjustmen }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">TANGGAL</label>
                    <div>{{ \Carbon\Carbon::parse($adjustmen->tanggaladjustmen)->format('d F Y') }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">KETERANGAN</label>
                    <div>{{ $adjustmen->keterangan }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">OPERATOR</label>
                    <div>{{ $adjustmen->user->name ?? 'Admin' }}</div>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-success small fw-bold">ADJUST POSITIF</span>
                    <span class="fw-bold">Rp {{ number_format($adjustmen->adjustpositif, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-danger small fw-bold">ADJUST NEGATIF</span>
                    <span class="fw-bold">Rp {{ number_format($adjustmen->adjustnegatif, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                    <span class="fw-bold">TOTAL</span>
                    <h4 class="fw-bold text-primary mb-0">Rp {{ number_format($adjustmen->grandtotal, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Daftar Item</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Barang</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($adjustmen->details as $detail)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $detail->namabarang }}</div>
                                    <small class="text-muted">{{ $detail->kodebarang }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $detail->jumlah > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $detail->jumlah > 0 ? '+' : '' }}{{ $detail->jumlah }}
                                    </span>
                                    <small class="text-muted ms-1">{{ $detail->namasatuan }}</small>
                                </td>
                                <td class="text-end">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
