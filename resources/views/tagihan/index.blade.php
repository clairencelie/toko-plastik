@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Penagihan Piutang</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Tagihan</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('tagihan.create') }}" class="btn btn-primary shadow-sm px-4 py-2">
        <i class="fas fa-plus me-2"></i> Buat Tagihan
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form action="{{ route('tagihan.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">PENCARIAN</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nomor tagihan atau keterangan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i> Filter
                </button>
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
                        <th class="ps-4">No. Tagihan</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th class="text-end">Total Tagihan</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihans as $tag)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $tag->notagihan }}</td>
                        <td>{{ \Carbon\Carbon::parse($tag->tgltagihan)->format('d M Y') }}</td>
                        <td>{{ $tag->keterangan }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($tag->grandtotal, 0, ',', '.') }}</td>
                        <td class="text-center pe-4">
                            <div class="btn-group">
                                <a href="{{ route('tagihan.show', $tag->notagihan) }}" class="btn btn-sm btn-light border">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>
                                <a href="{{ route('tagihan.print', $tag->notagihan) }}" target="_blank" class="btn btn-sm btn-light border">
                                    <i class="fas fa-print text-secondary"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice fa-3x opacity-25 mb-3"></i>
                            <h6 class="fw-bold">Tidak ada data tagihan</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-4 border-0">
        {{ $tagihans->appends(request()->query())->links() }}
    </div>
</div>
@endsection
