@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Daftar Supplier</h2>
        <a href="{{ route('supplier.create') }}" class="btn btn-primary">Tambah Supplier</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Supplier</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>Hutang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $supplier)
            <tr>
                <td>{{ $supplier->supplier }}</td>
                <td>{{ $supplier->keterangan }}</td>
                <td>{{ $supplier->alamat }}</td>
                <td>{{ $supplier->telepon }}</td>
                <td>{{ number_format($supplier->hutang, 0, ',', '.') }}</td>
                <td>
                    <a href="{{ route('supplier.edit', $supplier->supplier) }}" class="btn btn-primary btn-sm">Edit</a>
                    <form action="{{ route('supplier.destroy', $supplier->supplier) }}" method="POST" style="display:inline">
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
        {{ $suppliers->links() }}
    </div>
</div>
@endsection
