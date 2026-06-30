@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Detail Tagihan</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('tagihan.index') }}">Tagihan</a></li>
                <li class="breadcrumb-item active">{{ $tagihan->notagihan }}</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group shadow-sm">
        <a href="{{ route('tagihan.index') }}" class="btn btn-light border">
            Kembali
        </a>
        <a href="{{ route('tagihan.print', $tagihan->notagihan) }}" target="_blank" class="btn btn-primary">
            Cetak Tagihan
        </a>
        @if(auth()->user()->username === 'hdy')
        <a href="{{ route('tagihan.edit', $tagihan->notagihan) }}" class="btn btn-warning">
            Edit
        </a>
        <form action="{{ route('tagihan.destroy', $tagihan->notagihan) }}" method="POST" class="d-inline delete-form" id="deleteForm">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-danger btn-delete" data-no="{{ $tagihan->notagihan }}">
                Hapus
            </button>
        </form>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Ringkasan Tagihan</h5>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">NOMOR</label>
                    <div class="fw-bold">{{ $tagihan->notagihan }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">TANGGAL</label>
                    <div>{{ \Carbon\Carbon::parse($tagihan->tgltagihan)->format('d F Y') }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">PELANGGAN</label>
                    <div class="fw-bold text-primary">
                        {{ $tagihan->details->pluck('nama')->unique()->filter()->implode(', ') ?: '-' }}
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">KETERANGAN</label>
                    <div>{{ $tagihan->keterangan }}</div>
                </div>
                <div class="mt-4 p-3 bg-light rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold small text-muted">TOTAL TAGIHAN</span>
                        <h4 class="fw-bold mb-0 text-primary">Rp {{ number_format($tagihan->grandtotal, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Daftar Transaksi Ditagih</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No. Faktur</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Sudah Bayar</th>
                                <th class="text-end text-primary">Sisa Tagihan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tagihan->details as $detail)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $detail->nopenjualan }}</div>
                                    <small class="text-muted">{{ $detail->tgltagihan }}</small>
                                </td>
                                <td class="text-end">Rp {{ number_format($detail->total, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($detail->sudahbayar, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-primary">Rp {{ number_format($detail->sisabayar, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('penjualan.show', $detail->nopenjualan) }}" class="btn btn-sm btn-outline-primary">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3" class="text-end">GRAND TOTAL</td>
                                <td class="text-end text-primary">Rp {{ number_format($tagihan->grandtotal, 0, ',', '.') }}</td>
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
    $(document).on('click', '.btn-delete', function() {
        const no = $(this).data('no');
        if (confirm('Yakin ingin menghapus tagihan ' + no + '?')) {
            $(this).closest('.delete-form').submit();
        }
    });
</script>
@endpush
@endsection
