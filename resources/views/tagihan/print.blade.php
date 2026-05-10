<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tagihan - {{ $tagihan->notagihan }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; line-height: 1.4; color: #333; }
        .container { width: 100%; max-width: 800px; margin: auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .invoice-info { display: flex; justify-content: space-between; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f9f9f9; font-weight: bold; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; font-size: 14px; background-color: #f0f0f0; }
        .footer { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 200px; }
        .signature-space { height: 80px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .container { max-width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">SURAT TAGIHAN</h1>
            <p style="margin: 5px 0;">Toko Plastik Premium</p>
        </div>

        <div class="invoice-info">
            <div>
                <strong>KEPADA YTH:</strong><br>
                <span style="font-size: 16px;">{{ $tagihan->details->first()->nama ?? '-' }}</span>
            </div>
            <div class="text-right">
                <strong>No. Tagihan:</strong> {{ $tagihan->notagihan }}<br>
                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($tagihan->tgltagihan)->format('d/m/Y') }}
            </div>
        </div>

        <p>Bersama ini kami sampaikan daftar transaksi yang belum terselesaikan. Mohon kesediaannya untuk melakukan pembayaran tepat waktu.</p>

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>No. Faktur</th>
                    <th>Tanggal Faktur</th>
                    <th class="text-right">Total Faktur</th>
                    <th class="text-right">Sudah Bayar</th>
                    <th class="text-right">Sisa Tagihan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tagihan->details as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->nopenjualan }}</td>
                    <td>{{ $detail->tgltagihan }}</td>
                    <td class="text-right">{{ number_format($detail->total, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($detail->sudahbayar, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($detail->sisabayar, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-right">GRAND TOTAL</td>
                    <td class="text-right">Rp {{ number_format($tagihan->grandtotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top: 30px;">
            <strong>Keterangan:</strong><br>
            {{ $tagihan->keterangan }}
        </div>

        <div class="footer">
            <div class="signature-box">
                <p>Hormat Kami,</p>
                <div class="signature-space"></div>
                <p>( Admin Toko )</p>
            </div>
            <div class="signature-box">
                <p>Penerima,</p>
                <div class="signature-space"></div>
                <p>( ........................ )</p>
            </div>
        </div>

        <div class="no-print" style="margin-top: 30px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Cetak Sekarang</button>
        </div>
    </div>
</body>
</html>
