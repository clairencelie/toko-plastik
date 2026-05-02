@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Daftar Barang</h2>
        <a href="{{ route('barang.create') }}" class="btn btn-primary">Tambah Barang</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kelompok</th>
                <th>Satuan</th>
                <th>Harga Beli</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangs as $barang)
            <tr>
                <td>{{ $barang->kodebarang }}</td>
                <td>{{ $barang->namabarang }}</td>
                <td>{{ $barang->kelompokRel->keterangan ?? '-' }}</td>
                <td>{{ $barang->satuanRel->keterangan ?? '-' }}</td>
                <td>{{ number_format($barang->hargabeli, 0, ',', '.') }}</td>
                <td>
                    <a href="{{ route('barang.edit', $barang->kodebarang) }}" class="btn btn-primary btn-sm">Edit</a>
                    <form action="{{ route('barang.destroy', $barang->kodebarang) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 1rem;">
        {{ $barangs->links() }}
    </div>
</div>
@endsection
