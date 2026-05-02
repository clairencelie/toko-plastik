@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Edit Supplier</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('supplier.index') }}">Supplier</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('supplier.update', $supplier->supplier) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small text-uppercase">ID Supplier</label>
                        <input type="text" class="form-control bg-light border-0" value="{{ $supplier->supplier }}" disabled>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Supplier</label>
                        <input type="text" name="keterangan" class="form-control form-control-lg bg-light border-0" value="{{ old('keterangan', $supplier->keterangan) }}" placeholder="Masukkan nama supplier..." required>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control bg-light border-0" rows="3" placeholder="Alamat lengkap...">{{ old('alamat', $supplier->alamat) }}</textarea>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Telepon</label>
                        <input type="text" name="telepon" class="form-control bg-light border-0" value="{{ old('telepon', $supplier->telepon) }}" placeholder="Nomor telepon...">
                    </div>
                </div>
            </div>
            
            <div class="mt-5 pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                </button>
                <a href="{{ route('supplier.index') }}" class="btn btn-light px-4 py-2 border">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
