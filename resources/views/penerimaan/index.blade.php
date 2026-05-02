@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Riwayat Penerimaan Barang</h2>
        <a href="{{ route('penerimaan.create') }}" class="btn btn-primary">Catat Penerimaan Baru</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No Penerimaan</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Total</th>
                <th>Tunai</th>
                <th>Kredit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penerimaans as $penerimaan)
            <tr>
                <td>{{ $penerimaan->nopenerimaan }}</td>
                <td>{{ $penerimaan->tglpenerimaan }}</td>
                <td>{{ $penerimaan->supplierRel->keterangan ?? $penerimaan->namasupplier }}</td>
                <td>{{ number_format($penerimaan->grandtotal, 0, ',', '.') }}</td>
                <td>{{ number_format($penerimaan->tunai, 0, ',', '.') }}</td>
                <td>{{ number_format($penerimaan->kredit, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 1rem;">
        {{ $penerimaans->links() }}
    </div>
</div>
@endsection
