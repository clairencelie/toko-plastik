<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $penjualan->nopenjualan }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print Invoice</button>
    </div>

    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title">
                                <h2 style="margin:0">TOKO PLASTIK</h2>
                                <small style="font-size: 12px; font-weight: normal;">Premium Solutions for Plastic Needs</small>
                            </td>
                            <td>
                                Invoice #: {{ $penjualan->nopenjualan }}<br>
                                Tanggal: {{ \Carbon\Carbon::parse($penjualan->tglpenjualan)->format('d F Y') }}<br>
                                Jatuh Tempo: {{ $penjualan->tgljatuhtempo ? \Carbon\Carbon::parse($penjualan->tgljatuhtempo)->format('d F Y') : '-' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td>
                                <strong>Penerima:</strong><br>
                                {{ $penjualan->pelangganRel->namapelanggan ?? $penjualan->namapelanggan }}<br>
                                {{ $penjualan->pelangganRel->alamat ?? '-' }}
                            </td>
                            <td>
                                <strong>Petugas:</strong><br>
                                {{ $penjualan->namasalesman }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Item</td>
                <td style="text-align: center;">Jumlah</td>
                <td style="text-align: right;">Harga</td>
                <td style="text-align: right;">Subtotal</td>
            </tr>

            @foreach($penjualan->details as $detail)
            <tr class="item">
                <td>{{ $detail->namabarang }}</td>
                <td style="text-align: center;">{{ $detail->jumlah }} {{ $detail->namasatuan }}</td>
                <td style="text-align: right;">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                <td style="text-align: right;">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            <tr class="total">
                <td colspan="3"></td>
                <td style="text-align: right;">
                    <strong>Total: Rp {{ number_format($penjualan->grandtotal, 0, ',', '.') }}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td style="text-align: right;">
                    Tunai: Rp {{ number_format($penjualan->tunai, 0, ',', '.') }}
                </td>
            </tr>
            @if($penjualan->kredit > 0)
            <tr>
                <td colspan="3"></td>
                <td style="text-align: right; color: red;">
                    Sisa Kredit: Rp {{ number_format($penjualan->kredit, 0, ',', '.') }}
                </td>
            </tr>
            @endif
        </table>
        
        <div style="margin-top: 50px; text-align: center;">
            <p>Terima kasih atas kunjungan Anda!</p>
            <small>Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan.</small>
        </div>
    </div>
</body>
</html>
