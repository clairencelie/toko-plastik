@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Daftar Pelanggan</h2>
        <a href="{{ route('pelanggan.create') }}" class="btn btn-primary">Tambah Pelanggan</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Pelanggan</th>
                <th>Alamat</th>
                <th>Kota</th>
                <th>Telepon</th>
                <th>Piutang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pelanggans as $pelanggan)
            <tr>
                <td>{{ $pelanggan->kodepelanggan }}</td>
                <td>{{ $pelanggan->namapelanggan }}</td>
                <td>{{ $pelanggan->kecamatan }}</td>
                <td>{{ $pelanggan->kota }}</td>
                <td>{{ $pelanggan->telepon }}</td>
                <td>{{ number_format($pelanggan->piutang, 0, ',', '.') }}</td>
                <td>
                    <a href="{{ route('pelanggan.edit', $pelanggan->kodepelanggan) }}" class="btn btn-primary btn-sm">Edit</a>
                    <form action="{{ route('pelanggan.destroy', $pelanggan->kodepelanggan) }}" method="POST" style="display:inline">
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
        {{ $pelanggans->links() }}
    </div>
</div>
@endsection
