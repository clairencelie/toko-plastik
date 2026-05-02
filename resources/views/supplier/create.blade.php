@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Tambah Supplier Baru</h2>
    
    <form action="{{ route('supplier.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>ID Supplier (Angka)</label>
            <input type="number" name="autoid" required>
        </div>
        
        <div class="form-group">
            <label>Nama Supplier</label>
            <input type="text" name="namasupplier" required>
        </div>
        
        <div class="form-group">
            <label>Alamat</label>
            <input type="text" name="alamat">
        </div>
        
        <div class="form-group">
            <label>Telepon</label>
            <input type="text" name="telepon">
        </div>
        
        <div style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Simpan Supplier</button>
            <a href="{{ route('supplier.index') }}" class="btn" style="background:#ccc; color:#333;">Batal</a>
        </div>
    </form>
</div>
@endsection
