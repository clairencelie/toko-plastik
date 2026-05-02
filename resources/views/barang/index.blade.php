@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Master Barang</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Barang</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('barang.create') }}" class="btn btn-primary shadow-sm px-4 py-2">
        <i class="fas fa-plus me-2"></i> Tambah Barang
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form action="{{ route('barang.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">PENCARIAN</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nama atau kode barang..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">KELOMPOK</label>
                <select name="kelompok" class="form-select">
                    <option value="">Semua Kelompok</option>
                    @foreach($kelompoks as $kel)
                        <option value="{{ $kel->kelompok }}" {{ request('kelompok') == $kel->kelompok ? 'selected' : '' }}>{{ $kel->keterangan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">SUPPLIER</label>
                <select name="supplier" class="form-select">
                    <option value="">Semua Supplier</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->supplier }}" {{ request('supplier') == $sup->supplier ? 'selected' : '' }}>{{ $sup->keterangan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary px-3">
                        <i class="fas fa-filter"></i>
                    </button>
                    @if(request()->anyFilled(['search', 'kelompok', 'supplier']))
                        <a href="{{ route('barang.index') }}" class="btn btn-light border px-3" title="Reset">
                            <i class="fas fa-rotate-left"></i>
                        </a>
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
                        <th class="ps-4">Item Details</th>
                        <th>Kelompok</th>
                        <th>Satuan</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangs as $barang)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light p-2 rounded-3 me-3 text-primary fw-bold" style="width: 50px; text-align: center;">
                                    {{ substr($barang->namabarang, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $barang->namabarang }}</div>
                                    <small class="text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">CODE: {{ $barang->kodebarang }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-soft-primary text-primary border border-primary border-opacity-10">{{ $barang->kelompokRel->keterangan ?? '-' }}</span></td>
                        <td>{{ $barang->satuanRel->keterangan ?? '-' }}</td>
                        <td class="text-end fw-bold text-dark">Rp {{ number_format($barang->hargabeli, 0, ',', '.') }}</td>
                        <td class="text-center pe-4">
                            <div class="btn-group shadow-sm rounded-3">
                                <a href="{{ route('barang.edit', $barang->kodebarang) }}" class="btn btn-sm btn-white border-end" title="Edit">
                                    <i class="fas fa-pen-to-square text-primary"></i>
                                </a>
                                <form action="{{ route('barang.destroy', $barang->kodebarang) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-white" onclick="return confirm('Yakin hapus?')" title="Hapus">
                                        <i class="fas fa-trash-can text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <div class="mb-3">
                                <i class="fas fa-box-open fa-3x opacity-25"></i>
                            </div>
                            <h6 class="fw-bold">Tidak ada data ditemukan</h6>
                            <p class="small">Coba sesuaikan filter atau pencarian Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-4 border-0">
        {{ $barangs->appends(request()->query())->links() }}
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(79, 70, 229, 0.1); }
    .btn-white { background: white; border: 1px solid #e2e8f0; }
    .btn-white:hover { background: #f8fafc; }
</style>
@endsection
