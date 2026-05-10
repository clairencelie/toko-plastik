@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Pelunasan Piutang (Kas Masuk)</h2>
        <p class="text-muted mb-0">Riwayat penerimaan pembayaran dari pelanggan</p>
    </div>
    <a href="{{ route('kasmasuk.create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-2"></i> Catat Pelunasan
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="p-4 border-bottom">
            <form action="{{ route('kasmasuk.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No. Kas / No. Ref / Pelanggan..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-light border w-100">Filter</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No. Kas Masuk</th>
                        <th>Tanggal</th>
                        <th>No. Referensi</th>
                        <th>Pelanggan</th>
                        <th>Keterangan</th>
                        <th class="text-end pe-4">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold">{{ $payment->nokasmasuk }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($payment->tanggal)->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-light text-primary border">{{ $payment->noref }}</span>
                        </td>
                        <td>{{ $payment->nama }}</td>
                        <td>{{ $payment->keterangan }}</td>
                        <td class="text-end pe-4 fw-bold text-success">
                            Rp {{ number_format($payment->jumlah, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-3x mb-3 opacity-25"></i>
                            <p>Belum ada data pelunasan piutang.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection
