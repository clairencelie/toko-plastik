<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan - {{ $penjualan->nopenjualan }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11pt;
            color: #000;
            background: #fff;
        }

        /* ── Screen: tampilkan seperti kertas 9.5" x 5.5" (1 set, kertas terbagi 2) ── */
        @media screen {
            body { background: #888; padding: 20px; }
            .page {
                width: 221mm;       /* 9.5in - 2×10mm margin */
                min-height: 134mm;  /* 5.5in - 2×3mm  margin */
                background: #fff;
                margin: 0 auto;
                padding: 3mm 0;
                box-shadow: 0 0 14px rgba(0,0,0,.45);
            }
        }

        /* ── Print: kertas 9.5" x 5.5" (1 set), margin luar diatur @page ── */
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .page { width: 100%; padding: 0; margin: 0; }
            @page {
                size: 9.5in 5.5in;
                margin: 3mm 10mm;   /* printable area ≈ 221 × 134mm */
            }
            /* Tiap set lanjutan (>11 item) selalu mulai di lembar baru */
            .page-break { page-break-before: always; break-before: page; }
        }
        @media screen {
            .page + .page { margin-top: 20px; }
        }

        /* ── Header ── */
        .header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5mm;
        }
        .company-info { flex: 1; font-size: 10.5pt; line-height: 1.4; }
        .company-name { font-weight: bold; text-decoration: underline; font-size: 12pt; }
        .faktur-title {
            flex: 1;
            text-align: center;
            font-size: 13.5pt;
            font-weight: bold;
            padding-top: 3mm;
        }
        .header-spacer { flex: 1; }

        hr { border: none; border-top: 1.5px solid #000; margin: 1.5mm 0; }

        /* ── Info block ── */
        .info-section {
            display: flex;
            margin: 1.5mm 0 1.5mm;
            font-size: 10.5pt;
            line-height: 1.35;
        }
        .info-col { flex: 1; }
        .info-row { display: flex; }
        .info-label { min-width: 16ch; flex-shrink: 0; white-space: nowrap; }
        .info-sep   { width: 8px; }

        /* ── Tabel item ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            table-layout: auto;
        }

        /* Header tabel diulang otomatis tiap halaman */
        thead { display: table-header-group; }

        thead tr {
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
        }
        tbody tr:last-child { border-bottom: 1.5px solid #000; }

        /* Cegah row terpotong antar halaman */
        tbody tr { break-inside: avoid; page-break-inside: avoid; }

        th, td {
            padding: 0.7mm 1.8mm;
            vertical-align: middle;
            white-space: nowrap;
        }
        th { font-weight: bold; }

        /* Kolom nama barang boleh wrap jika sangat panjang */
        tbody td:nth-child(2) { white-space: normal; }

        .col-no      { width: 22px;  text-align: center; }
        .col-kemasan { width: 82px;  text-align: center; }
        .col-satuan  { width: 82px;  text-align: center; }
        .col-harga   { width: 88px;  text-align: right;  }
        .col-diskon  { width: 62px;  text-align: right;  }
        .col-subtot  { width: 92px;  text-align: right;  }

        /* ── Ringkasan + Tanda tangan: selalu nempel jadi 1 blok ── */
        .closing-block { break-inside: avoid; page-break-inside: avoid; }

        .summary {
            display: flex;
            margin-top: 2.5mm;
            font-size: 10.5pt;
            line-height: 1.55;
        }
        .summary-col { flex: 1; }
        .summary-row { display: flex; }
        .sum-label { flex-shrink: 0; white-space: nowrap; }
        /* Kolom kiri (Total Barang/Diskon/Grand Total) vs kanan (Tunai/Kredit)
           masing-masing dilebarkan pas label terpanjangnya sendiri biar titik
           dua nempel rapi, ga ada jarak kosong berlebih */
        .summary-col:first-child .sum-label { min-width: 13ch; }
        .summary-col:last-child  .sum-label { min-width: 7ch; }
        .sum-sep   { width: 8px; }
        .sum-val   { text-align: right; min-width: 92px; }

        /* ── Tanda tangan ── */
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 6mm;
            font-size: 10.5pt;
            line-height: 1.5;
        }
        .sign-space { margin-top: 9mm; }

        /* ── Kepadatan adaptif: makin banyak item di 1 lembar, makin mepet
             spacing-nya (termasuk font tabel) supaya tetap muat di kertas
             5.5", tapi kalau item sedikit (lembar masih longgar) spacing
             tetap normal di atas ── */
        .density-normal th, .density-normal td { padding: 0.5mm 1.8mm; }
        .density-normal .summary    { margin-top: 1mm; }
        .density-normal .footer     { margin-top: 2mm; }
        .density-normal .sign-space { margin-top: 3mm; }

        .density-compact table { font-size: 9pt; }
        .density-compact th, .density-compact td { padding: 0.35mm 1.8mm; }
        .density-compact .summary    { margin-top: 0.3mm; }
        .density-compact .footer     { margin-top: 0.5mm; }
        .density-compact .sign-space { margin-top: 1.5mm; }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="text-align:center; padding:8px; background:#f0f0f0; margin-bottom:14px;">
        <button onclick="window.print()" style="padding:6px 22px; font-size:13px; cursor:pointer;">
            Cetak Faktur
        </button>
    </div>

    @php $detailChunks = $penjualan->details->chunk(11); $rowNum = 0; @endphp
    @foreach($detailChunks as $pageIndex => $items)
    @php
        $itemCount = $items->count();
        $density = $itemCount >= 8 ? 'density-compact' : ($itemCount >= 5 ? 'density-normal' : '');
    @endphp
    <div class="page{{ $pageIndex > 0 ? ' page-break' : '' }}{{ $density ? ' '.$density : '' }}">

        @if($pageIndex === 0)
        {{-- ════ HEADER ════ --}}
        <div class="header">
            <div class="company-info">
                <div class="company-name">STEPHANY PLASTIK</div>
                <div>Perum Karaba Indah Blok K:5</div>
                <div>HP 0813 19232177</div>
            </div>
            <div class="faktur-title">FAKTUR PENJUALAN</div>
            <div class="header-spacer"></div>
        </div>

        <hr>

        {{-- ════ INFO ════ --}}
        <div class="info-section">
            <div class="info-col">
                <div class="info-row">
                    <span class="info-label">No Penjualan</span>
                    <span class="info-sep">:</span>
                    <span>{{ $penjualan->nopenjualan }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tgl Penjualan</span>
                    <span class="info-sep">:</span>
                    <span>{{ \Carbon\Carbon::parse($penjualan->tglpenjualan)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tgl Jatuh Tempo</span>
                    <span class="info-sep">:</span>
                    <span>{{ $penjualan->tgljatuhtempo ? \Carbon\Carbon::parse($penjualan->tgljatuhtempo)->format('d/m/Y') : '-' }}</span>
                </div>
            </div>
            <div class="info-col">
                <div class="info-row">
                    <span class="info-label">Pelanggan</span>
                    <span class="info-sep">:</span>
                    <span>{{ $penjualan->namapelanggan }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Salesman</span>
                    <span class="info-sep">:</span>
                    <span>{{ $penjualan->namasalesman }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Alamat</span>
                    <span class="info-sep">:</span>
                    <span>{{ $penjualan->pelangganRel->kecamatan ?? $penjualan->pelangganRel->alamat ?? '-' }}</span>
                </div>
            </div>
        </div>
        @endif

        {{-- ════ ITEMS ════ --}}
        <table>
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-barang">Nama Barang</th>
                    <th class="col-kemasan">Kemasan</th>
                    <th class="col-satuan">Satuan</th>
                    <th class="col-harga">Harga</th>
                    <th class="col-diskon">Diskon</th>
                    <th class="col-subtot">Sub Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $detail)
                @php $rowNum++; @endphp
                <tr>
                    <td class="col-no">{{ $rowNum }}</td>
                    <td class="col-barang">{{ $detail->namabarang }}</td>
                    <td class="col-kemasan">
                        @if($detail->namakemasan)
                            {{ rtrim(rtrim(number_format($detail->jumlahkemasan ?? 0, 2, '.', ''), '0'), '.') }} {{ $detail->namakemasan }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="col-satuan">
                        {{ rtrim(rtrim(number_format($detail->jumlah, 2, '.', ''), '0'), '.') }} {{ $detail->namasatuan }}
                    </td>
                    <td class="col-harga">{{ number_format($detail->harga, 2, '.', ',') }}</td>
                    <td class="col-diskon">{{ number_format($detail->diskon ?? 0, 2, '.', ',') }}</td>
                    <td class="col-subtot">{{ number_format($detail->subtotal, 2, '.', ',') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($loop->last)
        <div class="closing-block">
        {{-- ════ RINGKASAN ════ --}}
        <div class="summary">
            <div class="summary-col">
                <div class="summary-row">
                    <span class="sum-label">Total Barang</span>
                    <span class="sum-sep">:</span>
                    <span class="sum-val">{{ number_format($penjualan->totalbarang, 2, '.', ',') }}</span>
                </div>
                <div class="summary-row">
                    <span class="sum-label">Total Diskon</span>
                    <span class="sum-sep">:</span>
                    <span class="sum-val">{{ number_format($penjualan->totaldiskon, 2, '.', ',') }}</span>
                </div>
                <div class="summary-row" style="font-weight:bold;">
                    <span class="sum-label">Grand Total</span>
                    <span class="sum-sep">:</span>
                    <span class="sum-val">{{ number_format($penjualan->grandtotal, 2, '.', ',') }}</span>
                </div>
            </div>
            <div class="summary-col" style="padding-left:15mm;">
                <div class="summary-row">
                    <span class="sum-label">Tunai</span>
                    <span class="sum-sep">:</span>
                    <span class="sum-val">{{ number_format($penjualan->tunai, 2, '.', ',') }}</span>
                </div>
                <div class="summary-row">
                    <span class="sum-label">Kredit</span>
                    <span class="sum-sep">:</span>
                    <span class="sum-val">{{ number_format($penjualan->kredit, 2, '.', ',') }}</span>
                </div>
            </div>
        </div>

        {{-- ════ TANDA TANGAN ════ --}}
        <div class="footer">
            <div class="footer-left">
                <div>Yang Menerima,</div>
                <div style="font-size:8.5pt;">cap dan nama jelas</div>
                <div class="sign-space">_____________________</div>
            </div>
            <div style="text-align:right;">
                <div>Karawang, {{ \Carbon\Carbon::parse($penjualan->tglpenjualan)->format('d F Y') }}</div>
                <div>Hormat Kami,</div>
                <div class="sign-space">_____________________</div>
            </div>
        </div>
        </div>
        @endif

    </div>
    @endforeach
</body>
</html>
