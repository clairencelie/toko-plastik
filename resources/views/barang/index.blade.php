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
    <a href="{{ route('barang.create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-2"></i> Tambah Barang
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <form action="{{ route('barang.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <div class="input-group search-bar">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari nama atau kode barang..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-light border">Filter</button>
                @if(request('search'))
                    <a href="{{ route('barang.index') }}" class="btn btn-link text-decoration-none">Reset</a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Kode</th>
                        <th>Nama Barang</th>
                        <th>Kelompok</th>
                        <th>Satuan</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangs as $barang)
                    <tr>
                        <td class="ps-4 fw-medium text-primary">{{ $barang->kodebarang }}</td>
                        <td>
                            <div class="fw-bold">{{ $barang->namabarang }}</div>
                            <small class="text-muted">ID: {{ $barang->kodebarang }}</small>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $barang->kelompokRel->keterangan ?? '-' }}</span></td>
                        <td>{{ $barang->satuanRel->keterangan ?? '-' }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($barang->hargabeli, 0, ',', '.') }}</td>
                        <td class="text-center pe-4">
                            <div class="btn-group">
                                <a href="{{ route('barang.edit', $barang->kodebarang) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('barang.destroy', $barang->kodebarang) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin hapus?')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
                            Tidak ada data barang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $barangs->appends(request()->query())->links() }}
    </div>
</div>
@endsection
