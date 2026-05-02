@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Daftar Pelanggan</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Pelanggan</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('pelanggan.create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-user-plus me-2"></i> Tambah Pelanggan
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <form action="{{ route('pelanggan.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <div class="input-group search-bar">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari nama atau kode pelanggan..." value="{{ request('search') }}">
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
                        <th class="ps-4">ID</th>
                        <th>Nama Pelanggan</th>
                        <th>Kecamatan</th>
                        <th>Kota</th>
                        <th>Telepon</th>
                        <th class="text-end">Total Piutang</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggans as $pelanggan)
                    <tr>
                        <td class="ps-4 fw-medium text-primary">{{ $pelanggan->kodepelanggan }}</td>
                        <td class="fw-bold">{{ $pelanggan->namapelanggan }}</td>
                        <td>{{ $pelanggan->kecamatan }}</td>
                        <td>{{ $pelanggan->kota }}</td>
                        <td>{{ $pelanggan->telepon }}</td>
                        <td class="text-end fw-bold text-danger">Rp {{ number_format($pelanggan->piutang, 0, ',', '.') }}</td>
                        <td class="text-center pe-4">
                            <div class="btn-group">
                                <a href="{{ route('pelanggan.edit', $pelanggan->kodepelanggan) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('pelanggan.destroy', $pelanggan->kodepelanggan) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $pelanggans->appends(request()->query())->links() }}
    </div>
</div>
@endsection
