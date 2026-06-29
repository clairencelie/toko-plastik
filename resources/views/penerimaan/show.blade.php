@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Detail Penerimaan Barang</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('penerimaan.index') }}" class="text-decoration-none">Penerimaan</a></li>
                <li class="breadcrumb-item active">{{ $penerimaan->nopenerimaan }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->user()->username === 'hdy')
        <a href="{{ route('penerimaan.edit', $penerimaan->nopenerimaan) }}" class="btn btn-warning shadow-sm">
            <i class="fas fa-edit me-2"></i> Edit
        </a>
        <form action="{{ route('penerimaan.destroy', $penerimaan->nopenerimaan) }}" method="POST" id="delete-form">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-danger shadow-sm" onclick="confirmDelete()">
                <i class="fas fa-trash me-2"></i> Hapus
            </button>
        </form>
        @endif
        <a href="{{ route('penerimaan.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-uppercase small fw-bold text-muted mb-4">Informasi Transaksi</h6>
                <div class="mb-3">
                    <label class="small text-muted d-block">Nomor Penerimaan</label>
                    <span class="fw-bold text-primary">{{ $penerimaan->nopenerimaan }}</span>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block">Tanggal Penerimaan</label>
                    <span class="fw-bold">{{ \Carbon\Carbon::parse($penerimaan->tglpenerimaan)->format('d F Y') }}</span>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block">Supplier</label>
                    <span class="fw-bold">{{ $penerimaan->supplierRel->keterangan ?? $penerimaan->namasupplier }}</span>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block">Jatuh Tempo</label>
                    <span class="fw-bold text-danger">{{ $penerimaan->tgljatuhtempo ? \Carbon\Carbon::parse($penerimaan->tgljatuhtempo)->format('d F Y') : '-' }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Metode Bayar</span>
                    <span class="badge {{ $penerimaan->kredit > 0 ? 'bg-warning text-dark' : 'bg-success' }}">
                        {{ $penerimaan->kredit > 0 ? 'Kredit' : 'Tunai' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Total Pembayaran</span>
                    <span class="fw-bold text-primary">Rp {{ number_format($penerimaan->grandtotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="p-4 border-bottom bg-light">
                    <h6 class="text-uppercase small fw-bold text-muted mb-0">Rincian Barang</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penerimaan->details as $detail)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $detail->namabarang }}</div>
                                    <small class="text-muted">Kode: {{ $detail->kodebarang }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $detail->jumlah }} {{ $detail->namasatuan }}</span>
                                </td>
                                <td class="text-end">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <td colspan="3" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold text-primary">Rp {{ number_format($penerimaan->grandtotal, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('Yakin ingin menghapus penerimaan {{ $penerimaan->nopenerimaan }}? Stok barang akan dikembalikan.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection
