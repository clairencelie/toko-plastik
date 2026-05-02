@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Edit Pelanggan</h2>
    
    <form action="{{ route('pelanggan.update', $pelanggan->autoid) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label>ID Pelanggan (Tetap)</label>
            <input type="text" value="{{ $pelanggan->autoid }}" disabled>
        </div>
        
        <div class="form-group">
            <label>Nama Pelanggan</label>
            <input type="text" name="namapelanggan" value="{{ $pelanggan->namapelanggan }}" required>
        </div>
        
        <div class="form-group">
            <label>Alamat</label>
            <input type="text" name="alamat" value="{{ $pelanggan->alamat }}">
        </div>
        
        <div class="form-group">
            <label>Kota</label>
            <input type="text" name="kota" value="{{ $pelanggan->kota }}">
        </div>
        
        <div class="form-group">
            <label>Telepon</label>
            <input type="text" name="telepon" value="{{ $pelanggan->telepon }}">
        </div>
        
        <div style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('pelanggan.index') }}" class="btn" style="background:#ccc; color:#333;">Batal</a>
        </div>
    </form>
</div>
@endsection
