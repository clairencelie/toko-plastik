@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Edit Supplier</h2>
    
    <form action="{{ route('supplier.update', $supplier->autoid) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label>ID Supplier (Tetap)</label>
            <input type="text" value="{{ $supplier->autoid }}" disabled>
        </div>
        
        <div class="form-group">
            <label>Nama Supplier</label>
            <input type="text" name="namasupplier" value="{{ $supplier->namasupplier }}" required>
        </div>
        
        <div class="form-group">
            <label>Alamat</label>
            <input type="text" name="alamat" value="{{ $supplier->alamat }}">
        </div>
        
        <div class="form-group">
            <label>Telepon</label>
            <input type="text" name="telepon" value="{{ $supplier->telepon }}">
        </div>
        
        <div style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('supplier.index') }}" class="btn" style="background:#ccc; color:#333;">Batal</a>
        </div>
    </form>
</div>
@endsection
