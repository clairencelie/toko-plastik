<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan - {{ $penjualan->nopenjualan }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10.5pt;
            color: #000;
            background: #fff;
        }

        .page {
            width: 190mm;
            padding: 6mm 8mm;
            margin: 0 auto;
        }

        /* ── Header ── */
        .header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2mm;
        }
        .company-info { flex: 1; font-size: 10pt; line-height: 1.5; }
        .company-name { font-weight: bold; text-decoration: underline; font-size: 11pt; }
        .faktur-title {
            flex: 1;
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            padding-top: 4mm;
        }
        .header-spacer { flex: 1; }

        hr { border: none; border-top: 1px solid #000; margin: 2mm 0; }

        /* ── Info block ── */
        .info-section {
            display: flex;
            margin: 2mm 0 3mm;
            font-size: 10pt;
            line-height: 1.6;
        }
        .info-col { flex: 1; }
        .info-row { display: flex; }
        .info-label { min-width: 110px; }
        .info-sep   { width: 8px; }

        /* ── Items table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        thead tr {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        tbody tr:last-child { border-bottom: 1px solid #000; }
        th, td { padding: 1mm 2mm; vertical-align: middle; }
        th { font-weight: bold; }
        .col-no      { width: 22px;  text-align: center; }
        .col-kemasan { width: 90px;  text-align: center; }
        .col-satuan  { width: 90px;  text-align: center; }
        .col-harga   { width: 85px;  text-align: right; }
        .col-diskon  { width: 65px;  text-align: right; }
        .col-subtot  { width: 90px;  text-align: right; }

        /* ── Summary ── */
        .summary {
            display: flex;
            margin-top: 3mm;
            font-size: 10pt;
            line-height: 1.7;
        }
        .summary-col { flex: 1; }
        .summary-row { display: flex; }
        .sum-label { min-width: 100px; }
        .sum-sep   { width: 8px; }
        .sum-val   { text-align: right; min-width: 95px; }

        /* ── Footer ── */
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 10mm;
            font-size: 10pt;
            line-height: 1.6;
        }
        .footer-right { text-align: right; }
        .sign-space { margin-top: 14mm; }

        /* ── Print ── */
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .page { width: auto; padding: 5mm 8mm; margin: 0; }
            @page {
                size: auto;
                margin: 5mm 0;
            }
        }

        /* ── Screen preview ── */
        @media screen {
            body { background: #aaa; padding: 20px; }
            .page { background: #fff; box-shadow: 0 0 12px rgba(0,0,0,.4); }
        }
    </style>
    <script>
        // Set page height dynamically so printer only advances as far as the content.
        // Height must always exceed width (210mm) to stay portrait — if height < width
        // the browser treats the page as landscape and rotates the content.
        function setPageHeight() {
            var content = document.querySelector('.page');
            var heightPx = content.offsetHeight;
            var heightMm = Math.ceil(heightPx * 0.2646) + 10; // +10mm margin bawah
            heightMm = Math.max(heightMm, 211);               // min 211mm → selalu portrait
            var style = document.getElementById('dynamic-page-size');
            if (!style) {
                style = document.createElement('style');
                style.id = 'dynamic-page-size';
                document.head.appendChild(style);
            }
            style.textContent = '@page { size: 210mm ' + heightMm + 'mm; margin: 5mm 0; }';
        }

        window.onbeforeprint = setPageHeight;
        window.onafterprint = function() {
            var style = document.getElementById('dynamic-page-size');
            if (style) style.remove();
        };
    </script>
</head>
<body onload="window.print()">

    <div class="no-print" style="text-align:center; padding:10px; background:#f0f0f0; margin-bottom:10px;">
        <button onclick="window.print()" style="padding:7px 24px; font-size:13px; cursor:pointer;">
            Cetak Faktur
        </button>
    </div>

    <div class="page">

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
                @foreach($penjualan->details as $i => $detail)
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td class="col-barang">{{ $detail->namabarang }}</td>
                    <td class="col-kemasan">
                        @if($detail->namakemasan)
                            {{ number_format($detail->jumlahkemasan ?? 0, 2, '.', ',') }} {{ $detail->namakemasan }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="col-satuan">
                        {{ number_format($detail->jumlah, 2, '.', ',') }} {{ $detail->namasatuan }}
                    </td>
                    <td class="col-harga">{{ number_format($detail->harga, 2, '.', ',') }}</td>
                    <td class="col-diskon">{{ number_format($detail->diskon ?? 0, 2, '.', ',') }}</td>
                    <td class="col-subtot">{{ number_format($detail->subtotal, 2, '.', ',') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ════ SUMMARY ════ --}}
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

        {{-- ════ FOOTER ════ --}}
        <div class="footer">
            <div class="footer-left">
                <div>Yang Menerima,</div>
                <div>cap dan nama jelas</div>
                <div class="sign-space">_____________________</div>
            </div>
            <div class="footer-right">
                <div>Karawang, {{ \Carbon\Carbon::parse($penjualan->tglpenjualan)->format('d F Y') }}</div>
                <div>Hormat Kami,</div>
                <div class="sign-space">_____________________</div>
            </div>
        </div>

    </div>
</body>
</html>
