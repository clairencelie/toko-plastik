@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Edit Barang: {{ $barang->namabarang }}</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Barang</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('barang.update', $barang->kodebarang) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Barang</label>
                        <input type="text" name="namabarang" class="form-control form-control-lg bg-light border-0" value="{{ old('namabarang', $barang->namabarang) }}" required autofocus>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga Beli</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-0">Rp</span>
                            <input type="number" step="0.01" name="hargabeli" class="form-control bg-light border-0" value="{{ old('hargabeli', $barang->hargabeli) }}" required>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kelompok</label>
                        <select name="kelompok" class="form-select bg-light border-0" required>
                            @foreach($kelompoks as $kelompok)
                                <option value="{{ $kelompok->kelompok }}" {{ $barang->kelompok == $kelompok->kelompok ? 'selected' : '' }}>{{ $kelompok->keterangan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Supplier Utama</label>
                        <select name="supplier" class="form-select bg-light border-0" required>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->supplier }}" {{ $barang->supplier == $supplier->supplier ? 'selected' : '' }}>{{ $supplier->keterangan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kemasan</label>
                        <select name="kemasan" class="form-select bg-light border-0" required>
                            @foreach($kemasans as $kemasan)
                                <option value="{{ $kemasan->kemasan }}" {{ $barang->kemasan == $kemasan->kemasan ? 'selected' : '' }}>{{ $kemasan->keterangan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Satuan</label>
                        <select name="satuan" class="form-select bg-light border-0" required>
                            @foreach($satuans as $satuan)
                                <option value="{{ $satuan->satuan }}" {{ $barang->satuan == $satuan->satuan ? 'selected' : '' }}>{{ $satuan->keterangan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Isi Satuan</label>
                        <input type="number" step="0.01" name="isisatuan" class="form-control bg-light border-0" value="{{ old('isisatuan', $barang->isisatuan) }}">
                    </div>
                </div>
            </div>
            
            <div class="mt-5 pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-save me-2"></i> Perbarui Barang
                </button>
                <a href="{{ route('barang.index') }}" class="btn btn-light px-4 py-2 border">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
