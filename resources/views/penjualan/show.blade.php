@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Detail Penjualan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('penjualan.index') }}" class="text-decoration-none">Penjualan</a></li>
                <li class="breadcrumb-item active">{{ $penjualan->nopenjualan }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->user()->username === 'hdy')
        <a href="{{ route('penjualan.edit', $penjualan->nopenjualan) }}" class="btn btn-warning shadow-sm">
            Edit
        </a>
        <form action="{{ route('penjualan.destroy', $penjualan->nopenjualan) }}" method="POST" id="delete-form">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-danger shadow-sm" onclick="confirmDelete()">
                Hapus
            </button>
        </form>
        @endif
        <a href="{{ route('penjualan.print', $penjualan->nopenjualan) }}" class="btn btn-primary" target="_blank">
            Cetak Invoice
        </a>
        <a href="{{ route('penjualan.index') }}" class="btn btn-outline-secondary">
            Kembali
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
                    <label class="small text-muted d-block">Nomor Penjualan</label>
                    <span class="fw-bold text-primary">{{ $penjualan->nopenjualan }}</span>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block">Tanggal Penjualan</label>
                    <span class="fw-bold">{{ \Carbon\Carbon::parse($penjualan->tglpenjualan)->format('d F Y') }}</span>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block">Pelanggan</label>
                    <span class="fw-bold">{{ $penjualan->pelangganRel->namapelanggan ?? $penjualan->namapelanggan }}</span>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block">Salesman</label>
                    <span class="fw-bold">{{ $penjualan->namasalesman }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Metode Bayar</span>
                    <span class="badge {{ $penjualan->kredit > 0 ? 'bg-warning text-dark' : 'bg-success' }}">
                        {{ $penjualan->kredit > 0 ? 'Kredit' : 'Tunai' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tunai</span>
                    <span class="fw-bold">Rp {{ number_format($penjualan->tunai, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Grand Total</span>
                    <span class="fw-bold text-primary">Rp {{ number_format($penjualan->grandtotal, 0, ',', '.') }}</span>
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
                            @foreach($penjualan->details as $detail)
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
                                <td colspan="3" class="text-end fw-bold">Grand Total</td>
                                <td class="text-end fw-bold text-primary">Rp {{ number_format($penjualan->grandtotal, 0, ',', '.') }}</td>
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
        if (confirm('Yakin ingin menghapus penjualan {{ $penjualan->nopenjualan }}? Stok barang akan dikembalikan.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection
