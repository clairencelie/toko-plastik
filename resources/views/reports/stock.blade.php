@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Laporan Stok Barang (Mutasi)</h2>
    
    <table>
        <thead>
            <tr>
                <th>Barang</th>
                <th>Saldo Awal</th>
                <th>Beli</th>
                <th>Jual</th>
                <th>Adjustment</th>
                <th>Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $stock)
            <tr>
                <td>{{ $stock->barang->namabarang ?? $stock->kodebarang }}</td>
                <td>{{ number_format($stock->saldoawal, 2) }}</td>
                <td>{{ number_format($stock->beli, 2) }}</td>
                <td>{{ number_format($stock->jual, 2) }}</td>
                <td>{{ number_format($stock->adjustmen, 2) }}</td>
                <td><strong>{{ number_format($stock->saldoakhir, 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
