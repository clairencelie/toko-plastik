@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Riwayat Penjualan</h2>
        <a href="{{ route('penjualan.create') }}" class="btn btn-primary">Input Penjualan (Kasir)</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No Penjualan</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Total</th>
                <th>Tunai</th>
                <th>Kredit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualans as $penjualan)
            <tr>
                <td>{{ $penjualan->nopenjualan }}</td>
                <td>{{ $penjualan->tglpenjualan }}</td>
                <td>{{ $penjualan->pelangganRel->namapelanggan ?? $penjualan->namapelanggan }}</td>
                <td>{{ number_format($penjualan->grandtotal, 0, ',', '.') }}</td>
                <td>{{ number_format($penjualan->tunai, 0, ',', '.') }}</td>
                <td>{{ number_format($penjualan->kredit, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 1rem;">
        {{ $penjualans->links() }}
    </div>
</div>
@endsection
