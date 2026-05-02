@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Ringkasan Laporan Keuangan</h2>
    
    <div style="display: flex; gap: 2rem; margin-top: 1rem;">
        <div class="card" style="flex: 1; background: #e3f2fd;">
            <h3>Total Hutang (AP)</h3>
            <p style="font-size: 1.5rem; color: #d32f2f;">Rp {{ number_format($totalHutang, 0, ',', '.') }}</p>
        </div>
        <div class="card" style="flex: 1; background: #e8f5e9;">
            <h3>Total Piutang (AR)</h3>
            <p style="font-size: 1.5rem; color: #388e3c;">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="card" style="margin-top: 2rem; background: #fffde7;">
        <h3>Estimasi Laba Kotor (Penjualan)</h3>
        <table>
            <tr>
                <td>Total Pendapatan</td>
                <td>Rp {{ number_format($salesProfit->revenue, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total HPP (FIFO)</td>
                <td>(Rp {{ number_format($salesProfit->total_hpp, 0, ',', '.') }})</td>
            </tr>
            <tr style="font-weight: bold; border-top: 2px solid #333;">
                <td>Laba Kotor</td>
                <td>Rp {{ number_format($salesProfit->revenue - $salesProfit->total_hpp, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
