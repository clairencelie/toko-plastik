@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Tambah Barang Baru</h2>
    
    <form action="{{ route('barang.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Kode Barang</label>
            <input type="number" name="kodebarang" required>
        </div>
        
        <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="namabarang" required>
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <div class="form-group" style="flex: 1;">
                <label>Kelompok</label>
                <select name="kelompok" required>
                    @foreach($kelompoks as $kelompok)
                        <option value="{{ $kelompok->kelompok }}">{{ $kelompok->keterangan }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" style="flex: 1;">
                <label>Kemasan</label>
                <select name="kemasan" required>
                    @foreach($kemasans as $kemasan)
                        <option value="{{ $kemasan->kemasan }}">{{ $kemasan->keterangan }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <div class="form-group" style="flex: 1;">
                <label>Satuan</label>
                <select name="satuan" required>
                    @foreach($satuans as $satuan)
                        <option value="{{ $satuan->satuan }}">{{ $satuan->keterangan }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" style="flex: 1;">
                <label>Isi Satuan</label>
                <input type="number" step="0.01" name="isisatuan">
            </div>
        </div>
        
        <div class="form-group">
            <label>Supplier Utama</label>
            <select name="supplier" required>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->supplier }}">{{ $supplier->keterangan }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label>Harga Beli</label>
            <input type="number" step="0.01" name="hargabeli" required>
        </div>
        
        <div style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Simpan Barang</button>
            <a href="{{ route('barang.index') }}" class="btn" style="background:#ccc; color:#333;">Batal</a>
        </div>
    </form>
</div>
@endsection
