<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tagihan {{ $tagihan->notagihan }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            background: #fff;
        }

        .page {
            width: 210mm;
            min-height: 270mm;
            margin: 0 auto;
            padding: 14mm 14mm 24mm;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header .company-name {
            font-size: 15px;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 1px;
        }
        .header .doc-title {
            font-size: 12px;
            font-weight: bold;
            font-style: italic;
            text-decoration: underline;
        }

        /* ── Info Tagihan ── */
        .info-table td {
            padding: 1px 0;
            font-size: 11px;
            vertical-align: top;
        }
        .info-table td:first-child { width: 80px; }
        .info-table td:nth-child(2) { width: 12px; }

        /* ── Tabel Utama ── */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .main-table thead tr {
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
        }
        .main-table thead th {
            padding: 5px 5px;
            font-size: 11px;
            font-weight: bold;
            text-align: left;
        }
        .main-table tbody tr {
            border-bottom: 0.5px solid #bbb;
        }
        .main-table tbody td {
            padding: 5px 5px;
            font-size: 11px;
        }
        .main-table tfoot tr {
            border-top: 1.5px solid #000;
        }
        .main-table tfoot td {
            padding: 6px 5px;
            font-size: 11px;
        }

        .text-right  { text-align: right; }
        .text-center { text-align: center; }

        /* ── Tanda Tangan ── */
        .sign-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .sign-box {
            text-align: center;
            width: 140px;
        }
        .sign-box .sign-space {
            height: 55px;
        }
        .sign-box .sign-line {
            border-top: 1px solid #000;
        }

        /* ── Tombol cetak (tidak tercetak) ── */
        .no-print {
            margin-top: 24px;
            text-align: center;
        }

        @media print {
            .no-print { display: none !important; }
            .page { padding: 10mm 12mm 20mm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="page">

    {{-- ═══════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════ --}}
    <div class="header">
        <div class="company-name">STEPHANY PLASTIK</div>
        <div class="doc-title">DAFTAR TAGIHAN</div>
    </div>

    {{-- ═══════════════════════════════════════════════
         INFO TAGIHAN
    ═══════════════════════════════════════════════ --}}
    <table class="info-table" style="margin-bottom: 8px;">
        <tr>
            <td>No Tagihan</td>
            <td>:</td>
            <td>{{ $tagihan->notagihan }}</td>
        </tr>
        <tr>
            <td>Tgl Tagihan</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($tagihan->tgltagihan)->format('d/m/Y') }}</td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════════════
         TABEL PIUTANG
    ═══════════════════════════════════════════════ --}}
    <table class="main-table">
        <thead>
            <tr>
                <th style="width:28px">No</th>
                <th style="width:105px">No Faktur</th>
                <th style="width:75px">Jth Tempo</th>
                <th>Pelanggan</th>
                <th class="text-right" style="width:110px">Total Penjualan</th>
                <th class="text-right" style="width:100px">Sisa Bayar</th>
                <th style="width:90px">Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tagihan->details as $i => $detail)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $detail->nopenjualan }}</td>
                <td>
                    @isset($arMap[$detail->nopenjualan])
                        {{ \Carbon\Carbon::parse($arMap[$detail->nopenjualan]->tgljatuhtempo)->format('d/m/Y') }}
                    @else
                        -
                    @endisset
                </td>
                <td>{{ $detail->nama }}</td>
                <td class="text-right">{{ number_format($detail->total, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($detail->sisabayar, 0, ',', '.') }}</td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>Grand Total</strong></td>
                <td class="text-right">
                    <strong>{{ number_format($tagihan->grandtotal, 0, ',', '.') }}</strong>
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @if($tagihan->keterangan && $tagihan->keterangan !== '-')
    <div style="margin-top:8px; font-size:10px; color:#555;">
        Keterangan: {{ $tagihan->keterangan }}
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         TANDA TANGAN
    ═══════════════════════════════════════════════ --}}
    <div class="sign-section">
        <div class="sign-box">
            <p>Yang Menagih,</p>
            <div class="sign-space"></div>
            <div class="sign-line"></div>
            <p style="margin-top:4px; font-weight:bold;">{{ auth()->user()->name }}</p>
        </div>
    </div>

    {{-- Tombol cetak manual (tidak tercetak) --}}
    <div class="no-print">
        <button onclick="window.print()"
                style="padding:9px 28px; font-size:13px; cursor:pointer;">
            Cetak Sekarang
        </button>
    </div>

</div>

</body>
</html>
