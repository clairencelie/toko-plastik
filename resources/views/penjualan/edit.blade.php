@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Edit Penjualan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('penjualan.index') }}" class="text-decoration-none">Penjualan</a></li>
                <li class="breadcrumb-item"><a href="{{ route('penjualan.show', $penjualan->nopenjualan) }}" class="text-decoration-none">{{ $penjualan->nopenjualan }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('penjualan.update', $penjualan->nopenjualan) }}" method="POST" id="penjualan-form">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">No. Transaksi</label>
                            <input type="text" class="form-control bg-light fw-bold text-primary" value="{{ $penjualan->nopenjualan }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Tanggal</label>
                            <input type="date" name="tglpenjualan" class="form-control" value="{{ $penjualan->tglpenjualan }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Pelanggan</label>
                            <select name="kodepelanggan" class="form-select select2-pelanggan" required>
                                <option value="">Pilih Pelanggan</option>
                                @foreach ($pelanggans as $pelanggan)
                                    <option value="{{ $pelanggan->kodepelanggan }}" {{ $penjualan->pelanggan == $pelanggan->kodepelanggan ? 'selected' : '' }}>
                                        {{ $pelanggan->namapelanggan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Jatuh Tempo</label>
                            <input type="date" name="tgljatuhtempo" class="form-control" value="{{ $penjualan->tgljatuhtempo }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-uppercase small text-muted">Item Penjualan</h6>
                    <button type="button" class="btn btn-sm btn-primary" id="add-row">
                        <i class="fas fa-plus me-1"></i> Tambah Barang
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="item-table">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 40%">Barang</th>
                                    <th style="width: 15%" class="text-center">Jumlah</th>
                                    <th style="width: 20%" class="text-end">Harga Jual</th>
                                    <th style="width: 20%" class="text-end">Subtotal</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penjualan->details as $index => $detail)
                                <tr class="item-row">
                                    <td class="p-3">
                                        <select name="items[{{ $index }}][kodebarang]" class="form-select item-select select2-barang" required>
                                            <option value="">Cari Barang...</option>
                                            @foreach ($barangs as $barang)
                                                <option value="{{ $barang->kodebarang }}"
                                                    data-price="{{ $barang->hargajual ?? 0 }}"
                                                    data-stock="{{ $barang->stok->saldoakhir ?? 0 }}"
                                                    {{ $detail->kodebarang == $barang->kodebarang ? 'selected' : '' }}>
                                                    {{ $barang->namabarang }} (Stok: {{ $barang->stok->saldoakhir ?? 0 }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" name="items[{{ $index }}][jumlah]" class="form-control text-center qty" min="0.01" step="0.01" value="{{ $detail->jumlah }}" required>
                                            <span class="input-group-text small bg-light stock-label" title="Stok Tersedia">
                                                {{ $barangs->firstWhere('kodebarang', $detail->kodebarang)?->stok?->saldoakhir ?? 0 }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $index }}][harga]" class="form-control text-end price" step="0.01" value="{{ $detail->harga }}" required>
                                    </td>
                                    <td class="text-end fw-bold p-3">
                                        <span class="subtotal-text">{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-link text-danger p-0 remove-row">
                                            <i class="fas fa-times-circle fa-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-dark p-4 text-white rounded-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-uppercase opacity-75">Bayar Tunai</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-white text-white opacity-75">Rp</span>
                                        <input type="number" name="tunai" class="form-control bg-transparent border-white text-white fw-bold" value="{{ $penjualan->tunai }}" id="input-tunai">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-uppercase opacity-75">Kembali</label>
                                    <div class="h4 fw-bold mb-0 mt-1">Rp <span id="kembali">0</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="fw-bold text-uppercase small opacity-75 d-block mb-1">Total Pembayaran</span>
                            <h1 class="fw-bold mb-3">Rp <span id="grand-total">{{ number_format($penjualan->grandtotal, 0, ',', '.') }}</span></h1>
                            <input type="hidden" name="grandtotal" id="input-grandtotal" value="{{ $penjualan->grandtotal }}">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('penjualan.show', $penjualan->nopenjualan) }}" class="btn btn-outline-light px-4">Batal</a>
                                <button type="submit" class="btn btn-primary btn-lg px-5 shadow" id="submit-btn">
                                    <i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    $(document).ready(function() {
        function initSelect2(scope) {
            $(scope).find('.select2-pelanggan').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Pelanggan'
            });
            $(scope).find('.select2-barang').select2({
                theme: 'bootstrap-5',
                placeholder: 'Cari Barang...'
            });
        }

        initSelect2('body');
        updateGrandTotal();

        const itemTable = $('#item-table tbody');
        let rowCount = {{ count($penjualan->details) }};

        $('#add-row').click(function() {
            const firstRow = $('.item-row:first');
            const newRow = firstRow.clone();

            newRow.find('.select2-container').remove();
            newRow.find('select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id').removeAttr('aria-hidden');
            newRow.find('option').removeAttr('data-select2-id');

            newRow.find('select').val('');
            newRow.find('.qty').val('');
            newRow.find('.price').val('');
            newRow.find('.subtotal-text').text('0');
            newRow.find('.stock-label').text('0');
            newRow.find('.remove-row').show();

            newRow.find('input, select').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, `[${rowCount}]`));
                }
            });

            itemTable.append(newRow);
            initSelect2(newRow);
            rowCount++;
        });

        $(document).on('click', '.remove-row', function() {
            if ($('.item-row').length <= 1) {
                alert('Minimal harus ada satu item.');
                return;
            }
            $(this).closest('tr').remove();
            updateGrandTotal();
        });

        $(document).on('change', '.select2-barang', function() {
            const row = $(this).closest('tr');
            const option = $(this).find(':selected');
            const price = option.data('price') || 0;
            const stock = option.data('stock') || 0;
            row.find('.price').val(price);
            row.find('.stock-label').text(stock);
            calculateRow(row);
        });

        $(document).on('input', '.qty, .price', function() {
            calculateRow($(this).closest('tr'));
        });

        $(document).on('input', '#input-tunai', function() {
            updateKembali();
        });

        function calculateRow(row) {
            const qty = parseFloat(row.find('.qty').val()) || 0;
            const price = parseFloat(row.find('.price').val()) || 0;
            const subtotal = qty * price;
            row.find('.subtotal-text').text(subtotal.toLocaleString('id-ID'));
            updateGrandTotal();
        }

        function updateGrandTotal() {
            let total = 0;
            $('.subtotal-text').each(function() {
                const val = $(this).text().replace(/\./g, '').replace(',', '.');
                total += parseFloat(val) || 0;
            });
            $('#grand-total').text(total.toLocaleString('id-ID'));
            $('#input-grandtotal').val(total);
            updateKembali();
        }

        function updateKembali() {
            const grandTotal = parseFloat($('#input-grandtotal').val()) || 0;
            const tunai = parseFloat($('#input-tunai').val()) || 0;
            const kembali = Math.max(0, tunai - grandTotal);
            $('#kembali').text(kembali.toLocaleString('id-ID'));
        }
    });
</script>
@endpush
@endsection
