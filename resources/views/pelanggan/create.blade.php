@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Tambah Pelanggan Baru</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('pelanggan.index') }}">Pelanggan</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('pelanggan.store') }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pelanggan</label>
                        <input type="text" name="namapelanggan" class="form-control form-control-lg bg-light border-0" placeholder="Masukkan nama pelanggan..." required autofocus>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control bg-light border-0" rows="3" placeholder="Alamat lengkap..."></textarea>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kota</label>
                        <input type="text" name="kota" class="form-control bg-light border-0" placeholder="Kota...">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Telepon</label>
                        <input type="text" name="telepon" class="form-control bg-light border-0" placeholder="Nomor telepon...">
                    </div>
                </div>
            </div>
            
            <div class="mt-5 pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-save me-2"></i> Simpan Pelanggan
                </button>
                <a href="{{ route('pelanggan.index') }}" class="btn btn-light px-4 py-2 border">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
