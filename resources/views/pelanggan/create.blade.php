@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Tambah Pelanggan Baru</h2>
    
    <form action="{{ route('pelanggan.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>ID Pelanggan (Angka)</label>
            <input type="number" name="autoid" required>
        </div>
        
        <div class="form-group">
            <label>Nama Pelanggan</label>
            <input type="text" name="namapelanggan" required>
        </div>
        
        <div class="form-group">
            <label>Alamat</label>
            <input type="text" name="alamat">
        </div>
        
        <div class="form-group">
            <label>Kota</label>
            <input type="text" name="kota">
        </div>
        
        <div class="form-group">
            <label>Telepon</label>
            <input type="text" name="telepon">
        </div>
        
        <div style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Simpan Pelanggan</button>
            <a href="{{ route('pelanggan.index') }}" class="btn" style="background:#ccc; color:#333;">Batal</a>
        </div>
    </form>
</div>
@endsection
